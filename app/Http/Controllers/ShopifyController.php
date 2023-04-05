<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ShopifyController extends Controller
{
    public function testWebhook(Request $request)
    {

        if ($request->hasHeader('X-Shopify-Hmac-Sha256')) {

            if($this->verifyWebhook(file_get_contents('php://input'), $request->header('X-Shopify-Hmac-Sha256'))) {

                $data = $request->json()->all();

                $data['HEADERS'] = $request->header();
                $id = $request->header('X-Shopify-Webhook-Id');

                $test = WebhookLog::where('name', 'ShopifyWebhook - '.$id)->first();

                if (!$test) {
                    http_response_code(200);
                    $webhook = new WebhookLog();
                    $webhook->name = 'ShopifyWebhook - '.$id;
                    $webhook->data = json_encode($data);
                    $webhook->save();

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
