<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AppServise
{
    private static $orderInvoiceStatus = array('no', 'yes');
    private static $orderPaymentMethod = array('undefined', 'Credit card', 'Сash payment', 'PayPal');
    private static $orderPaymentStatus = array('undefined', 'LOST_CART', 'INCOMPLETE', 'AWAITING_PAYMENT', 'PAID');

    public static function TransLit($str) {
        $rus = array(' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array(' ', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Gh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sh', '', 'Yi', '', 'Ye', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'gh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sh', '', 'yi', '', 'ye', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }

    public static function TranslitIvrit($str) {
        $lat = array(' ', 'g', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        $iv = array(' ', 'ש', 'ב', 'ה', 'ר', 'ט', 'ו', 'י', 'י', 'ו', 'ע', 'א', 'ס', 'ד', 'ו', 'ז', 'ח', 'י', 'ק', 'ל', 'ז', 'כ', 'ג', 'ב', 'ב', 'נ', 'מ');
        $str = strtolower($str);
        return str_replace($lat, $iv, $str);
    }


    public static function getOrderPaymentMethod() {
        return self::$orderPaymentMethod;
    }
    public static function getOrderPaymentStatus() {
        return self::$orderPaymentStatus;
    }
    public static function getOrderInvoiceStatus() {
        return self::$orderInvoiceStatus;
    }

    public function urlFileDownloadToStorage($patch, $url)
    {
        $fileName = basename($url);
        Storage::disk('local')
            ->put($patch  . $fileName, file_get_contents($url));
    }

    public static function generateOrderId($n)
    {
        $nr = rand(100, 999);
        $n = $nr.$n;
        $r = '';

        for ($i = 1; $n >=0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i -1))) . $r;
            $n -= pow(26, $i);
        }
        $r = "T-$r";
        return $r;
    }

    public static function getCountryFromIP($ip)
    {

        $country = exec("whois $ip  | grep -i country"); // Run a local whois and get the result back
        //$country = strtolower($country); // Make all text lower case so we can use str_replace happily
        // Clean up the results as some whois results come back with odd results, this should cater for most issues
        $country = str_replace("country:", "", "$country");
        $country = str_replace("Country:", "", "$country");
        $country = str_replace("Country :", "", "$country");
        $country = str_replace("country :", "", "$country");
        $country = str_replace("network:country-code:", "", "$country");
        $country = str_replace("network:Country-Code:", "", "$country");
        $country = str_replace("Network:Country-Code:", "", "$country");
        $country = str_replace("network:organization-", "", "$country");
        $country = str_replace("network:organization-usa", "us", "$country");
        $country = str_replace("network:country-code;i:us", "us", "$country");
        $country = str_replace("eu#countryisreallysomewhereinafricanregion", "af", "$country");
        $country = str_replace("", "", "$country");
        $country = str_replace("countryunderunadministration", "", "$country");
        $country = str_replace(" ", "", "$country");

        return $country;
    }


    public static function getQuest($url)
    {
        $curl = curl_init();
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
        curl_close($curl);


        return json_decode($response, true);
    }
}
