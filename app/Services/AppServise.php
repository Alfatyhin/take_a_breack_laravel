<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AppServise
{
    private static $orderInvoiceStatus = array('no', 'yes');
    private static $orderPaymentMethod = array('undefined', 'Credit card', 'Сash payment', 'PayPal', 'Bit');
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

    public static function generateOrderId($n, $v = 'T')
    {
        $nr = rand(100, 999);
        $n = $nr.$n;
        $r = '';

        for ($i = 1; $n >=0 && $i < 10; $i++) {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i -1))) . $r;
            $n -= pow(26, $i);
        }
        $r = "$v-$r";
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
        foreach ($products as &$product) {
            $cat_names = [];
            $cat_ids = json_decode($product->categories, true);
            $product->image = json_decode($product->image, true);
            $product->galery = json_decode($product->galery, true);
            $product->data = json_decode($product->data, true);
            $product->variables = json_decode($product->variables, true);
            $product->options = json_decode($product->options, true);
            $product->translate = json_decode($product->translate, true);

            if (!empty($product->options)) {

                foreach ($product->options as $option) {
                    $name = $option['name'];
                    $options_map[$name] = $option;
                    foreach ($option['choices'] as $item) {
                        $key = $item['text'];
                        $choices_map[$key] = $item;
                    }
                    $options_map[$name]['choices'] = $choices_map;
                }

            }

            if (empty($product->image)) {
                $category_id = $product->category_id;
                if (isset($categories[$category_id])) {
                    $category = $categories[$category_id];
                    $product->image = json_decode($category->image, true);
                } else {
//                    dd($product->toArray(), $categories);
                }
            }

            if ($product->unlimited == 0 && $product->count > 0) {
                $cat_names['have'] = 'have';
            }
            $label = false;
            if (!isset($cat_names['have']) && !empty($product->variables)) {
                foreach ($product->variables as $vk => $variant) {
                    if ($variant['unlimited'] == false && $variant['quantity'] > 0 ) {
                        if ($variant['unlimited'] == 0 && $variant['quantity'] > 0) {
                            foreach($variant['options'] as $option) {
                                $opt_name = $option['name'];
                                $label[$opt_name]['values'][] = $option['value'];
                                $label[$opt_name]['nameTranslated'] = $options_map[$opt_name]['nameTranslated'];
                            }
                        }
                        if (!isset($cat_names['have'])) {
                            $cat_names['have'] = 'have';
                        }
                    }
                }
                $product->stok_label = $label;
//                if ($label) {
//                    dd($product->stok_label);
//                }
            }


            if (!empty($product->category_id) && isset($categories[$product->category_id])) {
                $cat_name = $categories[$product->category_id]['slag'];
                if (!isset($cat_all_count[$cat_name])) {
                    $cat_all_count[$cat_name] = 0;
                }
                $cat_all_count[$cat_name] ++;
                if ($cat_all_count[$cat_name] <= 2 ) {
                    $cat_names['all'] = 'all';
                }
                $cat_names[] = $cat_name;
            }
            if (!empty($cat_ids)) {
                $product->categories = $cat_ids;
                foreach ($cat_ids as $cat_id) {
                    if (isset($categories[$cat_id])) {
                        $cat_names[] = $categories[$cat_id]['name'];
                    }
                }

            }
            if (sizeof($cat_names) == 0){
                $product->cat_names = 'no_category';
            } else {
                $cat_names_str = implode(' ', $cat_names);
                $product->cat_names = $cat_names_str;
            }

            if (!empty($product->variables)) {
                if (sizeof($product->variables) > 1) {
                    foreach ($product->variables as $variant) {
                        $v_price = $variant['defaultDisplayedPrice'];
                        if ($v_price < $product->price)
                            $product->price = $v_price;
                    }
                }
            }

            if (isset($cat_names['have'])) {
                $product->in_stock = true;
            } else {
                $product->in_stock = false;
            }
        }

        return $products;
    }

    public static function CategoriesShopPrepeare($categories)
    {
        foreach ($categories as &$category) {
            $category->data = json_decode($category->data, true);
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
}
