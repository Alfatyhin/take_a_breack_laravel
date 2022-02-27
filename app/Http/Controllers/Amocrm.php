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

        if ($ownerDetails->getName()) {
            $messages[] = $ownerDetails->getName() . ' Integration is Work';
        } else {
            $messages[] = "error token ";
            $amoCrmService = new AmoCrmServise();
            $messages[] = $amoCrmService->getButton();
        }

        return view('message', [
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


        if (!empty($post['leads'])) {
            WebhookLog::addLog('amo web hook ', $post);

            $ecwidService = new EcwidService();
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
                                    dd($old_status_id);
                                }
                                $paymentStatusArray = array_flip(AppServise::getOrderPaymentStatus());
                                var_dump('update status');
                                if (empty($api_mode) || $api_mode == 'Ecwid') {
                                    $res = $ecwidService->orderInAmoStatusUpdate($orer_id, $status_id, $statusPaidAmo);

                                    if (!empty($res['updateCount'])) {
                                        $ecwidPaymentStatus = $res['data']['paymentStatus'];
                                        $order->ecwidStatus = $res['data']['fulfillmentStatus'];
                                        $order->paymentStatus = $paymentStatusArray[$ecwidPaymentStatus];
                                    }else {
                                        AppErrors::addError('error update ststus to ecwid id ' . $orer_id, $res);
                                    }

                                } else {
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

                                    $order->amoStatus = $status_id;
                                    $order->paymentStatus = $paymentStatusArray[$paymentStatus];
                                    $order->amoId = $item['id'];
                                    $order->save();

                                }


                                // отправка инвойса
                                var_dump($statusPaidAmo);
                                if ($statusPaidAmo == '436781' && $order->invoiceStatus == 0) {

                                    var_dump('create invoice');
                                    // статус оплачено
                                    $paymentDate = new Carbon();
                                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                                    $order->paymentDate = $paymentDateString;
                                    $order->save();

                                    if (empty($api_mode) || $api_mode == 'Ecwid') {
                                        var_dump('Ecvad data invoice');
                                        $orderEcwid = $ecwidService->getOrderBuId($orer_id);
                                        try {
                                            $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
                                        } catch (\Exception $e) {
                                            AppErrors::addError("error invoice Data to " . $order->order_id, $orderEcwid);
                                        }
                                    } elseif (empty($api_mode) || $api_mode == 'ServerTB') {
                                        var_dump('Server data invoice');
                                        $orderData = json_decode($order->orderData, true);
                                        $orderData['id'] = $order->order_id;
                                        $invoiceDada = OrderService::getOrderDataToGinvoice($orderData);
                                    }


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

        $paymentMetods = AppServise::getOrderPaymentMethod();
        $paymentStatuses = AppServise::getOrderPaymentStatus();

        $id = $request->get('id');
        WebhookLog::addLog('new amo order ', $id);

        $orderService = new OrderService();
        $order = Orders::where('order_id', $id)->first();
//        print_r($order->toArray());
        $orderData = json_decode($order['orderData'], true);
        $orderData['paymentMethod'] = $paymentMetods[$order['paymentMethod']];
        $orderData['paymentStatus'] = $paymentStatuses[$order['paymentStatus']];
        $orderData['order_id'] = $order->order_id;


        if ($id) {
            $client = Clients::where('email', $orderData['Cart']['person']['email'])->first();

            if (!empty($client->amoId)) {
                $amoData['clientAmoId'] = $client->amoId;
            }

            // пролучаем массив для амо
            $amoData = $orderService::getAmoDataLead($orderData);
            $amoCrmServise = new AmoCrmServise();
            $amoNotes = $orderService::getAmoNotes($orderData);

            $amoData['text_note'] = $amoNotes;
            $res = $amoCrmServise->NewOrder($amoData);

            if (!empty($res['amo_id'])) {

                $order = Orders::firstOrCreate([
                    'order_id' => $order->order_id
                ]);
                $order->amoId = $res['amo_id'];
                $order->save();

                $client->amoId = $res['client_id'];
                $client->save();


                $amoCrmServise->addTextNotesToLead($order->amoId, $amoNotes);

//                $amoProductsList = EcwidService::amoProductsList($orderEcwid['items']);
//                $amoCrmServise->addProductsToLead($amoProductsList, $order->amoId);

                $order->amoData = json_encode($res);
                $order->save();

            } else {
                var_dump('error create amo lead', $res);
                AppErrors::addError('error create amo lead', $res);
            }
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

    public function getContacts()
    {

        $contacts = Storage::disk('local')->get('/data/amo_contacts.json');
        $contacts = json_decode($contacts, true);

        echo "<pre>";

        foreach ($contacts as $page) {

//            var_dump($page['contacts']);
            foreach ($page['contacts'] as $item) {
                $email = false;
                $contactId = $item['id'];
//                var_dump($contactId);
                $data = $item['custom_fields_values'];

                if (!empty($data)) {

                    foreach ($data as $itemData) {
                        $fieldCode = $itemData['field_code'];

                        if (empty($fieldCode)) {
//                        var_dump($itemData);
                        } else {

                            if ($fieldCode == 'EMAIL') {
                                $email = $itemData['values'][0]['value'];

                            }

                        }

                    }


                } else {
                    $email = 'not custom fields';
                }

                if ($email) {
                    $testContact[$email][] = $item;
                } else {
                    $testContact['not mail'][] = $item;
                }



            }
        }

        foreach ($testContact as $key => $item) {
            $size = sizeof($item);


            if ($key != 'not custom fields'
                && $key != 'not mail'
                && $key != 'lubarove@gmail.com'
                && $key != 'Jivo'
                && $key != 'Get more'
            ) {
                if ($size > 1) {
                    echo "$key - ( $size ) <br>";

                }
            }
        }

    }
}
