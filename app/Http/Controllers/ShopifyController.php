<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ShopifyController extends Controller
{
    public function testWebhook(Request $request)
    {
        $data = $request->json()->all();
        $data['HEADERS'] = $request->header();
        $id = $request->header('X-Shopify-Webhook-Id');

        http_response_code(200);
        $webhook = new WebhookLog();
        $webhook->name = 'ShopifyWebhook - '.$id;
        $webhook->data = json_encode($data);
        $webhook->save();
    }
}
