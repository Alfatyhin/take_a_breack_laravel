<?php


namespace App\Services;


class EcwidService
{
    private static $secret_key = ECWID_APP_TOKEN;
    private static $shop_id = ECWID_SHOP_ID;
    private static $secret_token = ECWID_SECRET_TOKEN;

    private $id;
    private $order_id;
    private $description;
    private $create_at;
    private $update_at;

    public function __construct($data)
    {
        $this->id          = $data['id'];
        $this->order_id    = $data['order_id'];
        $this->description = $data['description'];
        $this->create_at   = $data['create_at'];
        $this->update_at   = $data['update_at'];
    }

//    public function save()
//    {
//
//        if(ECWID_TEST_MODE != 1) {
//            $db = Db::getInstance();
//
//            $insert = "INSERT INTO ecwid (
//            `order_id`, `description`, `create_at`, `update_at`)
//            VALUES (
//            :order_id , :description, :create_at, :update_at)";
//
//            $db->exec($insert, __METHOD__, [
//                ':order_id'    => $this->order_id,
//                ':description' => $this->description,
//                ':create_at'   => $this->create_at,
//                ':update_at'   => $this->update_at
//            ]);
//
//            $id = $db->lastInsertId();
//            $this->id = $id;
//
//            return $id;
//        }
//
//    }

    public static function decoder($data)
    {

        $secret_key = self::$secret_key;
        // Get the encryption key (16 first bytes of the app's client_secret key)
        $encryption_key = substr($secret_key, 0, 16);

        // Decrypt payload
        $json_data = Ecwid::aes_128_decrypt($encryption_key, $data);

        // Decode json
        $json_decoded = json_decode($json_data, true);

        return $json_decoded;
    }

    private static function aes_128_decrypt($key, $data) {
        // Ecwid sends data in url-safe base64. Convert the raw data to the original base64 first
        $base64_original = str_replace(array('-', '_'), array('+', '/'), $data);

        // Get binary data
        $decoded = base64_decode($base64_original);

        // Initialization vector is the first 16 bytes of the received data
        $iv = substr($decoded, 0, 16);

        // The payload itself is is the rest of the received data
        $payload = substr($decoded, 16);

        // Decrypt raw binary payload
        $json = openssl_decrypt($payload, "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv);
        //$json = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv); // You can use this instead of openssl_decrupt, if mcrypt is enabled in your system

        return $json;
    }

    // подготовка запроса для получения урл
//    public static function getUrl($postData)
//    {
//        $data = Ecwid::decoder($postData);
//
//        // var_dump($data);
//
//        // order to ewcid
//        $items = $data['cart']['order']['items'];
//        // construct order to icredit
//        $total = 0;
//        foreach ($items as $key => $item) {
//            $ecwidItems[$key]['CatalogNumber'] = $item['sku'];
//            $ecwidItems[$key]['Quantity'] = $item['quantity'];
//            $ecwidItems[$key]['UnitPrice'] = $item['price'];
//            $ecwidItems[$key]['Description'] = $item['name'];
//
//            $total = $total + ($item['quantity'] * $item['price']);
//        }
//
//        if (!empty($data['cart']['order']['shippingOption']['shippingRate'])) {
//
//            $ecwidItems[++$key]['CatalogNumber'] = 'delivery';
//            $ecwidItems[$key]['Quantity'] = 1;
//            $ecwidItems[$key]['UnitPrice'] = $data['cart']['order']['shippingOption']['shippingRate'];
//            $ecwidItems[$key]['Description'] = 'delivery';
//
//            // добавляем в расчет чаевые
//            $total = $total + $data['cart']['order']['shippingOption']['shippingRate'];
//        }
//
//        if ($data['cart']['order']['orderExtraFields']) {
//
//            foreach ($data['cart']['order']['orderExtraFields'] as $item) {
//
//                if ($item['id'] == 'tips') {
//
//                    $tips = (int) $item['value'] / 100;
//
//                    if ($tips > 0) {
//                        $ecwidItems[++$key]['CatalogNumber'] = 'tips ' . (int) $item['value'];
//                        $ecwidItems[$key]['Quantity'] = 1;
//                        $ecwidItems[$key]['UnitPrice'] = $total * $tips;
//                        $ecwidItems[$key]['Description'] = 'tips ' . $item['value'];
//
//                    }
//                }
//            }
//
//        }
//
//        $ecwid = [
//            'referenceTransactionId' => $data['cart']['order']['referenceTransactionId'],
//            'storeId'                => $data['storeId'],
//            'token'                  => $data['token'],
//            'returnUrl'              => $data['returnUrl'],
//            'lang'                   => $data['lang']
//        ];
//
//        $ecwidJson = json_encode($ecwid);
//
//
//        $order['lang']    = $data['lang'];
//        $order['items']   = $ecwidItems;
//        $order['orderId'] = $data['cart']['order']['id'];
//        $order['custom2'] = 'Ecwid';
//        $order['custom3'] = $ecwidJson;
//        $order['email']   = $data['cart']['order']['email'];
//        $order["phone"]   = $data["cart"]["order"]["shippingPerson"]["phone"];
//        $order["name"]    = $data["cart"]["order"]["shippingPerson"]["name"];
//
//        if(ECWID_TEST_MODE == 1) {
//            var_dump('<hr> Ecwid order to iCredit', $order);
//            return Icredit::getUrl($order);
//        } else {
//            return Icredit::getUrl($order);
//        }
//
//    }

//    public static function payStatusUpdate($transaction, $token, $storeId, $status)
//    {
//        if ($status == 3) {
//            $paymentStatus = 'AWAITING_PAYMENT';
//        } elseif ($status == 4) {
//            $paymentStatus = 'PAID';
//        } else {
//            $paymentStatus = 'INCOMPLETE';
//        }
//
//        $storeId = self::$shop_id;
//        $token = self::$secret_token;
//
//        $url = "https://app.ecwid.com/api/v3/$storeId/orders/$transaction" . "?token=$token";
//        $data = array(
//            'paymentStatus'=> $paymentStatus,
//            'externalTransactionId' => $transaction
//        );
//        $data_json = json_encode($data);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $response  = curl_exec($ch);
//        curl_close($ch);
//
//        return $response;
//
//    }


    // обновление статусов на еквиде
//    public static function orderStatusUpdate($orderId, $status, $statusPay)
//    {
//        if ($statusPay == 2) {
//            $paymentStatus = 'AWAITING_PAYMENT';
//        } elseif ($statusPay == 4) {
//            $paymentStatus = 'PAID';
//        } else {
//            $paymentStatus = 'INCOMPLETE';
//        }
//
//        $storeId = self::$shop_id;
//        $token = self::$secret_token;
//
//        $url = "https://app.ecwid.com/api/v3/$storeId/orders/$orderId" . "?token=$token";
//
//        $data = array(
//            'paymentStatus'=> $paymentStatus,
//            'fulfillmentStatus'=> $status
//        );
//
//        $data_json = json_encode($data);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $response  = curl_exec($ch);
//        curl_close($ch);
//
//        return $response;
//
//    }


//    public static function orderDelede($orderId)
//    {
//        $storeId = self::$shop_id;
//        $token = self::$secret_token;
//
//        $url = "https://app.ecwid.com/api/v3/$storeId/orders/$orderId" . "?token=$token";
//
//        $data = array(
//            'deleteCount'=> 1
//        );
//
//        $data_json = json_encode($data);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $response  = curl_exec($ch);
//        curl_close($ch);
//
//        return $response;
//
//    }

    public static function getOrderBuId($orderId) {

        $curl = curl_init();

        $shopId = self::$shop_id;
        $token = self::$secret_token;
        $url = "https://app.ecwid.com/api/v3/$shopId/orders/$orderId?token=$token";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public static function getProduct ($productId) {
        $curl = curl_init();

        $shopId = self::$shop_id;
        $token = self::$secret_token;
        $url = "https://app.ecwid.com/api/v3/$shopId/products/$productId?token=$token";


        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: application/json"
            ],
        ]);



        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $errorMessage['Ecwid-getProduct'] = $err;
            AppServise::setErrorLog($errorMessage);
        } else {
            return $response;
        }


    }

    public static function productInventory ($productId, $count) {

        $shopId = self::$shop_id;
        $token = self::$secret_token;
        $url ="https://app.ecwid.com/api/v3/$shopId/products/$productId/inventory?token=$token";

        $data = array(
            'quantityDelta'=> $count
        );

        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            $errorMessage['Ecwid-productInventory'] = $err;
            AppServise::setErrorLog($errorMessage);
        } else {
            return $response;
        }

    }

}
