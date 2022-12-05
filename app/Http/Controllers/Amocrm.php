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
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class Amocrm extends Controller
{
    private $amoService;
    public function __construct(AmoCrmServise $service) {
        $this->amoService = $service;
    }

    
    public function integrationAmoCrm()
    {
        $title = 'Amo-CRM integration';
        $amoCrmService = new AmoCrmServise();
        $ownerDetails = $amoCrmService->getAccount();

        if ($ownerDetails && $ownerDetails->getName()) {
            $messages[] = $ownerDetails->getName() . ' Integration is Work';
        } else {
            $messages[] = "error token ";
            $amoCrmService = new AmoCrmServise();
            $messages[] = $amoCrmService->getButton();
        }
//        dd($messages);
//        $messages[] = $amoCrmService->getButton();

        return view('amocrm.index', [
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

        $testData = $request->post('data-test');
        if (!empty($testData)) {
            echo "<p>test amo webhook</p><pre>";
            $post = json_decode($testData, true);
            $test = true;
        } else {
            $test = false;
            $post = $request->post();
        }


        WebhookLog::addLog('amo web hook ', $post);

        if (!empty($post['leads'])) {
//            WebhookLog::addLog('amo web hook ', $post);

            foreach ($post['leads'] as $event => $items) {

                if ($event == 'status') { // изменение статуса
                    foreach ($items as $item) {

                        echo "<hr>";
                        var_dump($item['custom_fields']);
                        echo "<hr>";

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



                            $order = Orders::where('order_id', $orer_id)->first();


                            $old_status_id = $item['old_status_id'];
                            $status_id = $item['status_id'];

                            // меняем статус
                            if($status_id != $old_status_id) {

                                if ($test) {
                                    print_r($status_id);
//                                    dd($old_status_id);
                                }
                                $paymentStatusArray = array_flip(AppServise::getOrderPaymentStatus());

                                switch ($statusPaidAmo) {
                                    case 436781:
                                        $paymentStatus = 'PAID';
                                        break;
                                    case 436783:
                                        $paymentStatus = 'AWAITING_PAYMENT';
                                        break;
                                    default:
                                        $paymentStatus = 'INCOMPLETE';
                                        break;
                                }
//                                dd($paymentStatus);

                                $order->amoStatus = $status_id;
                                $order->paymentStatus = $paymentStatusArray[$paymentStatus];
                                $order->amoId = $item['id'];
                                $order->save();


                                // отправка инвойса
                                if ($statusPaidAmo == '436781' && $order->invoiceStatus == 0) {

                                    // статус оплачено
                                    $paymentDate = new Carbon();
                                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                                    $order->paymentDate = $paymentDateString;
                                    $order->save();

                                    $orderData = json_decode($order->orderData, true);
                                    $orderData['id'] = $order->order_id;
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

                } else { // не обновление статуса
                    //
                }
            }
        }

    }

    public function createAmoLeadBuEcwidId(Request $request)
    {
        echo "<pre>";
        $orderId = $request->get('id');

        if (!empty($orderId)) {

            $ecwidService = new EcwidService();
            $orderEcwid = $ecwidService->getOrderBuId($orderId);


            //////////////////////////////////////////////////////////////////
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
            ///////////////////////////////////////////////////////////////////////////

            var_dump($client->toArray());

            // пролучаем массив для амо
            $amoDataEcwid = EcwidService::getAmoDataLead($orderEcwid);
            $amoCrmServise = new AmoCrmServise();
            // создаем сделку
            if (!empty($client->amoId)) {
                $amoDataEcwid['clientAmoId'] = $client->amoId;
            }
//            $amoDataEcwid['test'] = true;

            $res = $amoCrmServise->NewOrder($amoDataEcwid);

            if (!empty($res['amo_id'])) {

                echo "amo order create - " . $res['amo_id'];

                $order = Orders::firstOrCreate([
                    'order_id' => $orderId
                ]);
                $order->amoId = $res['amo_id'];
                $order->save();

                $client->amoId = $res['client_id'];
                $client->save();


                $amoNotes = EcwidService::getAmoNotes($orderEcwid);
                $amoCrmServise->addTextNotesToLead($order->amoId, $amoNotes);

                $amoProductsList = EcwidService::amoProductsList($orderEcwid['items']);
                $amoCrmServise->addProductsToLead($amoProductsList, $order->amoId);

                $order->amoData = json_encode($res);
                $order->save();
            } else {
                var_dump('error create amo lead', $res);
                AppErrors::addError('error create amo lead', $res);
            }

        }
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
