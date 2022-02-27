<?php

namespace App\Http\Controllers;

use App\Models\IcreditPayments;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\PayPalService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function index(Request $request)
    {
        $paypalService = new PayPalService('test');

        echo "<pre>";

    }

    public function getButton(Request $request)
    {
        $order_id = $request->get('id');
        $order = false;
        $paypalService = new PayPalService('live');
        $client_id = $paypalService->getClientId();

        if ($order_id) {
            $order = Orders::where('order_id', $order_id)->first();
        }

        $orderData = json_decode($order->orderData, true);

        if ($orderData['Cart']['person']['name'] == 'test') {
            $orderData['option']['total_price'] = 1;
        }

        return view('paypal.button', [
            'client_id'  => $client_id,
            'orderData'  => $orderData,
            'order_id'   => $order_id
        ]);
    }

    public function orderCapture(Request $request)
    {
        $post = $request->post();
        $data_test = $request->post('data-test');
        $paypalService = new PayPalService('live');

        if ($data_test) {
            $post = json_decode($data_test, true);
            $id = $post['data']['id'];
            echo "<pre>";

            dd($post);
        } else {
            $id = $post['data']['id'];
            WebhookLog::addLog('PayPal payd '.$id, $post);
        }

        header('Access-Control-Allow-Origin: *');
        http_response_code(200);

        $res_order = $paypalService->checkoutOrder($id);

        $status = false;
        if ($res_order) {
            $status = $res_order['status'];
            if ($status == 'COMPLETED') {
                foreach ($res_order['purchase_units'] as $item) {
                    $order_id = $item['custom_id'];
                    $order = Orders::where('order_id', $order_id)->first();
                    $order->paymentStatus = 4;
                    $order->paymentDate = new Carbon();
                    $order->save();

                    $payment = new IcreditPayments();
                    $payment->orderId = $order_id;
                    $payment->paymentStatus = $status;
                    $payment->data = json_encode($res_order);


                    $ecwidService = new EcwidService();
                    $data = $order->orderData;
                    $data = json_decode($data, true);
                    $ecwidService->productsUpdateCount($data);

                    AppServise::getQuest("https://takeabreak.website/api/create_amo_order?id=" . $order_id);
                }
            }
        }

        if ($status) {
            $res = [
                'result' => true
            ];
        } else {
            $res = [
                'result' => false
            ];
        }
        echo json_encode($res);
    }

}
