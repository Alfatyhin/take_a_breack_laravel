<?php

namespace App\Http\Controllers;

use App\Mail\NewOrder;
use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\IcreditPayments;
use App\Models\Orders as OrdersModel;
use App\Models\WebhookLog;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\IcreditServise;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;

;

class Orders extends Controller
{


    public function getIcreditPaymentUrl(Request $request)
    {
        $Data = $request->all();
        if (!empty($Data['data'])) {


            $data = $Data['data'];
            $order = new EcwidService();
            $order->setData($data);
            $dataOrder = $order->getIcreditDataOrder();

            WebhookLog::addLog('iCredid payment step 1', $dataOrder);

            /////////////////////////////////////////////////////////////////////////

            $icreditServise = new IcreditServise();
            $result = $icreditServise->getUrl($dataOrder);


            $paymentUrl = $result['URL'];
//            dd($paymentUrl);
            if ($paymentUrl) {
                $ecwidData = $order->getData();
                $orderPay['transaction'] = $ecwidData['cart']['order']['referenceTransactionId'];
                $orderPay['returnUrl'] = $ecwidData['returnUrl'];
                $orderPay['orderId'] = $ecwidData['cart']['order']['id'];
                $orderPay['orderShop'] = "Ecwid";
                if (preg_match('/test/', $dataOrder['email'])) {
                    $orderPay['test_mode'] = true;

                } else {
                    $orderPay['test_mode'] = false;
                }
                session()->flash('orderPay', $orderPay);


                return redirect($paymentUrl);
            }

        }
    }


    //////////////////////////////////////////////////////////////////////////////////////////
    public function getWebHookLog(Request $request)
    {
        $logs = WebhookLog::latest('id')->paginate(10);

        return view('app.webhooks', [
                'webhoocks' => $logs,
            ]
        );
    }

    public function orders(Request $request)
    {

        $paymentMethod = AppServise::getOrderPaymentMethod();
        $paymentStatus = AppServise::getOrderPaymentStatus();
        $invoiceStatus = AppServise::getOrderInvoiceStatus();

        $order_id = false;
        $orderSearch = false;


        if (!empty($request->get('order_id'))) {

            $order_id = $request->get('order_id');

            $orderSearch =  DB::table('orders')
                ->where('orders.order_id', $order_id)
                ->join('clients', 'orders.clientId', '=', 'clients.id')
                ->select('orders.*', 'clients.name', 'clients.email')
                ->first();

            if (!$orderSearch) {
                $testOrder = OrdersModel::where('order_id', $order_id)->first();
                if ($testOrder) {
                    dd($testOrder->toArray());
                }
            }

        }

        if (!empty($request->get('date-from')) && !empty($request->get('date-to'))) {

            $date_from = new Carbon($request->get('date-from'));
            $date_to = new Carbon($request->get('date-to') . ' 23:59');

        } elseif ($request->get('dates')) {

            if ($request->get('dates') == 'today') {
                $date = new Carbon();
                $date_from = new Carbon($date->format('Y-m-d'));
                $date_to = new Carbon($date->format('Y-m-d 23:59'));

            }
            if ($request->get('dates') == 'month') {

                $date = new Carbon('first day of this month');
                $date_from = new Carbon($date->format('Y-m-d 00:00'));

                $date = new Carbon('last day of this month');
                $date_to = new Carbon($date->format('Y-m-d 23:59'));
            }

        } else {

            if (session()->has('dates')) {
                $dates = session('dates');
                $date_from = $dates['date_from'];
                $date_to = $dates['date_to'];

            } else {

                $date_from = new Carbon('first day of this month' . ' 00:00');
                $date_to = new Carbon('last day of this month' . ' 23:59');
            }

        }


        $dates['date_from'] = $date_from;
        $dates['date_to'] = $date_to;
        session(['dates' => $dates]);
        session()->save();


        echo "<pre>";
//        var_dump($date_from->format('Y-m-d H:i'));
//        var_dump($date_to->format('Y-m-d H:i'));
        // для таблицы
        $orders = DB::table('orders')
            ->whereBetween('orders.created_at', [$date_from, $date_to])
            ->whereBetween('orders.paymentStatus', [2, 4])
            ->latest('orders.id')
            ->join('clients', 'orders.clientId', '=', 'clients.id')
            ->select('orders.*', 'clients.name', 'clients.email')
            ->paginate(10);

        // статмистика
        $ordersAll = DB::table('orders')
            ->whereBetween('orders.created_at', [$date_from, $date_to])
            ->whereBetween('orders.paymentStatus', [2, 4])
            ->get();


        $paydPeriodInfo = [];
        $paydPeriodInfo['заказов'] = sizeof($ordersAll);
        $paydPeriodInfo['средний чек'] = 0;
        $paydPeriodInfo['totall'] = 0;

        if (!empty($ordersAll)) {
            foreach ($ordersAll as $k => $item) {

                if ($item->paymentStatus == 1) {
//                    $lostCart[] = $item;
//                    unset($ordersAll[$k]);
                } else {
                    $x = $item->paymentMethod;
                    $paymethodName = $paymentMethod[$x];

                    if (empty($paydPeriodInfo[$paymethodName])) {
                        $paydPeriodInfo[$paymethodName] = $item->orderPrice;
                    } else {
                        $paydPeriodInfo[$paymethodName] += $item->orderPrice;
                    }
                    $paydPeriodInfo['totall'] += $item->orderPrice;
                }
            }

            if (!empty(sizeof($orders))) {
                $paydPeriodInfo['средний чек'] = round($paydPeriodInfo['totall'] / sizeof($ordersAll), 2);
                $paydPeriodInfo = array_reverse($paydPeriodInfo);
            }
        }


        $date_start = new Carbon('first day of this month');
        $date_end = new Carbon('last day of this month');

        $priceMonth = OrdersModel::whereBetween('created_at', [$date_start, $date_end])
            ->sum('orderPrice');

        $priceYear = OrdersModel::whereYear('created_at', $date_start->format('Y'))
            ->sum('orderPrice');

//        print_r($orders->items()[0]);

        echo "</pre>";

        return view('orders.index', [
            'orders'         => $orders,
            'paymentMethod'  => $paymentMethod,
            'paymentStatus'  => $paymentStatus,
            'invoiceStatus'  => $invoiceStatus,
            'priceMonth'     => $priceMonth,
            'paydPeriodInfo' => $paydPeriodInfo,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
            'priceYear'      => $priceYear,
            'order_id'       => $order_id,
            'orderSearch'    => $orderSearch
        ]);
    }





    public function createOrderByEcwidId(Request $request)
    {
        $orderId = $request->get('orderId');

        if (!empty($orderId)) {
            $ecwidService = new EcwidService();
            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            // уменьшаем количество составляюших в наборах
            $ecwidService->productsService($orderEcwid['items'], [
                'subProductCountAction' => 'down',
            ]);


            $paymentMethod = EcwidService::getPaymentMethod($orderEcwid);

            if ($orderEcwid['paymentStatus'] == 'PAID') {
                $paymentStatus = 4;
            } else {
                $paymentStatus = 3;
            }

            $client = Clients::firstOrNew([
                'email' => $orderEcwid['email']
            ]);
            $client->name = $orderEcwid['billingPerson']['name'];
            $client->phone = $orderEcwid['billingPerson']['phone'];
            $res = $client->save();
            if ($res) {
                echo "client save $res <br>";
            }

            $order = \App\Models\Orders::firstOrCreate([
                'order_id' => $orderId
            ]);
            $order->paymentMethod = $paymentMethod;
            $order->paymentStatus = $paymentStatus;
            $order->clientId = $client->id;
            $order->orderPrice = $orderEcwid['total'];
            $order->orderData = json_encode($orderEcwid);
            $res = $order->save();
            if ($res) {
                echo "order save $res <br>";
            }



            if ($paymentStatus == 4) {
                if ($paymentMethod != 2) {
                    $date = $orderEcwid['createDate'];
                    $paymentDate = Carbon::parse($date);
                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                }

                $order->paymentDate = $paymentDateString;
                $res = $order->save();
                if ($res) {
                    echo "order update $res <br>";
                }
            }
        }

       return view('app.create_order_by_ecwid_id', []);

    }


    public function getNewOrderId(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);

        $data_test = $request->post('data-test');
        if ($data_test) {
            $data = json_decode($data_test, true);
            echo "<pre>";
//            dd($data);
        } else {
            $data = $request->post('data');
            WebhookLog::addLog('new create order 1', $data);
        }

        try {
            $client = Clients::firstOrNew([
                'email' => $data['Cart']['person']['email']
            ]);

            $client->name = $data['Cart']['person']['name'];
            $client->save();

            ////////////////////////////////////////////////////////////


            $order = new OrdersModel();
            $order->order_id = '-' . rand(100, 999);
            $order->paymentMethod = 0;
            $order->paymentStatus = 1;
            $order->clientId = $client->id;
            $order->orderPrice = (float) $data['option']['total_price'];
            $order->orderData = json_encode($data);
            $order->save();

            $order_id = AppServise::generateOrderId($order->id);
            $order->order_id = $order_id;
            $res = $order->save();


            if ($res) {
                http_response_code(200);
                $res = [
                    'result'    => true,
                    'order_id'  => $order_id,
                    'client_id' => $client->id,
                    'res'       => $res
                ];
                echo json_encode($res);
            } else {
                $res = [
                    'result' => false,
                ];
                echo json_encode($res);
            }

        } catch (\Exception $e) {
            $res = [
                'result' => false,
            ];
            echo json_encode($res);
            AppErrors::addError("error create order", $data);
        }

    }

    public function createOrder(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $ecwidService = new EcwidService();
        $data_test = $request->post('data-test');

        if ($data_test) {
            $data = json_decode($data_test, true);
            echo "<pre>";
//            dd($data);
        } else {
            $data = $request->post('data');
            WebhookLog::addLog('new create order 2 '.$data['Cart']['order_id'], $data);
        }

        $total_price = 0;
        $discount = false;
        if (!empty($data['option']['promo_code'])) {
            $code = $data['option']['promo_code'];
            $discounts = $ecwidService->getDiscountCoupons($code);
            foreach ($discounts['items'] as $item) {
                if ($item['code'] == $code && $item['status'] == 'ACTIVE') {
                    $discount = $item;
                }
            }
        }


        foreach ($data['Cart']['items'] as $k => &$item) {
            $produdt_id = $item['id'];
            $varieble_id = $item['variable_id'];
            $product = $ecwidService->getProduct($produdt_id);

            $item['name'] = $product['name'];
            $item['nameTranslated'] = $product['nameTranslated'];

            if ($varieble_id > 0 ) {
                foreach ($product['combinations'] as $variant) {
                    if ($variant['id'] == $varieble_id) {
                        foreach ($variant['options'] as $option) {
                            if ($option['name'] == 'Size') {
                                $item['option']['name'] = $option['name'];
                                $item['option']['value'] = $option['value'];
                                $item['option']['nameTranslated'] = $option['nameTranslated'];

                            }
                        }
                        $item['price'] = $variant['defaultDisplayedPrice'];
                        $item['sku'] = $variant['sku'];
                    }
                }

            } else {
                $item['price'] = $product['defaultDisplayedPrice'];
                $item['sku'] = $product['sku'];
            }
            if (!empty($item['option_value'])) {
                foreach ($product['options'] as $option) {
                    foreach ($option['choices'] as $choice) {
                        if ($choice['text'] == $item['option_value']) {
                            if ($choice['priceModifierType'] == 'ABSOLUTE') {
                                $item['price'] = $product['price'] + $choice['priceModifier'];
                            }
                        }
                    }
                }
            }

            $total_price += $item['price'] * $item['count'];
        }
        $data['option']['products_price'] = $total_price;

        if ($discount) {
            $data['Cart']['discount'] = [];
            if ($discount['discountType'] == 'PERCENT') {
                $data['Cart']['discount']['display'] = $discount['discount'] . '%';
                $data['Cart']['discount']['total_discount'] = round($total_price * ($discount['discount'] / 100), 2);
            } else {
                $data['Cart']['discount']['display'] = $discount['discount'] . '₪';
                $data['Cart']['discount']['total_discount'] = $discount['discount'];
            }
            $total_price -= $data['Cart']['discount']['total_discount'];
        }
        $total_price += $data['option']['delivery_price'];
        if ($data['option']['tips_price'] > 0) {
            $data['option']['tips_value'] = $total_price * $data['option']['tips_price'] / 100;
            $total_price += $data['option']['tips_value'];
        }
        $data['option']['total_price'] = $total_price;


        try {
            $client = Clients::firstOrNew([
                'email' => $data['Cart']['person']['email']
            ]);

            $client->name = $data['Cart']['person']['name'];
            $client->phone = $data['Cart']['person']['phone'];
            $client->save();

            ////////////////////////////////////////////////////////////
            $paymentMethods = AppServise::getOrderPaymentMethod();
            $paymentMethods = array_flip($paymentMethods);
            $payment_method = $data['option']['payment_method'];
            $paymentMethod = $paymentMethods[$payment_method];

            $order = OrdersModel::firstOrNew([
                'order_id' => $data['Cart']['order_id']
            ]);

            $order->paymentMethod = $paymentMethod;
            if ($client->name == 'test') {
                $order->paymentStatus = 4;

            }
            $order->clientId = $client->id;
            $order->orderPrice = (float) $data['option']['total_price'];
            $order->orderData = json_encode($data);
            $res = $order->save();

            if ($res) {
                http_response_code(200);
                if ($paymentMethod == 2) {

                    $order->paymentStatus = 3;
                    $order->save();

                    if (empty($order->amoId)) {
                        if (!$data_test) {
                            AppServise::getQuest("https://takeabreak.website/api/create_amo_order?id=" . $order->order_id);
                        }
                    }

                    $ecwidService->productsUpdateCount($data);

                    $res = [
                        'result'   => true,
                        'order_id' => $order->order_id,
                        'res'      => $res
                    ];
                    echo json_encode($res);
                }
                if ($paymentMethod == 1) {

                        $res_json = [
                            'result'   => true,
                            'order_id' => $order->order_id,
                            'url'      => 'https://takeabreak.website/api/cart_payment_url?id='.$order->order_id
                        ];
                        echo json_encode($res_json);

                }
                if ($paymentMethod == 3) {

                        $res_json = [
                            'result'   => true,
                            'order_id' => $order->order_id,
                            'url'      => 'https://takeabreak.website/api/paypal/button?id='.$order->order_id
                        ];
                        echo json_encode($res_json);

                }

            }

        } catch (\Exception $e) {
            $res = [
                'result' => false,
            ];
            echo json_encode($res);
            AppErrors::addError("error create order", $data);
        }
    }


    public function getPopapIcreditPaymentUrl(Request $request)
    {
        $order_id = $request->get('id');
        $order = OrdersModel::where('order_id', $order_id)->first();
        $data = json_decode($order->orderData, true);

        $orderService = new OrderService();
        $data['order_id'] = $order->order_id;
        $icreditData = $orderService->getIcreditDataOrder($data);
        $icreditService = new IcreditServise();
        $res = $icreditService->getUrl($icreditData);


        $paymentUrl = $res['URL'];
        WebhookLog::addLog('Orders getPopapIcreditPaymentUrl ', $res);
        if ($paymentUrl) {
            $orderPay['orderId'] = $order->order_id;
            $orderPay['orderShop'] = "Tilda-server";
            if (preg_match('/test/', $data['Cart']['person']['email'])) {
                $orderPay['test_mode'] = true;

            } else {
                $orderPay['test_mode'] = false;
            }
            session()->flash('orderPay', $orderPay);


            return redirect($paymentUrl);
        }

    }

    public function checkOrderPayStatus(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        http_response_code(200);

        $order_id = $request->get('id');
        $order = OrdersModel::where('order_id', $order_id)->first();

        $paymentStatus = $order->paymentStatus;
        if ($paymentStatus == 4) {
            $res = [
                'result' => true,
                'status' => 'PAID'
            ];
            echo json_encode($res);
        } else {
            $res = [
                'result' => false
            ];
            echo json_encode($res);
        }

    }


    public function ecwidWebHook(Request $request)
    {

        $testData = $request->post('data-test');
        if (!empty($testData)) {
            $data = json_decode($testData, true);
            $header = $data['header'];
            $Data = $data['data'];

        } else {
            $Data = $request->all();
            $header = request()->header('x-ecwid-webhook-signature');

            if (!empty($Data)) {
                $webhoock['ip'] = $this->getIp();
                $webhoock['data'] = $Data;
                $webhoock['header'] = $header;

                WebhookLog::addLog('ecwid webhook new', $webhoock);
            }

        }

        // Reply with 200OK to Ecwid
        http_response_code(200);

        $ecwidService = new EcwidService();
        $headerHash = $ecwidService->getHeaderHash($Data);
        if ($header != $headerHash) {
            echo "error header \n";
            WebhookLog::addLog('error ecwid webhook header', $header);
            die;
        } else {
            echo "header test true \n";
        }

        if (!empty($testData)) {
            $data = $Data['data'];
            $orderId = $data['orderId'];
            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            $amoDataEcwid = EcwidService::getAmoDataLead($orderEcwid);
            dd($amoDataEcwid);
        }

        if ($Data['eventType'] == 'order.created') {

            $data = $Data['data'];

            $orderId = $data['orderId'];

            Storage::disk('local')->append('data/ecwid webhook log.txt',
                $orderId . ' order eventType - created ');


            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            // уменьшаем количество составляюших в наборах
            $ecwidService->productsService($orderEcwid['items'], [
                'subProductCountAction' => 'down',
            ]);

            Storage::disk('local')
                ->append('data/ecwid webhook log.txt', ' get order ecwid Data');

            $paymentMethod = EcwidService::getPaymentMethod($orderEcwid);

            if ($orderEcwid['paymentStatus'] == 'PAID') {
                $paymentStatus = 4;
            } else {
                $paymentStatus = 3;
            }

            $client = Clients::firstOrNew([
                'email' => $orderEcwid['email']
            ]);

            if (!empty($orderEcwid['billingPerson']['name'])) {
                $client->name = $orderEcwid['billingPerson']['name'];
            } elseif (!empty($orderEcwid['shippingPerson']['name'])) {
                $client->name = $orderEcwid['shippingPerson']['name'];
            }

            if (!empty($orderEcwid['billingPerson']['phone'])) {
                $client->phone = $orderEcwid['billingPerson']['phone'];
            } elseif (!empty($orderEcwid['shippingPerson']['phone'])) {
                $client->phone = $orderEcwid['shippingPerson']['phone'];
            }
            $client->save();

            $order = OrdersModel::firstOrCreate([
                'order_id' => $orderId
            ]);
            $order->paymentMethod = $paymentMethod;
            $order->paymentStatus = $paymentStatus;
            $order->clientId = $client->id;
            $order->orderPrice = $orderEcwid['total'];
            $order->orderData = json_encode($orderEcwid);
            $order->save();

            Storage::disk('local')
                ->append('data/ecwid webhook log.txt', 'order save');


            if ($paymentStatus == 4) {
                if ($paymentMethod != 2) {
                    $date = $orderEcwid['createDate'];
                    $paymentDate = Carbon::parse($date);
                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                } else {
                    $paymentDate = new Carbon();
                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                }

                $order->paymentDate = $paymentDateString;
                $order->save();
            }


            if ($order->invoiceStatus == 0 && $paymentStatus == 4) {

                try {
                    $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
                } catch (\Exception $e) {
                    AppErrors::addError("error invoice Data to " . $order->ecwiId, $orderEcwid);
                }

                $invoice = new GreenInvoiceService($order);


                try {
                    $res = $invoice->newDoc($invoiceDada);

                    if (isset($res['errorCode'])) {
                        AppErrors::addError("invoice create error to " . $order->ecwiId, json_encode($res));

                    } else {
                        $order->invoiceStatus = 1;
                        $order->invoiceData = json_encode($res);
                        $order->save();
                    }

                } catch (\Exception $e) {
                    AppErrors::addError("error invoice newDoc to " . $order->ecwiId, $invoiceDada);
                }
            }

            if (empty($order->amoId)) {
                // пролучаем массив для амо
                $amoDataEcwid = EcwidService::getAmoDataLead($orderEcwid);
                $amoCrmServise = new AmoCrmServise();
                // создаем сделку
                if (!empty($client->amoId)) {
                    $amoDataEcwid['clientAmoId'] = $client->amoId;
                }
                $res = $amoCrmServise->NewOrder($amoDataEcwid);

                if (!empty($res['amo_id'])) {
                    $order->amoId = $res['amo_id'];
                    $order->save();

                    if (empty($client->amoId)) {
                        $client->amoId = $res['client_id'];
                        $client->save();
                    }


                    $amoNotes = EcwidService::getAmoNotes($orderEcwid);
                    $amoCrmServise->addTextNotesToLead($order->amoId, $amoNotes);

                    $amoProductsList = EcwidService::amoProductsList($orderEcwid['items']);
                    $amoCrmServise->addProductsToLead($amoProductsList, $order->amoId);

                    $order->amoData = json_encode($res);
                    $order->save();
                } else {
                    AppErrors::addError('error create amo lead', $res);
                }
            }
            // end eventType - 'order.created' end
        } elseif ($Data['eventType'] == 'product.updated') {

            echo "<pre>";
            $productId = $Data['entityId'];
            $product = $ecwidService->getProduct($productId);
            $ecwidService->inStockUpdate($product);
        }
    }

    public function getIp() {

        $keys = [
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim($_SERVER[$key]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                } else {
                    return '0.0.0';
                }
            }
        }
    }


    public function checkPaymentStatusIcredit(Request $request)
    {
        $orderId = $request->get('orderId');

        $icreditPay = IcreditPayments::where('orderId', $orderId)->first()->toArray();


        if ($icreditPay['paymentStatus'] == 'VERIFIED') {
            $ecwidService = new EcwidService();
            $result = $ecwidService->paymentStatusUpdate($orderId, 'PAID');

            if ($result['updateCount'] == 1) {
                echo "<p>status from Ecwid Update</p>";
            } else {
                echo "<p>status from Ecwid not Update</p>";
            }

            $order = OrdersModel::where('order_id', $orderId)->first();

            $order->paymentStatus = 4;
            $res = $order->save();
            if ($res) {
                echo "<p>order status from this server Update</p>";
            } else {
                echo "<p>order status from this server not Update</p>";
            }
        }


    }



    public function createInvoice(Request $request)
    {

        $orderId = $request->get('orderId');
        $order = OrdersModel::where('order_id', $orderId)->first();
        $orderData = json_decode($order->orderData, true);


        echo "<pre>";

        if (isset($orderData['Cart'])) {
            $orderData['id'] = $order->order_id;
            $invoiceDada = OrderService::getOrderDataToGinvoice($orderData);
        } else {
            $ecwidService = new EcwidService();
            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
        }

        $date = new Carbon();
        $invoiceDada['payDate'] = $date->format('Y-m-d');
        $invoice = new GreenInvoiceService($order);



        try {
            $res = $invoice->newDoc($invoiceDada);

            if (isset($res['errorCode'])) {

                echo "invoice not create <br>";
                var_dump($res);
                echo GreenInvoiceService::getError($res['errorCode']);

                AppErrors::addError("invoice create error to " . $orderId, $res);

            } else {
                $order->invoiceStatus = 1;
                $order->invoiceData = json_encode($res);
                $order->save();

                echo "invoice create <br>";
                var_dump($res);
            }

        } catch (\Exception $e) {
            AppErrors::addError("error invoice newDoc to " . $orderId, $invoiceDada);
        }

    }


    public function gInvoceWebhook(Request $request)
    {
        $Data = $request->all();

        $data_test = $request->post('data-test');

        if ($data_test) {
            $Data = json_decode($data_test, true);
            echo "<pre>";
            dd($Data);
        } else {
            WebhookLog::addLog('test Ginvoce call', $Data);
        }

        http_response_code(200);

        $order_id = $Data['custom'];

        if ($Data['payment_status'] == 'Completed') {
            $order = OrdersModel::where('order_id', $order_id)->first();
            $order->invoiceStatus = true;
            $order->save();
        }

        $response = [
            'success' => true
        ];
        echo json_encode($response);
    }

    public function sendMail(Request $request)
    {
//        header('Access-Control-Allow-Origin: *');
        http_response_code(200);


        $order_id = $request->get('id');
        $order = OrdersModel::where('order_id', $order_id)->first();
        $client_id = $order->clientId;
        $client = Clients::where('id', $client_id)->first();


        $orderData = json_decode($order->orderData, true);
        $orderData['mailler']['send_mail'] = 0;
        $order->orderData = json_encode($orderData);
        $order->save();
        $order->orderData = $orderData;

        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        $shop_setting = json_decode($shop_setting, true);

        if ($client->email == 'test@mail.ru') {
            $client->email = 'virikidorhom@gmail.com';

        }

        try {
            Mail::to($client)->send(new NewOrder($order, $shop_setting));
            $orderData['mailler']['send_mail'] = 1;
            $order->orderData = json_encode($orderData);
            $order->save();
            $order->orderData = $orderData;
        } catch (\Exception $e) {
            AppErrors::addError("error isend mail to", $order_id);
        }


        return view('mail.new_order', [
            'order' => $order,
            'shop_setting' => $shop_setting
        ]);

    }

    public function testMail(Request $request)
    {

        $order_id = $request->get('id');
        $order = OrdersModel::where('order_id', $order_id)->first();
        $client_id = $order->clientId;
        $client = Clients::where('id', $client_id)->first();
        if ($client->email == 'test@mail.ru') {
            $client->email = 'virikidorhom@gmail.com';
        }


        $orderData = json_decode($order->orderData, true);
        $order->orderData = $orderData;


        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        $shop_setting = json_decode($shop_setting, true);

//        dd($orderData);

//        echo '<pre>';
//        print_r($orderData);
//        dd($order->toArray());
//        echo '</pre>';

        if ($client->email == 'virikidorhom@gmail.com') {

            try {
                Mail::to($client)->send(new NewOrder($order, $shop_setting));
                print_r('send mail test');
            } catch (\Exception $e) {
                AppErrors::addError("error isend mail to", $order_id);
            }

        }
        $send = $request->get('send');
        if ($send == 1) {
            Mail::to($client)->send(new NewOrder($order, $shop_setting));
            print_r('send mail ' . $client->email);
        }


        return view('mail.new_order', [
            'order' => $order,
            'shop_setting' => $shop_setting
        ]);

    }

}
