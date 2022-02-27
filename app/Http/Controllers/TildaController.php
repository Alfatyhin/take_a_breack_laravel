<?php

namespace App\Http\Controllers;

use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Services\GreenInvoiceService;
use App\Services\IcreditServise;
use App\Services\TildaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TildaController extends Controller
{
    public function newOrder(Request $request)
    {
        http_response_code(200);

        $post = $request->post();

        if (!empty($post['data-test'])) {
            $post = json_decode($post['data-test'], true);
            $post = $post['POST'];
            echo "<pre>";
            dd($post);

        } else {

            Storage::disk('local')->put('data/tilda-post-last.json', json_encode($post));

            $header = request()->header();
            $order_id = $post['payment']['orderid'];
            $webhoock['POST'] = $post;
            $webhoock['header'] = $header;
            WebhookLog::addLog('tilda new order - ' . $order_id, $webhoock);

        }

        $client = Clients::firstOrCreate([
            'email' => $post['Email']
        ], [
            'name' => $post['payment']['delivery_fio']
        ]);
        $client->phone = $post['Phone'];
        $client->save();

        $order = Orders::firstOrCreate([
            'order_id' => $post['payment']['orderid']
        ]);

        if ($post['paymentsystem'] == 'custom') {
            $payMethod = 1;
        } elseif ($post['paymentsystem'] == 'paypal') {
            $payMethod = 3;
        } elseif ($post['paymentsystem'] == 'cash') {
            $payMethod = 2;
        } else {
            $payMethod = 0;
        }

        $order->paymentMethod = $payMethod;
        if ($order->paymentStatus != 4) {
            $order->paymentStatus = '3';
        }
        if ($payMethod = 3 && !empty($post['payment']['systranid'])) {
            $order->paymentStatus = '4';
        }
        $order->clientId = $client->id;
        $order->orderPrice = $post['payment']['amount'];
        $order->orderData = json_encode($post);
        $order->save();

        echo "order save orderId = " . $order->order_id . '<br>';

        if ($order->paymentStatus == 4 && $order->invoiceStatus == 0) {
            $orderData = $order->orderData;


            try {
                $orderData = json_decode($order->orderData, true);

                $date = $order->created_at;
                $date = new Carbon($date);
                $orderData['date'] = $date->format('Y-m-d');
                $orderData['externalTransactionId'] = $post['payment']['systranid'];
                $orderData['paymentModule'] = 'PayPal';

                $invoiceData = TildaService::getDataToGreenInvoice($orderData);

            } catch (\Exception $e) {
                echo "<p>error invoice Data</p>";
                AppErrors::addError("error invoice Data to " . $order->order_id, $orderData);
            }

            $invoice = new GreenInvoiceService($order);

            try {
                $res = $invoice->newDoc($invoiceData);

                if (isset($res['errorCode'])) {
                    echo "<p>error invoice Code</p>";
                    var_dump($res);
                    AppErrors::addError("invoice create error to " . $order->order_id, json_encode($res));

                } else {
                    $order->invoiceStatus = 1;
                    $order->invoiceData = json_encode($res);
                    $order->save();

                    echo "<p>invoice Created </p>";
                }

            } catch (\Exception $e) {
                echo "<p>error invoice Doc</p>";
                AppErrors::addError("error invoice newDoc to " . $order->order_id, $invoiceData);
            }

        }




    }


    public function iCreditPayment(Request $request)
    {

        $post = $request->post();
        $test_mode = false;

        if (!empty($post['data-test'])) {
            $post = json_decode($post['data-test'], true);
            $post = $post['POST'];
            $test_mode = true;
            echo "<pre>";
//            dd($post);
            $order_id = preg_replace('/[0-9]+:/', '', $post['order_id']);

        } else {

            if ($test_mode) {
                echo "<pre>";

            } else {
                $hook_name = 'tilda payment order - ';
                $order_id = preg_replace('/[0-9]+:/', '', $post['order_id']);

                $webhoock['POST'] = $post;
                $webhoock['header'] = request()->header();
                WebhookLog::addLog( $hook_name . $order_id, $webhoock);
            }

        }

        if (isset($post['signature'])) {

            $TildaService = new TildaService($post);

            if ($TildaService->validate()) {

                // Reply with 200OK to Ecwid
                http_response_code(200);


                $icraditDataOrder = $TildaService->getIcreditDataOrder();

                /////////////////////////////////////////////////////////////////////////

                $icreditServise = new IcreditServise();
                $result = $icreditServise->getUrl($icraditDataOrder);


                if ($test_mode) {
                    echo "<p>icredit response</p>";
                    print_r($result);
                }
                $paymentUrl = $result['URL'];

                if ($paymentUrl) {

                    $orderPay['signature'] = $post['signature'];
                    $orderPay['returnUrl'] = $post['url_redirect'];
                    $orderPay['orderId'] = $order_id;
                    $orderPay['referer_url'] = $request->header('REFERER');

                    $order = Orders::where('order_id', $order_id)->first();

                    if (!$order) {
                        print_r($post);

                        $client = Clients::firstOrCreate([
                            'email' => $post['mail']
                        ], [
                            'name' => $post['name']
                        ]);
                        $client->phone = $post['phone'];
                        $client->save();

                        $order = Orders::firstOrCreate([
                            'order_id' => $order_id
                        ]);
                        $order->paymentMethod = 1;
                        $order->paymentStatus = 3;
                        $order->clientId = $client->id;
                        $order->orderPrice = $post['order_summ'];
                        $order->orderData = json_encode($post);
                        $order->save();

                        echo "order save orderId = " . $order->order_id . '<br>';
                    } else {
                        print_r('order isset');
                    }


                    if ($test_mode) {
                        echo "<p>orderPay session input</p>";
                        print_r($orderPay);
                    }
                    session()->put(['orderPay' => $orderPay]);

                    if ($test_mode) {
                        return redirect('orders/thanks');
                    } else {
                        return redirect($paymentUrl);
                    }

                    return redirect($paymentUrl);
                } else {
                    print_r($result);
                }

            } else {
                // Reply with 200OK to Ecwid
                http_response_code(400);
                echo "no valid data";
            }

        } else {
            // Reply with 200OK to Ecwid
            http_response_code(400);
            echo "error data";
        }
    }

    public function testTildaInput()
    {
        $pach = 'data/tilda-post-last.json';


        echo '<pre>';
        if ( Storage::disk('local')->exists($pach) ) {

            $res = Storage::disk('local')->get($pach);
            $res = json_decode($res,true);
            dd($res);

        } else {

            var_dump('file not exit');
        }
    }

    public function createInvoiceOrder(Request $request) {

        $order_id = $request->get('orderId');

        if (!empty($order_id)) {

            $order = Orders::where('order_id', $order_id)->first();
            if (!empty($order)) {
                $order->paymentStatus = 4;
                $order->save();

                if ($order->invoiceStatus == 0 && $order->paymentStatus == 4) {


                    $orderData = json_decode($order->orderData, true);

                    $date = $order['created_at'];
                    $date = new Carbon($date);
                    $orderData['date'] = $date->format('Y-m-d');
                    $orderData['paymentModule'] = 'iCredit';


                    $invoiceData = TildaService::getDataToGreenInvoice($orderData);


                    $invoice = new GreenInvoiceService($order);


                    $res = $invoice->newDoc($invoiceData);

                    if (isset($res['errorCode'])) {
                        echo "<p>error invoice Code</p>";
                        var_dump($res);
                        AppErrors::addError("invoice create error to " . $order->ecwiId, json_encode($res));

                    } else {
                        $order->invoiceStatus = 1;
                        $order->invoiceData = json_encode($res);
                        $order->save();

                        echo "<p>invoice Created </p>";
                    }

                }
            }
        }
    }

}
