<?php

namespace App\Http\Controllers;

use App\Facades\TranslitTextService;
use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\IcreditPayments;
use App\Models\Orders;
use App\Models\User;
use App\Models\WebhookLog;
use App\Services\AppServise;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use League\OAuth2\Client\Token\AccessTokenInterface;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {

        return view('index');
    }

    public function orders(Request $request)
    {

        $paymentMethod = AppServise::getOrderPaymentMethod();
        $paymentStatus = AppServise::getOrderPaymentStatus();
        $invoiceStatus = AppServise::getOrderInvoiceStatus();

        if (empty($request->get('date-from')) && empty($request->get('date-to'))) {
            $orders = DB::table('orders')
                ->latest('orders.id')
                ->join('clients', 'orders.clientId', '=', 'clients.id')
                ->select('orders.*', 'clients.name', 'clients.email')
                ->paginate(10);
        }




        $date_from = new Carbon('first day of this month');
        $date_to = new Carbon('last day of this month');


        if (!empty($request->get('date-from')) && !empty($request->get('date-to'))) {
            $date_from = new Carbon($request->get('date-from'));
            $date_to = new Carbon($request->get('date-to'));

            $orders = DB::table('orders')
                ->whereBetween('orders.created_at', [$date_from, $date_to->addDay()])
                ->latest('orders.id')
                ->join('clients', 'orders.clientId', '=', 'clients.id')
                ->select('orders.*', 'clients.name', 'clients.email')
                ->paginate(10);

            $date_to->addDays(-1);
        }

        $orderPayd = Orders::whereBetween('paymentDate', [$date_from, $date_to->addDay()])
            ->get();
        $date_to->addDays(-1);


        $paydPeriodInfo = [];
        $paydPeriodInfo['?????????????? ??????'] = 0;
        $paydPeriodInfo['totall'] = 0;
        foreach ($orderPayd as $item) {
            $x = $item->paymentMethod;
            $paymethodName = $paymentMethod[$x];

            if (empty($paydPeriodInfo[$paymethodName])) {
                $paydPeriodInfo[$paymethodName] = $item->orderPrice;
            } else {
                $paydPeriodInfo[$paymethodName] += $item->orderPrice;
            }
            $paydPeriodInfo['totall'] += $item->orderPrice;

        }
        if (!empty(sizeof($orderPayd))) {
            $paydPeriodInfo['?????????????? ??????'] = round($paydPeriodInfo['totall'] / sizeof($orderPayd), 2);
            $paydPeriodInfo = array_reverse($paydPeriodInfo);
        }




        $date_start = new Carbon('first day of this month');
        $date_end = new Carbon('last day of this month');

        $priceMonth = Orders::where('paymentStatus', '4')
            ->whereBetween('paymentDate', [$date_start, $date_end->addDay()])
            ->sum('orderPrice');

        $priceMonthAwaiting = Orders::where('paymentStatus', '3')
            ->whereBetween('created_at', [$date_start, $date_end->addDay()])
            ->sum('orderPrice');


        $priceYear = Orders::where('paymentStatus', '4')
            ->whereYear('created_at', $date_start->format('Y'))
            ->sum('orderPrice');

        $priceYearAwaiting = Orders::where('paymentStatus', '3')
            ->whereYear('created_at', $date_start->format('Y'))
            ->sum('orderPrice');

        echo "</pre>";

        return view('orders.index', [
            'orders' => $orders,
            'paymentMethod'  => $paymentMethod,
            'paymentStatus'  => $paymentStatus,
            'invoiceStatus'  => $invoiceStatus,
            'priceMonth'     => $priceMonth,
            'paydPeriodInfo' => $paydPeriodInfo,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
            'priceYear'      => $priceYear,
            'priceMonthAwaiting' => $priceMonthAwaiting,
            'priceYearAwaiting'  => $priceYearAwaiting,

        ]);
    }


    public function createOrderByEcwidId(Request $request)
    {
        $orderId = $request->get('orderId');

        $ecwidService = new EcwidService();
        $orderEcwid = $ecwidService->getOrderBuId($orderId);
        // ?????????????????? ???????????????????? ???????????????????????? ?? ??????????????
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

        $order = Orders::firstOrCreate([
            'ecwidId' => $orderId
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

    public function orderDelete(Request $request)
    {
        $orderId = $request->get('id');

        if ($orderId) {
            $order = Orders::where('ecwidId', $orderId)->first();

            if ($order) {
                $res = $order->delete();
                if ($res) {
                    $message[] = "order $orderId delete";
                }
            } else {
                $message[] = "order $orderId not found";
            }

        }

        return view('message', [
            'title' => 'order-delete',
            'messages' => $message,
        ]);

    }

    //////////////////////////////////////////////////////////////////////////////////////////
    public function getWebHookLog(Request $request)
    {
       $logs = WebhookLog::latest('id')->paginate(10);;

        return view('app.webhooks', [
            'webhoocks' => $logs,
            ]
        );
    }

    public function getEcwidOrderLog()
    {
        $log = Storage::disk('local')->get('data/ecwid webhook log.txt');

        echo "<pre>";
        print_r($log);
    }

    public function allUsers(Request $request)
    {
        $users = User::latest('id')->paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function allClients(Request $request)
    {
        $clients = Clients::latest('id')->paginate(10);

        return view('clients.index', [
            'clients' => $clients,
        ]);
    }


    public function appInvoiceSetting(Request $request)
    {
        $dataJson = Storage::disk('local')->get('data/app-setting.json');
        $settingData = json_decode($dataJson, true);

        $invoice_mode_paypal = $request->get('invoice_mode_paypal');

        if ($invoice_mode_paypal) {
            $settingData['invoice_mode_paypal'] = $invoice_mode_paypal;
            Storage::disk('local')->put('data/app-setting.json', json_encode($settingData));
        }

        return view('app.invoice_setting', [
            'settingData' => $settingData,
        ]);
    }

    public function testInvoice(Request $request)
    {
        $orderId = $request->get('orderId');

        $ecwidService = new EcwidService();
        $orderEcwid = $ecwidService->getOrderBuId($orderId);

        $invoice = new GreenInvoiceService();
        $invoice = $invoice->setMode(2);



        $orderEcwid['extraFields']['gustom_lang'] = 'he';
        $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
        echo "<pre>";

        $res = $invoice->newDoc($invoiceDada);

        if (isset($res['errorCode'])) {
            AppErrors::addError("invoice create error to " . $orderId, json_encode($res));

        } else {
           var_dump($res);
        }


    }


    public function importDB()
    {
        $dump = Storage::get('/data/damp_db.json');
        $dump = json_decode($dump, true);

        foreach ($dump as $tbName => $value) {
            var_dump($tbName);
            foreach ($value as $item) {
                if ($tbName == 'users') {
                    $user = new User();
                    $user->fill($item);
                    $user->save();
                }

                if ($tbName == 'clients') {
                    $data = new Clients();
                    $data->fill($item);
                    $data->save();
                }

                if ($tbName == 'icredit_payments') {
                    $data = new IcreditPayments();
                    $data->fill($item);
                    $data->save();
                }

                if ($tbName == 'orders') {
                    $data = new Orders();
                    $data->fill($item);
                    $data->save();
                }
            }
        }
    }

    public function exportDB()
    {
        $tables = array ('users', 'clients', 'icredit_payments', 'orders');

        foreach ($tables as $tableName) {
            $data = DB::table($tableName)->get()->toArray();
            $dump[$tableName] = $data;
        }
        echo "<pre>";

        $res = Storage::disk('local')->put('/data/damp_db.json', json_encode($dump));
        var_dump($res);
    }

    public function testServiceProvider()
    {
        $str = '??????  ???????? ???? ?????????? ?????????? $% ?????? + -  ???? ??????????';
        $strtr = TranslitTextService::Translit($str);
        echo "test str - $str" . '<br>'
            . "translit - $strtr <br> retranslit - "
            . TranslitTextService::ReTranslit($strtr)
            . "<br> fileName translit - " . TranslitTextService::TranslitFileName($str);


    }


}
