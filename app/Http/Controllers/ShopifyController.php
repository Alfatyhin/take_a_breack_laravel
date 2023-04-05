<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\UtmModel;
use App\Models\WebhookLog;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShopifyController extends Controller
{


    public function testWebhook(Request $request)
    {
        $data = $request->post('json');
        $data = json_decode($data, true);


        $action = $data['action'];

        if ($action == 'orders/create') {
//            WebhookLog::addLog("Shopify new order webhook", $data);

            $client_data = $data['customer'];

            $client['clientName'] = $client_data['first_name'] . ' ' . $client_data['last_name'];
            if (isset($client_data['email']))
                $client['email'] = $client_data['email'];
            else
                $client['email'] = 'generate_'.time().'@site.com';
            if (isset($client_data['phone']))
                $client['phone'] = $client_data['phone'];

            $client = OrderService::clientCreateOrUpdate($client);

            $order = new Orders();
            $order->order_id = $data['name'];
            $order->clientId = $client->id;
            $order->orderData = json_encode($data);
            if ($data['financial_status'] == 'paid')
                $order->paymentStatus = 4;
//            $order->save();

            $amoData = $this->AmoOrderPrepeare($data);

            dd($amoData);

        }

    }

    public function webhook(Request $request)
    {

        if ($request->hasHeader('X-Shopify-Hmac-Sha256')) {

            if($this->verifyWebhook(file_get_contents('php://input'), $request->header('X-Shopify-Hmac-Sha256'))) {

                $data = $request->json()->all();
                $data['action'] = $request->header('X-Shopify-Topic');
                $id = $request->header('X-Shopify-Webhook-Id');
                $action = $request->header('X-Shopify-Topic');

                $test = WebhookLog::where('name', "ShopifyWebhook - |$action|".$id)->first();

                if (!$test) {
                    http_response_code(200);
                    $webhook = new WebhookLog();
                    $webhook->name = "ShopifyWebhook - |$action|".$id;
                    $webhook->data = json_encode($data);
                    $webhook->save();

                }

                if ($action == 'orders/create') {
                    $shipping_address = $data['shipping_address'];
                }

            }
        }

    }

    private function verifyWebhook($data, $header)
    {
        $secret = env('SHOPY_SECRET');

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $secret, true));
        return hash_equals($calculated_hmac, $header);
    }

    private function AmoOrderPrepeare($data)
    {
        $products = $data['line_items'];

        // формируем массив данных для амо
        $pipelineId = '4651807'; // воронка
        $statusId = '43924885'; // статус


        foreach ($products as $key => $item) {
            $product_name = $item['name'];
            $tags[] = $product_name;
        }

        if ($data['financial_status'] == 'paid') {
            $payment = 'Оплачен';
        } else {
            $payment = false;
        }

        // deliwery adress
        $address = '';
        if (isset($data['shipping_address']) && !empty($data['shipping_address'])) {

            $address = $data['shipping_address']['city']
                . ' ' . $data['shipping_address']['address1'];
        }


        $tags[] = $data['customer_locale'];

        foreach ($data['note_attributes'] as $item) {
            if($item['name'] == 'delivery_date_origin') {
                $date = $item['value'];
            }
            if($item['name'] == 'Delivery Time') {
                $time = $item['value'];
            }
        }

        if (!isset($time)) {
            $time = '9:00-21:00';
        }

        $timeDelivery = $time;

        if (!isset($date)) {
            $date = new Carbon();
            $data['date'] = $date->format('Y-m-d');
        }

        $delivery_time = $data['time'];
        $time = str_replace(':00', '', $delivery_time);
        $time = str_replace('-', ':', $time);
        $delivery_date_time = $data['date'] . ' ' . $time . ':00 +0000';
        $date = Carbon::parse($delivery_date_time);
        $dateOrder = strtotime($date->format('Y-m-d H:i:s'));


        if (!isset($data['client_comment'])) {
            $data['client_comment'] = '';
        }

        $dataOrderAmo = [
            'order name'  => $data['name'],
            'order_id'    => $data['order_id'],
            'api_mode'    => 'ShopTB',
            'order price' => $data['total_price'],
            'pipelineId'  => $pipelineId,
            'statusId'    => $statusId,
            'notes'       => $data['client_comment'],
            'name'        => $data['first_name'] . ' ' . $data['first_name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'address'     => $address,
            'payment'     => $payment,
            'date'        => $dateOrder,
            'time'        => $timeDelivery,
            'tags'        => $tags
        ];

        return $dataOrderAmo;
    }
}
