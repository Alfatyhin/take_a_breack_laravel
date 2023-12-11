<?php

namespace App\Http\Controllers;

use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\Orders;
use App\Models\WebhookLog;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\OrderService;
use App\Services\SendpulseService;
use App\Services\ShopifyClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Array_;

class ShopifyController extends Controller
{

    public function test(Request $request)
    {

        $Client = new ShopifyClient();
        $test = $Client->get('customers');

        dd($test);
        $response = $Client->createClient();
        dd($response);


    }

    public function testWebhook(Request $request)
    {
        $data = $request->post('json');
        $data = json_decode($data, true);


        $action = $data['action'];

        if ($action == 'orders/create') {
//            WebhookLog::addLog("Shopify new order webhook", $data);


            $client = $this->getAmoContactData($data);

            $client = OrderService::clientCreateOrUpdate($client);

            $order = Orders::firstOrCreate([
                'order_id' => $data['name']
            ]);
            $order->clientId = $client->id;
            $order->orderPrice = $data['total_price'];
            $order->orderData = json_encode($data);
            if ($data['financial_status'] == 'paid')
                $order->paymentStatus = 4;
            $order->invoiceStatus = 1;
//            $order->save();
//

            $amoCrmService = new AmoCrmServise();
            $amoData = $this->AmoOrderPrepeare($data);
            $amoNotes = $this->AmoNotesPrepeare($data);
            $amoData['text_note'] = $amoNotes;
            $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);

            $amoData['order name'] = 'test - '.$amoData['order name'];
//            $lead = $amoCrmService->createNewLead($amoData);

            dd($amo_contact, $amoData, $amoNotes);

            if ($amo_contact->id != $client->amoId) {
                $client->amoId = $amo_contact->id;
                $client->save();
            }


            $open_lead = $amoCrmService->searchOpenLeadByContactId($client->amoId);

            if ($open_lead) {
                $lead = $amoCrmService->updateLead($open_lead, $amoData);
            } else {

                $lead = $amoCrmService->createNewLead($amoData);
                $amoCrmService->addContactToLead($amo_contact, $lead);
            }

            if ($lead) {
                $amoCrmService->addTextNotesToLead($lead->id, $amoNotes);

                $amoProducts = $this->getShopAmoProducts($amoCrmService, $data);
                $amoCrmService->addSopProductsToLead($lead->id, $amoProducts);

                $amo_invoice_id = $amoCrmService->addInvoiceToLead($amo_contact->id, $order->order_id, $lead->id, (float) $order->orderPrice, $order->paymentStatus);
                $amoData['invoice_id'] = $amo_invoice_id;

                $order->amoData = json_encode($amoData);
                $order->amoId =$lead->id;
                $order->save();

                dd('завершено');
            } else {

                dd('нет лида');
                AppErrors::addError('error create amo lead', $amoData);
                return false;
            }

        } elseif ($action == 'checkouts/update' || $action == 'checkouts/create') {

            if (isset($data['customer'])) {

                $client = $this->getAmoContactData($data);

                $client = OrderService::clientCreateOrUpdate($client);

                $amoCrmService = new AmoCrmServise();
                $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);

                $amo_data = $amo_contact->toArray();
                WebhookLog::addLog("add/update amo contact id " . $amo_data['id'], $amo_data);

                dd($amo_contact->toArray());
            } else {
                dd('not data', $data);
            }
        }

    }

    public function workWebhook(Request $request)
    {
        $data = $request->post('json');
        $data = json_decode($data, true);


        $action = $data['action'];

        if ($action == 'orders/create') {
//            WebhookLog::addLog("Shopify new order webhook", $data);


            $client = $this->getAmoContactData($data);

            $client = OrderService::clientCreateOrUpdate($client);

            $order = Orders::firstOrCreate([
                'order_id' => $data['name']
            ]);
            $order->clientId = $client->id;
            $order->orderPrice = $data['total_price'];
            $order->orderData = json_encode($data);
            if ($data['financial_status'] == 'paid')
                $order->paymentStatus = 4;
            $order->invoiceStatus = 1;
//            $order->save();
//

            $amoCrmService = new AmoCrmServise();
            $amoData = $this->AmoOrderPrepeare($data);
            $amoNotes = $this->AmoNotesPrepeare($data);
            $amoData['text_note'] = $amoNotes;
            $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);

            $amoData['order name'] = 'test - '.$amoData['order name'];
//            $lead = $amoCrmService->createNewLead($amoData);


            if ($amo_contact->id != $client->amoId) {
                $client->amoId = $amo_contact->id;
                $client->save();
            }


            $open_lead = $amoCrmService->searchOpenLeadByContactId($client->amoId);

            if ($open_lead) {
                $lead = $amoCrmService->updateLead($open_lead, $amoData);
            } else {

                $lead = $amoCrmService->createNewLead($amoData);
                $amoCrmService->addContactToLead($amo_contact, $lead);
            }

            if ($lead) {
                $amoCrmService->addTextNotesToLead($lead->id, $amoNotes);

                $amoProducts = $this->getShopAmoProducts($amoCrmService, $data);
                $amoCrmService->addSopProductsToLead($lead->id, $amoProducts);

                $amo_invoice_id = $amoCrmService->addInvoiceToLead($amo_contact->id, $order->order_id, $lead->id, (float) $order->orderPrice, $order->paymentStatus);
                $amoData['invoice_id'] = $amo_invoice_id;

                $order->amoData = json_encode($amoData);
                $order->amoId =$lead->id;
                $order->save();

                dd('завершено');
            } else {

                dd('нет лида');
                AppErrors::addError('error create amo lead', $amoData);
                return false;
            }

        } elseif ($action == 'checkouts/update' || $action == 'checkouts/create') {

            if (isset($data['customer'])) {

                $client = $this->getAmoContactData($data);

                $client = OrderService::clientCreateOrUpdate($client);

                $amoCrmService = new AmoCrmServise();
                $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);

                $amo_data = $amo_contact->toArray();
                WebhookLog::addLog("add/update amo contact id " . $amo_data['id'], $amo_data);

                dd($amo_contact->toArray());
            } else {
                dd('not data', $data);
            }
        }

    }


    public function webhook(Request $request)
    {


        if ($request->hasHeader('X-Shopify-Hmac-Sha256')) {

            if($this->verifyWebhook(file_get_contents('php://input'), $request->header('X-Shopify-Hmac-Sha256'))) {

                http_response_code(200);

                $data = $request->json()->all();
                $data['action'] = $request->header('X-Shopify-Topic');
                $id = $request->header('X-Shopify-Webhook-Id');
                $action = $request->header('X-Shopify-Topic');
                $test = WebhookLog::where('name', "ShopifyWebhook - |$action|".$id)->first();



                if (!$test) {
                    $webhook = new WebhookLog();
                    $webhook->name = "ShopifyWebhook - |$action|".$id;
                    $webhook->data = json_encode($data);
                    $webhook->save();


                    if ($action == 'orders/create') {

                        $client = $this->getAmoContactData($data);

                        $client = OrderService::clientCreateOrUpdate($client);

                        $order = new Orders();
                        $order->order_id = $data['name'];
                        $order->clientId = $client->id;
                        $order->orderPrice = $data['total_price'];
                        $order->orderData = json_encode($data);
                        if ($data['financial_status'] == 'paid')
                            $order->paymentStatus = 4;
                        $order->invoiceStatus = 1;
                        $order->save();



                        $amoCrmService = new AmoCrmServise();
                        $amoData = $this->AmoOrderPrepeare($data);
                        $amoData['id'] = $order->id;
                        $amoNotes = $this->AmoNotesPrepeare($data);
                        $amoData['text_note'] = $amoNotes;
                        $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);

                        if ($amo_contact->id != $client->amoId) {
                            $client->amoId = $amo_contact->id;
                            $client->save();
                        }


                        $open_lead = $amoCrmService->searchOpenLeadByContactId($client->amoId);

                        if ($open_lead) {
                            $lead = $amoCrmService->updateLead($open_lead, $amoData);
                        } else {

                            $lead = $amoCrmService->createNewLead($amoData);
                            $amoCrmService->addContactToLead($amo_contact, $lead);
                        }

                        if ($lead) {
                            $amoCrmService->addTextNotesToLead($lead->id, $amoNotes);

                            $amoProducts = $this->getShopAmoProducts($amoCrmService, $data);
                            $amoCrmService->addSopProductsToLead($lead->id, $amoProducts);

                            $amo_invoice_id = $amoCrmService->addInvoiceToLead($amo_contact->id, $order->order_id, $lead->id, (float) $order->orderPrice, $order->paymentStatus);
                            $amoData['invoice_id'] = $amo_invoice_id;

                            $order->amoData = json_encode($amoData);
                            $order->amoId =$lead->id;
                            $order->save();

                        } else {
                            AppErrors::addError('error create amo lead', $amoData);
                        }

                    }

                }  elseif ($action == 'checkouts/update' || $action == 'checkouts/create') {

                    if (isset($data['customer'])) {
                        $client = $this->getAmoContactData($data);

                        $client = OrderService::clientCreateOrUpdate($client);

                        $amoCrmService = new AmoCrmServise();
                        $amo_contact = $this->searchOrCreateAmoContact($amoCrmService, $client, $data);
                        $amo_data = $amo_contact->toArray();
                        WebhookLog::addLog("add/update amo contact id " . $amo_data['id'], $amo_data);

                    }
                }


            } else {
                dd('not verification');
            }
        } else {
            dd('not verification');
        }
    }




    private function getAmoContactData($data)
    {
        $client_data = $data['customer'];

        $client['clientName'] = $client_data['first_name'] . ' ' . $client_data['last_name'];
        if (isset($client_data['email']))
            $client['email'] = $client_data['email'];
        else
            $client['email'] = 'generate_'.time().'@site.com';
        if (isset($client_data['phone'])) {
            $client['phone'] = $client_data['phone'];
        } elseif (isset($data['phone'])) {
            $client['phone'] = $data['phone'];
        } elseif (isset($data['billing_address']['phone']) && !empty($data['billing_address']['phone'])) {

            $client['phone'] = $data['billing_address']['phone'];
            if (isset($data['billing_address']['country_code']) && !empty($data['billing_address']['country_code']) && $data['billing_address']['country_code'] == 'IL') {
                $client['phone'] = '972-'.$client['phone'];
            }
        }

        return $client;
    }


    private function verifyWebhook($data, $header)
    {
        $secret = env('SHOPY_SECRET');

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $secret, true));
        return hash_equals($calculated_hmac, $header);
    }


    private function AmoOrderPrepeare($data)
    {

        // формируем массив данных для амо
        $pipelineId = '4651807'; // воронка
        $statusId = '43924885'; // статус

        $products = $data['line_items'];
        foreach ($products as $key => $item) {
            $product_name = $item['name'];
            $tags[] = $product_name;
        }

        if ($data['financial_status'] == 'paid') {
            $payment = 'Оплачен';
        } elseif ($data['financial_status'] == 'pending') {
            $payment = 'Оплата наличными по факту';
        } else {
            $payment = false;
        }

        // deliwery adress
        $address = '';
        if (isset($data['shipping_address']) && !empty($data['shipping_address'])) {

            $address = $data['shipping_address']['city']
                . ' ' . $data['shipping_address']['address1'];
        }


        $tags[] = $data['customer_locale'];

//        dd($data['note_attributes']);
        foreach ($data['note_attributes'] as $item) {
            if($item['name'] == 'delivery_date_origin') {
                $date = $item['value'];
            } elseif($item['name'] == 'Choose Time') {
                $time = $item['value'];
            } else {

            }

        }

        if (!isset($time)) {
            $time = '9:00-21:00';
        }

        $timeDelivery = $time;

        if (!isset($date)) {
            $date = new Carbon();
            $date = $date->format('Y-m-d');
        }

//        $time = str_replace(':00', '', $time);
//        $time = str_replace('-', ':', $time);
        $delivery_date_time = $date . ' ' . $time;
        $date = Carbon::parse($date);
//        dd($date);
        $dateOrder = strtotime($date->format('Y-m-d H:i:s'));

        if(!isset($data['note'])) {
            $data['note'] = '';
        }

        $dataOrderAmo = [
            'order name'  => $data['name'],
            'order_id'    => $data['name'],
            'api_mode'    => 'Shopyfi',
            'order price' => $data['total_price'],
            'pipelineId'  => $pipelineId,
            'statusId'    => $statusId,
            'notes'       => $data['note'],
            'name'        => $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'address'     => $address,
            'payment'     => $payment,
            'date'        => $dateOrder,
            'time'        => $timeDelivery,
            'tags'        => $tags
        ];

        return $dataOrderAmo;
    }


    private function AmoNotesPrepeare($data)
    {
        $ordersNotes = 'Детали заказа: ' . $data['name'];

        $products = $data['line_items'];
        foreach ($products as $key => $item) {
            $product_name = $item['name'];

            $ordersNotes .= "\n" . $item['quantity'] . "x - {$item['price']} шек " . $product_name . ' ';
        }


        if (isset($data['order_data']['products_total'])) {
            $ordersNotes .= "\n ---------------------- \n Итого: {$data['subtotal_price']} шек (без скидки)";
        }
        $ordersNotes .= "\n ---------------------- \n";

        if (isset($data['otherPerson'])) {
            if (empty($data['otherPerson'])) {
                $data['otherPerson'] = 'неизвестно';
            }
            $ordersNotes .= "\n ---------------------- \n
            Доставка в подарок: для {$data['nameOtherPerson']} tel {$data['phoneOtherPerson']}
            \n ---------------------- \n";
        }


        $attributes = '';
        foreach ($data['note_attributes'] as $item) {
            if($item['name'] == 'delivery_date_origin') {
                $date = $item['value'];
            } elseif($item['name'] == 'Delivery Time') {
                $time = $item['value'];
            } else {
                $attributes .= "\n ".$item['name'].' - '.$item['value'];
            }
        }

        if(!empty($attributes)) {
            $attributes = "дополнительные данные: $attributes \n ----------------------\n ";
        }

        if (!isset($time)) {
            $time = '9:00-21:00';
        }

        if (!isset($date)) {
            $date = new Carbon();
            $date = $date->format('Y-m-d');
        }

        $timeDelivery = $date . ' время ' . $time;


        $shipping = 'Доставка: ';
        if (isset($data['shipping_address']) && !empty($data['shipping_address'])) {

            foreach ($data['shipping_address'] as $key => $val) {

                if (!empty($val)) {
                    $shipping .= "\n $key - " . $val;
                }
            }

            $shipping .= "\n стоимость - " . $data['total_shipping_price_set']['shop_money']['amount'] . 'шек'
                . "\n ---------------------- \n";


        } else {
            $shipping = 'Самовывоз ' . $timeDelivery . "\n ---------------------- \n";
        }



        if (isset($data['order_data']['discount']) && !empty($data['order_data']['discount'])) {
            $code = $data['order_data']['discount']['code'];
            $discount = "скидка {$data['order_data']['discount']['text']} coupon - $code  \n";
        } else {
            $discount = '';
        }
        if (isset($data['order_data']['tips']) && !empty($data['order_data']['tips'])) {
            $tips = "Чаевые {$data['order_data']['tips']} \n";
        } else {
            $tips = '';
        }

        if(isset($data['note'])) {
            $orderComments = 'Комментарий покупателя: ' . "\n"
                . $data['note'] . "\n ---------------------- \n";
        } else {
            $orderComments = 'Комментарий покупателя: ' . "\n"
                . "Нет комментария " . "\n ---------------------- \n";
        }


        $notes = $ordersNotes . $attributes . $orderComments . $discount . $tips . $shipping;

        $notes = $notes . "\n                    Итого: {$data['current_total_price']} шек";

        return $notes;
    }


    private function searchOrCreateAmoContact(AmoCrmServise $amoCrmService, Clients $client, $orderData)
    {
        $contactData = [
            'name' => $client->name,
            'email' => $client->email,
        ];

        if ($client->phone) {
            $contactData['phone'] = OrderService::phoneAmoFormater($client->phone);
        }

        $client_data = json_decode($client->data, true);
        if (isset($client_data['clientBirthDay'])) {
            $date = AppServise::dateFormater($client_data['clientBirthDay']);
            if ($date) {
                $date = new Carbon($date);
                $date_time = strtotime($date->format('Y-m-d H:i:s'));
                $contactData['birthday'] = $date_time;
            }
        }

        if (isset($orderData['customer']['default_address']['city'])) {
            $contactData['city'] = AppServise::getCityNameByLang($orderData['customer']['default_address']['city'], 'ru');
        }

        if (isset($orderData['customer_locale'])) {
            if (preg_match('/^en/', $orderData['customer_locale'])) {
                $contactData['lang'] = 'Английский';
            } elseif (preg_match('/^ru/', $orderData['customer_locale'])) {
                $contactData['lang'] = 'Русский';
            } elseif (preg_match('/^il/', $orderData['customer_locale'])) {
                $contactData['lang'] = 'Иврит';
            }

        } elseif (isset($orderData['client_details']['accept_language'])) {
            if (preg_match('/^en/', $orderData['client_details']['accept_language'])) {
                $contactData['lang'] = 'Английский';
            } elseif (preg_match('/^ru/', $orderData['client_details']['accept_language'])) {
                $contactData['lang'] = 'Русский';
            } elseif (preg_match('/^il/', $orderData['client_details']['accept_language'])) {
                $contactData['lang'] = 'Иврит';
            }
        }

        if (!empty($client->amoId)) {
            $contact = $amoCrmService->getContactBuId($client->amoId);
        } else {
            $contact = $this->searchAmoContact($amoCrmService, $client);
        }

        if (!$contact) {
            $contact = $amoCrmService->createContact($contactData);
        } else {
            $contact = $amoCrmService->syncContactData($contact, $contactData);
        }

        return $contact;
    }


    private function searchAmoContact(AmoCrmServise $amoCrmService, Clients $client)
    {

        $contact = $amoCrmService->searchContactFilter($client->email);

        if (!$contact && $client->phone) {
            $contact = $amoCrmService->searchContactFilter($client->phone);
        }
        if (!$contact) {
            $clientData = json_decode($client->data, true);
            if (isset($clientData['phones'])) {
                foreach ($clientData['phones'] as $phone) {
                    if (!$contact) {
                        $contact = $amoCrmService->searchContactFilter($phone);
                    }
                    if (!$contact) {
                        $contact = $amoCrmService->searchContactFilter(OrderService::phoneAmoFormater($phone));
                    }
                }
            }

        }

        return $contact;
    }


    private function getShopAmoProducts(AmoCrmServise $amoCrmService, $orderData)
    {
        $select_name = 'Сайт витрина';

        if (isset($orderData['line_items']) && !empty($orderData['line_items'])) {
            $products = $orderData['line_items'];
            foreach ($products as &$item)
            {
                $name = $item['name'];

                $data = [
                    'name' => $name,
                    'sku' => 'SF-ID-'.$item['id'],
                    'price' => $item['price'],
                    '523159' => 'Shopify'
                ];

                $product_amo = $amoCrmService->getCatalogElementBuSku($item['id'], $select_name);
                if (!$product_amo) {
                    $product_amo = $amoCrmService->setCatalogElement($data, $select_name);
                }

                $customFields = $product_amo->getCustomFieldsValues();
                $fieldPrice = $customFields->getBy('fieldCode', 'PRICE');
                $price_amo = $fieldPrice->getValues()->first()->value;

                if ($name != $product_amo->name || $item['price'] != $price_amo) {
                    $product_amo = $amoCrmService->updateCatalogElement($product_amo, $data, $select_name);
                }


                $item['count'] = $item['fulfillable_quantity'];
                $item['amo_model'] = $product_amo;
            }

        } else {
            return false;
        }

        return $products;
    }
}
