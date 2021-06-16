<?php


namespace App\Services;


use App\Models\AppErrors;
use App\Models\IcreditLogs;

class IcreditServise
{
    private $payToken;
    private $testURL = "https://icredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";
    private $prodURL = "https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";

    public function __construct()
    {

    }

    private function setPayToken($lang) {

        switch ($lang) {
            case 'he':
                $this->payToken = $_ENV['ICREDIT_PAY_TOKEN_HE'];
                break;
            case 'test':
                $this->payToken = $_ENV['ICREDIT_PAY_TOKEN_TEST'];
                break;
            default:
                $this->payToken = $_ENV['ICREDIT_PAY_TOKEN_EN'];
        }
        return $this->payToken;
    }


    public function getUrl($data)
    {
        $token = $this->setPayToken($data['lang']);

        // default prod url
        $url = $this->testURL;

        if ($data['lang'] == 'test') {
            // change to test url
            $url = $this->prodURL;
        }

        $newData = array(
            "GroupPrivateToken" => $token,
            "Items" => $data['items'],
            "Custom1" => $data['orderId'],
            "Custom2" => $data['custom2'],
            "RedirectURL" => $_ENV['ICREDIT_THANKS'],
            "IPNURL" => $_ENV['ICREDIT_RESPONSE_PAY'],
            "EmailAddress" => $data['email'],
            "PhoneNumber" => $data["phone"],
            "CustomerFirstName" => $data["name"]
        );


        $post = json_encode($newData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);// prevent ssl error on localhost
        //curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);// prevent ssl error on localhost
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if ($result['URL']) {
            return $result;
        } else {
            return false;
        }

    }

    public static function checkPaymentStatus(array $data)
    {

        if ($data['GroupPrivateToken'] =='bb8a47ab-42e0-4b7f-ba08-72d55f2d9e41') { // testing token

            $url = 'https://testicredit.rivhit.co.il/API/PaymentPageRequest.svc/Verify';

        } else {

            $url = 'https://icredit.rivhit.co.il/API/PaymentPageRequest.svc/Verify';
        }

        $post = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);// prevent ssl error on localhost
        //curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);// prevent ssl error on localhost
        $result = curl_exec($ch);
        curl_close($ch);


        $res = json_decode($result, true);

        return $res;
    }

}
