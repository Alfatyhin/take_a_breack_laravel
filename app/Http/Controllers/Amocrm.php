<?php

namespace App\Http\Controllers;

use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Providers\EcwidProvider;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class Amocrm extends Controller
{
    private $amoService;
    public function __construct(AmoCrmServise $service) {
        $this->amoService = $service;
    }


    public function integrationAmoCrm(Request $request)
    {
        $title = 'Amo-CRM integration';
        $amoCrmService = new AmoCrmServise();
        $ownerDetails = $amoCrmService->getAccount();

        if ($ownerDetails && $ownerDetails->getName()) {
            $messages[] = $ownerDetails->getName() . ' Integration is Work';
        } else {
            $messages[] = "error token ";
            $messages[] = $amoCrmService->getButton();
        }
//        dd($messages);
//        $messages[] = $amoCrmService->getButton();

        return view('amocrm.index', [
            'error_log'=> $request->error_log,
            'title'    => $title,
            'messages' => $messages
        ]);
    }

    // обработка ответа амо
    // запись токена
    public function callBack(Request $request)
    {
        $amoCrmService = new AmoCrmServise();

        echo '<pre>';
        $get = $request->all();

        // получаем код подтвержнения
        $state = Storage::disk('local')->get('data/amo-state.txt');

        if (empty($get['state']) || empty($state) || ($get['state'] !== $state)) {

            $res = AppErrors::addError('amo - invalid state', $get);
            var_dump($res);

        } else {

            $accessToken = $amoCrmService->getAccessTokenByCods($get);
            $res = $amoCrmService->saveToken($accessToken);

            if ($res === true) {
                Storage::disk('local')->put('data/amo-state.txt', "");
                $message = "токен успешно записан";
                echo $message;
            } else {
                $message = "ошибка записи токена";
                echo $message;
            }

        }
    }


    public function amoWebhook(Request $request)
    {

        http_response_code(200);
        $test = false;
        $post = $request->post();
        $site = env('APP_NAME');

        if($request->get('test') == 1) {
            $test = '{"leads":{"status":[{"id":"23777015","name":"#S-AOEPW","status_id":"50746495","old_status_id":"43471420","price":"325","responsible_user_id":"216744","last_modified":"1673614504","modified_user_id":"216744","created_user_id":"0","date_create":"1673561284","pipeline_id":"4651807","tags":[{"id":"192141","name":"I2CRM (WhatsApp)"},{"id":"222457","name":"WhatsApp"}],"account_id":"29039599","custom_fields":[{"id":"509001","name":"\u0410\u0434\u0440\u0435\u0441 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":[{"value":"Tel Aviv \u05d4\u05db\u05e0\u05e1\u05ea \u05d4\u05d2\u05d3\u05d5\u05dc\u05d4 17"}]},{"id":"514141","name":"\u042d\u0442\u0430\u0436","values":[{"value":"2"}]},{"id":"511647","name":"\u041d\u043e\u043c\u0435\u0440 \u043a\u0432\u0430\u0440\u0442\u0438\u0440\u044b\/\u043e\u0444\u0438\u0441\u0430","values":[{"value":"7"}]},{"id":"514563","name":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","values":[{"value":"Irene Feigin Sudai"}]},{"id":"514565","name":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","values":[{"value":"+972 50-833-5952"}]},{"id":"308363","name":"\u041e\u043f\u043b\u0430\u0442\u0430","values":[{"value":"\u041e\u043f\u043b\u0430\u0447\u0435\u043d","enum":"436781"}]},{"id":"520559","name":"\u0414\u0430\u0442\u0430 \u0441\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437\u0430\/\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":["1673560800"]},{"id":"520561","name":"\u0412\u0440\u0435\u043c\u044f","values":[{"value":"9:00-21:00"}]},{"id":"512455","name":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430","values":[{"value":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430: #S-AOEPW\r\n1x - 280 \u0448\u0435\u043a \u041f\u043e\u0434\u0430\u0440\u043e\u0447\u043d\u044b\u0439 \u043d\u0430\u0431\u043e\u0440 \r\n ---------------------- \r\n \u0418\u0442\u043e\u0433\u043e: 280 \u0448\u0435\u043a (\u0431\u0435\u0437 \u0441\u043a\u0438\u0434\u043a\u0438)\r\n ---------------------- \r\n\u0441\u043f\u043e\u0441\u043e\u0431 \u043e\u043f\u043b\u0430\u0442\u044b - undefined \r\n ---------------------- \r\n\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439 \u043f\u043e\u043a\u0443\u043f\u0430\u0442\u0435\u043b\u044f: \r\n\u041d\u0435\u0442 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u044f \r\n ---------------------- \r\n\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430: \r\n \u0410\u0434\u0440\u0435\u0441 - Tel Aviv \u05d4\u05db\u05e0\u05e1\u05ea \u05d4\u05d2\u05d3\u05d5\u05dc\u05d4 17 7 \u044d\u0442-2\r\n \u0434\u0430\u0442\u0430 - \r\n \u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u044c - 45\u0448\u0435\u043a\r\n ---------------------- \r\n\r\n \u0418\u0442\u043e\u0433\u043e: 325 \u0448\u0435\u043a"}]},{"id":"519327","name":"\u0440\u0430\u0441\u043f\u0435\u0447\u0430\u0442\u0430\u0442\u044c \u0437\u0430\u043a\u0430\u0437 (\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430)","values":[{"value":"https:\/\/takeabreak.co.il\/api\/orders\/view-order\/S-AOEPW"}]},{"id":"516743","name":"\u042f\u0437\u044b\u043a","values":[{"value":"\u0410\u043d\u0433\u043b\u0438\u0439\u0441\u043a\u0438\u0439","enum":"548397"}]},{"id":"489653","name":"Api order ID","values":[{"value":"S-AOEPW"}]},{"id":"511579","name":"Api mode","values":[{"value":"ShopTB"}]}],"created_at":"1673561284","updated_at":"1673614504"}]},"account":{"subdomain":"takebreak","id":"29039599","_links":{"self":"https:\/\/takebreak.amocrm.ru"}}}';

            $post = json_decode($test, true);

            dd($post);
        }


        if (!empty($post['leads'])) {


            foreach ($post['leads'] as $event => $items) {


                if ($event == 'status' || $event == 'update') { // изменение статуса
                    foreach ($items as $item) {


                        if($request->get('test') != 1) {
                            WebhookLog::addLog('amo web hook leads ' . $event, $post);
                        }

                        if (isset($item['custom_fields'])) {
                            foreach ($item['custom_fields'] as $field) {
                                if ($field['id'] == 489653) {
                                    $orer_id = $field['values'][0]['value'];
                                }
                                if ($field['id'] == 308363) {
                                    $statusPaidAmo = $field['values']['0']['enum'];
                                }
                                if ($field['id'] == 511579) {
                                    $api_mode = $field['values']['0']['value'];
                                }
                            }

                            // если заказ с сайта
                            if (isset($orer_id)) {

                                $order = Orders::withTrashed()->where('order_id', $orer_id)->first();
                                if ($order->trashed()) {
                                    $order->restore();
                                }
                                $status_id = $item['status_id'];

                                // меняем статус
                                if($status_id && isset($statusPaidAmo) ) {


                                    $paymentStatusArray = array_flip(AppServise::getOrderPaymentStatus());

                                    switch ($statusPaidAmo) {
                                        case 436781:
                                            $paymentStatus = 'PAID';
                                            break;
                                        case 436783:
                                            $paymentStatus = 'AWAITING_PAYMENT';
                                            break;
                                        case 547421:
                                            $paymentStatus = 'AWAITING_PAYMENT';
                                            break;
                                        default:
                                            $paymentStatus = 'INCOMPLETE';
                                            break;
                                    }

                                    $order->amoStatus = $status_id;
                                    $order->paymentStatus = $paymentStatusArray[$paymentStatus];
                                    $order->amoId = $item['id'];
                                    $order->save();



                                    // отправка инвойса
                                    if ($statusPaidAmo == '436781' && $order->invoiceStatus == 0 && $site != 'Take a Break Server') {

                                        // статус оплачено
                                        $paymentDate = new Carbon();
                                        $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                                        $order->paymentDate = $paymentDateString;
                                        $order->invoiceStatus = 1;
                                        $order->save();

                                        $orderData = json_decode($order->orderData, true);
                                        $orderData['id'] = $order->order_id;

                                        // проверка клиента
                                        $client_id = $order->clientId;
                                        $client = Clients::where('id', $client_id)->first();

                                        if (empty($orderData['clientName'])) {
                                            $orderData['clientName'] = $client->name;
                                        }
                                        if (empty($orderData['email'])) {
                                            $orderData['email'] = $client->email;
                                        }
                                        if (empty($orderData['phone'])) {
                                            $orderData['phone'] = $client->phone;
                                        }

                                        $order->ordeData = $orderData;


                                        $invoiceDada = OrderService::getShopOrderDataToGinvoice($order);

                                        $invoice = new GreenInvoiceService($order);

                                        if (!empty($invoiceDada)) {
                                            try {
                                                $res = $invoice->newDoc($invoiceDada);
                                                if (isset($res['errorCode'])) {
                                                    AppErrors::addError("invoice create error to " . $order->order_id, json_encode($res));

                                                } else {
                                                    $order->invoiceStatus = 1;
                                                    $order->invoiceData = json_encode($res);
                                                    $order->save();
                                                }

                                            } catch (\Exception $e) {
                                                AppErrors::addError("error invoice newDoc to " . $order->order_id, $invoiceDada);
                                            }

                                        } else {
                                            var_dump('empty invoice data');
                                        }
                                    }

                                }
                            }
                        }


                    }

                } else { // не обновление статуса
                    //
                }
            }
        }

        return 'sugess';
    }

    public function createOrderToApi(Request $request)
    {

        $orderService = new OrderService();
        $order_id = $request->get('id');
        try {
            $orderService->createOrderToAmocrm($order_id);
        } catch (Exception $e) {

        }
        $res = ['res' => true];
        echo json_encode($res);
    }

    public function createOrderToApiBuOrderData(Request $request)
    {

        $orderService = new OrderService();
        $order_data = $request->post('data');
        try {
            $orderService->createOrderToAmocrmNew($order_data);
        } catch (Exception $e) {

        }
        $res = ['res' => true];
        echo json_encode($res);
    }

    public function getOrderById(Request $request)
    {
        $id = $request->get('id');

        if ($id) {

            $res = $this->amoService->getOrderById($id);

            dd($res);
        }

    }


    public function UsersDuplicateCollaps(Request $request)
    {
        $amoCrmService = $this->amoService;

        $clients = Clients::where('amoId', '!=', null)->get();

        foreach ($clients as $client) {
            $amo_client = $amoCrmService->getContactBuId($client->amoId);

            if ($amo_client) {
                $orders = Orders::where('clientId', $client->id)->get();

                dd($orders);


//                $test_email = $amoCrmService->getContactDoubles($client->email);
//                $test_phones = $amoCrmService->getContactDoubles($client->phone);
//
//
//
//                $diff = array_diff_key($test_email, $test_phones);
//                if ($diff) {
//                    $result = $test_email + $diff;
//
//                    dd($client, $amo_client, $test_email, $test_phones, $result, $diff);
//                } else {
//                    $result = $test_email;
//                }
//
//
//
//                if ($client->data) {
//                    dd($client->data);
//                }
//
//                if (sizeof($result) != 1) {
//                    dd($client, $amo_client, $test_email, $test_phones, $result);
//                }

            }
        }

    }


    public function pipelineTest(Request $request)
    {
        $test = '{"id": 960, "clientId": 331, "order_id": "S-ZQOJ", "orderData": "{\"_token\":\"UTN4tM9EyHipP189iVS6qBkysGzd3MfB2LfXWAlf\",\"lang\":\"en\",\"delivery\":\"delivery\",\"clientName\":\"\\u05de\\u05d9\\u05ea\\u05e8 \\u05de\\u05e6\\u05e0\\u05e8\",\"city_id\":\"49\",\"city\":\"\\u05e4\\u05ea\\u05d7 \\u05ea\\u05e7\\u05d5\\u05d5\\u05d4\",\"street\":\"\\u05d0\\u05d7\\u05d3 \\u05d4\\u05e2\\u05dd\",\"house\":\"22\",\"flat\":null,\"floor\":\"6\",\"phone\":\"+972 052-687-2887\",\"nameOtherPerson\":null,\"phoneOtherPerson\":null,\"email\":\"meitarmatzner16@gmail.com\",\"clientBirthDay\":null,\"date\":\"2022-6-28\",\"time\":\"11:00-14:00\",\"methodPay\":\"4\",\"client_comment\":null,\"premium\":\"0\",\"order_data\":{\"products\":{\"1-0-71\":{\"id\":71,\"stock_count\":\"0\",\"variant\":\"0\",\"options\":[{\"key\":\"0\",\"value\":{\"text\":\"S\",\"priceModifier\":\"0\",\"textTranslated\":{\"en\":\"Mini\",\"he\":null,\"ru\":\"\\u041c\\u0438\\u043d\\u0438\"},\"priceModifierType\":\"ABSOLUTE\"},\"name\":{\"en\":\"Size\",\"he\":\"\\u05d2\\u05d5\\u05d3\\u05dc\",\"ru\":\"\\u0420\\u0430\\u0437\\u043c\\u0435\\u0440\"}}],\"count\":\"1\",\"price\":\"199\",\"sku\":\"00043S1\",\"name\":{\"en\":\"Tiramisu\",\"he\":\"\\u05e2\\u05d5\\u05d2\\u05ea \\u05e7\\u05e4\\u05d4 - \\u05d2\\u05dc\\u05d9\\u05d3\\u05d4 (\\u05d8\\u05d9\\u05e8\\u05de\\u05d9\\u05e1\\u05d5)\",\"ru\":\"\\u0422\\u0438\\u0440\\u0430\\u043c\\u0438\\u0441\\u0443\"}}},\"delivery_price\":30,\"items\":{\"1-0-71\":{\"id\":71,\"stock_count\":\"0\",\"variant\":\"0\",\"options\":[{\"key\":\"0\",\"value\":{\"text\":\"S\",\"priceModifier\":\"0\",\"textTranslated\":{\"en\":\"Mini\",\"he\":null,\"ru\":\"\\u041c\\u0438\\u043d\\u0438\"},\"priceModifierType\":\"ABSOLUTE\"},\"name\":{\"en\":\"Size\",\"he\":\"\\u05d2\\u05d5\\u05d3\\u05dc\",\"ru\":\"\\u0420\\u0430\\u0437\\u043c\\u0435\\u0440\"}}],\"count\":\"1\",\"price\":\"199\",\"sku\":\"00043S1\",\"name\":{\"en\":\"Tiramisu\",\"he\":\"\\u05e2\\u05d5\\u05d2\\u05ea \\u05e7\\u05e4\\u05d4 - \\u05d2\\u05dc\\u05d9\\u05d3\\u05d4 (\\u05d8\\u05d9\\u05e8\\u05de\\u05d9\\u05e1\\u05d5)\",\"ru\":\"\\u0422\\u0438\\u0440\\u0430\\u043c\\u0438\\u0441\\u0443\"}}},\"products_total\":199,\"order_total\":229}}", "created_at": "2022-06-26T13:04:03.000000Z", "orderPrice": 229, "updated_at": "2022-06-26T13:04:03.000000Z", "paymentMethod": "4", "paymentStatus": 3}';

        $test = json_decode($test, true);

        dd(json_decode($test['orderData'], true));

        $AmoCrmService = $this->amoService;
        $test = $AmoCrmService->getPipelines();
        dd($test);
    }

}
