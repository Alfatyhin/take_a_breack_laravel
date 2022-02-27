<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PayPalService
{
    private $patch;
    private $client_id;
    private $client_secret;
    private $token;
    private $testPatch = 'https://api-m.sandbox.paypal.com';
    private $prodPatch = 'https://api-m.paypal.com';

    public function __construct($mod_patch)
    {
        if ($mod_patch == 'live') {
            $this->patch = $this->prodPatch;
            $this->client_id = $_ENV['PAYPAL_CLIENT_ID'];
            $this->client_secret = $_ENV['PAYPAL_CLIENT_SECRET'];
        } else {
            $this->patch = $this->testPatch;
            $this->client_id = $_ENV['PAYPAL_CLIENT_ID_TEST'];
            $this->client_secret = $_ENV['PAYPAL_CLIENT_SECRET_TEST'];
        }


        $this->setToken();
    }

    private function getToken()
    {
        $url = '/v1/oauth2/token';
        $body = 'grant_type=client_credentials';

        $id = $this->client_id;
        $secret = $this->client_secret;
        $autch = base64_encode("$id:$secret");
        $headers = array(
            'Accept: application/json',
            'Accept-Language: en_US',
            "Authorization: Basic $autch",
            'Content-Type: application/x-www-form-urlencoded'
        );

        $res = $this->postQuest($headers, $body, $url);
        $token_json = $res['result'];

        $tokenData = json_decode($token_json, true);
        $date = new Carbon();
        $tokenData['date_save'] = $date->unix();
        $tokenData['date_save_str'] = $date->format('Y-m-d H:i:s');

        if (!empty($tokenData['access_token'])) {
            Storage::disk('local')->put('data/paypal-assets.json', json_encode($tokenData));
            return true;
        } else {
            return false;
        }

    }

    private function setToken()
    {
        $token_lson = Storage::disk('local')->get('data/paypal-assets.json');
        $tokenData = json_decode($token_lson, true);
        $date = Carbon::parse($tokenData['date_save_str']);
        $date_end = $date->addSeconds($tokenData['expires_in']);
        $date_test = new Carbon();

        if ($date_test > $date_end) {
            // get new token
            $this->getToken();
            $this->setToken();
        } else {
            $this->token = $tokenData['access_token'];
        }
    }


    public function getClientId()
    {
        return $this->client_id;
    }

    public function checkoutOrder($paymant_id)
    {
        $url = '/v2/checkout/orders/'.$paymant_id;
        $id = $this->client_id;
        $secret = $this->client_secret;
        $autch = base64_encode("$id:$secret");
        $headers = array(
            "Authorization: Basic $autch",
            'Content-Type: application/json'
        );

        $res = $this->getQuest($headers, $url);
        if (!empty($res['result']) && $res['http_code'] == 200) {
            return json_decode($res['result'], true);
        } else {
            return false;
        }

    }



    private function getQuest($headers, $url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->patch.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $curl_error = curl_error($curl);
        $curl_errno = curl_errno($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'error'      => $curl_error,
            'errno'      => $curl_errno,
            'http_code'  => $http_code,
            'result'     => $response,
        ];
    }

    private function postQuest($headers, $body, $url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->patch.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $curl_error = curl_error($curl);
        $curl_errno = curl_errno($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'error'      => $curl_error,
            'errno'      => $curl_errno,
            'http_code'  => $http_code,
            'result'     => $response,
        ];
    }

}
