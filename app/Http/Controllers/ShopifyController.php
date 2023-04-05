<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ShopifyController extends Controller
{


    public function testWebhook(Request $request)
    {
        $data = $request->post('json');
        $data = json_decode($data);

        dd($data);

        $action = $data['action'];

        if ($action == 'orders/create') {
            $shipping_address = $data['shipping_address'];
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

                $test = WebhookLog::where('name', 'ShopifyWebhook - '.$id)->first();

                if (!$test) {
                    http_response_code(200);
                    $webhook = new WebhookLog();
                    $webhook->name = 'ShopifyWebhook - '.$id;
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
}
