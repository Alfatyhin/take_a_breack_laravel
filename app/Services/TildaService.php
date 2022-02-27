<?php


namespace App\Services;


class TildaService
{
    private $shopid = 130765;
    private $order;
    private $signature;
    private $test_mode = 0;

    public function __construct($data)
    {
        $this->order = $data;
        $this->signature = $_ENV['TILDA_ICREDIT_PAYMENT_SIGNATURE'];
    }

    public function validate(): bool
    {
        $data = $this->order;
        $signature = $data['signature'];
        unset($data['signature']);
        ksort($data, SORT_STRING );
        $postStr = implode('|', $data);
        $postStr = $this->signature
            . '|' . $postStr;

        $testSignature = md5($postStr);

        if (!empty($data['name'])
            && !empty($data['mail'])
            && !empty($data['phone'])
            && !empty($data['order_id'])
            && $testSignature == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function getIcreditDataOrder()
    {
        $data = $this->order;
        $products = json_decode($data['products'], true);

        if ($this->test_mode == 1 || $data["mail"] == 'test@test.af') {
            $data['language'] = 'test';
        }

        foreach ($products as $key => $item) {
//            $orderDataProducts[$key]['CatalogNumber'] = $item['sku'];
            $orderDataProducts[$key]['Quantity'] = $item['quantity'];
            $orderDataProducts[$key]['UnitPrice'] = $item['price'];
            $orderDataProducts[$key]['Description'] = $item['name'];

            if ($this->test_mode == 2) {
                $orderDataProducts[$key]['UnitPrice'] = 1;
            }

        }
        $order_id = preg_replace('/[0-9]+:/', '', $data['order_id']);

        $orderData['lang']    = $data['language'];
        $orderData['items']   = $orderDataProducts;
        $orderData['orderId'] = $order_id;
        $orderData['custom2'] = 'Tilda';
        $orderData['email']   = $data['mail'];
        $orderData["phone"]   = $data["phone"];
        $orderData["name"]    = $data["name"];

        return $orderData;
    }

    public static function getDataToGreenInvoice(array $data)
    {
        $date = $data['date'];

        if (!empty($data['Email'])) {
            $orderData['email'] = $data['Email'];
        } else {
            $orderData['email'] = $data['mail'];
        }

        if (!empty($data['payment']['delivery_fio'])) {
            $name = trim($data['payment']['delivery_fio']);
        } else {
            $name = trim($data['name']);
        }

        $name = AppServise::TransLit($name);
        $orderData['name'] = $name;

        if (!empty($data['lang'])) {
            $orderData['lang'] = $data['lang'];
        } else {
            $orderData['lang'] = 'he';
        }

        if (!empty($data['Phone'])) {
            $orderData['phone'] = $data['Phone'];
        } else {
            $orderData['phone'] = $data['phone'];
        }

        if (!empty($data['payment']['delivery_address'])) {
            $adres = $data['payment']['delivery_address'];
            $array = explode(',', $adres);

            if ($data['payment']['delivery'] != 'Pickup') {
                $orderData['city'] = trim($array[1]);
                unset($array[0]);
                $orderData['address'] = implode(',', $array);
            } else {
                $orderData['city'] = '';
                $orderData['address'] = '';
            }
        } else {
            $orderData['city'] = '';
            $orderData['address'] = '';
        }

        if (!empty($data['payment']['orderid'])) {
            $orderData['remarks'] = " Details - Order number: " . $data['payment']['orderid'];
            $orderData['orderNames'] = " Order number: " . $data['payment']['orderid'];

        } else {
            $orderData['remarks'] = " Details - Order number: " . $data['order_id'];
            $orderData['orderNames'] = " Order number: " . $data['order_id'];
        }

        if (!empty($data['payment']['products'])) {
            foreach ($data['payment']['products'] as $item) {
                $items[] =  [
                    "catalogNum"   => $item['sku'],
                    "description"  => $item['name'],
                    "quantity"     => $item['quantity'],
                    "price"        => $item['price'],
                    "currency"     => "ILS",
                    "currencyRate" => 1,
                    "vatType"      => 0
                ];


                $size = '';
                if (!empty($item['options'])) {
                    $size = '- ' . $item['options'][0]['variant'];
                }

                $total = $item['quantity'] * $item['price'];

                $orderData['remarks'] .= "\n {$item['name']} $size : {$item['price']} ILS x {$item['quantity']} = $total ILS";

                $orderData['orderNames'] .= "\n {$item['name']} $size ({$item['quantity']}) ";
            }
            $orderData['items'] = $items;

            if ($data['payment']['delivery'] != 'Pickup') {

                // стоимоссть доставки
                $orderData['delivery'] = "\n delivery: " . "\n  ............... {$data['payment']['delivery_price']} ILS";

            }

        } else {

            $products = json_decode($data['products'], true);

            foreach ($products as $item) {
                $items[] =  [
                    "description"  => $item['name'],
                    "quantity"     => $item['quantity'],
                    "price"        => $item['price'],
                    "currency"     => "ILS",
                    "currencyRate" => 1,
                    "vatType"      => 0
                ];


                $size = '';
                if (!empty($item['options'])) {
                    $size = '- ' . $item['options'][0]['variant'];
                }

                $total = $item['quantity'] * $item['price'];

                $orderData['remarks'] .= "\n {$item['name']} $size : {$item['price']} ILS x {$item['quantity']} = $total ILS";

                $orderData['orderNames'] .= "\n {$item['name']} $size ({$item['quantity']}) ";
            }
            $orderData['items'] = $items;
            $orderData['delivery'] = '';

        }



        if (isset($data['externalTransactionId'])) {
            $orderData['payId'] = $data['externalTransactionId'];
        } else {
            $orderData['payId'] = '';
        }

        if (!empty($data['payment']['amount'])) {
            $orderData['total'] = $data['payment']['amount'];
        } else {
            $orderData['total'] = $data['order_summ'];
        }


        $orderData['payDate'] = $date;

        if (!empty($data['paymentModule'])) {
            if ($data['paymentModule'] == 'iCredit'
                || $data['paymentModule'] == 'PayPalStandard'
                || $data['paymentModule'] == 'PayPal')
            {
                $orderData['type'] = 3;
            }
            if ($data['paymentModule'] == 'iCredit' ) {

                $orderData['bankName'] = 'iCredit';

            } elseif ($data['paymentModule'] == 'PayPalStandard' || $data['paymentModule'] == 'PayPal') {

                $orderData['bankName'] = 'PayPal';

            }
        } else {
            $orderData['type'] = 1;
            $orderData['bankName'] = 'none';
        }

        return $orderData;
    }

    public static function getSignature($data)
    {
        $signature = $_ENV['TILDA_ICREDIT_PAYMENT_SIGNATURE'];
        ksort($data, SORT_STRING );
        $postStr = implode('|', $data);
        $postStr = $signature
            . '|' . $postStr;

        $result = md5($postStr);

        return $result;
    }

    public static function postAction($url, $data)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        return $result;
    }
}
