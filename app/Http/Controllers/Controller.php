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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use League\OAuth2\Client\Token\AccessTokenInterface;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(Request $request)
    {


        return view('index');
    }


    public function orderDelete(Request $request)
    {
        $orderId = $request->get('id');

        if ($orderId) {
            $order = Orders::where('order_id', $orderId)->first();

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
        }

        $invoice_mode_cache = $request->get('invoice_mode_cache');
        if ($invoice_mode_cache) {
            $settingData['invoice_mode_cache'] = $invoice_mode_cache;
        }

        if ($invoice_mode_paypal) {
            Storage::disk('local')->put('data/app-setting.json', json_encode($settingData));
        }

        return view('app.invoice_setting', [
            'settingData' => $settingData,
        ]);
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
        $str = 'шла  Саша по шоссе честь $% йод + -  яд ёжики';
        $strtr = TranslitTextService::Translit($str);
        echo "test str - $str" . '<br>'
            . "translit - $strtr <br> retranslit - "
            . TranslitTextService::ReTranslit($strtr)
            . "<br> fileName translit - " . TranslitTextService::TranslitFileName($str);


    }


}
