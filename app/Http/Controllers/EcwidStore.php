<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Services\EcwidService;
use Carbon\Carbon;
use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EcwidStore extends Controller
{

    private $ecwidService;
    public function __construct(EcwidService $service) {
        $this->ecwidService = $service;
    }

    public function storeJs()
    {
        $dey_offer_id = Storage::disk('local')->get('data/dey_offer_id.txt');
        $dey_offer = $this->ecwidService->getProduct($dey_offer_id);
        echo " var dey_offer = " . json_encode($dey_offer) . "; ";
        /////////////////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/ecwid-shop.js');

        echo $jsfile;
    }

    public function tildaJs()
    {
        $dey_offer_id = Storage::disk('local')->get('data/dey_offer_id.txt');
        $dey_offer = $this->ecwidService->getProduct($dey_offer_id);
        echo " var dey_offer = " . json_encode($dey_offer) . "; ";

        ////////////////////////////////////////////////////
        $products = $this->ecwidService
            ->getAllProducts();
        $products = $products['items'];
        echo " var products = " . json_encode($products) . "; ";
        /////////////////////////////////////////////////
        $categories = $this->ecwidService->getCategories();
//        dd($categories);
        foreach ($categories['items'] as $category) {
            $name = $category['name'];
            $data[$name] = $category;
        }
        echo " var shop = []; shop['categories'] = " . json_encode($data) . "; ";

        ///////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-v1.js');
        echo $jsfile;
    }

    public function createOrder(Request $request)
    {
        header('Access-Control-Allow-Origin: *');


        $ecwidService = $this->ecwidService;

        $data_test = $request->post('data-test');
        if ($data_test) {
            $data = json_decode($data_test, true);
            echo "<pre>";
            print_r($data);

        } else {
            $data = $request->post('data');
            WebhookLog::addLog('test create order ', $data);
//            print_r($data);
        }

    }

    public function deleteOrder(Request $request)
    {
        $id = $request->get('id');
        $ecwidService = $this->ecwidService;
        $res = $ecwidService->deleteOrder($id);
        dd($res);
    }

    public function sheckPromoCode(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        $promo_code = $request->get('promo_code');
        $code_data = false;

        $res = $this->ecwidService->getDiscountCoupons($promo_code);
        foreach ($res['items'] as $item) {
            if ($item['code'] == $promo_code && $item['status'] == 'ACTIVE') {
                $code_data = $item;
            }
        }
        echo json_encode($code_data);
    }

    public function tildaJsv2()
    {

        $this->ecwidBase();
        ///////////////////////////////////////////////////////
        echo "console.log('start server script v2');";
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-v2.js');
        echo $jsfile;
    }

    public function tildaJsv3()
    {
        $this->ecwidBaseTest();
        ///////////////////////////////////////////////////////
        echo "console.log('start server script v3');";
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-v3.js');
        echo $jsfile;
    }
    public function ecwidCart()
    {
        $this->ecwidBase();
        ///////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-cart.js');
        echo $jsfile;
    }
    public function ecwidCartTest()
    {
        $order_calc = Storage::disk('local')->get('js/order_calc.json');
        echo " var order_calc = " . $order_calc . "; ";
        ////////////////////////////////////////////////////
        $this->ecwidBase();
        ///////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-cart-test.js');
        echo $jsfile;
    }
    public function ecwidOrder()
    {
        $this->ecwidBase();
        ///////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-order.js');
        echo $jsfile;
    }

    public function ecwidBase()
    {
        $stockCategories = array('', '123457253', '123633263');
        $stockCategories = array_flip($stockCategories);

        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        echo " var shop_setting = " . $shop_setting . "; ";
        ////////////////////////////////////////////////////

        $delivery_json = Storage::disk('local')->get('js/delivery.json');
        echo " var delivery_data = " . $delivery_json . "; ";
        ////////////////////////////////////////////////////

        $cityes_json = Storage::disk('local')->get('js/israel-city.json');
        echo " var cityes_data = " . $cityes_json . ";
        var cityes = cityes_data['citys_all'];
        ";

        ////////////////////////////////////////////////////
        $dey_offer_id = Storage::disk('local')->get('data/dey_offer_id.txt');
        $dey_offer = $this->ecwidService->getProduct($dey_offer_id);
        echo " var dey_offer = " . json_encode($dey_offer) . "; ";

        ////////////////////////////////////////////////////
        $categories = $this->ecwidService->getCategories();
        foreach ($categories['items'] as $category) {
            $name = $category['name'];
            $data[$name] = $category;
            $id = $category['id'];
            $data_id[$id] = $category;

            $products_all = $this->ecwidService->getProductsByCategoryId($category['id']);
            $products_id = [];
            foreach ($products_all['items'] as $product) {
                $category_id = $product['defaultCategoryId'];
                $id = $product['id'];

                if (isset($stockCategories[$category_id])) {
                    $product['only_in_stock'] = true;
                } else {
                    $product['only_in_stock'] = false;
                }

                if ($product['unlimited'] == false) {

                    if (isset($product['quantity'])) {
                        if ($product['quantity'] > 0) {
                            $products[$id] = $product;
                            $products_id[] = $product['id'];
                        }
                    }

                } else {
                    $products[$id] = $product;
                    $products_id[] = $product['id'];
                }
            }
            $data[$name]['products_id'] = $products_id;
        }
        echo " var products = " . json_encode($products) . "; ";
        echo " var shop = []; shop['categories'] = " . json_encode($data) . "; shop['category_id'] = " . json_encode($data_id) . "; ";
        ///////////////////////////////////////////////////////

        $jsfile = Storage::disk('local')->get('js/translit-ekwid-store.js');
        echo $jsfile;
    }

    public function ecwidBaseTest()
    {
        $stockCategories = array('', '123457253', '123633263');
        $stockCategories = array_flip($stockCategories);

        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        echo " var shop_setting = " . $shop_setting . "; ";
        ////////////////////////////////////////////////////

        $delivery_json = Storage::disk('local')->get('js/delivery.json');
        echo " var delivery_data = " . $delivery_json . "; ";
        ////////////////////////////////////////////////////

        $cityes_json = Storage::disk('local')->get('js/israel-city.json');
        echo " var cityes_data = " . $cityes_json . ";
        var cityes = cityes_data['citys_all'];
        ";

        ////////////////////////////////////////////////////
        $dey_offer_id = Storage::disk('local')->get('data/dey_offer_id.txt');
        $dey_offer = $this->ecwidService->getProduct($dey_offer_id);
        echo " var dey_offer = " . json_encode($dey_offer) . "; ";

        ////////////////////////////////////////////////////
        $categories = $this->ecwidService->getCategories();
        foreach ($categories['items'] as $category) {
            $name = $category['name'];
            $data[$name] = $category;
            $id = $category['id'];
            $data_id[$id] = $category;

            $products_all = $this->ecwidService->getProductsByCategoryId($category['id']);
            $products_id = [];
            foreach ($products_all['items'] as $product) {
                $category_id = $product['defaultCategoryId'];
                $id = $product['id'];

                if (isset($stockCategories[$category_id])) {
                    $product['only_in_stock'] = true;
                } else {
                    $product['only_in_stock'] = false;
                }

                if ($product['unlimited'] == false) {

                    if (isset($product['quantity'])) {
                        if ($product['quantity'] > 0) {
                            $products[$id] = $product;
                            $products_id[] = $product['id'];
                        }
                    }

                } else {
                    $products[$id] = $product;
                    $products_id[] = $product['id'];
                }
            }
            $data[$name]['products_id'] = $products_id;
        }
        echo " var products = " . json_encode($products) . "; ";
        echo " var shop = []; shop['categories'] = " . json_encode($data) . "; shop['category_id'] = " . json_encode($data_id) . "; ";
        ///////////////////////////////////////////////////////

        $jsfile = Storage::disk('local')->get('js/translit-ekwid-store.js');
        echo $jsfile;
    }


    public function tildaJs01()
    {
        $jsfile = Storage::disk('local')->get('js/translit-ekwid-store.js');
//        echo $jsfile;
        ///////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////
        $jsfile = Storage::disk('local')->get('js/tilda-ecwid-v0.1.js');
        echo $jsfile;
    }


    public function getEcwidProducts(Request $request)
    {
        $products = $this->ecwidService
            ->getAllProducts();

        return view('ecwid.products', [
            'products' => $products,
        ]);
    }

    public function EcwidShop(Request $request)
    {

        $categories = $this->ecwidService
            ->getCategories();

        foreach ($categories['items'] as $item) {
            $categoryId = $item['id'];

            if ($item['enabled']) {
                $categoryName = $item['name'];
                $products = $this->ecwidService
                    ->getProductsByCategoryId($categoryId);
                $productList[$categoryName]['description'] = $item;
                $productList[$categoryName]['catalog']  = $products;
            }
        }
//        var_dump($products['items'][0]);

        return view('ecwid.shop', [
            'productList' => $productList
        ]);
    }


    public function getEcwidProductById(Request $request)
    {
        echo "<pre>";
        $productId = $request->get('id');
        $product = $this->ecwidService->getProduct($productId);

        print_r($product);

    }

    public function getOrderById(Request $request)
    {

        $orderId = $request->get('orderId');

        $ecwidOrder = false;
        $order = false;
        $rateDiscount = false;
        $message = false;

        if (!empty($orderId)) {
                $ecwidOrder = $this->ecwidService->getOrderBuId($orderId);
                if (isset($ecwidOrder['errorMessage'])) {
                    $message = $ecwidOrder['errorMessage'];
                    $orderId = false;
                } else {
                    $order = \App\Models\Orders::where('order_id', $orderId)->first();


                    if (!empty($ecwidOrder['couponDiscount'])) {
                        $discount = $ecwidOrder['couponDiscount'];
                        $total = $ecwidOrder['subtotal'];
                        $rateDiscount = 100 / ($total / $discount);
                        $rateDiscount = "скидка - $discount ($rateDiscount%)";
                    } else {
                        $rateDiscount = 'no discount';
                    }
                }


        }

//        dd($ecwidOrder);

        return view('ecwid.order', [
            'orderId'    => $orderId,
            'ecwidOrder' => $ecwidOrder,
            'order'      => $order,
            'discount'   => $rateDiscount,
            'message'    => $message
        ]);

    }

    public function allCategories()
    {
        $categories = $this->ecwidService->getCategories();

        echo "<pre>";
       foreach ($categories['items'] as $item) {

           $img = $item['thumbnailUrl'];
           $name = $item['name'];
           echo "<img src='$img'><br> $name <br>";

           foreach ($item['nameTranslated'] as $k => $v) {
               echo "$k - $v <br>";
           }

           $desc = $item['description'];

           echo "<hr> Описание <br> $desc <br>";

           foreach ($item['descriptionTranslated'] as $k => $v) {
               echo "$k - $v <hr>";
           }

           $products = $this->ecwidService->getProductsByCategoryId($item['id']);
           foreach ($products['items'] as $product) {
               print_r("{$product['name']} <hr>");
           }
       }

    }

    public function AbandoneBaskets()
    {

        $date = new Carbon();
        $dateFrom = new Carbon($date->format('Y-m-d 00:00'));
        $dateTo = new Carbon($date->format('Y-m-d 23:59'));
        $dateFrom->addDays(-7);
        $dateTo->addDays(-2);
        $showHidden = 'false';

        echo "<pre>";
        var_dump($dateFrom->format('Y-m-d H:i'), $dateTo->format('Y-m-d H:i'));

        $res = $this->ecwidService->getAbondoneBascets($showHidden, $dateFrom->unix(), $dateTo->unix());

        var_dump($res);

    }

    public function shopSettings(Request $request)
    {
        $message = false;
        if (session()->has('message')) {
            $message = session('message');
        }



        $order_calc = $request->post('order_calc');
        if (!empty($order_calc)) {
            foreach ($order_calc as $k => $item) {
                if (empty($item['to_summ'])) {
                    unset($order_calc[$k]);
                }
            }
            $res = Storage::disk('local')->put('js/order_calc.json', json_encode($order_calc));
            if($res) {
                session()->flash('message', ['order calc save']);
                return redirect('/ecwid-store/settings');
            }
        }
        ///////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        $all_products = $this->ecwidService
            ->getAllProducts();
        $all_products = $all_products['items'];
        foreach ($all_products as $product) {
            $id = $product['id'];
            $category_id = $product['defaultCategoryId'];

            if (isset($stockCategories[$category_id])) {
                $product['only_in_stock'] = true;
            } else {
                $product['only_in_stock'] = false;
            }

            if ($product['unlimited'] == false) {

                if (isset($product['quantity'])) {
                    if ($product['quantity'] > 0) {
                        $products[$id] = $product;
                    }
                }

            } else {
                $products[$id] = $product;
            }

        }
        $all_products = $products;
        /////////////////////////////////////////////////

        $shop_setting = $request->get('shop');
        if (!empty($shop_setting)) {
//            dd($shop_setting);
            $res = Storage::disk('local')->put('js/shop_setting.json', json_encode($shop_setting));
            if($res) {
                session()->flash('message', ['shop setting save']);
                return redirect('/ecwid-store/settings');
            }
        }
        ///////////////////////////////////////////////////////////

        $shop_translit_json = $request->get('shop_translit_json');
        if (!empty($shop_translit_json)) {
            $res = Storage::disk('local')->put('js/translit-ekwid-store.js', $shop_translit_json);
            if($res) {
                session()->flash('message', ['json translit save']);
                return redirect('/ecwid-store/settings');
            }
        }
        ///////////////////////////////////////////////////////////
        $dey_offer_id = $request->get('dey_offer_id');
        if (!empty($dey_offer_id)) {
            $res = Storage::disk('local')->put('data/dey_offer_id.txt', $dey_offer_id);
            if($res) {
                session()->flash('message', ['dey offer save']);
                return redirect('/ecwid-store/settings');
            }
        }
        ////////////////////////////////////////////////////////////////////////
        $save_cityes = $request->post('save_cityes');
        if (!empty($save_cityes)) {
            $cityes = Storage::disk('local')->get('js/israel-city.json');
            $cityes = json_decode($cityes, true);
            $cityes_new = $request->post('city');
            foreach ($cityes_new as $k => $value) {
               if (empty($value['ru']) || empty($value['he'])) {
                   unset($cityes_new[$k]);
               }
            }
            $cityes['citys_all'] = $cityes_new;
            $res = Storage::disk('local')->put('js/israel-city.json', json_encode($cityes));
            if($res) {
                session()->flash('message', ['city save']);
                return redirect('/ecwid-store/settings');
            }
        }
        //////////////////////////////////////////////////////////////////////
        $delivery = $request->post('delivery');
        if (!empty($delivery)) {
            $delivery_cityes = $request->post('city');
            foreach ($delivery as $k => $value) {
                if (!empty($delivery_cityes[$k])) {
                    $delivery[$k]['cityes'] = $delivery_cityes[$k];
                }
                if (empty($delivery[$k]['cityes'])
                    || empty($value['min_sum_order'])
                    || empty($value['rate_delivery'])) {
                    unset($delivery[$k]);
                } else {
                    foreach ($delivery_cityes[$k] as $city) {
                        $data_cityes[$city][] = $k;
                    }
                }
                foreach ($value['rate_delivery_to_summ_order'] as $k_rate => $val) {
                    if (!isset($val['sum_order']) || !isset($val['rate_delivery'])) {
                        unset ($delivery[$k]['rate_delivery_to_summ_order'][$k_rate]);
                    }
                }
            }



            $delivery_data['delivery'] = $delivery;
            $delivery_data['cityes_data'] = $data_cityes;
//            dd($delivery_data);
            $res = Storage::disk('local')->put('js/delivery.json', json_encode($delivery_data));
            if($res) {
                session()->flash('message', ['delivery save']);
                return redirect('/ecwid-store/settings');
            }
        }

        $order_calc = Storage::disk('local')->get('js/order_calc.json');
        $order_calc = json_decode($order_calc, true);
//        dd($order_calc);

        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        $shop_setting = json_decode($shop_setting, true);
//        dd($shop_setting);

        $shop_translit_json = Storage::disk('local')->get('js/translit-ekwid-store.js');

        $dey_offer_id = Storage::disk('local')->get('data/dey_offer_id.txt');
        $dey_offer = $this->ecwidService->getProduct($dey_offer_id);

        $productsAll = $this->ecwidService->getAllProducts();
        $categories = $this->ecwidService->getCategories();

        foreach ($productsAll['items'] as $product) {

            foreach ($product['categories'] as $category) {
                if ($category['enabled']) {
                    $id = $category['id'];
                    $products[$id][] = $product;
                }
            }
        }

        $cityes = Storage::disk('local')->get('js/israel-city.json');
        $cityes = json_decode($cityes, true);

//        $cityes = "{\"citys_all\":[{\"ru\":\"all\",\"he\":\"all\",\"en\":\"all\"},{\"ru\":\"Акко\",\"he\":\"עכו\",\"en\":\"\"},{\"ru\":\"Арад\",\"he\":\"ערד\",\"en\":\"\"},{\"ru\":\"Ариэль\",\"he\":\"אריאל\",\"en\":\"\"},{\"ru\":\"Афула\",\"he\":\"עפולה\",\"en\":\"\"},{\"ru\":\"Ашдод\",\"he\":\"אשדוד\",\"en\":\"\"},{\"ru\":\"Ашкелон\",\"he\":\"אשקלון\",\"en\":\"\"},{\"ru\":\"Бака-эль-Гарбия\",\"he\":\"באקה אל-גרבייה\",\"en\":\"\"},{\"ru\":\"Бат-Ям\",\"he\":\"בת ים\",\"en\":\"\"},{\"ru\":\"Бейт-Шеан\",\"he\":\"בית שאן\",\"en\":\"\"},{\"ru\":\"Бейт-Шемеш\",\"he\":\"בית שמש\",\"en\":\"\"},{\"ru\":\"Бейтар-Илит\",\"he\":\"ביתר עילית\",\"en\":\"\"},{\"ru\":\"Беэр-Шева\",\"he\":\"באר שבע\",\"en\":\"\"},{\"ru\":\"Бней-Брак\",\"he\":\"בני ברק\",\"en\":\"\"},{\"ru\":\"Герцлия\",\"he\":\"הרצליה\",\"en\":\"\"},{\"ru\":\"Гиватаим\",\"he\":\"גבעתיים\",\"en\":\"\"},{\"ru\":\"Гиват-Шмуэль\",\"he\":\"גבעת שמואל\",\"en\":\"\"},{\"ru\":\"Димона\",\"he\":\"דימונה\",\"en\":\"\"},{\"ru\":\"Иерусалим\",\"he\":\"ירושלים\",\"en\":\"\"},{\"ru\":\"Йехуд-Моноссон\",\"he\":\"יהוד-מונוסון\",\"en\":\"\"},{\"ru\":\"Йокнеам\",\"he\":\"יקנעם\",\"en\":\"\"},{\"ru\":\"Калансуа\",\"he\":\"קלנסווה\",\"en\":\"\"},{\"ru\":\"Кармиэль\",\"he\":\"כרמיאל\",\"en\":\"\"},{\"ru\":\"Кафр-Касем\",\"he\":\"כפר קאסם\",\"en\":\"\"},{\"ru\":\"Кирьят-Ата\",\"he\":\"קריית אתא\",\"en\":\"\"},{\"ru\":\"Кирьят-Бялик\",\"he\":\"קריית ביאליק\",\"en\":\"\"},{\"ru\":\"Кирьят-Гат\",\"he\":\"קריית גת\",\"en\":\"\"},{\"ru\":\"Кирьят-Малахи\",\"he\":\"קריית מלאכי\",\"en\":\"\"},{\"ru\":\"Кирьят-Моцкин\",\"he\":\"קריית מוצקין\",\"en\":\"\"},{\"ru\":\"Кирьят-Оно\",\"he\":\"קריית אונו\",\"en\":\"\"},{\"ru\":\"Кирьят-Шмона\",\"he\":\"קריית שמונה\",\"en\":\"\"},{\"ru\":\"Кирьят-Ям\",\"he\":\"קריית ים\",\"en\":\"\"},{\"ru\":\"Кфар-Сава\",\"he\":\"כפר סבא\",\"en\":\"\"},{\"ru\":\"Лод\",\"he\":\"לוד\",\"en\":\"\"},{\"ru\":\"Маале-Адумим\",\"he\":\"מעלה אדומים\",\"en\":\"\"},{\"ru\":\"Маалот-Таршиха\",\"he\":\"מעלות-תרשיחא\",\"en\":\"\"},{\"ru\":\"Мигдаль-ха-Эмек\",\"he\":\"מגדל העמק\",\"en\":\"\"},{\"ru\":\"Модиин-Илит\",\"he\":\"מודיעין עילית\",\"en\":\"\"},{\"ru\":\"Модиин-Маккабим-Реут\",\"he\":\"מודיעין-מכבים-רעות\",\"en\":\"\"},{\"ru\":\"Нагария\",\"he\":\"נהריה\",\"en\":\"\"},{\"ru\":\"Назарет\",\"he\":\"נצרת\",\"en\":\"\"},{\"ru\":\"Ноф-ха-Галиль\",\"he\":\"נוף הגליל\",\"en\":\"\"},{\"ru\":\"Нес-Циона\",\"he\":\"נס ציונה\",\"en\":\"\"},{\"ru\":\"Нетания\",\"he\":\"נתניה\",\"en\":\"\"},{\"ru\":\"Нетивот\",\"he\":\"נתיבות\",\"en\":\"\"},{\"ru\":\"Нешер\",\"he\":\"נשר\",\"en\":\"\"},{\"ru\":\"Ор-Акива\",\"he\":\"אור עקיבא\",\"en\":\"\"},{\"ru\":\"Ор-Йехуда\",\"he\":\"אור יהודה\",\"en\":\"\"},{\"ru\":\"Офаким\",\"he\":\"אופקים\",\"en\":\"\"},{\"ru\":\"Петах-Тиква\",\"he\":\"פתח תקווה\",\"en\":\"\"},{\"ru\":\"Раанана\",\"he\":\"רעננה\",\"en\":\"\"},{\"ru\":\"Рамат-Ган\",\"he\":\"רמת גן\",\"en\":\"\"},{\"ru\":\"Рамат-ха-Шарон\",\"he\":\"רמת השרון\",\"en\":\"\"},{\"ru\":\"Рамла\",\"he\":\"רמלה\",\"en\":\"\"},{\"ru\":\"Рахат\",\"he\":\"רהט\",\"en\":\"\"},{\"ru\":\"Реховот\",\"he\":\"רחובות\",\"en\":\"\"},{\"ru\":\"Ришон-ле-Цион\",\"he\":\"ראשון לציון\",\"en\":\"\"},{\"ru\":\"Рош-ха-Аин\",\"he\":\"ראש העין\",\"en\":\"\"},{\"ru\":\"Сахнин\",\"he\":\"סח'נין\",\"en\":\"\"},{\"ru\":\"Сдерот\",\"he\":\"שדרות\",\"en\":\"\"},{\"ru\":\"Тайбе\",\"he\":\"טייבה\",\"en\":\"\"},{\"ru\":\"Тамра\",\"he\":\"טמרה\",\"en\":\"\"},{\"ru\":\"Тверия\",\"he\":\"טבריה\",\"en\":\"\"},{\"ru\":\"Тель-Авив\",\"he\":\"תל אביב\",\"en\":\"\"},{\"ru\":\"Тира\",\"he\":\"טירה\",\"en\":\"\"},{\"ru\":\"Тират-Кармель\",\"he\":\"טירת כרמל\",\"en\":\"\"},{\"ru\":\"Умм-эль-Фахм\",\"he\":\"אום אל-פחם\",\"en\":\"\"},{\"ru\":\"Хадера\",\"he\":\"חדרה\",\"en\":\"\"},{\"ru\":\"Хайфа\",\"he\":\"חיפה\",\"en\":\"\"},{\"ru\":\"Ход-ха-Шарон\",\"he\":\"הוד השרון\",\"en\":\"\"},{\"ru\":\"Холон\",\"he\":\"חולון\",\"en\":\"\"},{\"ru\":\"Цфат\",\"he\":\"צפת\",\"en\":\"\"},{\"ru\":\"Шефарам\",\"he\":\"שפרעם\",\"en\":\"\"},{\"ru\":\"Эйлат\",\"he\":\"אילת\",\"en\":\"\"},{\"ru\":\"Эльад\",\"he\":\"אלעד\",\"en\":\"\"},{\"ru\":\"Явне\",\"he\":\"יבנה\",\"en\":\"\"}],\"en\":[{\"name\":\"Acre\",\"km2\":\"13.5\"},{\"name\":\"Afula\",\"km2\":\"26.9\"},{\"name\":\"Arad\",\"km2\":\"93.1\"},{\"name\":\"Arraba\",\"km2\":\"8.25\"},{\"name\":\"Ashdod\",\"km2\":\"47.2\"},{\"name\":\"Ashkelon\",\"km2\":\"47.8\"},{\"name\":\"Baqa al-Gharbiyye\",\"km2\":\"16.4\"},{\"name\":\"Bat Yam\",\"km2\":\"8.2\"},{\"name\":\"Beersheba\",\"km2\":\"117.5\"},{\"name\":\"Beit She'an\",\"km2\":\"7.3\"},{\"name\":\"Beit Shemesh\",\"km2\":\"34.3\"},{\"name\":\"Bnei Brak\",\"km2\":\"7.1\"},{\"name\":\"Dimona\",\"km2\":\"29.9\"},{\"name\":\"Eilat\",\"km2\":\"84.8\"},{\"name\":\"El'ad\",\"km2\":\"2.8\"},{\"name\":\"Giv'at Shmuel\",\"km2\":\"2.6\"},{\"name\":\"Givatayim\",\"km2\":\"3.3\"},{\"name\":\"Hadera\",\"km2\":\"49.4\"},{\"name\":\"Haifa\",\"km2\":\"63.7\"},{\"name\":\"Herzliya\",\"km2\":\"21.6\"},{\"name\":\"Hod HaSharon\",\"km2\":\"21.6\"},{\"name\":\"Holon\",\"km2\":\"18.9\"},{\"name\":\"Jerusalem\",\"km2\":\"125.2\"},{\"name\":\"Kafr Qasim\",\"km2\":\"8.7\"},{\"name\":\"Karmiel\",\"km2\":\"19.2\"},{\"name\":\"Kfar Saba\",\"km2\":\"14.2\"},{\"name\":\"Kfar Yona\",\"km2\":\"11.0\"},{\"name\":\"Kiryat Ata\",\"km2\":\"16.7\"},{\"name\":\"Kiryat Bialik\",\"km2\":\"8.2\"},{\"name\":\"Kiryat Gat\",\"km2\":\"16.3\"},{\"name\":\"Kiryat Malakhi\",\"km2\":\"4.6\"},{\"name\":\"Kiryat Motzkin\",\"km2\":\"3.8\"},{\"name\":\"Kiryat Ono\",\"km2\":\"4.1\"},{\"name\":\"Kiryat Shmona\",\"km2\":\"14.2\"},{\"name\":\"Kiryat Yam\",\"km2\":\"4.3\"},{\"name\":\"Lod\",\"km2\":\"12.2\"},{\"name\":\"Ma'alot-Tarshiha\",\"km2\":\"6.8\"},{\"name\":\"Migdal HaEmek\",\"km2\":\"7.6\"},{\"name\":\"Modi'in-Maccabim-Re'ut\",\"km2\":\"50.2\"},{\"name\":\"Nahariya\",\"km2\":\"10.2\"},{\"name\":\"Nazareth\",\"km2\":\"14.1\"},{\"name\":\"Nesher\",\"km2\":\"12.8\"},{\"name\":\"Ness Ziona\",\"km2\":\"15.6\"},{\"name\":\"Netanya\",\"km2\":\"29.0\"},{\"name\":\"Netivot\",\"km2\":\"5.6\"},{\"name\":\"Nof HaGalil\",\"km2\":\"32.5\"},{\"name\":\"Ofakim\",\"km2\":\"10.3\"},{\"name\":\"Or Akiva\",\"km2\":\"3.5\"},{\"name\":\"Or Yehuda\",\"km2\":\"5.1\"},{\"name\":\"Petah Tikva\",\"km2\":\"35.9\"},{\"name\":\"Qalansawe\",\"km2\":\"8.4\"},{\"name\":\"Ra'anana\",\"km2\":\"14.9\"},{\"name\":\"Rahat\",\"km2\":\"19.6\"},{\"name\":\"Ramat Gan\",\"km2\":\"13.2\"},{\"name\":\"Ramat HaSharon\",\"km2\":\"16.8\"},{\"name\":\"Ramla\",\"km2\":\"11.9\"},{\"name\":\"Rehovot\",\"km2\":\"23.0\"},{\"name\":\"Rishon LeZion\",\"km2\":\"58.7\"},{\"name\":\"Rosh HaAyin\",\"km2\":\"24.4\"},{\"name\":\"Safed\",\"km2\":\"29.2\"},{\"name\":\"Sakhnin\",\"km2\":\"9.8\"},{\"name\":\"Sderot\",\"km2\":\"4.5\"},{\"name\":\"Shefa-'Amr\",\"km2\":\"19.8\"},{\"name\":\"Tamra\",\"km2\":\"29.3\"},{\"name\":\"Tayibe\",\"km2\":\"18.7\"},{\"name\":\"Tel Aviv-Yafo\",\"km2\":\"51.8\"},{\"name\":\"Tiberias\",\"km2\":\"10.9\"},{\"name\":\"Tira\",\"km2\":\"11.9\"},{\"name\":\"Tirat Carmel\",\"km2\":\"5.6\"},{\"name\":\"Umm al-Fahm\",\"km2\":\"22.3\"},{\"name\":\"Yavne\",\"km2\":\"10.7\"},{\"name\":\"Yehud-Monosson\",\"km2\":\"5.0\"},{\"name\":\"Yokneam Illit\",\"km2\":\"7.4\"}]}";
//        $cityes = json_decode($cityes, true);
//        dd($cityes);

        $delivery =  Storage::disk('local')->get('js/delivery.json');
        $delivery = json_decode($delivery, true);

//        $delivery = '{"delivery":{"0":{"name":{"ru":"Зона 1","en":"Zone 1","he":"Zone 1"},"min_sum_order":"100","rate_delivery":"30","rate_delivery_to_summ_order":[{"sum_order":{"min":"400","max":"0"},"rate_delivery":"0"}],"cityes":{"7":"7","12":"12","28":"28","46":"46","48":"48","50":"50","55":"55","62":"62","69":"69"}},"1":{"name":{"ru":"Зона 2","en":"Zone 2","he":"Zone 2"},"min_sum_order":"100","rate_delivery":"55","rate_delivery_to_summ_order":[{"sum_order":{"min":"400","max":"0"},"rate_delivery":"30"}],"cityes":{"22":"22","31":"31","49":"49","51":"51","54":"54","56":"56","68":"68","74":"74"}},"3":{"name":{"ru":"Нетания","en":"Netanya","he":"נתניה"},"min_sum_order":"150","rate_delivery":"70","rate_delivery_to_summ_order":[{"sum_order":{"min":"400","max":"0"},"rate_delivery":"49"}],"cityes":{"4":"4","42":"42"}},"4":{"name":{"ru":"Ашкелон","en":"Ashkelon","he":"אשקלון"},"min_sum_order":"150","rate_delivery":"80","rate_delivery_to_summ_order":[{"sum_order":{"min":"400","max":"0"},"rate_delivery":"55"}],"cityes":{"5":"5"}},"5":{"name":{"ru":"Хайфа","en":"Haifa","he":"חיפה"},"min_sum_order":"200","rate_delivery":"120","rate_delivery_to_summ_order":[],"cityes":{"67":"67"}},"6":{"name":{"ru":"Иерусалим","en":"Jerusalem","he":"ירושלים"},"min_sum_order":"200","rate_delivery":"110","rate_delivery_to_summ_order":[],"cityes":{"17":"17"}}},"cityes_data":{"4":[3],"5":[4],"7":[0],"12":[0],"17":[6],"22":[1],"28":[0],"31":[1],"42":[3],"46":[0],"48":[0],"49":[1],"50":[0],"51":[1],"54":[1],"55":[0],"56":[1],"62":[0],"67":[5],"68":[1],"69":[0],"74":[1]}}';
//        $delivery = json_decode($delivery, true);
//
//        foreach($delivery['delivery'] as $k => &$value) {
//            foreach($value['cityes'] as $key => $v) {
//                if (isset($cityes['citys_all'][$key]['ru'])) {
//                    $city_name = $cityes['citys_all'][$key]['ru'];
//                } else {
//                    print_r($value);
//                    unset($value['cityes'][$key]);
//                }
//
//            }
//        }
//        dd($delivery);



        return view('ecwid.shop_settings', [
            'categories' => $categories,
            'products'   => $products,
            'dey_offer'  => $dey_offer,
            'cityes'     => $cityes,
            'delivery'   => $delivery,
            'message'    => $message,
            'shop_translit' => $shop_translit_json,
            'shop_setting' => $shop_setting,
            'order_calc' => $order_calc,
            'all_products' => $all_products
        ]);

    }

}
