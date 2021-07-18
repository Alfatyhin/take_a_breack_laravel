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
        $post = $request->post();

        if (!empty($post['leads'])) {

            $ecwidService = new EcwidService();
            foreach ($post['leads'] as $event => $items) {

                if ($event == 'status') { // изменение статуса
                    foreach ($items as $item) {

                        var_dump($item['custom_fields']);
                        echo "<hr>";

                        foreach ($item['custom_fields'] as $field) {
                            if ($field['id'] == 489653) {
                                $ecwidId = $field['values'][0]['value'];
                            }
                            if ($field['id'] == 308363) {
                                $statusPaidAmo = $field['values']['0']['enum'];
                            }
                        }

                        // если заказ с сайта
                        if (isset($ecwidId)) {

                            WebhookLog::addLog('amo web hook ' . $ecwidId, $post);

                            $order = Orders::firstOrCreate([
                                'ecwidId' => $ecwidId,
                            ]);


                            $old_status_id = $item['old_status_id'];
                            $status_id = $item['status_id'];

                            // меняем статус
                            if($status_id != $old_status_id) {
                                $res = $ecwidService->orderInAmoStatusUpdate($ecwidId, $status_id, $statusPaidAmo);

                                if (!empty($res['updateCount'])) {
                                    $paymentStatusArray = array_flip(AppServise::getOrderPaymentStatus());
                                    $ecwidPaymentStatus = $res['data']['paymentStatus'];
                                    $order->amoStatus = $status_id;
                                    $order->amoId = $item['id'];
                                    $order->ecwidStatus = $res['data']['fulfillmentStatus'];
                                    $order->paymentStatus = $paymentStatusArray[$ecwidPaymentStatus];
                                    $order->save();
                                } else {
                                    AppErrors::addError('error update ststus to ecwid id ' . $ecwidId, $res);
                                }


                                // отправка инвойса
                                if ($statusPaidAmo == '436781' && $order->invoiceStatus == 0) {
                                    // статус оплачено
                                    $paymentDate = new Carbon();
                                    $paymentDateString = $paymentDate->format('Y-m-d H:i:s');
                                    $order->paymentDate = $paymentDateString;
                                    $order->save();

                                    $orderEcwid = $ecwidService->getOrderBuId($ecwidId);
                                    try {
                                        $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
                                    } catch (\Exception $e) {
                                        AppErrors::addError("error invoice Data to " . $order->ecwiId, $orderEcwid);
                                    }

                                    $invoice = new GreenInvoiceService();

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
        $orderId = $request->get('id');

        if (!empty($orderId)) {

            $ecwidService = new EcwidService();
            $orderEcwid = $ecwidService->getOrderBuId($orderId);
            // пролучаем массив для амо
            $amoDataEcwid = EcwidService::getAmoDataLead($orderEcwid);
            $amoCrmServise = new AmoCrmServise();
            $res = $amoCrmServise->NewOrder($amoDataEcwid);

            $client = Clients::firstOrNew([
                'email' => $orderEcwid['email']
            ]);
            $client->name = $orderEcwid['billingPerson']['name'];
            $client->phone = $orderEcwid['billingPerson']['phone'];
            $client->save();

            if (!empty($res['amo_id'])) {

                echo "amo order create - " . $res['amo_id'];

                $order = Orders::firstOrCreate([
                    'ecwidId' => $orderId
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
                AppErrors::addError('error create amo lead', $res);
            }

        }
    }

    public function getOrderById(Request $request)
    {
        $id = $request->get('id');

        if ($id) {

            $res = $this->amoService->getOrderById($id);

            echo "<pre>";
            var_dump($res);
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
