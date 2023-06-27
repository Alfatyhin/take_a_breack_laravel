<?php

namespace App\Http\Controllers;

use AmoCRM\Models\AccountModel;
use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Providers\EcwidProvider;
use App\Services\AmoChatsService;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\GreenInvoiceService;
use App\Services\OrderService;
use App\Services\SendpulseService;
use App\Services\ShopifyClient;
use Carbon\Carbon;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class Amocrm extends Controller
{
    private $amoService;
    public function __construct(AmoCrmServise $service) {
        $this->amoService = $service;
    }

    public function incommingChatMessage(Request $request, $score_id)
    {
        $data = $request->json()->all();

        http_response_code(200);
        $webhook = new WebhookLog();
        $webhook->name = 'Amocrm - incommingChatMessage - '.$score_id;
        $webhook->data = json_encode($data);
        $webhook->save();

        $bot_id = '64283d700269870cfd494252';

        if ($data['message']['message']['type'] == 'text') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => $data['message']['message']['type'],
                    'text' => [
                        'body' => $data['message']['message']['text']
                    ]
                ]
            ];

        } elseif ($data['message']['message']['type'] == 'picture') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => "image",
                    'image' => [
                        'link' => $data['message']['message']['media'],
                        'caption' => $data['message']['message']['text']
                    ]
                ]
            ];
        } elseif ($data['message']['message']['type'] == 'file') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => "document",
                    'document' => [
                        'link' => $data['message']['message']['media'],
                        'caption' => $data['message']['message']['text']
                    ]
                ]
            ];
        }

        if (isset($data_message)) {
            $Service = new SendpulseService();
            $res = $Service->sendWhatsapp('contacts/sendByPhone', $data_message);




            if ($res->success === true) {

                $webhook = new WebhookLog();
                $webhook->name = 'SendpulseService - res';
                $webhook->data = json_encode($res);
                $webhook->save();

                $status_data = [
                    'msgid' => $data['message']['message']['id'],
                    'delivery_status' => 1,
                    'error_code' => '',
                    'error' =>  ''
                ];
            } else {

                $webhook = new WebhookLog();
                $webhook->name = 'SendpulseService - error';
                $webhook->data = json_encode($res->errors);
                $webhook->save();

                $status_data = [
                    'msgid' => $data['message']['message']['id'],
                    'delivery_status' => -1,
                    'error_code' => '',
                    'error' =>  json_encode($res->errors)
                ];
            }


            $AmoChatsService = new AmoChatsService();
            $AmoChatsService->messageStatus($status_data);

        } else {
            $status_data = [
                'msgid' => $data['message']['message']['id'],
                'delivery_status' => -1,
                'error_code' => '',
                'error' =>  'непредвиденное исключение'
            ];

            $AmoChatsService = new AmoChatsService();
            $AmoChatsService->messageStatus($status_data);
        }


    }

    public function testIncommingChatMessage(Request $request)
    {
        $data = $request->post('json');
        $data = json_decode($data, true);

        $bot_id = '64283d700269870cfd494252';

        if ($data['message']['message']['type'] == 'text') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => $data['message']['message']['type'],
                    'text' => [
                        'body' => $data['message']['message']['text']
                    ]
                ]
            ];

        } elseif ($data['message']['message']['type'] == 'picture') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => "image",
                    'image' => [
                        'link' => $data['message']['message']['media'],
                        'caption' => $data['message']['message']['text']
                    ]
                ]
            ];
        } elseif ($data['message']['message']['type'] == 'file') {
            $data_message= [
                'bot_id' => $bot_id,
                'phone' => $data['message']['receiver']['phone'],
                'message' => [
                    'type' => "document",
                    'document' => [
                        'link' => $data['message']['message']['media'],
                        'caption' => $data['message']['message']['text']
                    ]
                ]
            ];
        }

        $data_message['phone'] = '+380992363774';

        if (isset($data_message)) {

            $Service = new SendpulseService();
            $res = $Service->sendWhatsapp('contacts/sendByPhone', $data_message);


            if ($res->success === true) {

                $webhook = new WebhookLog();
                $webhook->name = 'SendpulseService - res';
                $webhook->data = json_encode($res);
                $webhook->save();

                $status_data = [
                    'msgid' => $data['message']['message']['id'],
                    'delivery_status' => 1,
                    'error_code' => '',
                    'error' =>  ''
                ];
            } else {

                $webhook = new WebhookLog();
                $webhook->name = 'SendpulseService - error';
                $webhook->data = json_encode($res->errors);
                $webhook->save();

                $status_data = [
                    'msgid' => $data['message']['message']['id'],
                    'delivery_status' => -1,
                    'error_code' => '',
                    'error' =>  json_encode($res->errors)
                ];
            }


            $AmoChatsService = new AmoChatsService();
            $AmoChatsService->messageStatus($status_data);

        } else {
            $status_data = [
                'msgid' => $data['message']['message']['id'],
                'delivery_status' => -1,
                'error_code' => '',
                'error' =>  'непредвиденное исключение'
            ];

            $AmoChatsService = new AmoChatsService();
            $AmoChatsService->messageStatus($status_data);
        }


    }

    public function integrationAmoCrm(Request $request)
    {
        $title = 'Amo-CRM integration';
        $amoCrmService = new AmoCrmServise();
        $ownerDetails = $amoCrmService->getAccount();

        if ($ownerDetails && $ownerDetails->getName()) {
            $messages[] = $ownerDetails->getName() . ' Integration is Work';
            $messages[] = $ownerDetails->getAmojoId() . ' - amojoId';
            $messages[] = $ownerDetails->getUuid() . ' - getUuid';
            $messages[] = $ownerDetails->getId() . ' - getId';
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


    public function widgetDownload()
    {
        $res =  $this->amoService->pacWidgetZipFile();


        if ($res) {

            return Storage::disk('public')->download('amo_widget/widget.zip');
        }

    }

    public function amoWebhook(Request $request)
    {

        http_response_code(200);
        $test = false;
        $post = $request->post();
        $site = env('APP_NAME');

        if($request->get('test') == 1) {
            $test = '{"leads":{"update":[{"id":"24527277","name":"#1019","status_id":"43924885","price":"210","responsible_user_id":"216744","last_modified":"1682059129","modified_user_id":"0","created_user_id":"0","date_create":"1682059128","pipeline_id":"4651807","account_id":"29039599","custom_fields":[{"id":"509001","name":"\u0410\u0434\u0440\u0435\u0441 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":[{"value":"\u05e8\u05d0\u05e9\u05d5\u05df \u05dc\u05e6\u05d9\u05d5\u05df \u05d0\u05dc\u05d9\u05e8\u05d6 \u05e9\u05dc\u05de\u05d4 7"}]},{"id":"514563","name":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","values":[{"value":"\u05d0\u05dc\u05d5\u05e0\u05d4 \u05d1\u05d5\u05e8\u05d9\u05e1\u05e0\u05e7\u05d5"}]},{"id":"308363","name":"\u041e\u043f\u043b\u0430\u0442\u0430","values":[{"value":"\u041e\u043f\u043b\u0430\u0447\u0435\u043d","enum":"436781"}]},{"id":"520559","name":"\u0414\u0430\u0442\u0430 \u0441\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437\u0430\/\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":["1682024400"]},{"id":"520561","name":"\u0412\u0440\u0435\u043c\u044f","values":[{"value":"9:00-21:00"}]},{"id":"512455","name":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430","values":[{"value":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430: #1019\n1x - 170.00 \u0448\u0435\u043a Gift Set RAW - Small \n ---------------------- \n\u0434\u043e\u043f\u043e\u043b\u043d\u0438\u0442\u0435\u043b\u044c\u043d\u044b\u0435 \u0434\u0430\u043d\u043d\u044b\u0435: \n Store Pickup - Pick up-Emanuel Ringelblum 3, Holon \n ----------------------\n \u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439 \u043f\u043e\u043a\u0443\u043f\u0430\u0442\u0435\u043b\u044f: \n\u041d\u0435\u0442 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u044f \n ---------------------- \n\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430: \n first_name - \u05d0\u05dc\u05d5\u05e0\u05d4\n address1 - \u05d0\u05dc\u05d9\u05e8\u05d6 \u05e9\u05dc\u05de\u05d4 7\n phone - 052-475-8596\n city - \u05e8\u05d0\u05e9\u05d5\u05df \u05dc\u05e6\u05d9\u05d5\u05df\n zip - 7533699\n country - Israel\n last_name - \u05d1\u05d5\u05e8\u05d9\u05e1\u05e0\u05e7\u05d5\n address2 - 38\n name - \u05d0\u05dc\u05d5\u05e0\u05d4 \u05d1\u05d5\u05e8\u05d9\u05e1\u05e0\u05e7\u05d5\n country_code - IL\n \u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u044c - 40.00\u0448\u0435\u043a\n ---------------------- \n\n \u0418\u0442\u043e\u0433\u043e: 210.00 \u0448\u0435\u043a"}]},{"id":"519327","name":"\u0440\u0430\u0441\u043f\u0435\u0447\u0430\u0442\u0430\u0442\u044c \u0437\u0430\u043a\u0430\u0437 (\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430)","values":[{"value":"https:\/\/takeabreak.co.il\/api\/orders\/view-order\/#1019"}]},{"id":"489653","name":"Api order ID","values":[{"value":"#1019"}]},{"id":"511579","name":"Api mode","values":[{"value":"Shopyfi"}]}],"link_changed":"1","created_at":"1682059128","updated_at":"1682059129"}]},"account":{"subdomain":"takebreak","id":"29039599","_links":{"self":"https:\/\/takebreak.amocrm.ru"}}}';


            $post = json_decode($test, true);

        }
        if($request->get('test') != 1) {
            WebhookLog::addLog('amo web hook', $post);
        }

        if (!empty($post['leads'])) {


            foreach ($post['leads'] as $event => $items) {


                if ($event == 'status' || $event == 'update') { // изменение статуса
                    foreach ($items as $item) {

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

                                    if ($item['status_id'] == '142') {
                                        $amoSService = new AmoCrmServise();
                                        $amo_lead = $amoSService->getOrderById($item['id']);

                                        $amo_contact_id = $amo_lead->getContacts()->first()->getId();
                                        $amo_contact = $amoSService->getContactBuId($amo_contact_id);

                                        $contact_data['name'] = $amo_contact->getName();

                                        $fields = $amo_contact->getCustomFieldsValues();

                                        foreach ($fields as $item_field) {
                                            $name_field = $item_field->fieldName;
                                            $value_field = $item_field->getValues()->first()->value;

                                            if ($name_field == 'Email') {
                                                $contact_data['email'] = $value_field;
                                                $variables_data['email'] = $value_field;
                                            } elseif ($name_field == 'Телефон') {
                                                $contact_data['phone'] =  preg_replace('/[^0-9]/', '', $value_field);
                                                $variables_data['﻿phone'] =  preg_replace('/[^0-9]/', '', $value_field);
                                            } elseif ($name_field == 'Город') {
                                                $contact_data['Город'] = $value_field;
                                                $variables_data['Город'] = $value_field;
                                            } elseif ($name_field == 'Язык') {
                                                $contact_data['Язык'] = $value_field;
                                                $variables_data['Язык'] = $value_field;
                                            }
                                        }

                                        if (isset($contact_data['phone'])) {
                                            $SendpulseService = new SendpulseService();

                                            $bot_id = '64283d700269870cfd494252';
                                            $test_contact = $SendpulseService->getWhatsapp("contacts/getByPhone?phone={$contact_data['phone']}&bot_id=$bot_id");

                                            if ($test_contact->success === false) {
                                                $contact_data_senpulse['name'] = $contact_data['name'];
                                                $contact_data_senpulse['phone'] = $contact_data['phone'];
                                                $contact_data_senpulse['bot_id'] = $bot_id;

                                                $SendpulseService->sendWhatsapp('contacts', $contact_data_senpulse);
                                                $test_contact = $SendpulseService->getWhatsapp("contacts/getByPhone?phone={$contact_data['phone']}&bot_id=$bot_id");
                                            }

                                            if ($test_contact->success === true) {
                                                $contact_id = $test_contact->data->id;
                                                foreach ($variables_data as $v_name => $variable) {
                                                    $variable_data = [
                                                        'contact_id' => $contact_id,
                                                        'variable_name' => $v_name,
                                                        'variable_value' => $variable,

                                                    ];
                                                    $SendpulseService->sendWhatsapp('contacts/setVariable', $variable_data);
                                                }
                                            }
                                        }

                                    }

                                    // отправка инвойса
                                    if ($statusPaidAmo == '436781'
                                        && $order->invoiceStatus == 0
                                        && $site != 'Take a Break Server'
                                        && $api_mode != 'Shopyfi') {

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


    public function UsersDuplicateCollaps(Request $request, $client_id = false)
    {

        set_time_limit(60*60);

        $amoCrmService = $this->amoService;

        if ($client_id) {
            $clients[] = Clients::find($client_id);
        } else {
            $clients = Clients::where('amoId', '!=', null)->orderBy('id', 'desc')->get();
        }


        foreach ($clients as $client) {

            $amo_deleted = [];
            $result = [];
            $client_data = [];

            $new_amo_id = $request->get(('new_amo_id'));

//            $renew['email'][] = $client->email;
//            $renew['phone'][] = $client->phone;
//
//            $amo_client = $amoCrmService->getContactBuId($client->amoId);
//            $test = $amoCrmService->renewContactData($amo_client, $renew);
//
//            dd($test);

            if ($client && $client->data) {
                $client_data = json_decode($client->data, true);
            }


            if (!$client_data
                || ($client_data && !isset($client_data['amo_double_checked']))
                || ($client_data && empty($client_data['amo_double_checked']))) {

                $amo_client = $amoCrmService->getContactBuId($client->amoId);

                $test_email = $amoCrmService->getContactDoubles($client->email);
                $test_phones = $amoCrmService->searchContactByPhone($client->phone);

                if ($test_email && $test_phones) {
                    $diff = array_diff_key($test_email, $test_phones);
                    $result = $test_phones;
                    if ($diff) {

                        foreach ($diff as $key => $val) {
                            $result = Arr::add($result, $key, $val);
                        }

                    } else {
                        $result = $test_email;
                    }
                } else {
                    if ($test_email) {
                        $result = $test_email;
                    }
                    if ($test_phones) {
                        $result = $test_phones;
                    }
                }

                if (isset($client_data['phones']) && sizeof($client_data['phones']) > 1) {

                    foreach ($client_data['phones'] as &$item) {
                        $item = OrderService::phoneAmoFormater($item);
                    }

                    $phones = array_unique($client_data['phones']);

                    if (sizeof($client_data['phones']) != sizeof($phones)) {
                        $client_data['phones'] = $phones;
                        $client->data = json_encode($client_data);
                        $client->save();
                    }

                    if (sizeof($client_data['phones']) > 1) {

                        foreach ($client_data['phones'] as $item) {
                            $test_phones = $amoCrmService->searchContactByPhone($client->phone);
                            foreach ($test_phones as $key => $val) {
                                $result = Arr::add($result, $key, $val);
                            }

                        }
                    }

                }


                if (sizeof($result) > 1) {


//                    $data = $result[$new_amo_id];
//                    $phones = array_unique($data['fields']['Телефон']);
//                    $mails = array_unique($data['fields']['Email']);
//
//                    foreach ($phones as &$item_phone) {
//                        $item_phone = OrderService::phoneAmoFormater($item_phone);
//                    }
//
//                    $phones = array_unique($phones);
//
//                    $renew['email'] = $mails;
//                    $renew['phone'] = $phones;
//
//                    $renew_client = $amoCrmService->getContactBuId($new_amo_id);
//                    $test = $amoCrmService->renewContactData($renew_client, $renew);

                    if ($client_id && !$new_amo_id) {
                        print_r("<p>Выберите коосновной контакт</p> ");
                        foreach ($result as $res_item) {

                            $url = route("amocrm_users_duplicate", ["client" => $client->id, 'new_amo_id' => $res_item['id']]);
                            print_r("<p> <a href='$url'>{$res_item['id']}</a></p>");
                        }
                        dd($test_email, $test_phones, $result);
                    }


                    $phones = [];
                    $mailes = [];
                    foreach ($result as $item) {
                        if (isset($item['fields']['Телефон'])) {
                            foreach ($item['fields']['Телефон'] as $phone) {
                                $phones[] = OrderService::phoneAmoFormater($phone);
                            }
                        }
                        if (isset($item['fields']['Email'])) {
                            foreach ($item['fields']['Email'] as $email) {
                                $mailes[] = $email;
                            }
                        }
                        if (isset($item['fields']['Город']) && !isset($result[$new_amo_id]['fields']['Город'])) {
                            $update_amo['city'] = $item['fields']['Город'][0];
                        }

                        $phones = array_unique($phones);
                        $mailes = array_unique($mailes);

                    }


                    $update_amo['email'] = $mailes;
                    $update_amo['phone'] = $phones;

                    if ($client_id && $new_amo_id) {
                        $contact = $amoCrmService->getContactBuId($new_amo_id);
                        $new_contact = $amoCrmService->syncContactData($contact, $update_amo);
                    }

                    $deletes = $result;
                    unset($deletes[$new_amo_id]);

                    foreach ($deletes as $item) {
                        $amo_old_id = $item['id'];
                        $test_client = Clients::where('amoId', $amo_old_id)->first();

                        if ($test_client && $new_amo_id) {
                            $test_client->amoId = $new_amo_id;
                            $test_client->save();
                        }
                        $amo_deleted[] = $amo_old_id;
                    }

                    if (isset($amo_deleted)) {

                        if ($client_id && $new_amo_id) {

                            if (!$amo_client) {
                                $client->amoId = $new_amo_id;
                                $client->save();
                            }

                            print_r("<p>Удалите дубли из первого блока и перезагрузите страницу</p>");

                            dd($amo_deleted, $result, $new_contact->toArray());
                        } else {

                            if ($client_id && !$amo_client) {
                                dd('test 1', $result);
                            }

                            $client_data['amo_double_checked'] = 0;
                            $client->data = json_encode($client_data);
                            $client->save();
                        }

                    } else {
                        if ($client_id && !$amo_client) {
                            dd('test 2', $result);
                        }

                        $client_data['amo_double_checked'] = 1;
                        $client->data = json_encode($client_data);
                        $client->save();

                    }


                } else {

                    $client_data['amo_double_checked'] = 1;
                    if (sizeof($result) == 1) {
                        $client->amoId = key($result);
                    } elseif (sizeof($result) == 0) {
                        $client_data['amo_double_checked'] = -1;
                    }

                    $client->data = json_encode($client_data);
                    $client->save();
                    if ($client_id) {
                        dd('done clear 1');
                    }
                }
            }
        }
        dd('done clear final');

    }

    public function contacts(Request $request, $page = false)
    {
        set_time_limit(60*60);
        $amoCrmService = $this->amoService;
        $next_page = false;
        $prev_page = false;
        $doubles = [];
        $doubles_search = [];
        $stop = 10;

        $post = $request->post();

        if ($post) {
            if (!empty($post['contact']) && !empty($post['merge'])) {
                $contact_id = $post['contact'];
                $contact = $amoCrmService->getContactBuId($contact_id);
                if (!isset($post['merge'][$contact_id])) {
                    foreach ($post['merge'] as $item) {
                        $double = $amoCrmService->getContactBuId($item);

                        if (!empty($doubles)) {
                            $new_contact = $amoCrmService->mergeContacts($contact_id, $item);
                        }

                    }
                    dd('done');
                } else {
                    dd('нельзя соединить контакт сам с собой' );
                }
            }
        }

        if ($page) {
            $contacts = session('contacts');
        } else {
            $contacts = $amoCrmService->getContacts();
        }

        if ($contacts->getNextPageLink()) {
           $next_page =  $contacts->getNextPageLink();
        }
        if ($contacts->getPrevPageLink()) {
            $prev_page =  $contacts->getPrevPageLink();
        }

        $contacts_data = $contacts->toArray();



        if ($next_page) {

            if ($page) {
                $x = $page;
            } else {
                $x = 0;
            }
            $size = 0;

//            while ($size < $stop) {

                if ($next_page && $x > 0) {
                    $contacts = $amoCrmService->getContacts($contacts);

                    if ($contacts->getNextPageLink()) {
                        $next_page =  $contacts->getNextPageLink();
                    }
                    if ($contacts->getPrevPageLink()) {
                        $prev_page =  $contacts->getPrevPageLink();
                    }

                    $contacts_data = $contacts->toArray();
                }

                foreach ($contacts_data as $item) {

                    dd($item);




//                    $id = $item['id'];
//                    $item_data = [];
//                    if ($item['custom_fields_values']) {
//                        foreach ($item['custom_fields_values'] as $field) {
//                            if ($field['field_code'] == 'PHONE') {
//                                foreach ($field['values'] as $value) {
//                                    $item_data['phones'][] = $value['value'];
//                                }
//                            }
//                            if ($field['field_code'] == 'EMAIL') {
//                                foreach ($field['values'] as $value) {
//                                    $item_data['emails'][] = $value['value'];
//                                }
//                            }
//                        }
//                        if (!empty($item_data['emails'])) {
//                            foreach ($item_data['emails'] as $email) {
//                                $doubles_search = $amoCrmService->getContactDoubles($email);
//                                foreach ($doubles_search as $item_search) {
//                                    if ($item_search['id'] != $id && !isset($doubles_search[$item_search['id']])) {
//                                        $doubles_search[$item_search['id']]= $item_search['id'];
//                                        $doubles[$id]['emails'][] = $item_search;
//                                    }
//                                }
//                            }
//                        }
//                        if (!empty($item_data['phones'])) {
//                            foreach ($item_data['phones'] as $phone) {
//                                $doubles_search = $amoCrmService->getContactDoubles($phone);
//                                foreach ($doubles_search as $item_search) {
//                                    if ($item_search['id'] != $id && !isset($doubles_search[$item_search['id']])) {
//                                        $doubles_search[$item_search['id']]= $item_search['id'];
//                                        $doubles[$id]['phones'][] = $item_search;
//                                    }
//                                }
//                            }
//                        }
//                        if (isset($doubles[$id])) {
//                            $doubles_search[$item_search['id']]= $item_search['id'];
//                            $doubles_contacts[$id] = $item;
//                        }
//                    }
                }

                $x++;
                if (!$next_page) {
                    $size = $stop;
                }
                if (sizeof($doubles) >= $stop) {
                    $size = $stop;
                }
                if ($page) {
                    if ($x >= $page + 10) {
                        $size = $stop;
                    }
                } else {
                    if ($x >= 10) {
                        $size = $stop;
                    }
                }

//            }

        }

        session(['contacts' => $contacts]);

//        dd($doubles, $next_page, $prev_page, $x);

        return view('amocrm.contacts', [
            'error_log'     => $request->error_log,
            'contacts_data' => $contacts_data,
            'doubles'       => $doubles,
            'next_page'     => $next_page,
            'prev_page'     => $prev_page,
            'page'          => $x,
        ]);
    }


    public function pipelineTest(Request $request)
    {
//        $test = '{"id": 960, "clientId": 331, "order_id": "S-ZQOJ", "orderData": "{\"_token\":\"UTN4tM9EyHipP189iVS6qBkysGzd3MfB2LfXWAlf\",\"lang\":\"en\",\"delivery\":\"delivery\",\"clientName\":\"\\u05de\\u05d9\\u05ea\\u05e8 \\u05de\\u05e6\\u05e0\\u05e8\",\"city_id\":\"49\",\"city\":\"\\u05e4\\u05ea\\u05d7 \\u05ea\\u05e7\\u05d5\\u05d5\\u05d4\",\"street\":\"\\u05d0\\u05d7\\u05d3 \\u05d4\\u05e2\\u05dd\",\"house\":\"22\",\"flat\":null,\"floor\":\"6\",\"phone\":\"+972 052-687-2887\",\"nameOtherPerson\":null,\"phoneOtherPerson\":null,\"email\":\"meitarmatzner16@gmail.com\",\"clientBirthDay\":null,\"date\":\"2022-6-28\",\"time\":\"11:00-14:00\",\"methodPay\":\"4\",\"client_comment\":null,\"premium\":\"0\",\"order_data\":{\"products\":{\"1-0-71\":{\"id\":71,\"stock_count\":\"0\",\"variant\":\"0\",\"options\":[{\"key\":\"0\",\"value\":{\"text\":\"S\",\"priceModifier\":\"0\",\"textTranslated\":{\"en\":\"Mini\",\"he\":null,\"ru\":\"\\u041c\\u0438\\u043d\\u0438\"},\"priceModifierType\":\"ABSOLUTE\"},\"name\":{\"en\":\"Size\",\"he\":\"\\u05d2\\u05d5\\u05d3\\u05dc\",\"ru\":\"\\u0420\\u0430\\u0437\\u043c\\u0435\\u0440\"}}],\"count\":\"1\",\"price\":\"199\",\"sku\":\"00043S1\",\"name\":{\"en\":\"Tiramisu\",\"he\":\"\\u05e2\\u05d5\\u05d2\\u05ea \\u05e7\\u05e4\\u05d4 - \\u05d2\\u05dc\\u05d9\\u05d3\\u05d4 (\\u05d8\\u05d9\\u05e8\\u05de\\u05d9\\u05e1\\u05d5)\",\"ru\":\"\\u0422\\u0438\\u0440\\u0430\\u043c\\u0438\\u0441\\u0443\"}}},\"delivery_price\":30,\"items\":{\"1-0-71\":{\"id\":71,\"stock_count\":\"0\",\"variant\":\"0\",\"options\":[{\"key\":\"0\",\"value\":{\"text\":\"S\",\"priceModifier\":\"0\",\"textTranslated\":{\"en\":\"Mini\",\"he\":null,\"ru\":\"\\u041c\\u0438\\u043d\\u0438\"},\"priceModifierType\":\"ABSOLUTE\"},\"name\":{\"en\":\"Size\",\"he\":\"\\u05d2\\u05d5\\u05d3\\u05dc\",\"ru\":\"\\u0420\\u0430\\u0437\\u043c\\u0435\\u0440\"}}],\"count\":\"1\",\"price\":\"199\",\"sku\":\"00043S1\",\"name\":{\"en\":\"Tiramisu\",\"he\":\"\\u05e2\\u05d5\\u05d2\\u05ea \\u05e7\\u05e4\\u05d4 - \\u05d2\\u05dc\\u05d9\\u05d3\\u05d4 (\\u05d8\\u05d9\\u05e8\\u05de\\u05d9\\u05e1\\u05d5)\",\"ru\":\"\\u0422\\u0438\\u0440\\u0430\\u043c\\u0438\\u0441\\u0443\"}}},\"products_total\":199,\"order_total\":229}}", "created_at": "2022-06-26T13:04:03.000000Z", "orderPrice": 229, "updated_at": "2022-06-26T13:04:03.000000Z", "paymentMethod": "4", "paymentStatus": 3}';
//
//        $test = json_decode($test, true);
//
//        dd(json_decode($test['orderData'], true));

        $AmoCrmService = $this->amoService;
        $test = $AmoCrmService->getPipelines();

        $post = '{"leads":{"update":[{"id":"25157743","name":"#1103","status_id":"43471465","old_status_id":"50746495","price":"325","responsible_user_id":"216744","last_modified":"1687429626","modified_user_id":"216744","created_user_id":"0","date_create":"1687065261","pipeline_id":"4651807","account_id":"29039599","custom_fields":[{"id":"509001","name":"\u0410\u0434\u0440\u0435\u0441 \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":[{"value":"Petah Tikva Y.L. Perets 3"}]},{"id":"514563","name":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","values":[{"value":"\u0421\u043e\u0444\u0438\u044f \u0422\u0430\u0442\u0430\u0440\u0438\u043d\u043e\u0432\u0430"}]},{"id":"308363","name":"\u041e\u043f\u043b\u0430\u0442\u0430","values":[{"value":"\u041e\u043f\u043b\u0430\u0447\u0435\u043d","enum":"436781"}]},{"id":"520559","name":"\u0414\u0430\u0442\u0430 \u0441\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437\u0430\/\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438","values":["1687381200"]},{"id":"520561","name":"\u0412\u0440\u0435\u043c\u044f","values":[{"value":"09:00 AM - 01:00 PM"}]},{"id":"512455","name":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430","values":[{"value":"\u0414\u0435\u0442\u0430\u043b\u0438 \u0437\u0430\u043a\u0430\u0437\u0430: #1103\n1x - 270.00 \u0448\u0435\u043a Raspberry vanilla biscuit cake - Mini. 4-6 servings (750 g) \n1x - 15.00 \u0448\u0435\u043a Inscription on the cake - Add an inscription on the cake \n ---------------------- \n\u0434\u043e\u043f\u043e\u043b\u043d\u0438\u0442\u0435\u043b\u044c\u043d\u044b\u0435 \u0434\u0430\u043d\u043d\u044b\u0435: \n Gift Message - Happy Birthday! \u2665\ufe0f\n Order Type - Shipping\n Choose Date - 22-06-2023\n Choose Time - 09:00 AM - 01:00 PM\n Choose Day - Thursday\n Date Format - dd-mm-yy \n ----------------------\n \u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0439 \u043f\u043e\u043a\u0443\u043f\u0430\u0442\u0435\u043b\u044f: \n\u041d\u0435\u0442 \u043a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u044f \n ---------------------- \n\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430: \n first_name - \u0421\u043e\u0444\u0438\u044f\n address1 - Y.L. Perets 3\n phone - 052-453-9823\n city - Petah Tikva\n zip - 4920633\n country - Israel\n last_name - \u0422\u0430\u0442\u0430\u0440\u0438\u043d\u043e\u0432\u0430\n name - \u0421\u043e\u0444\u0438\u044f \u0422\u0430\u0442\u0430\u0440\u0438\u043d\u043e\u0432\u0430\n country_code - IL\n \u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u044c - 40.00\u0448\u0435\u043a\n ---------------------- \n\n \u0418\u0442\u043e\u0433\u043e: 325.00 \u0448\u0435\u043a"}]},{"id":"519327","name":"\u0440\u0430\u0441\u043f\u0435\u0447\u0430\u0442\u0430\u0442\u044c \u0437\u0430\u043a\u0430\u0437 (\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430)","values":[{"value":"https:\/\/takeabreak.co.il\/api\/orders\/view-order\/#1103"}]},{"id":"489653","name":"Api order ID","values":[{"value":"#1103"}]},{"id":"511579","name":"Api mode","values":[{"value":"Shopyfi"}]}],"created_at":"1687065261","updated_at":"1687429626"}]},"account":{"subdomain":"takebreak","id":"29039599","_links":{"self":"https:\/\/takebreak.amocrm.ru"}}}';

        $post = json_decode($post, true);

        if (!empty($post['leads'])) {


            foreach ($post['leads'] as $event => $items) {


                if ($event == 'status' || $event == 'update') { // изменение статуса
                    foreach ($items as $item) {

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


                                    if ($api_mode == 'Shopyfi') {

                                        $order_shopy_id = str_replace('#', '', $orer_id);
                                        $Client = new ShopifyClient();

                                        $orderSopy = $Client->getOrderById(5338916880687);
                                        dd($orderSopy);


                                        if ($status_id == '43471465') {
                                            // заказ готов к упаковке
                                        }
                                        if ($status_id == '50746498') {
                                            // готов к самовывозу
                                        }
                                        dd($test, $item);
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
        dd($test, $post);
    }

}
