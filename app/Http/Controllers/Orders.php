<?php

namespace App\Http\Controllers;

use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\IcreditPayments;
use App\Models\Orders as OrdersModel;
use App\Models\WebhookLog;
use App\Providers\AppServiceProvider;
use App\Services\AmoCrmServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\IcreditServise;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

;

class Orders extends Controller
{

    public function getIcreditPaymentUrl(Request $request)
    {
        $Data = $request->all();
        if (!empty($Data['data'])) {

            $data = $Data['data'];
            $order = new EcwidService();
            $order->setData($data);
            $result = $order->getIcreditUrl();
            $paymentUrl = $result['URL'];

            if ($paymentUrl) {
                $ecwidData = $order->getData();
                $orderPay['transaction'] = $ecwidData['cart']['order']['referenceTransactionId'];
                $orderPay['returnUrl'] = $ecwidData['returnUrl'];
                $orderPay['orderId'] = $ecwidData['cart']['order']['id'];
                session()->flash('orderPay', $orderPay);


                return redirect($paymentUrl);
            }

        }
    }

    public function orderThanksIcredit()
    {
        $orderPay = session('orderPay');
        $icreditPay = IcreditPayments::where('orderId', $orderPay['orderId'])->first()->toArray();

        if ($icreditPay['paymentStatus'] == 'VERIFIED') {
            $paymentStatus = 4;
        } else {
            $paymentStatus = 3;
        }

        $ecwidService = new EcwidService();
        $ecwidService->payStatusUpdate($orderPay['transaction'], $paymentStatus);

        return redirect($orderPay['returnUrl']);
    }

    // слушает iCredit webhook
    public function orderRequestIcredit(Request $request)
    {
        $Data = $request->all();
        // Reply with 200OK to Ecwid
        http_response_code(200);
        WebhookLog::addLog('iCredit', $Data);

        if ($Data['Custom1']) {
            // авто проверка статуса оплаты
            $data = array(
                "GroupPrivateToken" => $Data['GroupPrivateToken'],
                "SaleId" => $Data['SaleId'],
                "TotalAmount" => $Data['TransactionAmount']
            );
            $res = IcreditServise::checkPaymentStatus($data);

            if ($res['Status']) {
                $icreditPayment = new IcreditPayments();
                $icreditPayment->orderId = $Data['Custom1'];
                $icreditPayment->paymentStatus = $res['Status'];
                $icreditPayment->data = json_encode($Data);
                $icreditPayment->save();
            } else {
                AppErrors::addError('iCredit', $res);
            }
        }
    }



    public function ecwidWebHook(Request $request)
    {

        $testData = $request->post('data-test');
        if (!empty($testData)) {
            $data = json_decode($testData, true);
            $header = $data['header'];
            $Data = $data['data'];

        } else {
            $Data = $request->all();
            $header = request()->header('x-ecwid-webhook-signature');

            if (!empty($Data)) {
                $webhoock['ip'] = $this->getIp();
                $webhoock['data'] = $Data;
                $webhoock['header'] = $header;

                WebhookLog::addLog('ecwid webhook new', $webhoock);
            }

        }

        // Reply with 200OK to Ecwid
        http_response_code(200);

        $ecwidService = new EcwidService();
        $headerHash = $ecwidService->getHeaderHash($Data);
        if ($header != $headerHash) {
            echo "error header \n";
            WebhookLog::addLog('error ecwid webhook header', $header);
            die;
        } else {
            echo "header test true \n";
        }



        if ($Data['eventType'] == 'order.created') {

            $data = $Data['data'];

            $orderId = $data['orderId'];

            Storage::disk('local')->append('data/ecwid webhook log.txt',
                $orderId . ' order eventType - created ');


            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            // уменьшаем количество составляюших в наборах
            $ecwidService->productsService($orderEcwid['items'], [
                'subProductCountAction' => 'down',
            ]);

            Storage::disk('local')
                ->append('data/ecwid webhook log.txt', ' get order ecwid Data');

            $paymentMethod = EcwidService::getPaymentMethod($orderEcwid);

            if ($orderEcwid['paymentStatus'] == 'PAID') {
                $paymentStatus = 4;
            } else {
                $paymentStatus = 3;
            }

            $client = Clients::firstOrNew([
                'email' => $orderEcwid['email']
            ]);

            $client->name = $orderEcwid['billingPerson']['name'];
            if (!empty($orderEcwid['billingPerson']['phone'])) {
                $client->phone = $orderEcwid['billingPerson']['phone'];
            }
            $client->save();

            $order = OrdersModel::firstOrCreate([
                'ecwidId' => $orderId
            ]);
            $order->paymentMethod = $paymentMethod;
            $order->paymentStatus = $paymentStatus;
            $order->clientId = $client->id;
            $order->orderPrice = $orderEcwid['total'];
            $order->orderData = json_encode($orderEcwid);
            $order->save();

            Storage::disk('local')
                ->append('data/ecwid webhook log.txt', 'order save');


            if ($paymentStatus == 4) {
                if ($paymentMethod != 2) {
                    $date = $orderEcwid['createDate'];
                    $paymentDate = Carbon::parse($date);
                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                }

                $order->paymentDate = $paymentDateString;
                $order->save();
            }


            if ($order->invoiceStatus == 0 && $paymentStatus == 4) {

                try {
                    $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
                } catch (\Exception $e) {
                    AppErrors::addError("error invoice Data to " . $order->ecwiId, $orderEcwid);
                }

                $invoice = new GreenInvoiceService();

                // настройки аккаунта для инвойса
                // для PayPal
                if ($order->paymentMethod == 3) {

                    $dataJson = Storage::disk('local')->get('data/app-setting.json');
                    $settingData = json_decode($dataJson, true);

                    $invoice = $invoice->setMode($settingData['invoice_mode_paypal']);
                }


                try {
                    $res = $invoice->newDoc($invoiceDada);

                    if (isset($res['errorCode'])) {
                        AppErrors::addError("invoice create error to " . $order->ecwiId, json_encode($res));

                    } else {
                        $order->invoiceStatus = 1;
                        $order->invoiceData = json_encode($res);
                        $order->save();
                    }

                } catch (\Exception $e) {
                    AppErrors::addError("error invoice newDoc to " . $order->ecwiId, $invoiceDada);
                }
            }

            if (empty($order->amoId)) {
                // пролучаем массив для амо
                $amoDataEcwid = EcwidService::getAmoDataLead($orderEcwid);
                $amoCrmServise = new AmoCrmServise();
                // создаем сделку
                if (!empty($client->amoId)) {
                    $amoDataEcwid['clientAmoId'] = $client->amoId;
                }
                $res = $amoCrmServise->NewOrder($amoDataEcwid);

                if (!empty($res['amo_id'])) {
                    $order->amoId = $res['amo_id'];
                    $order->save();

                    if (empty($client->amoId)) {
                        $client->amoId = $res['client_id'];
                        $client->save();
                    }


                    $amoNotes = EcwidService::getAmoNotes($orderEcwid);
                    $amoCrmServise->addTextNotesToLead($order->amoId, $amoNotes);

                    $amoProductsList = EcwidService::amoProductsList($orderEcwid['items']);
                    $amoCrmServise->addProductsToLead($amoProductsList, $order->amoId);

                    $order->amoData = json_encode($res);
                    $order->save();
                } else {
                    AppErrors::addError('error create amo lead', $res);
                }
            }
            // end eventType - 'order.created' end
        } elseif ($Data['eventType'] == 'product.updated') {

            echo "<pre>";
            $productId = $Data['entityId'];
            $product = $ecwidService->getProduct($productId);
            $ecwidService->inStockUpdate($product);

        }
    }

    public function getIp() {

        $keys = [
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim($_SERVER[$key]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                } else {
                    return '0.0.0';
                }
            }
        }
    }




}
