<?php

namespace App\Http\Middleware;

use App\Models\AppErrors;
use App\Models\Coupons;
use App\Models\Orders;
use App\Models\UtmModel;
use App\Models\WebhookLog;
use App\Services\OrderService;
use Closure;
use Illuminate\Http\Request;
use Mockery\Exception;

class ShopThanks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request)
    {

        WebhookLog::addLog('OrderThanks After', "start");
        $order_id = session('last_order_id');

        if ($order_id) {
            $order = Orders::where('order_id', $order_id)->first();
        }
        $utm = session('utm');
        if ($utm) {
            $utm_new = new UtmModel();
            $utm_new->order_id = $order->order_id;
            foreach ($utm as $k => $v) {
                $utm_new->$k = $v;
            }
            $utm_new->save();
            $request->session()->forget('utm');
        }

        if ($order) {
            $order = Orders::where('id', $order->id)->first();
            if (is_string($order->orderData)) {
                $orderData = json_decode($order->orderData, true);
            } else {
                $orderData =  $order->orderData;
            }
            if (isset($orderData['order_data']['discount'])) {
                $code = $orderData['order_data']['discount']['code'];
                $promo_code = Coupons::where('code', $code)->first();
                $coupon_data = json_decode($promo_code->data, true);
                if ($promo_code) {
                    $promo_code->count += 1;
                    if ($coupon_data['count_limit'] != 0 && $promo_code->count >= $coupon_data['count_limit']) {
                        $promo_code->status = 'disable';

                        WebhookLog::addLog('OrderThanks After code', 'disable');
                    }
                    $promo_code->save();
                }
            }


            $OrderService = new OrderService();

            if (empty($order->amoId)) {
                $order_id = $order->order_id;

                if (env('APP_NAME') != "Take a Break Server") {
                    try {
                        $OrderService->createOrderToAmocrm($order_id);
                        WebhookLog::addLog('OrderThanks After create AMO Lead', "$order_id");
                    } catch (Exception $e) {
                        WebhookLog::addLog('OrderThanks After error create AMO Lead', "$order_id");
                    }


                    OrderService::sendMailNewOrder($order_id, 'send');
                    WebhookLog::addLog('OrderThanks After send mail', "$order_id");
                }

                $request->session()->forget('last_order_id');
            } else {
                dd($order->amoId);
            }

        } else {
            WebhookLog::addLog('OrderThanks After not order', "--");
        }

    }
}
