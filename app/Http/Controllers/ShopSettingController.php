<?php


namespace App\Http\Controllers;


use App\Models\Categories;
use App\Models\Clients;
use App\Models\Coupons;
use App\Models\Orders as OrdersModel;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Statistics;
use App\Models\User;
use App\Models\UtmModel;
use App\Services\AmoCrmServise;
use App\Services\AppServise;
use App\Services\EcwidService;
use App\Services\OrderService;
use App\Services\StatisticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic as ImageManager;

class ShopSettingController extends Controller
{
    private $image_sizes =  ['1500', '800', '400', '160'];

    public function index(Request $request)
    {

        return view('index',  [
                'error_log'      => $request->error_log,
            ]
        );
    }

    public function Orders(Request $request)
    {

        $paymentMethod = AppServise::getOrderPaymentMethod();
        $paymentStatus = AppServise::getOrderPaymentStatus();
        $invoiceStatus = AppServise::getOrderInvoiceStatus();

        $order_id = false;
        $orderSearch = false;


        if (!empty($request->get('order_id'))) {

            $order_id = $request->get('order_id');

            $orderSearch =  DB::table('orders')
                ->where('orders.order_id', $order_id)
                ->join('clients', 'orders.clientId', '=', 'clients.id')
                ->select('orders.*', 'clients.name', 'clients.email')
                ->first();

            if (!$orderSearch) {
                $orderSearch = OrdersModel::where('order_id', $order_id)->first();
            }
            if (!$orderSearch) {
                dd($orderSearch);
            }
        }

        if (!empty($request->get('date-from')) && !empty($request->get('date-to'))) {

            $date_from = new Carbon($request->get('date-from'));
            $date_to = new Carbon($request->get('date-to') . ' 23:59');

        } elseif ($request->get('dates')) {

            if ($request->get('dates') == 'today') {
                $date = new Carbon();
                $date_from = new Carbon($date->format('Y-m-d'));
                $date_to = new Carbon($date->format('Y-m-d 23:59'));

            }
            if ($request->get('dates') == 'month') {

                $date = new Carbon('first day of this month');
                $date_from = new Carbon($date->format('Y-m-d 00:00'));

                $date = new Carbon('last day of this month');
                $date_to = new Carbon($date->format('Y-m-d 23:59'));
            }

        } else {

            if (session()->has('dates')) {
                $dates = session('dates');
                $date_from = $dates['date_from'];
                $date_to = $dates['date_to'];

            } else {

                $date_from = new Carbon('first day of this month' . ' 00:00');
                $date_to = new Carbon('last day of this month' . ' 23:59');
            }

        }


        $dates['date_from'] = $date_from;
        $dates['date_to'] = $date_to;
        session(['dates' => $dates]);
        session()->save();

        // для таблицы
        $orders = DB::table('orders')
            ->where('orders.deleted_at', null)
            ->whereBetween('orders.created_at', [$date_from, $date_to])
            ->latest('orders.id')
            ->join('clients', 'orders.clientId', '=', 'clients.id')
            ->select('orders.*', 'clients.name', 'clients.email');




        if ($request->get('filter')) {
            $filter = $request->get('filter');
            $orders->where('orders.paymentMethod', $filter['method'])
                ->where('orders.paymentStatus', $filter['status']);
        }

//        $orders = $orders->get();
//
//        foreach ($orders as $order) {
//            $order_data = json_decode($order->orderData, true);
//            if (isset($order_data['step']) && $order_data['step'] == 4) {
//                if ($order_data['delivery_method'] == 'pickup') {
//                    $delivery_price = 0;
//                    $adress = 'самовывоз';
//                } else {
//                    $delivery_price = $order_data['order_data']['delivery_price'];
//
//                    $city = AppServise::getCityNameByLang($order_data['city'], 'ru');
//
//                    $adress = $city;
//
//                }
//
//                $products = $order_data['order_data']['products'];
//
//                foreach ($products as $item) {
//
//                    $dates = explode(' ', $order->created_at);
//
//                    $data[] = [
//                        'дата заказа' => $dates[0],
//                        'наименование' => $item['name']['ru'],
//                        'количество' => $item['count'],
//                        'цена' => $item['price'],
//                        'сумма' => $item['price'] * $item['count'],
//                        'адрес доставки' => $adress,
//                        'цена доставки' => $delivery_price,
//                    ];
//                }
//
//            } else {
////                if (!isset($order_data['step'])) {
////                    dd($order_data);
////                }
//            }
//        }
//
//        $str = 'дата заказа;наименование;количество;цена;сумма;адрес доставки;цена доставки';
//
//        Storage::put('csv-import/delivery-products.csv', $str);
//
//        foreach ($data as $item) {
//            $str = implode(';', $item);
//            Storage::append('csv-import/delivery-products.csv', $str);
//        }
//
//        $content = Storage::get('csv-import/delivery-products.csv');
//
//
//        dd($content);

        $orders = $orders->paginate(10);

        $utm_orders = UtmModel::whereBetween('created_at', [$date_from, $date_to])->get()->keyBy('order_id')->toArray();


        // статистика
        $paydPeriodInfo['заказов'] = DB::table('orders')
            ->where('orders.deleted_at', null)
            ->whereBetween('orders.created_at', [$date_from, $date_to])
            ->whereBetween('orders.paymentStatus', [2, 4])->count('id');


        foreach ($paymentMethod as $kpm => $method_name) {
            foreach ($paymentStatus as $kps => $status) {
                $summ = DB::table('orders')
                    ->where('orders.deleted_at', null)
                    ->whereBetween('orders.created_at', [$date_from, $date_to])
                    ->where('orders.paymentMethod', $kpm)
                    ->where('orders.paymentStatus', $kps)->sum('orderPrice');

                if ($summ > 0) {
                    $paydPeriodInfo['orders'][$kpm][$kps]['summ'] = $summ;
                    $paydPeriodInfo['orders'][$kpm][$kps]['count'] = DB::table('orders')
                        ->where('orders.deleted_at', null)
                        ->whereBetween('orders.created_at', [$date_from, $date_to])
                        ->where('orders.paymentMethod', $kpm)
                        ->where('orders.paymentStatus', $kps)->count('id');
                }
            }
        }



        $date_start = new Carbon('first day of this month');
        $date_end = new Carbon('last day of this month');

        $priceMonth = OrdersModel::whereBetween('created_at', [$date_start, $date_end])
            ->sum('orderPrice');

        $priceYear = OrdersModel::whereYear('created_at', $date_start->format('Y'))
            ->sum('orderPrice');

//        dd($request);

        return view('shop-settings.orders', [
            'error_log'      => $request->error_log,
            'orders'         => $orders,
            'paymentMethod'  => $paymentMethod,
            'paymentStatus'  => $paymentStatus,
            'invoiceStatus'  => $invoiceStatus,
            'priceMonth'     => $priceMonth,
            'paydPeriodInfo' => $paydPeriodInfo,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
            'priceYear'      => $priceYear,
            'order_id'       => $order_id,
            'orderSearch'    => $orderSearch,
            'message'        => $request->message,
            'utm_orders'     => $utm_orders
        ]);
    }

    public function OrderSegments(Request $request)
    {

        if (!empty($request->get('date-from')) && !empty($request->get('date-to'))) {

            $date_from = new Carbon($request->get('date-from'));
            $date_to = new Carbon($request->get('date-to') . ' 23:59');

        } elseif ($request->get('dates')) {

            if ($request->get('dates') == 'today') {
                $date = new Carbon();
                $date_from = new Carbon($date->format('Y-m-d'));
                $date_to = new Carbon($date->format('Y-m-d 23:59'));

            }
            if ($request->get('dates') == 'month') {

                $date = new Carbon('first day of this month');
                $date_from = new Carbon($date->format('Y-m-d 00:00'));

                $date = new Carbon('last day of this month');
                $date_to = new Carbon($date->format('Y-m-d 23:59'));
            }

        } else {

            if (session()->has('dates')) {
                $dates = session('dates');
                $date_from = $dates['date_from'];
                $date_to = $dates['date_to'];

            } else {

                $date_from = new Carbon('first day of this month' . ' 00:00');
                $date_to = new Carbon('last day of this month' . ' 23:59');
            }

        }

        $ordersAll = DB::table('orders')
            ->where('orders.deleted_at', null)
            ->whereBetween('orders.created_at', [$date_from, $date_to])
            ->latest('orders.id')
            ->get();


        $categories = Categories::all()->keyBy('id')->toArray();
        $isset_products = [];
        $data = [];
        foreach ($ordersAll as $item) {
            $orderData = json_decode($item->orderData, true);

            if (isset($orderData['step']) && $orderData['step'] == 4) {
                $products = $orderData['order_data']['products'];

                $phone = preg_replace("/[)( ]/", '', $orderData['phone']);


                foreach ($products as $pr_item) {


                    if (!isset($isset_products[$pr_item['id']])) {
                        $product = Product::find($pr_item['id']);
                        $isset_products[$pr_item['id']] = $product;
                    } else {
                        $product = $isset_products[$pr_item['id']];
                    }


                    if (sizeof($products) > 1) {
                        $name_cat = 'Больше 1 позиции в чеке';
                        $data['categoryes'][$name_cat][$phone][] = $product->name;
                    }


                    if (isset($product->name)) {
                        $data['products'][$product->name][$phone][] = $phone;
                    }


                    if ($product && $product->category_id) {
                        $category = $categories[$product->category_id];

                        $data['categoryes'][$category['name']][$phone][] = $phone;
                    }

                }
            }

        }


        if ($request->has('download')) {
            $name = $request->get('download');
            $type = $request->get('type');

            $save_data = $data[$type][$name];

            $file_name = str_replace(' ', '_', $name).'.csv';
            $file_csv_path = '/order-segments-files/'.$file_name;

            Storage::put($file_csv_path, 'phone;count;');

            foreach ($save_data as $phone => $v) {
                $size = sizeof($v);
                $str = '';
                if ($name == 'Больше 1 позиции в чеке') {
                    $str = implode(', ', $v);
                }
                Storage::append($file_csv_path, "$phone;$size;$str");
            }

            return Storage::disk('local')->download($file_csv_path);
        }



        return view('shop-settings.order-segments', [
            'error_log'      => $request->error_log,
            'message'        => $request->message,
            'segments'       => $data,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
        ]);
    }


    public function Categories(Request $request)
    {


        $categories = Categories::all()->sortBy('index_num')->keyBy('id');
        $products = Product::all()->sortBy('index_num')->keyBy('id');


        return view('shop-settings.categories', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'categories' => $categories,
            'products' => $products
        ]);

    }


    public function Products(Request $request)
    {

        $categories = Categories::all()->sortBy('index_num')->keyBy('id');
        $products = Product::all()->sortBy('index_num')->keyBy('id');

        $product_options = ProductOptions::all()->keyBy('id')->toArray();

        foreach ($product_options as &$item) {
            $item['options'] = json_decode($item['options'], true);
        }
        foreach ($products as $item) {
            if (!$item->category_id)
            $empty_categories[] = $item->id;
        }


        return view('shop-settings.products', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'categories' => $categories,
            'products' => $products,
            'empty_categories' => $empty_categories
        ]);

    }

    public function DeyOffer(Request $request)
    {

        $post = $request->post();
        if (!empty($post)) {
            $offer_id = $post['dey_offer_id'];
            $title = $post['title'];
            $dey_offer_data = [
                'id' => $offer_id,
                'title' => $title
            ];

            Storage::disk('local')->put('data/dey-offer.json', json_encode($dey_offer_data));
            $message[] = 'dey offer save';
        }

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $products = Product::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');
        $products = AppServise::ProductsShopPrepeare($products, $categories);



        $dey_offer_data = false;
        if (Storage::disk('local')->exists('data/dey-offer.json')) {
            $dey_offer_json = Storage::disk('local')->get('data/dey-offer.json');
            $dey_offer_data = json_decode($dey_offer_json, true);
        }



        return view('shop-settings.dey_offer', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'categories' => $categories,
            'products' => $products,
            'dey_offer_data' => $dey_offer_data
        ]);
    }


    public function migration(Request $request)
    {

        $command = $request->get('command');
        if ($command) {
            Artisan::call('migrate:' . $command);
        } else {
            Artisan::call('migrate');
        }


        echo 'done';
    }

    public function sortableSave(Request $request)
    {
        $save = $request->get('event_sortable');
        $name = $request->get('name');
        $sort = $request->get('sort');
        if (!empty($save) && !empty($name)) {
            if ($name == 'categories') {
                $models = Categories::all();

                foreach ($sort as $id => $num) {
                    $model = $models->find($id);
                    $model->index_num = $num;
                    $model->save();
                }

                session()->flash('message', ["sortable $name save"]);

                return redirect(route('shop_settings_categories'));

            } elseif ($name == 'product_galery') {
                $product = Product::where('id', $request->get('product_id'))->first();
                $image_galery = json_decode($product->galery, true);

//                foreach ($image_galery as &$data) {
//                    foreach ($data as &$item) {
//                        $item = str_replace('jpg', 'webp', $item);
//                    }
//                }

                foreach ($sort as $image_key => $v) {
                    $new_galery[] = $image_galery[$image_key];
                }

                $product->galery = json_encode($new_galery);
                $product->image = json_encode($new_galery[0]);
                $product->save();

                session()->flash('message', ["image sort"]);

                return back();

            } else {
                dd($name, $sort);

            }

        }
    }


    public function imageDownload(Request $request)
    {

        if ($request->hasFile('image')) {

            $request->validate([
                'image'     => 'required|image|mimes:jpeg,jpg,png,webp'
            ]);

            $file = $request->file('image');

            $file_name = $file->getClientOriginalName();
            $image_name = time();
            $path = 'public/images';
            if (Storage::exists("$path/$image_name.webp")) {
                session()->flash('message', ["image for this name isset, please rename download file"]);

                return back();
            }
            Storage::putFileAs($path, $file, $file_name);
            $file_path = $path.'/'.$file_name;
            $source = str_replace('public', 'storage', $file_path);

            $new_image = "storage/images/$image_name.webp";
            $pathes['originalImageUrl'] = '/'.$new_image;

            ImageManager::make($source)
                ->encode('webp', 100)
                ->save($new_image, 80);

            $images_sizes = $this->image_sizes;

            foreach ($images_sizes as $size) {

                $new_image = "storage/images/$size/$image_name.webp";
                $key = "image".$size."pxUrl";
                $pathes[$key] = '/'.$new_image;

                ImageManager::make($source)
                    ->resize($size, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
//                    ->crop($size, $size)
                    ->encode('webp', 100)
                    ->save($new_image, 100);
            }
            if (!preg_match("/webp$/", $file_name)) {
                Storage::delete($path."/".$file_name);
            }

            $image_to = $request->get('image_to');
            $id = $request->get('id');

            if ($image_to == 'category') {

                $category = Categories::where('id', $id)->first();
                $category_image_data = json_decode($category->image, true);

                if ($category_image_data) {
                    foreach ($category_image_data as $old_image) {

                        $old_image = str_replace('storage', 'public', $old_image);
                        if (Storage::exists($old_image)) {
                            $res = Storage::delete($old_image);
                        }
                    }
                }

                $category->image = json_encode($pathes);
                $category->save();

                session()->flash('message', ["image download"]);

                return redirect(route('shop_settings_categories'));

            }

            if ($image_to == 'product') {
                $product = Product::where('id', $id)->first();

                $image_galery = json_decode($product->galery, true);
                $image_galery[] = $pathes;

                $product->galery = json_encode($image_galery);
                $product->image = json_encode($image_galery[0]);
                $product->save();

                session()->flash('message', ["image add"]);

                return redirect(route('product_redact', ['product' => $product]));

            }
        }

    }

    public function imageTest(Request $request)
    {
        dd('stop');
        $products = Product::all();

        foreach ($products as $product) {
            $images = json_decode($product->galery, true);
            $flag = false;
            if ($images) {
                foreach ($images as $ki => &$item) {
                    foreach ($item as &$path) {
                        $path_data = explode('/', $path);
                        $path_data = array_slice($path_data,1);
                        $path_old = implode('/', $path_data);
                        $file_names = last($path_data);
                        $file_data = explode('.', $file_names);
                        $filename = $product->id . "_" . $ki . "_product";
                        $filename_del = $product->id . "_product";
                        $new_path_data = array_slice($path_data, 0,-1);
                        $new_path = "/" . implode('/', $new_path_data) . "/$filename.webp";
                        $new_path_del = "/" . implode('/', $new_path_data) . "/$filename_del.webp";

                        if (preg_match('/webp$/', $file_names)) {
                            if (!preg_match('/_product/', $file_names)) {
                                $flag = true;

                                if (Storage::disk('public_root')->exists($path)) {

                                    if (!Storage::disk('public_root')->exists($new_path)) {
                                        Storage::disk('public_root')->copy($path, $new_path);
                                    }
                                    $img_delete[] = $path;
                                    $img_delete[] = $new_path_del;

                                } else {
//                                Storage::disk('public_root')->copy('/storage/images/160/3p-1662726324.webp', '/storage/images/160/2921468169.webp');
//                                dd('test 2', $path, $product->id);
                                }
                                $path = "$new_path";
                            }

                        } else {
//                            dd($path_old, $path);
//                            $path = "$new_path";
//                            $flag = true;
//                            $file_path = "public/" . implode('/', array_slice($path_data, 1));
//                            if (Storage::disk('public_root')->exists($path) && $file_data[1] != 'webp') {
//
//                                $flag = true;
//                                $img = ImageManager::make($path_old)->encode('webp', 100);
//                                $img->save($new_path, 100);
//                                $img->destroy();
//                                Storage::disk('public_root')->delete($path_old);
//                            }
                        }

                    }
                }
            }


            if ($flag) {
                $product->galery = json_encode($images);
                $product->image = json_encode($images[0]);
                $product->save();

            }
        }

        if (isset($img_delete)) {
            foreach ($img_delete as $old_img) {
                if (Storage::disk('public_root')->exists($old_img)) {
                    Storage::disk('public_root')->delete($old_img);
                }
            }
        }
        dd('done');

    }

    public function imageDelete(Request $request)
    {
        $image_to = $request->get('image_to');
        $id = $request->get('id');

        if ($image_to == 'product') {
            $image_key = $request->get('img_key');
            $product = Product::where('id', $id)->first();

            $image_galery = json_decode($product->galery, true);
            $image_data = $image_galery[$image_key];

            foreach ($image_data as $old_image) {

                $old_image = str_replace('storage', 'public', $old_image);
                if (Storage::exists($old_image)) {
                    $res = Storage::delete($old_image);
                }
            }
            unset($image_galery[$image_key]);

            $image_galery = array_slice($image_galery, 0);

            $product->galery = json_encode($image_galery);
            if (isset($image_galery[0])) {
                $product->image = json_encode($image_galery[0]);
            } else {
                $product->image = null;
            }
            $product->save();

            session()->flash('message', ["image delete"]);

            return redirect(route('product_redact', ['product' => $product]));

        }
    }

    public function clientData(Request $request, Clients $client)
    {

        $paymentMethod = AppServise::getOrderPaymentMethod();
        $paymentStatus = AppServise::getOrderPaymentStatus();
        $invoiceStatus = AppServise::getOrderInvoiceStatus();

        $client->data = json_decode($client->data, true);
        $amoCrmService = new AmoCrmServise();
        $amo_clones = $amoCrmService->getContactDoubles($client->email);

        $amo_contact = $amoCrmService->getContactBuId($client->amoId);
        if (isset($client->data['phones'])) {
            $phones = array_unique($client->data['phones']);
            $client->data = json_encode($phones);
            $client->save();
            $client->data = json_decode($client->data, true);
        }

        $client_orders = OrdersModel::where('clientId', $client->id)
            ->select('order_id', 'id', 'paymentStatus', 'orderPrice', 'invoiceStatus', 'created_at', 'updated_at')
            ->get();

        if (!$amo_contact && $amo_clones) {
            $amo_clones_rev = array_reverse($amo_clones);
            $client->amoId = $amo_clones_rev[0]['id'];
            $client->save();
        }


        return view('shop-settings.client', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'paymentMethod'  => $paymentMethod,
            'paymentStatus'  => $paymentStatus,
            'invoiceStatus'  => $invoiceStatus,
            'client' => $client,
            'amo_clones' => $amo_clones,
            'client_orders' => $client_orders
        ]);

    }

    public function updateAmoContact(Request $request, Clients $client)
    {
        $phone = OrderService::phoneAmoFormater($client->phone);
        $contactData = [
            'name' => $client->name,
            'phone' => $phone,
            'email' => $client->email,
        ];
        $client_data = json_decode($client->data, true);

        if (isset($client_data['clientBirtDay'])) {
            $client_data_birthday = AppServise::dateFormater($client_data['clientBirtDay']);
            if ($client_data_birthday) {
               $client_data['clientBirthDay'] = $client_data_birthday;
            } else {
                $client_data['clientBirthDayStr'] = $client_data['clientBirtDay'];
            }
            unset($client_data['clientBirtDay']);
            $client->data = json_encode($client_data);
            $client->save();
        }

        if (isset($client_data['clientBirthDay']) && !empty($client_data['clientBirthDay'])) {
            $date = AppServise::dateFormater($client_data['clientBirthDay']);

            if ($date) {
                $date = new Carbon($date);
                $date_time = strtotime($date->format('Y-m-d H:i:s'));
                $contactData['birthday'] = $date_time;
            }
        }

        $amoCrmService = new AmoCrmServise();

        if (!empty($client->amoId)) {
            $contact = $amoCrmService->getContactBuId($client->amoId);
            $contact = $amoCrmService->syncContactData($contact, $contactData);
        }
        session()->flash('message', ['amo contact update']);

        return back();
    }

    public function delivery(Request $request)
    {
        $week_days = ['Вс','ПН','Вт','Ср','Чт','Пт','Сб',];

        $categories = Categories::where('enabled', 1)->get()->sortBy('index_num')->keyBy('id');

        $delivery_json = Storage::disk('local')->get('js/delivery.json');
        $cityes_json = Storage::disk('local')->get('js/israel-city.json');

        $delivery = json_decode($delivery_json, true);
        $cityes = json_decode($cityes_json, true);

        $shop_setting = Storage::disk('local')->get('js/shop_setting.json');
        $shop_setting = json_decode($shop_setting, true);

//        dd($delivery, $cityes);


        return view('shop-settings.delivery', [
            'error_log'  => $request->error_log,
            'message'    => $request->message,
            'categories' => $categories,
            'cityes'     => $cityes,
            'delivery'   => $delivery,
            'shop_setting' => $shop_setting,
            'week_days'  => $week_days,
        ]);
    }

    public function deliverySave(Request $request)
    {

        $shop_setting = $request->get('shop');


        if (!empty($shop_setting)) {
            $res = Storage::disk('local')->put('js/shop_setting.json', json_encode($shop_setting));
            if($res) {
                session()->flash('message', ['shop setting date-time delivery save']);
            }
        }

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
            $res = Storage::disk('local')->put('js/delivery.json', json_encode($delivery_data));
            if($res) {
                session()->flash('message', ['delivery save']);
            }
        }



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
            }
        }
        return redirect(route('crm_delivery'));
    }

    public function appInvoiceSetting(Request $request)
    {

        $dataJson = Storage::disk('local')->get('data/app-setting.json');
        $settingData = json_decode($dataJson, true);


        $invoice_mode_paypal = $request->get('invoice_mode_paypal');
        if ($invoice_mode_paypal) {
            $settingData['invoice_mode_paypal'] = $invoice_mode_paypal;
        }

        $invoice_mode_cache = $request->get('invoice_mode_cache');
        if ($invoice_mode_cache) {
            $settingData['invoice_mode_cache'] = $invoice_mode_cache;
        }

        $invoice_mode_bit = $request->get('invoice_mode_bit');
        if ($invoice_mode_cache) {
            $settingData['invoice_mode_bit'] = $invoice_mode_bit;
        }

        if ($invoice_mode_paypal) {
            Storage::disk('local')->put('data/app-setting.json', json_encode($settingData));
        }

        return view('shop-settings.invoice_setting', [
            'error_log'   => $request->error_log,
            'message'     => $request->message,
            'settingData' => $settingData,
        ]);
    }

    public function banner(Request $request)
    {

        $post = $request->post();

        if (Storage::disk('local')->exists('data/banner.json')) {
            $banner = Storage::disk('local')->get('data/banner.json');
            $banner = json_decode($banner, true);
        } else {
            $banner = ['en' => '', 'ru' => '', 'he' => ''];
        }

        if ($post) {
            $res = Storage::disk('local')->put('data/banner.json', json_encode($post['banner']));
            if($res) {
                session()->flash('message', ['banner save']);
                return redirect(route('banner'));
            }
        }

        return view('shop-settings.banner', [
            'error_log' => $request->error_log,
            'message'   => $request->message,
            'banner'    => $banner
        ]);
    }

    public function testOrderChangeCount(Request $request, OrdersModel $order)
    {
        $post = $request->post();
        $OrderService = new OrderService();
        $OrderService->changeProductsCountTest($order);

    }

    public function scriptsModules(Request $request)
    {
        $files = Storage::disk('views_shop')->files('layouts/seo');

        foreach ($files as $file_path) {
            $file_name = last(explode('/', $file_path));
            $files_data[] = [
                'file_path' => $file_path,
                'file_name' => $file_name,
                'file_text' => Storage::disk('views_shop')->get($file_path)
            ];
        }
        dd($files_data);
    }

    public function productOptions (Request $request)
    {
        $products_options = ProductOptions::all()->keyBy('id');
        $shop_langs = AppServise::getLangs();

        $options_select = ['SELECT' => 'список', 'SIZE' => 'размер', 'RADIO' => 'выбор', 'CHECKBOX' => 'флажки', 'TEXT' => 'текстовое поле'];


//        dd($products_options);


        return view('shop-settings.products_options', [
            'error_log'        => $request->error_log,
            'message'          => $request->message,
            'products_options' => $products_options,
            'options_select'   => $options_select,
            'shop_langs'       => $shop_langs
        ]);
    }

    public function translations(Request $request)
    {

        $products_options = ProductOptions::all()->keyBy('id');
        $shop_langs = AppServise::getLangs();

        $post = $request->post();

        if ($post) {
            $file_path = $post['file_path'];
            $str = "<?php"."\n"."\n"."return [";
            Storage::disk('views_lang')->put($file_path, $str);

            if (!empty($post['translite_add']['key']) && !empty($post['translite_add']['value'])) {
                $key = $post['translite_add']['key'];
                $value = $post['translite_add']['value'];
                if (!isset($post['translite'][$key])) {
                    $post['translite'][$key] = $value;
                } else {
                    dd("item key [$key] isset");
                }
            }
            foreach ($post['translite'] as $kstr => $vstr) {
                if (!empty($vstr)) {
                    $vstr = str_replace('"', '\\"', $vstr);
                    $str = "    \"$kstr\" => \"$vstr\",";
                    Storage::disk('views_lang')->append($file_path, $str);
                }
            }
            $str =  "\n".'];';
            Storage::disk('views_lang')->append($file_path, $str);

            session()->flash('message', ["file $file_path save"]);

            return back();
        }

        foreach ($shop_langs as $key => $item) {
            $lang = $key;
            $files_lang = Storage::disk('views_lang')->files("$lang");
            $files_test[$lang] = Storage::disk('views_lang')->files("$lang");
            foreach ($files_lang as $file_name) {
                if (preg_match('/shop/', $file_name)) {

                    $files[$lang]['names'][] = $file_name;
                    $files[$lang]['contents'][$file_name] = include_once("../resources/lang/$file_name");

                }
            }
        }

//        dd($files);


        return view('shop-settings.translations', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'products_options' => $products_options,
            'files' => $files,
            'shop_langs' => $shop_langs
        ]);
    }


    public function ChangeOrderId(Request $request, $orderId)
    {
        $order = OrdersModel::where('id', $orderId)->first();
        $order_id = rand(100, 999);
//        $order->order_id = AppServise::generateOrderId($order_id, 'S');
//        $order->clientId = 701;
//        $order->orderData = $old_data;
//        $order->orderPrice = 288;
//
//        $AmoService = new AmoCrmServise();
//        $amo_order = $AmoService->getOrderById($order->amoId);
//
//        $amoData['order_id'] = $order->order_id;
//        $AmoService->updateLead($amo_order, $amoData);
//        $amo_order_new = $AmoService->getOrderById($order->amoId);

        dd($order);
    }



    public function jsonImport(Request $request)
    {

        $products_all = Product::all()->keyBy('id');
        $categories = Categories::all()->keyBy('id');
        $products = AppServise::ProductsShopPrepeare($products_all, $categories);
        $products = $products->toArray();


//        $file_csv_path = 'csv-import/products.csv';
//        $first_key = key($products);
//        foreach ($products[$first_key] as $k => $v) {
//            $keys[] = $k;
//        }
//        $str = implode(';', $keys);
//        Storage::disk('local')->put($file_csv_path, $str);

        foreach ($products as &$item) {
//            $values = [];
            foreach ($item as $k => $v) {
                if (!is_array($v)) {
                    if ($k == 'category_id' && isset($categories[$v])) {
                        $category = $categories[$v];
//                        $v = $category->name;
                        $item['category_id'] = $category->name;
                    }
//                    $values[] = $v;
                } else {
//                    if ($k == 'image' && !empty($v)) {
//                        $image_url = url($v['image1500pxUrl']);
//                        $values[] = $image_url;
//                    } else {
//                        $values[] = '';
//                    }
                }
            }
//            $str = implode(';', $values);
//            Storage::disk('local')->append($file_csv_path, $str);
        }


        $file_csv_path = 'csv-import/products.json';

        Storage::disk('local')->put($file_csv_path, json_encode($products));
        return Storage::disk('local')->download($file_csv_path);
    }

    public function getComponents(Request $request)
    {

        $post = $request->post();

        if ($post) {
            Storage::disk('views_shop')->put($post['file_path'], $post['content']);
            session()->flash('message', ["file {$post['file_path']} save"]);
            return back();
        }

        $component = 'send_pulse';
        $files = Storage::disk('views_shop')->allFiles("new/layouts/$component");

        foreach ($files as $filepath) {
            $content = Storage::disk('views_shop')->get($filepath);
            $name = str_replace("new/layouts/$component/", '', $filepath);
            $data[$component][] = [
                'path' => $filepath,
                'name' => $name,
                'content' => $content
            ];
        }

        $component = 'scripts';
        $files = Storage::disk('views_shop')->allFiles("new/layouts/$component");

        foreach ($files as $filepath) {
            $content = Storage::disk('views_shop')->get($filepath);
            $name = str_replace("new/layouts/$component/", '', $filepath);
            $data[$component][] = [
                'path' => $filepath,
                'name' => $name,
                'content' => $content
            ];
        }

        return view('shop-settings.components', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'files' => $data,
        ]);

    }

    public function getDbTable(Request $request, $tb_name)
    {
        if ($tb_name == 'utm')
            $tb_name = 'utm_models';

        $tb_data = DB::table($tb_name)->latest()->paginate(50);


        return view('shop-settings.table_data', [
            'error_log'      => $request->error_log,
            'message' => $request->message,
            'tb_data' => $tb_data,
        ]);
    }

    public function Statistic(Request $request)
    {

        if ($request->has('date')) {
            $date_str = $request->get('date');
            $date = new Carbon($date_str);
        } else {
            $date = new Carbon();
        }

        $date_from = new Carbon($date->format('Y-m-d'));
        $date_to = new Carbon($date->format('Y-m-d 23:59:59'));

        if ($request->has('order_id') || $request->has('gclientId')) {

            $statistick = Statistics::whereBetween('created_at', [$date_from, $date_to]);

            if ($request->has('order_id')) {
               $statistick = $statistick->where('order_id', $request->get('order_id'));
            }

            $res = $statistick->get()->toArray();

            foreach ($res as &$item) {
                if (!empty($item['post_data'])) {
                    $item['post_data'] =  json_decode($item['post_data'], true);

                    if (isset($item['post_data']['order_data']) && is_string($item['post_data']['order_data'])) {
                        $item['post_data']['order_data'] =  json_decode($item['post_data']['order_data'], true);
                    }
                }
            }

            dd($res);

        } else {
            $statistics = StatisticService::getStatistics($date_from, $date_to);
            dd($statistics);
        }

    }

    public function checkOrders()
    {
        OrderService::checkOrders();
    }

    public function lostCartAmoCreateOrder(Request $request, OrdersModel $order)
    {
        $OrderService = new OrderService();
        return $OrderService->createOrderToAmocrm($order->order_id, '53836814');
    }



    public function AppLogView(Request $request)
    {


        $date_nau = new Carbon();

        if ($request->get('clear')) {
            Storage::disk('logs')->delete("laravel.log");
            return back();
        }

        if (Storage::disk('logs')->exists("laravel.log")) {
            $monolog = Storage::disk('logs')->get("laravel.log");
        } else {
            $monolog = 'not file';
        }
        $monolog = htmlspecialchars($monolog);
        $monolog = str_replace('['.$date_nau->format('Y'), '<hr size="4" color="brown"><b>['.$date_nau->format('Y'), $monolog);
        $monolog = str_replace('] ', ']</b> ', $monolog);


        return view('logs.app_error_log', [
            'error_log'      => $request->error_log,
            'route' => 'orders_log',
            'log' => $monolog,
        ]);
    }

    public function BlackListIp(Request $request)
    {
        $file = "data/black_list_ip.json";
        $black_list = false;

        $post = $request->post();


        if (isset($post['ips'])) {
            foreach ($post['ips'] as $ip) {
                if (!empty($ip)) {
                    $save[$ip] = true;
                }
            }
            if (isset($save)) {
                Storage::put($file, json_encode($save));
            } else {
                Storage::delete($file);
            }
        }




        if (Storage::exists($file)) {
            $black_list = Storage::get($file);
            $black_list = json_decode($black_list, true);
        }

        return view('shop-settings.black_list', [
            'error_log'      => $request->error_log,
            'black_list' => $black_list,
        ]);
    }

    public function DbProdImport(Request $request)
    {

        $prod_files = Storage::disk('prod_root')->allFiles('images');
        $this_files = Storage::disk('public')->allFiles('images');
        $diff = array_diff($prod_files, $this_files);

        foreach ($diff as $item_file) {
            $file = Storage::disk('prod_root')->get($item_file);
            Storage::disk('public')->put($item_file, $file);
        }

//        $table_name = 'users';
//        DB::table($table_name)->truncate();
//        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();
//
//        foreach ($prod_data as $item) {
//            $new_item = new User();
//            foreach ($item as $k => $v) {
//                $new_item->$k = $v;
//            }
//            $new_item->save();
//        }


        $table_name = 'orders';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new OrdersModel();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }


        $table_name = 'clients';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new Clients();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }


        $table_name = 'categories';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new Categories();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }


        $table_name = 'products';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {

            $new_item = new Product();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }

            $test = Product::where('slag', $item->slag)->get()->first();
            if ($test) {
                $new_item->slag = $item->slag."_".$item->id;
            }
            $new_item->save();
        }


//        $table_name = 'coupons';
//        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();
//
//        foreach ($prod_data as $item) {
//            $new_item = new Coupons();
//            foreach ($item as $k => $v) {
//                $new_item->$k = $v;
//            }
//            $new_item->save();
//        }


        $table_name = 'product_options';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new ProductOptions();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }



        $table_name = 'utm_models';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new UtmModel();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }



        $table_name = 'statistics';
        DB::table($table_name)->truncate();
        $prod_data = DB::connection('mysql_prod')->table($table_name)->get();

        foreach ($prod_data as $item) {
            $new_item = new Statistics();
            foreach ($item as $k => $v) {
                $new_item->$k = $v;
            }
            $new_item->save();
        }


        dd('done');

    }
}
