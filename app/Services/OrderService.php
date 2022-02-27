<?php


namespace App\Services;


use App\Models\AppErrors;
use App\Models\Clients;
use App\Models\Orders;
use App\Services\EcwidService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class OrderService
{

    public function __construct()
    {
    }

    public function sendInvoice(Orders $order, $orderEcwid) : array
    {

        if ($order->invoiceStatus == 0 && $order->paymentStatus == 4) {

            try {
                $invoiceDada = EcwidService::getDataToGreenInvoice($orderEcwid);
            } catch (\Exception $e) {
                AppErrors::addError("error invoice Data to " . $order->ecwiId, $orderEcwid);
            }

            $invoice = new GreenInvoiceService();

            // настройки аккаунта для инвойса
            // для PayPal
            if ($order->paymentMethod == 3) {

                $dataJson = Storage::disk('local')->get('data/app-setting.json');
                $settingData = json_decode($dataJson, true);

                $invoice = $invoice->setMode($settingData['invoice_mode_paypal']);
            }


            try {
                $res = $invoice->newDoc($invoiceDada);

                if (isset($res['errorCode'])) {
                    AppErrors::addError("invoice create error to " . $order->ecwiId, json_encode($res));

                } else {
                    $data[invoiceStatus] = 1;
                    $data[invoiceData] = json_encode($res);
                }

            } catch (\Exception $e) {
                AppErrors::addError("error invoice newDoc to " . $order->ecwiId, $invoiceDada);
            }

            return $data;
        }
    }

    public function getOrderDataToEcwid($data) {

        $ecwidService = new EcwidService();

        $ip = $_SERVER['REMOTE_ADDR'];
        $date = new Carbon();
        $order_date = $date->format("Y-m-d H:i:s");
        $discount = 0;
        $delivery_date = $data['option']['delivery_date'];
        $delivery_time = $data['option']['delivery_time'];
        $time = str_replace('-', ':', $delivery_time);
        $delivery_date_time = $delivery_date . ' ' . $time . ':00 +0200';
        if ($data['option']['delivery_method'] == 'delivery') {
            $address = $data['Cart']['person']['address']['street']
                . ' ' . $data['Cart']['person']['address']['house_number'];
            $city = $data['Cart']['person']['city'];
        } else {
            $address = '';
            $city = '';
        }


        $newEcwidOrderData = [
            'subtotal'           => (float) $data['option']['products_price'],
            'total'              => (float) $data['option']['total_price'],
            'email'              => $data['Cart']['person']['email'],
            'paymentMethod'      => $data['option']['payment_method'],
            'paymentModule'      => 'server',
            'paymentStatus'      => 'AWAITING_PAYMENT',
            'fulfillmentStatus'  => 'AWAITING_PROCESSING',
            'createDate'         => $order_date . " +0200",
            'ipAddress'          => $ip,
            'refererUrl'         => $data['option']['refererUrl'],
            'pickupTime'         => $delivery_date_time,
            'orderComments'      => $data['option']['comment'],
            'shippingPerson'     => [
                'name'        => $data['Cart']['person']['name'],
                'phone'       => $data['Cart']['person']['phone'],
                'street'      => $address,
                'city'        => $city,
            ],
            'billingPerson'      => [
                'name'        => $data['Cart']['person']['name'],
                'phone'       => $data['Cart']['person']['phone'],
                'street'      => $address,
                'city'        => $city,
                'countryCode' => 'IL',
                'postalCode'  => '1029200'
            ],
            'extraFields'        => [
                'delivery_date' => $delivery_date,
                'delivery_time' => $delivery_time,
                'gustom_lang'   => $data['option']['lang'],
                'server_order'  => 'true'
            ]

        ];


        if (!empty($data['option']['promo_code'])) {
            $code = $data['option']['promo_code'];
            $discounts = $ecwidService->getDiscountCoupons($code);
            foreach ($discounts['items'] as $item) {
                if ($item['code'] == $code && $item['status'] == 'ACTIVE') {
                    $summ = (int) $data['option']['products_price'];
                    $newEcwidOrderData['discountCoupon'] = $item;
                    if ($item['discountType'] == 'PERCENT') {
                        $discount = $summ * $item['discount'] / 100;
                        $discount = round($discount, 2);
                        $newEcwidOrderData['couponDiscount'] = $discount;
                    }
                    if ($item['discountType'] == 'ABS') {
                        $discount = $item['discount'];
                        $newEcwidOrderData['couponDiscount'] = $discount;
                    }
                }
            }

        }

        if (!empty($data['option']['tips_price'] > 0)) {
            $tips = ((int) $data['option']['products_price'] + (int) $data['option']['delivery_price'] - $discount) * (int) $data['option']['tips_price'] / 100;
            $tips = round($tips, 2);
            $newEcwidOrderData['customSurcharges'][] = [
                'id' => 'tips',
                "value" => (int) $data['option']['tips_price'],
                "type" => "PERCENT",
                "total" => $tips,
                "totalWithoutTax" => $tips,
                "description" => "Tip ({$data['option']['tips_price']}%)",
                "descriptionTranslated" => "Чаевые ({$data['option']['tips_price']}%)",
                "taxable" => false,
                "taxes" => [],
            ];

            $newEcwidOrderData['extraFields']['tips'] = "{$data['option']['tips_price']}%";
        }


        if ($data['option']['delivery_method'] == 'pickup') {
            $newEcwidOrderData['shippingOption'] = [
                'shippingMethodName' => 'Take a way / Самовывоз',
                'shippingRate' => (int) $data['option']['delivery_price'],
                "fulfillmentType" => "PICKUP"
            ];
        }
        if ($data['option']['delivery_method'] == 'delivery') {
            $newEcwidOrderData['shippingOption'] = [
                'shippingMethodName' => $data['option']['delivery_variant'],
                'shippingRate' => (int) $data['option']['delivery_price'],
                "fulfillmentType" => "DELIVERY"
            ];
            $newEcwidOrderData['extraFields']['room_number'] = $data['Cart']['person']['address']['room_number'];
        }

        foreach ($data['Cart']['items'] as $k => $item) {
            $id = $item['id'];
            $count = $item['count'];
            $variable_id = $item['variable_id'];
            $product = $ecwidService->getProduct($id);
            $sel_options = false;

            if ($variable_id != 0) {
                foreach ($product['combinations'] as $combination) {
                    if ($combination['id'] == $variable_id) {
                        $sku = $combination['sku'];
                        foreach ($combination['options'] as $option) {
                            if ($option['name'] == 'Size') {
                                $option['type'] = 'TEXT';
                                $sel_options[] = $option;
                            }
                        }
                        $price = $combination['defaultDisplayedPrice'];
                    }
                }
            } else {
                $sku = $product['sku'];
                $price = $product['price'];
            }

            $item_product = [
                'price'           => (int) $price,
                'sku'             => $sku,
                'quantity'        => (int) $count,
                'name'            => $product['name']
            ];

            if ($sel_options) {
                $item_product['selectedOptions'] = $sel_options;
            }

            $newEcwidOrderData['items'][$k] = $item_product;
        }

        return $newEcwidOrderData;
    }

    public static function getAmoNotes($data)
    {
        $ordersNotes = 'Детали заказа: #' . $data['Cart']['order_id'];
//
//        echo "<pre>";
//        print_r($data);

        $items = $data['Cart']['items'];
        foreach ($items as $key => $item) {
            $id = $item['id'];
            $varieble_id = $item['variable_id'];
            $product_name = $item['name'];

            if ($varieble_id > 0 ) {
                $product_name .= ' ' . $item['option']['name'] . ' ' . $item['option']['value'];
            }
            $ordersNotes .= "\n" . $item['count'] . "x - {$item['price']} шек " . $product_name . ' ';
        }


        $ordersNotes .= "\n ---------------------- \n Итого: {$data['Cart']['total_price']} шек (без скидки)";
        $ordersNotes .= "\n ---------------------- \n";
        $ordersNotes .= "способ оплаты - {$data['paymentMethod']} \n ---------------------- \n";

        $timeDelivery = $data['option']['delivery_date'] . ' время ' . $data['option']['delivery_time'];

        $shipping = '';
        if ($data['option']['delivery_method'] == 'pickup') {
            $shipping = 'Доставка: ' . "\n"
                . 'Самовывоз ' . $timeDelivery . "\n ---------------------- \n";

        } else {
            $address = $data['Cart']['person']['address']['city']
                . ' ' . $data['Cart']['person']['address']['street']
                . ' ' . $data['Cart']['person']['address']['house_number'];

            if (!empty($data['Cart']['person']['address']['room_number'])) {
                $address .= ' ' . $data['Cart']['person']['address']['room_number'];
            }
            if (!empty($data['option']['delivery_variant'])) {
                $delivery_variant = $data['option']['delivery_variant'];
            } else {
                $delivery_variant = 'неизвестно';
            }


            $shipping = 'Доставка: ' . "\n Служба доставки - "
                . $delivery_variant
                . "\n Адрес - " . $address
                . "\n дата - " . $timeDelivery
                . "\n стоимость - " . $data['option']['delivery_price'] . 'шек'
                . "\n ---------------------- \n";

        }

        if (!empty($data['Cart']['discount']) && $data['Cart']['discount'] != "false") {
            $code = $data['option']['promo_code'];
            $discount = "скидка {$data['Cart']['discount']['display']} ({$data['Cart']['discount']['total_discount']}шек) code - $code  \n";
        } else {
            $discount = '';
        }
        if (!empty($data['option']['tips_price'])) {
            $tips = "Чаевые {$data['option']['tips_price']}% \n";
        } else {
            $tips = '';
        }

        if(isset($data['option']['comment'])) {
            $orderComments = $data['option']['comment'];
        } else {
            $orderComments = '';
        }


        if (!empty($orderComments)) {
            $orderComments = 'Комментарий покупателя: ' . "\n"
                . $orderComments . "\n ---------------------- \n";
        } else {
            $orderComments = 'Комментарий покупателя: ' . "\n"
                . "Нет комментария " . "\n ---------------------- \n";
        }

        $notes = $ordersNotes . $orderComments . $discount . $tips . $shipping;

        $notes = $notes . "\n                    Итого: {$data['option']['total_price']} шек";

        return $notes;
    }


    public static function getAmoDataLead($data)
    {
        // формируем массив данных для амо
        $pipelineId = '4651807'; // воронка
        $statusId = '43924885'; // статус

        if (isset($data['option']['delivery_variant'])) {
            if (preg_match('/Boxit/', $data['option']['delivery_variant'])) {
                $statusId = '4651807'; // статус
            }
        }


        $items = $data['Cart']['items'];
//        dd($items);
        foreach ($items as $key => $item) {
            $id = $item['id'];
            $varieble_id = $item['variable_id'];
            $product_name = $item['name'];

            if ($varieble_id > 0 ) {
                $product_name .= ' ' . $item['option']['name'] . ' ' . $item['option']['value'];
            }
            $tags[] = $product_name;
        }
//        dd($data);

        if ($data['paymentMethod'] == 'Сash payment') {
            $payment = 'Оплата наличными по факту';
        } elseif ($data['paymentStatus'] == 'PAID') {
            $payment = 'Оплачен';
        }

        // deliwery adress
        $address = '';
        if ($data['option']['delivery_method'] == 'pickup') {
            $address = 'Самовывоз';
        } elseif ($data['option']['delivery_method'] == 'delivery') {
            $delivery = $data['Cart']['person']['address'];
            $address = $delivery['city']
                . ' ' . $delivery['street']
                . ' ' . $delivery['house_number'];
        }
        if (empty($data['option']['comment'])) {
            $data['option']['comment'] = '';
        }

        $timeDelivery = $data['option']['delivery_time'];

        if ($data['option']['lang'] == 'ru') {
            $lang = 'Русский';
        } elseif ($data['option']['lang'] == 'en') {
            $lang = 'Английский';
        } else {
            $lang = 'Иврит';
        }
        $tags[] = $lang;

        $delivery_time = $data['option']['delivery_time'];
        $time = str_replace(':00', '', $delivery_time);
        $time = str_replace('-', ':', $time);
        $delivery_date_time = $data['option']['delivery_date'] . ' ' . $time . ':00 +0000';
        $date = Carbon::parse($delivery_date_time);
        $dateOrder = strtotime($date->format('Y-m-d H:i:s'));

        $dataOrderAmo = [
            'order name'  => 'ServerTB #' . $data['order_id'],
            'order_id'    => $data['order_id'],
            'api_mode'    => 'ServerTB',
            'order price' => $data['option']['total_price'],
            'pipelineId'  => $pipelineId,
            'statusId'    => $statusId,
            'notes'       => $data['option']['comment'],
            'lang'        => $lang,
            'refer_URL'   => $data['option']['refererUrl'],
            'name'        => $data['Cart']['person']['name'],
            'email'       => $data['Cart']['person']['email'],
            'phone'       => $data['Cart']['person']['phone'],
            'address'     => $address,
            'payment'     => $payment,
            'date'        => $dateOrder,
            'time'        => $timeDelivery,
            'tags'        => $tags
        ];

        if (!empty($delivery['room_number'])) {
            $dataOrderAmo['room_number'] = $delivery['room_number'];
        }

        return $dataOrderAmo;
    }



    // подготовка запроса для получения урл
    public function getIcreditDataOrder($data)
    {
        /////////////////////////////////////////////////////
        // construct order to icredit
        $total = 0;
        $discount = 0;

        if ($data['Cart']['discount'] != 'false') {
           $discount = $data['Cart']['discount']['total_discount'];
        }

        $items = $data['Cart']['items'];
        foreach ($items as $key => $item) {
            $varieble_id = $item['variable_id'];
            $product_name = $item['name'];

            if ($varieble_id > 0 ) {
                $product_name .= ' ' . $item['option']['name'] . $item['option']['value'];
            }


            $orderItems[$key]['CatalogNumber'] = $item['sku'];
            $orderItems[$key]['Quantity'] = $item['count'];
            $orderItems[$key]['UnitPrice'] = $item['price'];
            $orderItems[$key]['Description'] = $product_name;

            $total = $total + $item['price'] * $item['count'];
        }

        if ($discount > 0) {
            $total -= $discount;
        }


        if ($data['option']['delivery_price'] > 0) {

            $orderItems[++$key]['CatalogNumber'] = 'delivery';
            $orderItems[$key]['Quantity'] = 1;
            $orderItems[$key]['UnitPrice'] = (int) $data['option']['delivery_price'];
            $orderItems[$key]['Description'] = 'delivery';

            $total = $total + (int) $data['option']['delivery_price'];
        }

        if ($data['option']['tips_price'] > 0) {

            $tips = (int) $data['option']['tips_price'] / 100;
            $tips_value = round($total * $tips, 2);

            $orderItems[++$key]['CatalogNumber'] = 'tips_' . $data['option']['tips_price'];
            $orderItems[$key]['Quantity'] = 1;
            $orderItems[$key]['UnitPrice'] = $tips_value;
            $orderItems[$key]['Description'] = 'tips ' . $data['option']['tips_price'] . '%';

            $total = $total + $tips_value;
        }

        if ($data['option']['lang'] != 'he') {
            $data['option']['lang'] = 'en';
        }

        $order['lang']     = $data['option']['lang'];
        $order['items']    = $orderItems;
        $order['orderId']  = $data['Cart']['order_id'];
        $order['custom2']  = 'ServerTB';
        $order['email']    = $data['Cart']['person']['email'];
        $order["phone"]    = $data['Cart']['person']["phone"];
        $order["name"]     = $data['Cart']['person']["name"];
        $order["discount"] = $discount;

//        print_r("total - $total");
//        dd($order);
        return $order;
    }

    public static function getOrderDataToGinvoice($data)
    {
//        print_r($data);
        $lang = 'he';
        $dateObj = new Carbon();
        $date = $dateObj->format('Y-m-d');

        $orderData['email'] = $data['Cart']['person']['email'];

        $name = trim($data['Cart']['person']['name']);

        $name = AppServise::TransLit($name);
        $orderData['name'] = $name;
        $orderData['lang'] = $data['option']['lang'];
        $orderData['phone'] = $data['Cart']['person']['phone'];
        $orderData['city'] = '';
        $orderData['address'] = '';

        if ($data['option']['delivery_method'] == 'delivery') {
            $orderData['city'] = AppServise::TransLit($data['Cart']['person']['address']['city']);
            $orderData['address'] = AppServise::TransLit($data['Cart']['person']['address']['street']);
        }


        $orderData['remarks'] = $data['Cart']['order_id'] . " פרטים - מספר הזמנה: " ;
        $orderData['orderNames'] =  $data['Cart']['order_id'] . " מספר הזמנה: ";

        foreach ($data['Cart']['items'] as $item) {
            $product_id = $item['id'];
            $variable_id = $item['variable_id'];

            $product_name = $item['name'];

            if ($variable_id > 0 ) {
                $product_name .= ' ' . $item['option']['name'] . '-' . $item['option']['value'];
            }


            $items[] =  [
                "catalogNum"   => $item['sku'],
                "description"  => $product_name,
                "quantity"     => $item['count'],
                "price"        => $item['price'],
                "currency"     => "ILS",
                "currencyRate" => 1,
                "vatType"      => 0
            ];

            $total = $item['count'] * $item['price'];
            $orderData['remarks'] .= "\n ILS $total = {$item['count']} x ILS {$item['price']} : $product_name";
            $orderData['orderNames'] .= "\n $product_name ({$item['count']}) ";
        }

        if ($data['Cart']['discount'] != 'false') {
            $discount = $data['Cart']['discount'];
            $orderData['remarks'] .= "\n ILS -{$discount['total_discount']} :discount {$discount['display']}";
        }

        $orderData['items'] = $items;

        if ($data['option']['delivery_method'] == 'delivery') {
            // стоимоссть доставки
            $orderData['delivery'] = "\n delivery:\n ILS {$data['option']['delivery_price']} ............... " ;
        }


            if ($data['option']['tips_price'] > 0) {

                $orderData['tips'] = "\n ___________________\n טיפים: "
                    . $data['option']['tips_price'] . "%\n"
                    . 'ILS '  . $data['option']['tips_value']
                    ." ...........";
            }



        if (isset($data['externalTransactionId'])) {
            $orderData['payId'] = $data['externalTransactionId'];
        } else {
            $orderData['payId'] = '';
        }

        $orderData['total'] = $data['option']['total_price'];
        $orderData['payDate'] = $date;


        if ($data['option']['payment_method'] == 'Credit card' || $data['option']['payment_method'] == 'PayPal') {
            $orderData['type'] = 3;

            if ($data['option']['payment_method'] == 'Credit card') {
                $orderData['bankName'] = 'iCredit';
            } elseif ($data['option']['payment_method'] == 'PayPal') {
                $orderData['bankName'] = 'PayPal';
            }
        } else {
            $orderData['type'] = 1;
            $orderData['bankName'] = 'none';
        }

        return $orderData;
    }

    public function sendMailNewOrder($order_id)
    {
        $ecwidService = new EcwidService();
        $order = Orders::where('order_id', $order_id)->first();
        $client_id = $order->clientId;
        $client = Clients::where('id', $client_id)->first();
        $client->email = 'virikidorhom@gmail.com';

        $orderData = json_decode($order->orderData, true);
        $lang = $orderData['option']['lang'];

        $discount = 0;
        if (!empty($data['option']['promo_code'])) {
            $code = $data['option']['promo_code'];
            $discounts = $ecwidService->getDiscountCoupons($code);
            foreach ($discounts['items'] as $item) {
                if ($item['code'] == $code) {
                    $discount = $item;
                }
            }
        }

        foreach ($orderData['Cart']['items'] as $k => $item) {
            $product_id = $item['id'];
            $product = $ecwidService->getProduct($product_id);

            if (!empty($product['nameTranslated'][$lang])) {
                $item['name'] = $product['nameTranslated'][$lang];
            } else {
                $item['name'] = $product['name'];
            }

            $orderData['Cart']['items'][$k] = $item;
        }

        $order->orderData = $orderData;


       Mail::to($client)->send(new NewOrder($order));
    }

}
