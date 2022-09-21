<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Orders;
use App\Models\Orders as OrdersModel;
use App\Models\Product;
use App\Models\WebhookLog;
use App\Services\AppServise;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getIcreditUrl(Request $request)
    {
        $Data = $request->post();
        WebhookLog::addLog('Api iCredit pUrl', $Data);

    }


    public function OrderView(Request $request, $order_id)
    {

        $order = OrdersModel::where('order_id', $order_id)->first();

        $orderData = json_decode($order->orderData, true);
        $lang = $orderData['lang'];


        foreach ($orderData['order_data']['products'] as &$item) {
            $product_image = Product::where('id', $item['id'])->value('image');
            if (isset($product_image)) {
                $product_image = json_decode($product_image, true);
                $product_image = $product_image['image160pxUrl'];
                $item['img_url'] = $product_image;
            }


            $opt_str = '';
            if (isset($item['options'])) {
                foreach ($item['options'] as $option) {
                    if (!empty($option['name'][$lang])) {
                        $name = $option['name'][$lang];
                    } else {
                        $name = $option['name']['en'];
                    }
                    if (!empty($option['value']['textTranslated'][$lang])) {
                        $value = $option['value']['textTranslated'][$lang];
                    } else {
                        $value = $option['value']['textTranslated']['en'];
                    }
                    $opt_str = " / $name $value";

                }
            }

            $item['info'] = $opt_str;

        }

        $order->orderData = $orderData;

        return view('api.order-view-ru', [
            'order' => $order,
        ]);
    }
}
