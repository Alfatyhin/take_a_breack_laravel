<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;

class AppServise
{
    private static $orderInvoiceStatus = array('no', 'yes');
    private static $orderPaymentMethod = array('undefined', 'Credit card', 'Сash payment', 'PayPal');
    private static $orderPaymentStatus = array('undefined', 'undefined_1', 'INCOMPLETE', 'AWAITING_PAYMENT', 'PAID');

    public static function TransLit($str) {
        $rus = array(' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array(' ', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Gh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Yh', 'Ych', 'Qt', 'Yi', 'Qm', 'Ye', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'gh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'yh', 'ych', 'qt', 'yi', 'qm', 'ye', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
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
}
