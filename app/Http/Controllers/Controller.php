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




    public function getEcwidOrderLog()
    {
        $log = Storage::disk('local')->get('data/ecwid webhook log.txt');

        echo "<pre>";
        print_r($log);
    }

    public function allUsers(Request $request)
    {
        if (!empty($request->post('user_change'))) {
            $post = $request->post();
            $user_id = $request->get('user_id');
            $user = User::find($user_id);
            if (isset($post['user_role'])) {
                if ($post['user_role'] == 'delete') {
                    $user->delete();
                } else {
                    $user->user_role = $post['user_role'];
                    $user->save();
                }
            }
            return back();
        }

        $users = User::latest('id')->paginate(50);
        $user_roles = ['user', 'admin', 'content manager', 'marketer'];


        return view('users.index', [
            'users' => $users,
            'user_roles' => $user_roles
        ]);
    }


    public function allClients(Request $request)
    {
        $clients = Clients::latest('id')->paginate(10);


        return view('clients.index', [
            'clients' => $clients,
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
