<?php


namespace App\Services;


use App\Models\Coupons;
use App\Models\ProductOptions;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\String_;
use ZipArchive;
use function PHPUnit\Framework\fileExists;

class AppServise
{
    private static $orderInvoiceStatus = array('no', 'yes');
    private static $orderPaymentMethod = array('undefined', 'Credit card', 'Сash payment', 'PayPal', 'Bit');
    private static $orderPaymentStatus = array('undefined', 'LOST_CART', 'INCOMPLETE', 'AWAITING_PAYMENT', 'PAID');

    public static function getAppVerse()
    {
        $files = [
            'css/style.css',
            'css/style-2.css',
            'css/cart-new.css',
            'scripts/app.js',
            'scripts/app-2.js',
            'js/calendar.js',
        ];

        $last_mod_max = 0;
        foreach ($files as $file) {
            $last_mod = Storage::disk('public_root')->lastModified($file);
            if ($last_mod > $last_mod_max) {
                $last_mod_max = $last_mod;
                $file_max = $file;
            }
        }

        return $last_mod_max;
    }

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

    public static function generateOrderId($v = 'T')
    {
        $nr = rand(100, 999);
        $n = $nr.rand(100, 999);;
        $r = '';

        for ($i = 1; $n >=0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i -1))) . $r;
            $n -= pow(26, $i);
        }
        $r = "$v-$r";
        return $r;
    }

    public static function generateCouponCode()
    {
        $nr = rand(100, 999);
        $n = $nr.rand(100, 999);;
        $r = '';

        for ($i = 1; $n >=0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i -1))) . $r;
            $n -= pow(26, $i);
        }


        $nr = rand(100, 999);
        $n = $nr.rand(100, 999);;
        $r2 = '';

        for ($i = 1; $n >=0 && $i < 10; $i++) {
            $r2 = chr(0x41 + ($n % pow(26, $i) / pow(26, $i -1))) . $r;
            $n -= pow(26, $i);
        }
        $res = "$r-$r2";

        $test = Coupons::where('code', $res)->first();

        if ($test) {
            $res = self::generateCouponCode();
        }

        return $res;
    }

    public static function getCountryFromIP($ip)
    {
        $url = "http://www.geoplugin.net/json.gp?ip=$ip";
        $header = [];

        $info = self::getQuest($url, $header);
        if ($info && isset($info['geoplugin_countryCode']) && isset($info['geoplugin_countryCode'])) {
            $data['countryCode'] = $info['geoplugin_countryCode'];
            $data['countryName'] = $info['geoplugin_countryName'];
        } else {
            $data = false;
        }

        return $data;
    }

    public static function ftrim($str)
    {

    }


    public static function getQuest($url, $headers = ["Accept: application/json"])
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
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);


        return json_decode($response, true);
    }


    public static function confirmReCaptcha($captcha)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $secret = '6LeR4tAfAAAAAB6I90PFSnynOsXTa42l9fF3EylH';
        $header = [];
        $data = [
            'secret' => $secret,
            'response' => $captcha
        ];
        $res = self::getQuest($url."?secret=$secret"."&response=$captcha", $header);

        return $res;
    }

    public static function ProductsShopPrepeare($products, $categories)
    {
        $product_options = ProductOptions::all()->keyBy('id')->toArray();
        foreach ($product_options as $k => $item) {
            $product_options[$k]['options'] = json_decode($item['options'], true);
            $product_options[$k]['nameTranslated'] = json_decode($item['nameTranslate'], true);
        }

        foreach ($products as $product_key => &$product) {
            $product->in_stock = false;
            $product->sale = false;
            unset($options);
            unset($option_data);
            $cat_names = [];
            $cat_ids = json_decode($product->categories, true);
            $product->image = json_decode($product->image, true);
            $product->galery = json_decode($product->galery, true);
            $product->data = json_decode($product->data, true);
            $product->variables = json_decode($product->variables, true);
            $product->options = json_decode($product->options, true);
            $product->translate = json_decode($product->translate, true);

            if (!$product->variables) {
                if ($product->price < $product->compareToPrice) {
                    $product->sale = true;
                }
            } else {
                foreach ($product->variables as $variable) {
                    if (isset($variable['compareToPrice'])) {
                        if ($variable['compareToPrice'] && $variable['defaultDisplayedPrice'] < $variable['compareToPrice']) {
                            $product->sale = true;
                        }
                    }
                }
            }

            if (!empty($product->options)) {

                $options = $product->options;
                foreach ($options as &$option) {
                    $opt_id = $option['options_id'];
                    $option_data = $product_options[$opt_id];
                    $option['name'] = $option_data['name'];
                    $option['type'] = $option_data['type'];
                    $option['nameTranslated'] = $option_data['nameTranslated'];
                    $name = $option['name'];
                    $options_map[$name] = $option;

                    if (isset($option['choices']) && !empty($option['choices'])) {
                        foreach ($option['choices'] as &$item) {
                            $ch_key = $item['var_option_id'];
                            $item['text'] = $option_data['options'][$ch_key]['text'];
                            if (isset($option_data['options'][$ch_key]['textTranslated'])) {
                                $item['textTranslated'] = $option_data['options'][$ch_key]['textTranslated'];
                            }
                            if (isset($option_data['options'][$ch_key]['description'])) {
                                $item['description'] = $option_data['options'][$ch_key]['description'];
                            }
                            if (isset($option_data['options'][$ch_key]['metrics'])) {
                                $item['metrics'] = $option_data['options'][$ch_key]['metrics'];
                            }
                            $key = $item['text'];
                            $choices_map[$key] = $item;
                        }
                        $options_map[$name]['choices'] = $choices_map;
                    }

                }

                $product->options = $options;
            }

            if (empty($product->image)) {
                $category_id = $product->category_id;
                if (isset($categories[$category_id])) {
                    $category = $categories[$category_id];
                    $product->image = json_decode($category->image, true);
                }
            }


            if (!empty($product->variables)) {
                $variables = $product->variables;
                foreach ($variables as &$variant) {
                    foreach ($variant['options'] as &$voption) {
                        if (isset($voption['options_id'])) {
                            $opt_id = $voption['options_id'];
                            $option_data = $product_options[$opt_id];
                            $voption['name'] = $option_data['name'];
                            $voption['nameTranslated'] = $option_data['nameTranslated'];

                            $ch_key = $voption['var_option_id'];
                            $voption['text'] = $option_data['options'][$ch_key]['text'];
                            if (isset($option_data['options'][$ch_key]['textTranslated'])) {
                                $voption['textTranslated'] = $option_data['options'][$ch_key]['textTranslated'];
                            }
                        }
                    }
                    if ($variant['unlimited'] == 0 && $variant['quantity'] > 0) {
                        $product->in_stock = true;
//                        dd($variant);
                    }
                }
                $product->variables = $variables;
            } else {

                if ($product->unlimited == 0 && $product->count > 0) {
                    $product->in_stock = true;
                }


                if ($product->unlimited == 0 && $product->count <= 0) {
                    $product->count = 0;
                    $product->enabled = 0;
                    unset($product->in_stock);
                    unset($product->sale);
                    $product->save();
                    unset($products[$product_key]);
                }
            }

        }

        return $products;
    }

    public static function CategoriesShopPrepeare($categories)
    {
        foreach ($categories as &$category) {
            $category->data = json_decode($category->data, true);
            if($category->products) {
                $category->products = json_decode($category->products, true);
            }
        }

        return $categories;
    }

    public static function OrderPrepeareToAmo($order)
    {

    }

    public static function getLangs()
    {
        $langs = [
            'en' => [
                'name' => 'english',
                'name_2' => 'en',
                'name_3' => 'eng',
                'name_ru' => 'Английский'
            ],
            'ru' => [
                'name' => 'русский',
                'name_2' => 'ру',
                'name_3' => 'рус',
                'name_ru' => 'Русский'
            ],
            'he' => [
                'name' => 'иврит',
                'name_2' => 'he',
                'name_3' => 'hed',
                'name_ru' => 'Иврит'
            ]
        ];

        return $langs;
    }

    public static function dateFormater($date)
    {
        $date = trim(preg_replace('/\\W/', '-', $date));

        if (preg_match('/\\d{4}-\\d{2}-\\d{2}/', $date) || preg_match('/\\d{2}-\\d{2}-\\d{4}/', $date)) {
            $date_new = new Carbon($date);
            $date = $date_new->format("Y-m-d");

        } else {
            $date = false;
        }

        return $date;
    }

    public static function getAllCountries()
    {
        $allCountries = Storage::disk('local')->get('js/all_countries.json');

        return json_decode($allCountries, true);
    }

    public static function getAllCityes()
    {
        $data = Storage::disk('local')->get('js/israel-city.json');
        $data = json_decode($data, true);

        return $data['citys_all'];
    }


    public static function getCityNameByLang($name, $lang)
    {

        $cityes = self::getAllCityes();
        $city_names['en'] = array_column($cityes, 'en');
        $city_names['ru'] = array_column($cityes, 'ru');
        $city_names['he'] = array_column($cityes, 'he');

        foreach ($city_names as $kl => $data) {

            $data = array_flip($data);

            if (isset($data[$name])) {

                if ($kl == $lang) {
                    return $name;
                } else {
                    return $cityes[$data[$name]][$lang];
                }
            }
        }

        return false;

    }

    public static function createZip(array $files, $path)
    {
        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::CREATE)!==TRUE) {
            dd("Невозможно открыть - $path");
        }
        foreach ($files as $item) {
            $zip->addFile($item['root_path'], $item['zip_path']);
        }

        $zip->close();

        if (fileExists($path)) {
            return $path;
        } else {
            return false;
        }

    }
}
