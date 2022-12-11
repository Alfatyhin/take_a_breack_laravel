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
        $all = $request->all();
        WebhookLog::addLog('iCredit thanks all', $all);

        $orderPay = session('orderPay');
        if (!empty($orderPay)) {
            WebhookLog::addLog('iCredit pay step 2', $orderPay);
        }

        $orderId = $all['id'];

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


            $order = Orders::where('order_id', $orderId)->first();
            if ($icreditPay->paymentStatus == 'VERIFIED') {
                $order->paymentStatus = 4;
            } else {
                $order->paymentStatus = 3;
            }
            $order->save();

            if ($order->invoiceStatus == 0) {
                $invoiceDada = OrderService::getShopOrderDataToGinvoice($order);
                try {

                    $invoiceService = new GreenInvoiceService($order);
                    $res = $invoiceService->newDoc($invoiceDada);

                    if (isset($res['errorCode'])) {

                        AppErrors::addError("invoice create error to " . $orderId, $res);

                    } else {

                        $order->invoiceStatus = 1;
                        $order->invoiceData = json_encode($res);
                        $order->save();

                    }

                } catch (\Exception $e) {

                    AppErrors::addError("error invoice newDoc to " . $orderId, $invoiceDada);

                }
            }

            $orderData = json_decode($order->orderData, true);
            $lang = $orderData['lang'];

            return redirect(route("order_thanks", ['lang'=> $lang]));
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

    public function testGetPaymentUrl(Request $request)
    {
        $id = $request->all('order_id');
        $order = Orders::Where('id', $id)->first();
        $orderData = json_decode($order->orderData, true);

        $orderService = new OrderService();
        $icreditService = new IcreditServise();

        if (isset($orderData['Cart'])) {
            $orderData = $orderService->getIcreditDataOrder($orderData);
        }

        if (isset($orderData['order_data'])) {
            $orderData = $orderService::getShopIcreditOrderData($order);
        }
        $orderData['name'] = 'test';
        $res = $icreditService->getUrl($orderData);
        session('orderPay', $res);

        if (isset($res['URL'])) {
            return redirect($res['URL']);
        } else {
            dd($orderData, $res);
        }
    }

    public function getIcreditPaymentUrl(Request $request)
    {
        $id = $request->all('order_id');
        $order = Orders::Where('id', $id)->first();
        $orderData = json_decode($order->orderData, true);

        $orderService = new OrderService();
        $icreditService = new IcreditServise();

        if (isset($orderData['Cart'])) {
            $orderData = $orderService->getIcreditDataOrder($orderData);
        }

        if (isset($orderData['order_data'])) {
            $orderData = $orderService::getShopIcreditOrderData($order);
        }

        $res = $icreditService->getUrl($orderData);
        session('orderPay', $res);

        if (isset($res['URL'])) {
            return redirect($res['URL']);
        } else {
            dd($orderData, $res);
        }
    }

}
