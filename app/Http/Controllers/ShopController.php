<?php

namespace App\Http\Controllers;

use App\Models\AppErrors;
use App\Models\Categories;
use App\Models\Clients;
use App\Models\Coupons;
use App\Models\Orders;
use App\Models\Product;
use App\Models\UtmModel;
use App\Models\WebhookLog;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\IcreditServise;
use App\Services\OrderService;
use App\Services\PayPalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use phpDocumentor\Reflection\Utils;
use SoapClient;
use function PHPUnit\Framework\matches;

class ShopController extends Controller
{

    private $v = '2.1.3';

    public function err404(Request $request, $lang = 'en')
    {
        $v = $this->v;
        http_response_code(404);

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);

        return view('errors.404', [
            'v' => $v,
            'lang' => $lang,
            'categories' => $categories,
            'noindex' => $request->noindex
        ]);
    }

    public function indexView(Request $request, $lang = 'en', $filter = 'all')
    {

        App::setLocale($lang);

        $v = $this->v;

        $client = session('client');

        $popapp_message = session('message_popapp');

        $category_select = Categories::where('index_num', 0)->select('products', 'slag')->first();

        $category_default = $category_select['slag'];
        $category_products = $category_select['products'];
        $category_products = json_decode($category_products, true);

        $category = Categories::where('slag', $category_default)->first();
        if (!$category) {
            return $this->err404($request, $lang);
        }
        if($category->products) {
            $category->products = json_decode($category->products, true);
        }

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);
        $products_all = Product::where('enabled', 1)->whereIn('id', $category_products)->get()->sortBy('index_num')->keyBy('id');
        $products = AppServise::ProductsShopPrepeare($products_all, $categories);


        $dey_offer_data = false;
        if (Storage::disk('local')->exists('data/dey-offer.json')) {
            $dey_offer_json = Storage::disk('local')->get('data/dey-offer.json');

            $dey_offer_data = json_decode($dey_offer_json, true);
            $offer_id = $dey_offer_data['id'];
            $dey_offer = $products[$offer_id];
        }
        if ($filter == 'in_stock') {
            foreach ($products as $key => $item) {
                if (!$item->in_stock) {
                    unset($products[$key]);
                }
            }
        }
        if ($filter == 'sale') {
            foreach ($products as $key => $item) {
                if (!$item->sale) {
                    unset($products[$key]);
                }
            }
        }



        return view("shop.new.index_master", [
            'v' => $v,
            'banner' => $request->banner,
            'client' => $client,
            'lang' => $lang,
            'filter' => $filter,
            'categories' => $categories,
            'products' => $products,
            'dey_offer_data' => $dey_offer_data,
            'category_active' => $category_default,
            'popapp_message' => $popapp_message,
            'category' => $category,
            'noindex' => $request->noindex
        ]);
    }

    public function indexFilterEn(Request $request, $filter)
    {

        return $this->indexView($request, 'en', $filter);
    }


    public function categoryView(Request $request, $category, $lang = 'en', $filter = 'all')
    {

        App::setLocale($lang);

        $v = $this->v;

        $client = session('client');

        $popapp_message = session('message_popapp');

        $category = Categories::where('slag', $category)->first();
        if (!$category) {
            return $this->err404($request, $lang);
        }
        $category_products = $category->products;



        $category_products = json_decode($category_products, true);

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);
        $category = $categories[$category->id];
        if (!empty($category_products)) {
            $products = Product::where('enabled', 1)->whereIn('id', $category_products)->get()->sortBy('index_num')->keyBy('id');
            $products = AppServise::ProductsShopPrepeare($products, $categories);
        } else {
            $products = false;
        }


        return view("shop.new.category_master", [
            'v' => $v,
            'banner' => $request->banner,
            'client' => $client,
            'lang' => $lang,
            'filter' => $filter,
            'categories' => $categories,
            'products' => $products,
            'category_active' => $category,
            'popapp_message' => $popapp_message,
            'category' => $category,
            'noindex' => $request->noindex
        ]);
    }

    public function categoryLang(Request $request, $lang, $category)
    {
        return $this->categoryView($request, $category, $lang);
    }


    public function ProductView(Request $request, $category_slag, $product_slag, $lang = 'en')
    {
        App::setLocale($lang);

        $v = $this->v;

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $product = Product::where('slag', $product_slag)->first();
        $products_data[] = $product;
        $products = AppServise::ProductsShopPrepeare($products_data, $categories);
        $product = $products[0];
        if (!$product) {
            return $this->err404($request, $lang);
        }

        $products_ids = [];
        foreach ($categories as $category) {
            $products_items = json_decode($category->products);
            if ($products_items) {
                $products_ids = array_merge($products_ids, $products_items);
            }
        }

        $products = Product::where('enabled', 1)->whereIn('id', $products_ids)->get()->sortBy('index_num')->keyBy('id');

        $products = AppServise::ProductsShopPrepeare($products, $categories);
        $rand_keys = array_rand($products->toArray(), 4);
        $category = Categories::where('slag', $category_slag)->first();
        $category_data = json_decode($category->data, true);
        $category->translate = json_decode($category->translate, true);


        if (empty($product->image)) {
            $product->image = $category->image;
        }


        if (!empty($product->variables)) {
            if (sizeof($product->variables) > 1) {
                $variables = $product->variables;
                foreach ($variables as &$variant) {
                    $v_price = $variant['defaultDisplayedPrice'];
                    if ($v_price < $product->price) {
                        $product->price = $v_price;
                    }
                }
                $product->variables = $variables;
            }
        }


//        dd($product->options, $product->variables);


        return view("shop.new.product_master", [
            'v' => $v,
            'lang' => $lang,
            'categories' => $categories,
            'category' => $category,
            'category_data' => $category_data,
            'products' => $products,
            'rand_keys' => $rand_keys,
            'product' => $product,
            'noindex' => $request->noindex
        ]);
    }

    public function ProductLang(Request $request, $lang, $category, $product)
    {
        return $this->ProductView($request, $category, $product, $lang);
    }

    public function CartView(Request $request, $lang = 'en', $step = 1)
    {
        App::setLocale($lang);

        $post = $request->post();
        $orderData = false;

        if ($step == 2 ) {


            if (!empty($post)) {
                $pattern_phone = "/^[+0-9]{2,4} \([0-9]{3}\) [0-9]{3} [0-9]{2} [0-9]{2,4}$/";
                $validate_array = [
                    'clientName' => 'required',
                    'clientLastName' => 'required',
                    'phone' => 'required|regex:'.$pattern_phone,
                    'email' => 'required|email:rfc,dns',
                    'order_data' => 'required|json'
                ];

                if (!empty($post['clientBirthDay'])) {
                    $validate_array['clientBirthDay'] = 'required|date_format:Y-m-d';
                }

                $this->validate($request, $validate_array);

                $post['email'] = strtolower($post['email']);
                $post['email'] = str_replace(' ', '', $post['email']);
                $post['order_data'] = json_decode($post['order_data'], true);


                if (empty($post['order_data']['products'])) {
                    $post['order_data'] = '';
                    $messages = [
                        'order_data.required' => __('shop-cart.пустая корзина'),
                    ];
                    $validate_array['order_data'] = "required";
                    Validator::make($post, $validate_array, $messages )->validate();
                }

                /////////////////////////////////////////////

                $phone = $post['phone'];
                $phone = OrderService::phoneAmoFormater($phone);
                $client = Clients::firstOrNew([
                    'email' => $post['email']
                ]);
                $data = json_decode($client->data, true);
                if (empty($client->name)) {
                    $client->name = $post['clientName'];
                }
                if (empty($client->phone)) {
                    $client->phone = $post['phone'];
                }
                if (isset($data['phones'])) {
                    $phones = $data['phones'];
                    $test_phones = array_reverse($phones);
                    if (!isset($test_phones[$phone])) {
                        $phones[] = $phone;
                    }
                    $data['phones'] = $phones;
                } else {
                    $data['phones'][] = $phone;
                }

                if($post['clientBirthDay']) {

                    $birth_day = AppServise::dateFormater($post['clientBirthDay']);
                    if ($birth_day) {
                        $data['clientBirthDay'] = $birth_day;
                        $data['clientBirthDayStr'] = $post['clientBirthDay'];
                    } else {
                        $data['clientBirthDayStr'] = $post['clientBirthDay'];
                    }
                }

                $client->data = json_encode($data);

                $client->save();
                session(['client' => $client]);
                ////////////////////////////////////////////


                if (isset($post['order_id'])) {
                    $order = Orders::where('order_id', $post['order_id'])->first();

                    if (!$order) {
                        $order = new Orders();
                        $order_id = rand(100, 999);
                        $order->order_id = AppServise::generateOrderId($order_id, 'S');
                    }

                } else {
                    $order = new Orders();
                    $order_id = rand(100, 999);
                    $order->order_id = AppServise::generateOrderId($order_id, 'S');
                }
                WebhookLog::addLog('new order step 1', $order->order_id);


                $orderData = OrderService::getShopOrderData($post);
                $order->clientId = $client->id;
                if (isset($post['gClientId'])) {
                    $order->gclientId = $post['gClientId'];
                }
                $order->paymentMethod = 0;
                $order->paymentStatus = 0;
                $order->orderPrice = $orderData['order_data']['order_total'];
                $order->orderData = json_encode($orderData);
                $order->save();
                session(['order_id' => $order->order_id]);

            }

        } elseif($step == 3 ) {

            WebhookLog::addLog('new order step 2', $post);

            $validate_array = [
                'date' => 'required|date_format:Y-n-j',
                'order_data' => 'required|json'
            ];

            $this->validate($request, $validate_array);

            unset($validate_array['order_data']);

            $post['order_data'] = json_decode($post['order_data'], true);


            if (empty($post['order_data']['products'])) {
                $post['order_data'] = '';
                $messages = [
                    'order_data.required' => __('shop-cart.пустая корзина'),
                ];
                $validate_array['order_data'] = "required";
                Validator::make($post, $validate_array, $messages )->validate();
            }


            if ($post['delivery'] == 'delivery') {
                $delivery_json = Storage::disk('local')->get('js/delivery.json');
                $cityes_json = Storage::disk('local')->get('js/israel-city.json');
                $cityes = json_decode($cityes_json, true);
                $delivery_setting = json_decode($delivery_json, true);
                $order_data = json_decode($post['order_data'], true);

                $city_pattern = 'no_city';
                if (!empty($post['city_id'])) {
                    if (isset($delivery_setting['cityes_data'][$post['city_id']])) {
                        $city_names = $cityes['citys_all'][$post['city_id']];
                        foreach ($city_names as $city_name) {
                            if ($post['city'] == $city_name) {
                                $city_pattern = $city_name;
                            }
                        }
                    }
                } else {
                    if ($post['city']) {
                        foreach ($cityes['citys_all'] as $k => $item) {
                            if ($item['ru'] == $post['city'] || $item['en'] == $post['city'] || $item['he'] == $post['city']  ) {
                                $post['city_id'] = $k;
                            }
                        }
                    }
                }

                $data_price['order_data'] = json_decode($post['order_data'], true);
                $data_price = OrderService::getShopOrderData($data_price);
                $order_price = $data_price['order_data']['order_total'];

                $deliv_id = $delivery_setting['cityes_data'][$post['city_id']][0];

                $delivery = $delivery_setting['delivery'][$deliv_id];

                $min_summ_order = $delivery['min_sum_order'];
                if ($order_price < $min_summ_order) {
                    $messages = [
                        'min_summ_order.required' => __('shop-cart.минимальная сумма заказа') . ' ' . $min_summ_order . ' ₪ !',
                    ];
                    $validate_array['min_summ_order'] = "required";
                    Validator::make($post, $validate_array, $messages )->validate();
                }

                if (empty($delivery['rate_delivery_to_summ_order'])) {
                    $post['order_data']['delivery_price'] = $delivery['rate_delivery'];
                    if (!empty($post['time']) && preg_match('/[0-9]{2}:00-[0-9]{2}:00/', $post['time'])) {
                        $post['order_data']['delivery_price'] += $post['order_data']['delivery_price'] / 100 * 30;
                        $post['order_data']['delivery_price'] = round($post['order_data']['delivery_price'], 2);
                    }
                }


                $validate_array['street'] = 'required';
                $validate_array['house'] = 'required';
            }

            if(isset($post['otherPerson'])) {
                $validate_array['nameOtherPerson'] = 'required';
                $validate_array['phoneOtherPerson'] = 'required';
            }

            $this->validate($request, $validate_array);

            $order = Orders::where('order_id', $post['order_id'])->first();
            if ($order) {
                $order_data = json_decode($order->orderData, true);

                $orderData = OrderService::getShopOrderData($post);

                foreach ($orderData as $k => $v) {
                    $order_data[$k] = $v;
                }
                $order_data['order_data_jsonform'] = $post['order_data'];

                $order->orderPrice = $orderData['order_data']['order_total'];
                $order->orderData = json_encode($order_data);
                $order->save();
            }

        }

        $order_number = false;
        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $products_ids = [];
        foreach ($categories as $category) {
            $products_items = json_decode($category->products);
            if ($products_items) {
                $products_ids = array_merge($products_ids, $products_items);
            }
        }

        $products = Product::where('enabled', 1)->whereIn('id', $products_ids)->get()->sortBy('index_num')->keyBy('id');
        $products = AppServise::ProductsShopPrepeare($products, $categories);
        $rand_keys = array_rand($products->toArray(), 15);


        $order_id = session('order_id');

        if ($order_id) {
            $order_number = $order_id;
        }

        $cityes = Storage::disk('local')->get('js/israel-city.json');
        $cityes = json_decode($cityes, true);

        $delivery =  Storage::disk('local')->get('js/delivery.json');
        $delivery = json_decode($delivery, true);


        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        ////////////////////////////////////////////////////

        $ip = $request->ip();
        $user_country = AppServise::getCountryFromIP($ip);
        $all_countries = AppServise::getAllCountries();

        return view("shop.new.cart-master", [
            'v' => $this->v,
            'lang' => $lang,
            'step' => $step,
            'categories' => $categories,
            'products' => $products,
            'rand_keys' => $rand_keys,
            'order_number' => $order_number,
            'order_data' => $orderData,
            'shop_setting' => $shop_setting,
            'delivery' => $delivery,
            'cityes' => $cityes,
            'post' => $post,
            'noindex' => $request->noindex,
            'user_country' => $user_country,
            'all_countries' => $all_countries
        ]);
    }


    public function NewOrder(Request $request, $lang = 'en')
    {
        $post = $request->post();

        if (!empty($post)) {

            $order = Orders::where('order_id', $post['order_id'])->first();
            $order_data = json_decode($order->orderData, true);

            $order_data['order_data'] = $order_data['order_data_jsonform'];
            if ($post['premium'] != '0') {
                $order_data['premium'] = $post['premium'];
            }

            $orderData = OrderService::getShopOrderData($order_data);

            $orderData['client_comment'] = $post['client_comment'];
            $orderData['methodPay'] = $post['methodPay'];


            $order->paymentMethod = $orderData['methodPay'];
            $order->paymentStatus = 3;
            $order->orderPrice = $orderData['order_data']['order_total'];
            $order->orderData = json_encode($orderData);
            $order->save();

            session(['last_order_id' => $order->order_id]);

            if ($order->paymentMethod == 2 || $order->paymentMethod == 4) {

                return redirect(route("order_thanks", ['lang'=> $lang]));

            } elseif ($order->paymentMethod == 1) {

                $icreditOrderData = OrderService::getShopIcreditOrderData($order);
                $iCreditService = new IcreditServise();
                $result = $iCreditService->getUrl($icreditOrderData);
                if (!empty($result['URL'])) {
                    session('orderPay', $result);
                    return redirect($result['URL']);
                } else {

                    session()->flash('message', ['error get payment url']);
                    return redirect(route("order_thanks", ['lang'=> $lang]));
                }

            } elseif ($order->paymentMethod == 3) {

                return redirect(route('paypal_button', ['order_id' => $order->order_id]));

            } else {

                dd($order->toArray());
            }
        }

    }


    public function getButtonPaypal(Request $request)
    {

        $order_id = $request->get('order_id');
        $order = false;
        $paypalService = new PayPalService('live');
        $client_id = $paypalService->getClientId();


        if ($order_id) {
            $order = Orders::where('order_id', $order_id)->first();
        }

        $orderData = json_decode($order->orderData, true);

        if (isset($orderData['order_data'])) {
            if ($orderData['clientName'] == 'test') {
                $orderData['order_data']['order_total'] = 1;
            }
        }
        $lang = $orderData['lang'];

        return view('paypal.button', [
            'v' => $this->v,
            'noindex' => $request->noindex,
            'client_id'  => $client_id,
            'orderData'  => $orderData,
            'order_id'   => $order_id,
            'lang'       => $lang
        ]);
    }

    public function OrderThanksView(Request $request, $lang)
    {
        App::setLocale($lang);

        $v = $this->v;
        $order_id = session('last_order_id');

        $test = $request->get('test');
        if (isset($test)) {
            $post = $request->post();
            $data = json_decode($post['data'], true);
            $order_id = $data['order_id'];
            $order = Orders::where('order_id', $order_id)->first();

            dd($order);
        }


        if ($order_id) {
            $order = Orders::where('order_id', $order_id)->first();
            WebhookLog::addLog('OrderThanksView last order', $order);
        } else {
            $order = [
                'orderPrice' => 0,
                'order_id' => '',
                'paymentMethod' => 3,
            ];
            $order = json_decode(json_encode($order));
        }


        $OrderService = new OrderService();
        $OrderService->changeProductsCount($order);


        $client_id = $order->clientId;
        $client = Clients::find($client_id);

        $client = $client->toArray();
        if (is_string($client['data'])) {
            $client['data'] = json_decode($client['data'], true);
        }

        // настройки аккаунта для инвойса
        $dataJson = Storage::disk('local')->get('data/app-setting.json');
        $invoiceSettingData = json_decode($dataJson, true);

        session(['order_id' => false]);

        return view("shop.new.order_thanks", [
            'v' => $v,
            'lang' => $lang,
            'order' => $order,
            'client' => $client,
            'noindex' => $request->noindex,
            'invoiceSettingData' => $invoiceSettingData
        ]);
    }

    public function deliveryIndex(Request $request, $lang = 'en')
    {
        App::setLocale($lang);

        $v = $this->v;
        $popapp_message = session('message_popapp');

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);

        $cityes = Storage::disk('local')->get('js/israel-city.json');
        $cityes = json_decode($cityes, true);

        $delivery =  Storage::disk('local')->get('js/delivery.json');
        $delivery = json_decode($delivery, true);

//        dd($cityes, $delivery);

        return view('shop.new.delivery-master', [
            'v' => $v,
            'banner' => $request->banner,
            'lang' => $lang,
            'delivery' => $delivery,
            'cityes' => $cityes,
            'categories' => $categories,
            'popapp_message' => $popapp_message,
            'noindex' => $request->noindex
        ]);
    }

    public function aboutIndex(Request $request, $lang = 'en')
    {
        App::setLocale($lang);

        $v = $this->v;
        $popapp_message = session('message_popapp');

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);


        return view('shop.new.page_master', [
            'v' => $v,
            'page' => 'about',
            'banner' => $request->banner,
            'lang' => $lang,
            'categories' => $categories,
            'popapp_message' => $popapp_message,
            'noindex' => $request->noindex
        ]);
    }

    public function contactsIndex(Request $request, $lang = 'en')
    {
        App::setLocale($lang);

        $v = $this->v;
        $popapp_message = session('message_popapp');

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);


        return view('shop.new.page_master', [
            'v' => $v,
            'page' => 'contacts',
            'banner' => $request->banner,
            'lang' => $lang,
            'categories' => $categories,
            'popapp_message' => $popapp_message,
            'noindex' => $request->noindex
        ]);
    }

    private function marketView(Request $request, $lang)
    {
        App::setLocale($lang);

        $v = $this->v;

        $client = session('client');

        $popapp_message = session('message_popapp');

        $categories = Categories::where('name', 'Market')->get()->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);

        foreach ($categories as $item) {
            $product_ids = json_decode($item->products, true);

            foreach ($product_ids as $id) {
                $products[$id] = Product::where('id', $id)->first();
            }
        }
        $products = AppServise::ProductsShopPrepeare($products, $categories);

        $cityes = Storage::disk('local')->get('js/israel-city.json');
        $cityes = json_decode($cityes, true);

        $delivery =  Storage::disk('local')->get('js/delivery.json');
        $delivery = json_decode($delivery, true);




        $category_active = $request->get('category');
        if (!$category_active) {
            $category_active = 'Market';
        }


        return view("shop.$lang.market", [
            'v' => $v,
            'banner' => $request->banner,
            'client' => $client,
            'lang' => $lang,
            'categories' => $categories,
            'products' => $products,
            'delivery' => $delivery,
            'cityes' => $cityes,
            'category_active' => $category_active,
            'popapp_message' => $popapp_message,
            'noindex' => $request->noindex
        ]);
    }

    public function marketRU(Request $request)
    {
        $lang = 'ru';
        return $this->marketView($request, $lang);
    }

    public function marketEn(Request $request)
    {
        $lang = 'en';
        return $this->marketView($request, $lang);
    }

    public function marketShortView(Request $request, $lang)
    {
        App::setLocale($lang);

        $v = $this->v;
        $popapp_message = session('message_popapp');
        $categories = Categories::where('name', 'Market')->get()->keyBy('id');
        $categories = AppServise::CategoriesShopPrepeare($categories);


        return view("shop.$lang.short-market", [
            'v' => $v,
            'lang' => $lang,
            'categories' => $categories,
            'popapp_message' => $popapp_message,
            'noindex' => $request->noindex
        ]);
    }

    public function marketShortEn(Request $request)
    {
        $lang = 'en';
        return $this->marketShortView($request, $lang);
    }

    public function marketShortRu(Request $request)
    {
        $lang = 'ru';
        return $this->marketShortView($request, $lang);
    }

    public function ProductRuOld(Product $product, Request $request)
    {
        $category = Categories::find($product->category_id);
        return redirect(route('product', ['category' => $category->slag, 'product' => $product->slag]), 301);
    }

    public function ProductEnOld(Product $product, Request $request)
    {
        $category = Categories::find($product->category_id);
        return redirect(route('product_en', ['category' => $category->slag, 'product' => $product->slag]), 301);
    }

    public function changeProductCount(Request $request)
    {
        $post = $request->post();
        $id = $post['id'];
        $token_key = $post['token_key'];
        $product = Product::find($id);
        $date = new Carbon();

        print_r($post);
        if (isset($post['variant'])) {
            $var_key = $post['variant'];
            $variables = json_decode($product->variables, true);
            $variant = $variables[$var_key];


            unset($variant['quantity_orders']);
            if ($post['product_count'] > 0) {
                $variant['quantity_orders'][$token_key] = [
                    'date' => $date->format('Y-m-d H:i:s'),
                    'product_count' => $post['product_count']
                ];
            } else {
                unset ($variant['quantity_orders'][$token_key]);
            }

            $variables[$var_key] = $variant;
            $product->variables = json_encode($variables);
            print_r($variant);
        } else {
            $data = json_decode($product->data, true);
            unset($data['product_ekwid']);
            if ($post['product_count'] > 0) {
                $data['quantity_orders'][$token_key] = [
                    'date' => $date->format('Y-m-d H:i:s'),
                    'product_count' => $post['product_count']
                ];
            } else {
                unset ($data['quantity_orders'][$token_key]);
            }

            print_r($data);
        }

        $product->save();

    }


    public function NewShortOrder(Request $request)
    {

        $post = $request->post();
        dd($post);

        $post['phone'] = str_replace('_', '', $post['phone']);

        $validate_array = [
            'order_price' => 'required|integer|min:5',
            'clientName' => 'required',
            'email' => 'required|email:rfc,dns'
        ];

        if ($post['phone'] == '+972 --') {
            unset($post['phone']);
        } else {

        }


        $this->validate($request, $validate_array);


        if (!empty($post)) {

            WebhookLog::addLog('new order shop post ', $post);

            $post['email'] = strtolower($post['email']);
            $post['email'] = str_replace(' ', '', $post['email']);


            /////////////////////////////////////////////

            if (isset($post['phone'])) {
                $phone = $post['phone'];
                $phone = OrderService::phoneAmoFormater($phone);
            }
            $client = Clients::firstOrNew([
                'email' => $post['email']
            ]);
            $data = json_decode($client->data, true);
            if (empty($client->name)) {
                $client->name = $post['clientName'];
            }
            if (empty($client->phone)) {
                $client->phone = $post['phone'];
            }
            if (isset($data['phones']) && isset($post['phone'])) {
                $phones = $data['phones'];
                $test_phones = array_reverse($phones);
                if (!isset($test_phones[$phone])) {
                    $phones[] = $phone;
                }
                $data['phones'] = $phones;
            } else {
                if(isset($post['phone'])) {
                    $data['phones'][] = $phone;
                }
            }
            if($post['clientBirthDay']) {

                $birth_day = AppServise::dateFormater($post['clientBirthDay']);
                if ($birth_day) {
                    $data['clientBirthDay'] = $birth_day;
                } else {
                    $data['clientBirthDayStr'] = $post['clientBirthDay'];
                }
            }

            $client->data = json_encode($data);

            $client->save();
            session(['client' => $client]);
            ////////////////////////////////////////////


            if (isset($post['order_id'])) {
                $order = Orders::where('order_id', $post['order_id'])->first();
            } else {
                $order = new Orders();
                $order_id = rand(100, 999);
                $order->order_id = AppServise::generateOrderId($order_id, 'U');
            }

            session(['order' => $order]);
            $lang = $post['lang'];

            $order_total = $post['order_price'] + ($post['order_price'] * $post['premium'] / 100);

            $orderData = $post;
            $orderData['order_data']['order_total'] = $order_total;
            $orderData['order_data']['tips'] = ($post['order_price'] * $post['premium'] / 100);

            $orderData['order_data']['products'][] = [
                'id' => $order->order_id,
                'sku' => "0-".$order->order_id,
                'count' => 1,
                'price' => $post['order_price'],
                'name' => [
                    'en' => "Deserts",
                    'ru' => "Deserts",
                    'he' => "Deserts"
                ]
            ];
            $order->clientId = $client->id;
            $order->paymentMethod = $orderData['methodPay'];
            $order->paymentStatus = 3;
            $order->orderPrice = $order_total;
            $order->orderData = json_encode($orderData);
            $order->save();

            WebhookLog::addLog('new order shop ' . $order->order_id, $order);

            if ($order->paymentMethod == 2 || $order->paymentMethod == 4) {

                return redirect(route("order_thanks_$lang"));

            } elseif ($order->paymentMethod == 1) {




                $icreditOrderData = OrderService::getShopIcreditOrderData($order);
                $iCreditService = new IcreditServise();
                $result = $iCreditService->getUrl($icreditOrderData);
                if (!empty($result['URL'])) {
                    session('orderPay', $result);
                    return redirect($result['URL']);
                } else {

                    session()->flash('message', ['error get payment url']);
                    return redirect(route("order_thanks_$lang"));
                }

            } elseif ($order->paymentMethod == 3) {

                return redirect(route('paypal_button', ['order_id' => $order->order_id]));

            } else {

                dd($order->toArray());
            }
        }

    }


    public function links()
    {
        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');

        $lang = 'en';
        print_r("<p>lang - $lang</p>");
        foreach ($categories as $category) {

            $translate = json_decode($category->translate, true);

            if (isset($translate['nameTranslated'][$lang])) {
                $name = $translate['nameTranslated'][$lang];
            } else {
                $name = $translate['nameTranslated']['en'];
            }

            print_r("<p><b>$name</b> - ");
            print_r(route("index_$lang", ['category' => $category->slag]));
            print_r("</p><hr>");
        }

        $lang = 'ru';
        print_r("<p>lang - $lang</p>");
        foreach ($categories as $category) {

            $translate = json_decode($category->translate, true);

            if (isset($translate['nameTranslated'][$lang])) {
                $name = $translate['nameTranslated'][$lang];
            } else {
                $name = $translate['nameTranslated']['en'];
            }

            print_r("<p><b>$name</b> - ");
            print_r(route("index_$lang", ['category' => $category->slag]));
            print_r("</p><hr>");
        }
    }


    public function OrderThanksRu(Request $request, $lang)
    {
        $lang = 'ru';

        return $this->OrderThanksView($request, $lang);
    }

    public function OrderThanksEn(Request $request)
    {
        $lang = 'en';

        return $this->OrderThanksView($request, $lang);
    }

    public function WholeSaleView(Request $request, $lang)
    {
        App::setLocale($lang);

        $v = $this->v;
        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');

        return view("shop.$lang.wholesale", [
            'v' => $v,
            'lang' => $lang,
            'categories' => $categories,
            'noindex' => $request->noindex
        ]);
    }

    public function WholeSaleRU(Request $request)
    {
        $lang = 'ru';

        return $this->WholeSaleView($request, $lang);
    }

    public function WholeSaleEn(Request $request)
    {
        $lang = 'en';

        return $this->WholeSaleView($request, $lang);
    }



    public function getPromoCode(Request $request)
    {

        $code = $request->get('promoCode');
        if (!empty($code)) {
            $coupon = Coupons::where('code', $code)->first();


            if (!empty($coupon)) {
                if ($coupon->status == 'active') {
                    $discount = json_decode($coupon->discount, true);
                    $mod = $discount['mod'];
                    if ($mod == 'ABS') {
                        $unit = '₪';
                    } else {
                        $unit = '%';
                    }
                    $result = array (
                        'result' => 'sugess',
                        'id' => $code,
                        'price' => $discount['value'],
                        'name' => $coupon->name,
                        'unit' => $unit,
                        'message' => 'coupon found'
                    );
                } else {
                    $result = array (
                        'result' => 'error',
                        'message' => 'coupon not active'
                    );
                }
            } else {
                $result = array (
                    'result' => 'error',
                    'message' => 'coupon not fount'
                );
            }

        } else {
            $result = array (
                'result' => 'error',
                'message' => 'code not fount'
            );
        }
        echo json_encode($result);
    }

    public function contactForm(Request $request)
    {
        $post = $request->post();
        if (empty($post['g-recaptcha-response'])) {
            print_r('confirm captcha');
            die;
        }
        if (!empty($post['clientName']) && !empty($post['phone']) && !empty($post['question']) && !empty($post['g-recaptcha-response'])) {

            $test_captcha = AppServise::confirmReCaptcha($post['g-recaptcha-response']);
            if ($test_captcha['success']) {
                $AmoService = new AmoCrmServise();
                $res = $AmoService->newLeadBuContactForm($post);
                $AmoService->addTextNotesToLead($res['amo_id'], $post['question']);

                session()->flash('message_popapp', 'showBlock');
                return redirect(route('index_'.$post['lang']));
            }

        }
    }

    public function saveContactBirth(Request $request)
    {
        $post = $request->post();

        $client = Clients::firstOrNew([
            'email' => $post['mailingMail']
        ]);

        if ($client) {
            $clientData = json_decode($client->data, true);

            if(!isset($clientData['birthday'])) {
                $clientData['birthday'] = $post['mailingBirthday'];
                $client->data = json_encode($clientData);
//                $client->save();

                $birth_date = new Carbon($post['mailingBirthday']);
//                dd($post['mailingBirthday']);
                $amoClientData = [
                    'email' => $post['mailingMail'],
                    'birthday' => $birth_date->timestamp
                ];

                $AmoService = new AmoCrmServise();
                $res = $AmoService->createLeadBirthday($amoClientData);


            } else {

                dd($client->toArray());
            }
            dd($client->toArray(), $post, $clientData);
        }

    }



    public function testUtm()
    {
        $utm = session('utm');
        dd($utm);
    }

    public static function sitemapGenerate()
    {

        $sitemap[] = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        $url_en = route('index');
        $url_ru = route('index', ['lang' => 'ru']);
        $url_he = route('index', ['lang' => 'he']);

        $sitemap[] = "<url>
    <loc>$url_en</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"ru\"
               href=\"$url_ru\"/>
  </url>";

        $sitemap[] = "<url>
    <loc>$url_ru</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";

        $sitemap[] = "<url>
    <loc>$url_he</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');

        foreach ($categories as $category) {

            $url_en = route('category_index', ['category' => $category->slag]);
            $url_ru = route('category', ['lang' => 'ru', 'category' => $category->slag]);
            $url_he = route('category', ['lang' => 'he', 'category' => $category->slag]);

            $sitemap[] = "<url>
    <loc>$url_en</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"ru\"
               href=\"$url_ru\"/>
  </url>";

            $sitemap[] = "<url>
    <loc>$url_ru</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";

            $sitemap[] = "<url>
    <loc>$url_he</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";
        }

        $products = Product::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');

        foreach ($products as $product) {

            if ($product->category_id && isset($categories[$product->category_id])) {
                $category = $categories[$product->category_id];

                $url_en = route('product_index', ['category' => $category->slag, 'product' => $product->slag]);
                $url_ru = route('product', ['lang' => 'ru', 'category' => $category->slag, 'product' => $product->slag]);
                $url_ru = route('product', ['lang' => 'he', 'category' => $category->slag, 'product' => $product->slag]);

                $sitemap[] = "<url>
    <loc>$url_en</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"ru\"
               href=\"$url_ru\"/>
  </url>";

                $sitemap[] = "<url>
    <loc>$url_ru</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";

                $sitemap[] = "<url>
    <loc>$url_he</loc>
    <xhtml:link
               rel=\"alternate\"
               hreflang=\"en\"
               href=\"$url_en\"/>
  </url>";
            }

        }


        $sitemap[] = '</urlset>';

        $sitemap_txt = implode('', $sitemap);

        Storage::disk('public_root')->put('site_map.xml', $sitemap_txt);

    }

}
