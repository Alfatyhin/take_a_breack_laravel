<?php

namespace App\Http\Controllers;

use App\Mail\NewOrder;
use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\IcreditPayments;
use App\Models\Orders as OrdersModel;
use App\Models\Product;
use App\Models\WebhookLog;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\IcreditServise;
use App\Services\OrderService;
use App\Services\SendpulseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\assertRegExp;

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
        $logs = WebhookLog::latest('id')->paginate(100);

//        foreach ($logs as $item) {
//            if ($item->name == 'OrderThanksView ') {
//                $data = json_decode($item->data, true);
//                $orderData = $data['orderData'];
//                dd($orderData);
//            }
//        }

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


    public function getOrderAmoData(Request $request, OrdersModel $order)
    {

        $paymentMetods = AppServise::getOrderPaymentMethod();
        $paymentStatuses = AppServise::getOrderPaymentStatus();

        $orderService = new OrderService();
        $orderData = json_decode($order['orderData'], true);
        $orderData['paymentMethod'] = $paymentMetods[$order['paymentMethod']];
        $orderData['paymentStatus'] = $paymentStatuses[$order['paymentStatus']];
        $orderData['order_id'] = $order->order_id;


        $amoData = $orderService::getShopAmoDataLead($orderData);
        $amoNotes = $orderService::getShopAmoNotes($orderData);

        dd($amoData, $amoNotes);
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
        $post = $request->post('data');
        $post = json_decode($post, true);

        if (!empty($post)) {


            $order = new OrdersModel();
            $order->order_id = $post['order_id'];
            $order->clientId = $post['clientId'];
            $order->paymentMethod = $post['paymentMethod'];
            $order->paymentStatus = $post['paymentStatus'];
            $order->orderPrice = $post['orderPrice'];
            $order->orderData = $post['orderData'];
            $res = $order->save();
            dd($res);
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


    public function orderDelete(\Illuminate\Http\Request $request)
    {
        $orderId = $request->get('id');

        if ($orderId) {
            $order = \App\Models\Orders::where('order_id', $orderId)->first();


            if ($order) {
                $res = $order->delete();
                if ($res) {
                    $message[] = "order $orderId delete";
                    session()->flash('message', $message);
                }
            } else {
                $message[] = "order $orderId not found";
                session()->flash('message', $message);
            }

        } else {
            dd('order not found');
        }


        return redirect()->route('shop_settings_orders');
    }


    public function orderRestore(\Illuminate\Http\Request $request)
    {
        $orderId = $request->get('id');

        if ($orderId) {
          OrdersModel::withTrashed()->where('order_id', $orderId)->restore();
            $message[] = "order $orderId restore";
            session()->flash('message', $message);
        }


        return redirect()->route('shop_settings_orders');
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

        } elseif (isset($orderData['order_data'])) {

            $invoiceDada = OrderService::getShopOrderDataToGinvoice($order);

        } else {

            dd($order);

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
            if ($order) {
                $order->invoiceStatus = true;
                $order->save();
            }
        }

        $response = [
            'success' => true
        ];
        echo json_encode($response);
    }

    public function sendMail(Request $request)
    {

        $order_id = $request->get('id');
        $order = OrderService::sendMailNewOrder($order_id, 'send');

        if ($order) {
            $orderData = $order->orderData;
            $lang = $orderData['lang'];
        } else {
            dd($order);
        }

        return view('mail.new_order_'.$lang, [
            'order' => $order
        ]);

    }

    public function testMail(Request $request)
    {
        $send = $request->get('send');
        if ($send) {
            $send = 'test_send';
        } else {
            $send = 'test_view';
        }
        $order_id = $request->get('id');
        $order = OrderService::sendMailNewOrder($order_id, $send); // test_view or test_send

        $order->orderData = json_decode($order->orderData, true);
        $orderData = $order->orderData;
        $lang = $orderData['lang'];

        return view('mail.new_order_'.$lang, [
            'order' => $order,
        ]);

    }


    public function testSendpulse(Request $request, OrdersModel $order)
    {
        $order_data = json_decode($order->orderData, true);

        $order_data['email'] = 'virikidorhom@gmail.com';
        $order_data['phone'] = '+972555555555';

        $lang = $order_data['lang'];

        $products_data = $order_data['order_data']['products'];
        $total_price = 0;
        foreach ($products_data as $item) {
            if (!empty($item['name'][$lang])) {
                $name = $item['name'][$lang];
            } else {
                $name = $item['name']['en'];
            }

            $text = '';
            if (isset($item['options'])) {
                foreach ($item['options'] as $option) {
                    if (!empty($option['name'][$lang])) {
                        $opt_name = $option['name'][$lang];
                    } else {
                        $opt_name = $option['name']['en'];
                    }
                    if (!empty($option['value']['textTranslated'][$lang])) {
                        $opt_value = $option['value']['textTranslated'][$lang];
                    } else {
                        $opt_value = $option['value']['textTranslated']['en'];
                    }

                    $text .= " $opt_name - $opt_value";
                }
            }

            $product = Product::find($item['id']);

            $image_data = json_decode($product->image, true);
            $image = "https://takeabreak.co.il" . $image_data['image400pxUrl'];


            $products[] = [
                'id' => $item['id'],
                'name' => $name,
                'text' => $text,
                'img' => $image,
                'price' => $item['price'],
                'quantity' => $item['count'],
                'amount' => $item['count'] * $item['price'],
            ];
            $total_price += $item['count'] * $item['price'];
        }


        $translite = [
            'header_title' => [
                'ru' => 'магазин авторских сладостей',
                'en' => "shop of author's sweets",
                'he' => "shop of author's sweets"
            ],
            'com_back' => [
                'ru' => 'Возвращайтесь к нам снова!',
                'en' => "Come back to us again!",
                'he' => "Come back to us again!"
            ],
            'goo_shop' => [
                'ru' => 'перейти в магазин',
                'en' => "go to the store",
                'he' => "go to the store"
            ]
        ];

        $sendpulseData = [
            'order_id' => $order['order_id'],
            'header_title' => $translite['header_title'][$lang],
            'com_back' => $translite['com_back'][$lang],
            'goo_shop' => $translite['goo_shop'][$lang],
            'lang' => $lang,
            'email' => $order_data['email'],
            'phone' => $order_data['phone'],
            'products' => $products,
            'total_amount' => $total_price,
        ];

        $res = SendpulseService::sendLostCart($sendpulseData);
        dd($res);

        dd($order->toArray(), $order_data, $sendpulseData);

    }

    public function createAmoInvoiceToOrder(Request $request, OrdersModel $order)
    {
        $client = Clients::find($order->clientId);
        $amoData = json_decode($order->amoData, true);

        $amoCrmService = new AmoCrmServise();
        $amo_invoice_id = $amoCrmService->addInvoiceToLead($client->amoId, $order->order_id, $order->amoId, (float) $order->orderPrice, $order->paymentStatus);
        $amoData['invoice_id'] = $amo_invoice_id;

        $order->amoData = json_encode($amoData);
        $order->save();
    }

    public function testIcreditPaymentData(Request $request)
    {
        $data = $request->post('data');
        $data = json_decode($data, true);
        $id = $data['order_id'];
        $order = OrdersModel::where('order_id', $id)->first();
        if ($order) {
            $icreditOrderData = OrderService::getShopIcreditOrderData($order);
            dd($icreditOrderData);
            $iCreditService = new IcreditServise();
            $result = $iCreditService->getUrl($icreditOrderData);
        }
        dd($data);

    }

    public function OrderLogView(Request $request)
    {

        $date = $request->get('date');
        if ($date) {
            $date_nau = new Carbon($date);
        } else {
            $date_nau = new Carbon();
        }

        $date_str = $date_nau->format("Y-m-d");
        $date_pre = $date_nau->addDays(-1);

        if (Storage::disk('logs')->exists("orders-$date_str.log")) {
            $monolog = Storage::disk('logs')->get("orders-$date_str.log");
        } else {
            $monolog = 'not file';
        }
        $monolog = htmlspecialchars($monolog);
        $monolog = str_replace('['.$date_nau->format('Y'), '<hr><b>['.$date_nau->format('Y'), $monolog);
        $monolog = str_replace('] ', ']</b> ', $monolog);


        return view('logs.index', [
            'error_log'      => $request->error_log,
            'route' => 'orders_log',
            'log' => $monolog,
            'date_str' => $date_str,
            'date_pre' => $date_pre
        ]);
    }

}
