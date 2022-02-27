<?php


namespace App\Http\Controllers;


use App\Models\AppErrors;
use App\Models\IcreditPayments;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\IcreditServise;
use App\Services\OrderService;
use App\Services\TildaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IcreditController extends Controller
{


    public function index(Request $request)
    {
        $orderId = $request->get('orderId');

        if (!empty($orderId)) {
            $orderId = $request->get('orderId');
            $Icredit = IcreditPayments::where('orderId', $orderId)->paginate(20);
        } else {
            $Icredit = IcreditPayments::orderBy('id', 'DESC')->paginate(20);
        }


        return view('icredit.index', [
            'icredit' => $Icredit,
        ]);

    }


    public function orderThanksIcredit(Request $request)
    {
        $orderPay = session('orderPay');

        WebhookLog::addLog('iCredit thanks', $orderPay);
        $all = $request->all();
        WebhookLog::addLog('iCredit thanks all', $all);
        if (!empty($orderPay)) {
            WebhookLog::addLog('iCredit pay step 2', $orderPay);
        }

        $test_mode = false;
        if ($request->post('data-test')) {
            $dataTest = $request->post('data-test');
            $dataTest = json_decode($dataTest, true);
            $orderId = $dataTest['id'];

            return redirect("https://takeabreak.website/orders/thanks?id=$orderId&test_mode=1");
        }

        if (isset($all['test_mode'])) {
            if ($all['test_mode'] == 1) {
                $test_mode = true;
            }
        }


        if (isset($orderPay['referer_url']) && $orderPay['referer_url'] != 'https://takeabreak.website/orders/webhooks') {
            session()->forget('orderPay');
        }


        if (empty($orderPay)) {
            $orderId = $request->get('id');
            WebhookLog::addLog('iCredit pay step 3', $orderId);
        }

        if (!empty($orderId)) {

            $icreditPay = IcreditPayments::where('orderId', $orderId)->first();


            $x = 1;
            while ($x <= 5) {
                sleep(1);
                echo '.';
                $icreditPay = IcreditPayments::where('orderId', $orderId)->first();
                if (!empty($icreditPay)) {
                    $x = 6;
                }
                $x++;
            }



            if (!empty($icreditPay) && $icreditPay['paymentStatus'] == 'VERIFIED') {

                $data = json_decode($icreditPay['data'], true);

                if ($data['Custom2'] == 'ServerTB') {

                    $paymentDate = new Carbon();
                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                    $order = Orders::where('order_id', $orderId)->first();
                    $order->paymentStatus = 4;
                    $order->paymentDate = $paymentDateString;
                    $order->save();

                    if ($test_mode) {
//                        dd($paymentDateString);
                    } else {
                        AppServise::getQuest("https://takeabreak.website/api/create_amo_order?id=" . $order->order_id);
                    }


                    $orderData = json_decode($order->orderData, true);

                    $invoiceDada = OrderService::getOrderDataToGinvoice($orderData);



                    if ($orderData['Cart']['person']['name'] == 'test') {
                        $invoiceDada['email'] = 'virikidorhom@gmail.com';
                    }

                    if ($test_mode) {
//                        dd($invoiceDada);
                        echo "<h2> test mode - invoice not create </h2>";
                    } else {
                        if ($order->invoiceStatus != 1) {

//                            $invoiceDada['payDate'] = $paymentDate->format('Y-m-d');
//                            $invoice = new GreenInvoiceService($order);

//                            $res = $invoice->newDoc($invoiceDada);
//                            if (isset($res['errorCode'])) {
//                                AppErrors::addError("invoice create error to " . $orderId, $res);
//
//                            } else {
//                                $order->invoiceStatus = 1;
//                                $order->invoiceData = json_encode($res);
//                                $order->save();
//
//                                echo "<h2> invoice created </h2>";
//                            }

                        }



                        $ecwidService = new EcwidService();
                        $ecwidService->productsUpdateCount($orderData);

                    }


                    return redirect("https://takeabreak.website/api/orders/view_mail?id=$order->order_id");
                }

                if ($data['Custom2'] == 'Ecwid') {
                    $ecwidService = new EcwidService();
                    $ecwidService->payStatusUpdate($orderPay['transaction'], 4);
                    $log['order_id'] = $data['Custom1'];
                    $log['status'] = $icreditPay['paymentStatus'];
                    WebhookLog::addLog('iCredit pay step 3', $log);
                }

                sleep(2);

                if (isset($orderPay['referer_url']) && $orderPay['referer_url'] != 'https://takeabreak.website/orders/webhooks') {
                    dd($order->toArray());
                }
                if ($data['Custom2'] == 'Ecwid') {
                    if ($orderPay['test_mode']) {
                        echo "<pre>";
                        print_r($icreditPay->toArray());
                    }
                    return redirect($orderPay['returnUrl']);
                } else {
                    if ($data['Custom2'] != 'ServerTB') {
                        return redirect($orderPay['referer_url']);
                    }
                }


            } else {

                echo "<h2> you paid no success </h2>";
                sleep(5);
                if (isset($orderPay['referer_url']) && $orderPay['referer_url'] != 'https://takeabreak.website/orders/webhooks') {
                   dd('test end');
                }


                return redirect($orderPay['returnUrl']);


            }

        } else {
            echo "<h2> Thanck You..";
        }

    }


    // слушает iCredit webhook
    public function orderRequestIcredit(Request $request)
    {
        $Data = $request->all();
        header("Access-Control-Allow-Origin: *");
        header("HTTP/1.1 200 OK");

        if (empty($request->post('data-test'))) {
            $header = request()->header();
            $logData['data'] = $Data;
            $logData['header'] = $header;
            $logData['ip'] = $_SERVER['REMOTE_ADDR'];
            WebhookLog::addLog('iCredit', $logData);
        } else {
            $logData['data'] = $Data;
            $header = request()->header();
            $logData['data'] = $Data;
            $logData['header'] = $header;
            $logData['ip'] = $_SERVER['REMOTE_ADDR'];
            $Data = $request->post('data-test');
            $Data = json_decode($Data, true);
            $Data = $Data['data'];
            echo '<pre>';
            print_r($Data);
        }


        if ($Data['Custom1']) {
            // авто проверка статуса оплаты
            $data = array(
                "GroupPrivateToken" => $Data['GroupPrivateToken'],
                "SaleId" => $Data['SaleId'],
                "TotalAmount" => $Data['TransactionAmount']
            );
            $res = IcreditServise::checkPaymentStatus($data);

//            dd($res);

            if ($res['Status']) {
                $icreditPayment = new IcreditPayments();
                $icreditPayment->orderId = $Data['Custom1'];
                $icreditPayment->paymentStatus = $res['Status'];
                $icreditPayment->data = json_encode($Data);
                $icreditPayment->save();

                if ($Data['Custom2'] == 'Ecwid' && $res['Status'] == 'VERIFIED') {
                    $ecwidService = new EcwidService();
                    $ecwidService->paymentStatusUpdate($Data['Custom1'], 4);
                }

            } else {
                AppErrors::addError('iCredit', $res);
            }
        }
    }

}
