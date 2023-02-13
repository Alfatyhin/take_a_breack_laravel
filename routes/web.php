<?php

use App\Http\Controllers\Amocrm;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiRest;
use App\Http\Controllers\AppErrorsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\IcreditController;
use App\Http\Controllers\Orders;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOptionsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopSettingController;
use App\Http\Controllers\TildaController;
use App\Services\AmoCrmServise;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//
//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/dashboard', array(ShopSettingController::class, 'index'))
    ->middleware(['isAdmin', "ShopSetting", "ip_bloked"])
    ->name('dashboard');

require __DIR__.'/auth.php';


Route::get('/test_get_url/', [IcreditController::class, 'testGetPaymentUrl'])->name('test_get_url');



////////////////////////////////////////////////////////////////////////////


Route::any('/amocrm/callback', [Amocrm::class, 'callBack']);

Route::any('/api/amocrm/amowebhok', [Amocrm::class, 'amoWebhook'])
    ->name('api_amo_webhook');


Route::any('/orders/thanks', [IcreditController::class, 'orderThanksIcredit'])
    ->name('icredit_order_thanks');

Route::any('/orders/response', [IcreditController::class, 'orderRequestIcredit'])
    ->name('icredit_hebhook');

////////////////////////////////////////////////////////////////////////////

Route::get('/api/json/import', [ShopSettingController::class, 'jsonImport'])
    ->name('scv_import');



Route::any('data/db/prod-import', [ShopSettingController::class, 'DbProdImport'])
    ->name('db_prod_import');


Route::prefix('crm')->middleware(['isAdmin', "ShopSetting", "ip_bloked"])->group(function () {

    Route::any('/', array(ShopSettingController::class, 'index'))->name('crm_index');

    Route::get('/amocrm', [Amocrm::class, 'integrationAmoCrm'])
        ->name('amocrm');

    Route::get('/', [Amocrm::class, 'UsersDuplicateCollaps'])
        ->name('amocrm_users_duplicate');


    Route::any('data/db/prod-import', [ShopSettingController::class, 'DbProdImport'])
        ->name('db_prod_import');

    Route::get('/amocrm/order', [Amocrm::class, 'getOrderById'])
        ->name('amo.get_order');

    Route::get('/amocrm/pipeline-test', [Amocrm::class, 'pipelineTest']);

    Route::any('/users', [Controller::class, 'allUsers'])->name('users');

    Route::any('amo/create-invoice-to-order/{order}', [Orders::class, 'createAmoInvoiceToOrder'])
        ->name('amo_create_invoice_to_order');

    Route::any('amo/get_order_data_to_amo/{order}', [Orders::class, 'getOrderAmoData'])
        ->name('get_order_data_to_amo');


    Route::any('/alex-payd', [PaypalController::class, 'alexpayd'])
        ->name('alex_payd');

    Route::get('/artisan/migrate', [ShopSettingController::class, 'migration'])
        ->name('artisan_migrate');

    Route::any('/shop-settings/categories', array(ShopSettingController::class, 'Categories'))
        ->name('shop_settings_categories');


    Route::get('/shop-settings/product-clone/{product}', array(ProductController::class, 'clone'))
        ->middleware(['isAdmin'])->name('product_cone');


    Route::get('/shop-settings/product-redact/{product}', array(ProductController::class, 'RedactProduct'))
        ->middleware(['isAdmin'])->name('product_redact');

    Route::get('/shop-settings/product-delete/{product}', array(ProductController::class, 'deleteProduct'))
        ->middleware(['isAdmin'])->name('product_delete');

    Route::get('/shop-settings/products-fix', array(ProductController::class, 'fixProducts'))
        ->middleware(['isAdmin']);

    Route::any('/shop-settings/product/create', array(ProductController::class, 'createProduct'))
        ->name('shop_settings_product_create');

    Route::any('/shop-settings/category/create', array(CategoriesController::class, 'create'))
        ->name('shop_settings_category_create');

    Route::any('/shop-settings/category/delete/{category}', array(CategoriesController::class, 'delete'))
        ->name('shop_settings_category_delete');

    Route::any('/shop-settings/products', array(ShopSettingController::class, 'Products'))
        ->name('shop_settings_products');

    Route::any('/shop-settings/products_options', array(ShopSettingController::class, 'productOptions'))
        ->name('shop_settings_products_options');

    Route::any('/shop-settings/products_options/add', array(ProductOptionsController::class, 'add'))
        ->name('shop_settings_products_options_add');

    Route::any('/shop-settings/products_options/save/{option}', array(ProductOptionsController::class, 'save'))
        ->name('shop_settings_products_option_save');

    Route::any('/shop-settings/dey-offer', array(ShopSettingController::class, 'DeyOffer'))
        ->name('dey_offer');

    Route::any('/shop-settings/save-sortable/', array(ShopSettingController::class, 'sortableSave'))
        ->name('save_sortable');

    Route::any('/shop-settings/orders', array(ShopSettingController::class, 'Orders'))
        ->name('shop_settings_orders');

    Route::any('/shop-settings/orders/segments', array(ShopSettingController::class, 'OrderSegments'))
        ->name('shop_settings_orders_segments');

    Route::any('/shop-settings/coupons-discount/{name?}', array(CouponsController::class, 'couponsDiscounts'))
        ->name('coupons_discount');

    Route::any('/shop-settings/save/coupon-status', array(CouponsController::class, 'couponStatusSave'))
        ->name('coupon_status_save');

    Route::any('/shop-settings/coupon-data-change', array(CouponsController::class, 'dataSave'))
        ->name('coupon_data_change');

    Route::any('/shop-settings/coupons-groups', array(CouponsController::class, 'CouponsGroups'))
        ->name('coupons_groups');

    Route::any('/shop-settings/coupons-groups/generate/{coupon_group}', array(CouponsController::class, 'CouponsGenerate'))
        ->name('coupons_groups_generate');

    Route::any('/shop-settings/coupons-groups/change/{coupon_group}', array(CouponsController::class, 'CouponsGroupChange'))
        ->name('coupons_groups_change');

    Route::any('/shop-settings/coupons-groups/list/{coupon_group}', array(CouponsController::class, 'CouponsGroupList'))
        ->name('coupons_groups_list');

    Route::any('/shop-settings/image-download', array(ShopSettingController::class, 'imageDownload'))
        ->name('image_download');

    Route::any('/shop-settings/image-delete', array(ShopSettingController::class, 'imageDelete'))
        ->name('image_delete');

    Route::any('/shop-settings/image-test', array(ShopSettingController::class, 'imageTest'))
        ->name('image_test');

    Route::any('/shop-settings/client/{client}', array(ShopSettingController::class, 'clientData'))
        ->name('client_data');

    Route::any('/shop-settings/delivery', array(ShopSettingController::class, 'delivery'))
        ->name('crm_delivery');

    Route::any('/shop-settings/delivery/save', array(ShopSettingController::class, 'deliverySave'))
        ->name('delivery_save');

    Route::get('/shop-settings/invoice-setting', [ShopSettingController::class, 'appInvoiceSetting'])
        ->name('invoice_setting');

    Route::any('/shop-settings/banner', [ShopSettingController::class, 'banner'])
        ->name('banner');

    Route::any('/shop-settings/test-change-product-count/{order}', [ShopSettingController::class, 'testOrderChangeCount'])
        ->name('test_change_product_count');

    Route::any('/shop-settings/test-icredit-payment-data', [Orders::class, 'testIcreditPaymentData'])
        ->name('test_icredit_payment_data');

    Route::any('/shop-settings/update-amo-contact/{client}', [ShopSettingController::class, 'updateAmoContact'])
        ->name('update_amo_contact');

    Route::any('/shop-settings/script-modules', [ShopSettingController::class, 'scriptsModules'])
        ->name('script_modules');

    Route::any('/shop-settings/translations', [ShopSettingController::class, 'translations'])
        ->name('translations');

    Route::any('/orders/log-view', [Orders::class, 'OrderLogView'])
        ->name('orders_log');

    Route::any('/order/set-paid-status/{order}', [Orders::class, 'setOrderPaidStatus'])
        ->name('order_set_paid_status');

    Route::any('/app/log-view', [ShopSettingController::class, 'AppLogView'])
        ->name('app_log');

    Route::any('/orders/change_order_id/{id}', [ShopSettingController::class, 'ChangeOrderId'])
        ->name('change_order_id');


    Route::any('/shop-settings/components', [ShopSettingController::class, 'getComponents'])
        ->name('components');


    Route::any('/db/{tb_name}', [ShopSettingController::class, 'getDbTable'])
        ->name('db');


    Route::any('/site-statistics', [ShopSettingController::class, 'Statistic'])
        ->name('statistics');



    Route::any('/shop-settings/check_orders', [ShopSettingController::class, 'checkOrders'])
        ->name('check_orders');


    Route::any('/amo/lost_order_create/{order}', [ShopSettingController::class, 'lostCartAmoCreateOrder'])
        ->name('amo_lost_order_create');



    Route::any('/{lang}/cart/{step?}/{order_id?}', [ShopController::class, 'CartView'])
        ->where('lang', '|ru|en|he|')
        ->name('crm_lost_cart');



    Route::any('/black-list-ip', [ShopSettingController::class, 'BlackListIp'])
        ->name('black_list_ip');

});


Route::any('cart-test', array(ShopController::class, 'cartTest'))
    ->name('test_cart');



// без проверки csrf для интеграций
// (не менять uri)orders/icredit
Route::post('/orders/icredit', [Orders::class, 'getIcreditPaymentUrl'])
    ->name('order.paymenturl');;





Route::post('/api/getIcreditUrl', [ApiController::class, 'getIcreditUrl']);

Route::post('/api/add_new_order', [Orders::class, 'createOrder'])
    ->name('add_new_order');
Route::post('/api/get_new_order_id', [Orders::class, 'getNewOrderId'])
    ->name('get_new_order_id');
Route::get('/api/cart_payment_url', [Orders::class, 'getPopapIcreditPaymentUrl'])
    ->name('cart_payment_url');
Route::get('/api/check_order_paystatus', [Orders::class, 'checkOrderPayStatus']);
Route::get('/api/get_order', [Orders::class, 'sendMail']);


Route::post('/api/tilda-new-order', [TildaController::class, 'newOrder'])
    ->name('tilda_new_order');
Route::post('/api/tilda-icredit-payment', [TildaController::class, 'iCreditPayment'])
    ->name('tilda_payment');

Route::get('/api/create_amo_order', [Amocrm::class, 'createOrderToApi'])
    ->name('api_create_amo_order');

Route::get('/api/create_amo_order_test', [Amocrm::class, 'createOrderToApiTest'])
    ->name('api_create_amo_order_test');

Route::any('/api/test_request', [ApiRest::class, 'testRequest']);




Route::get('/icredit', [IcreditController::class, 'index'])
    ->middleware(['isAdmin'])->name('icredit_index');




Route::get('/api/orders/view-order/{order_id}', [ApiController::class, 'OrderView'])
    ->name('api_order_view');

Route::get('/api/orders/send_mail', [Orders::class, 'sendMail']);

Route::get('/api/orders/view_mail', [Orders::class, 'testMail']);

Route::get('/orders/test_mail', [Orders::class, 'testMail'])
    ->middleware(['isAdmin'])->name('orders_test_mail');

Route::any('/orders/test_sendpulse/{order}', [Orders::class, 'testSendpulse'])
    ->middleware(['isAdmin'])->name('orders_test_sendpulse');

Route::get('/orders/delete', [Orders::class, 'orderDelete'])
    ->middleware(['isAdmin'])->name('delete_order');

Route::get('/orders/restore', [Orders::class, 'orderRestore'])
    ->middleware(['isAdmin'])->name('restore_order');

Route::get('/orders/webhooks', [Orders::class, 'getWebHookLog'])
    ->middleware(['isAdmin'])->name('webhook.log');


Route::get('/clients', [Controller::class, 'allClients'])
    ->middleware(['isAdmin'])->name('clients');

Route::get('/invoice-create', [Orders::class, 'createInvoice'])
    ->middleware(['isAdmin'])->name('invoice_create');

Route::get('/export-db', [Controller::class, 'exportDB'])
    ->middleware(['isAdmin']);

Route::get('/import-db', [Controller::class, 'importDB'])
    ->middleware(['isAdmin']);





Route::any('/api/ginvoice/webhook', [Orders::class, 'gInvoceWebhook'])
    ->name('api_ginvoice');

Route::get('/paypal', [PaypalController::class, 'index'])
    ->middleware(['isAdmin']);

Route::any('/api/paypal/order/capture', [PaypalController::class, 'orderCapture'])
    ->name('paypal_capture');




Route::get('/order/check-payment-status/icredit', [Orders::class, 'checkPaymentStatusIcredit'])
    ->middleware(['isAdmin'])->name('order_sheck_payment_status_icredit');

Route::get('/app-errors/', [AppErrorsController::class, 'index'])
    ->middleware(['isAdmin'])->name('app_errors');

Route::get('/api/getIp', [ApiRest::class, 'getIP']);


Route::any('categories/category-products-save', array(CategoriesController::class, 'CategoryProductsSave'))
    ->middleware(['isAdmin', 'ShopSetting'])->name('category_products_save');

Route::any('categories/category-save', array(CategoriesController::class, 'CategorySave'))
    ->middleware(['isAdmin'])->name('category_category_save');

Route::any('categories/category-save-data/{category}', array(CategoriesController::class, 'CategoryDataSave'))
    ->middleware(['isAdmin'])->name('category_save_data');

Route::post('product/enabled', array(ProductController::class, 'ProductEnabled'))
    ->middleware(['isAdmin'])->name('product_enabled');

Route::any('product/save/{product}', array(ProductController::class, 'ProductSave'))
    ->middleware(['isAdmin'])->name('product_save');





Route::middleware(["Shop"])->group(function () {

//    Route::get('/site_map.xml', [ShopController::class, 'sitemap']);


    Route::get('/404', [ShopController::class, 'err404'])->name('404');
    Route::get('/{lang?}/404', [ShopController::class, 'err404'])
        ->where('lang', 'ru|en|he');

    Route::get('/{lang?}/shop-error', [ShopController::class, 'shopError'])
        ->where('lang', 'ru|en|he')
        ->name('shop_error');


    Route::get('/check-promo-code', [ShopController::class, 'getPromoCode'])->name('check_promo_code');

    Route::get('/ru/market', [ShopController::class, 'marketRU'])->name('market_ru');

    Route::get('/delivery', [ShopController::class, 'deliveryIndex']);

    Route::any('/change-product-count', [ShopController::class, 'changeProductCount'])->name('change_product_count');

    Route::get('/market', [ShopController::class, 'marketEn'])->name('market_en');

    Route::get('/short-market', [ShopController::class, 'marketShortView'])->name('short_market');

    Route::get('/{lang}/short-market', [ShopController::class, 'marketShortLang'])
        ->where('lang', 'ru|en|he')
        ->name('short_market_lang');

//    Route::get('/wholesale/', [ShopController::class, 'WholeSaleEn'])->name('wholesale_en');
//
//    Route::get('/ru/wholesale/', [ShopController::class, 'WholeSaleRU'])->name('wholesale_ru');

    Route::any('/new-short-order/', [ShopController::class, 'NewShortOrder'])->name('new_short_order');

    Route::any('/links/', [ShopController::class, 'links'])->name('shop_links');

    Route::post('/send-contact-form/', [ShopController::class, 'contactForm'])->name('send_contact_form');

    Route::post('/send-birthday/', [ShopController::class, 'saveContactBirth'])->name('send_birthday');

    Route::get('/utm', [ShopController::class, 'testUtm']);

    Route::get('/paypal/payment/{order_id}', [ShopController::class, 'getButtonPaypal'])
        ->name('paypal_button');


    Route::any('/{lang}/order_not_found/{order_id?}', [ShopController::class, 'OrderNotFound'])
        ->where('lang', 'ru|en|he')
        ->name('order_not_found');


    Route::any('/sesssion_forget', [ShopController::class, 'SessionForget']);

    Route::get('/{lang?}/delivery', [ShopController::class, 'deliveryIndex'])
        ->where('lang', 'ru|en|he')
        ->name('delivery');

    Route::get('/about', [ShopController::class, 'aboutIndex'])
        ->name('about_en');


    Route::get('/{lang}/about', [ShopController::class, 'aboutIndex'])
        ->where('lang', 'ru|en|he')
        ->name('about');

    Route::get('/contacts', [ShopController::class, 'contactsIndex'])
        ->name('contacts_en');


    Route::get('/{lang}/contacts', [ShopController::class, 'contactsIndex'])
        ->where('lang', 'ru|en|he')
        ->name('contacts');

    Route::get('/{lang?}', [ShopController::class, 'indexView'])
        ->where('lang', 'ru|en|he')
        ->name('index');


    Route::get('/{filter?}', [ShopController::class, 'indexFilterEn'])
        ->where('filter', 'in_stock|sale')
        ->name('index_filter_en');

    Route::get('/{lang?}/{filter?}', [ShopController::class, 'indexView'])
        ->where('lang', 'ru|en|he')
        ->where('filter', 'in_stock|sale')
        ->name('index_filter');


    Route::any('/{lang}/cart/{step?}/', [ShopController::class, 'CartView'])
        ->where('lang', 'ru|en|he')
        ->name('cart');

    Route::get('/category/{category?}', [ShopController::class, 'categoryView'])->name('category_index');

    Route::get('/{lang}/category/{category}', [ShopController::class, 'categoryLang'])
        ->where('lang', 'ru|en|he')
        ->name('category');

    Route::get('/{lang?}/{category}/{product}', [ShopController::class, 'ProductLang'])
        ->where('lang', 'ru|en|he')
        ->name('product');

    Route::any('/{lang}/new-order', [ShopController::class, 'NewOrder'])
        ->where('lang', 'ru|en|he')
        ->name('new_order');

    Route::any('/{lang}/order_thanks/', [ShopController::class, 'OrderThanksView'])
        ->middleware('ShopThanks')
        ->where('lang', 'ru|en|he')
        ->name('order_thanks');


    Route::get('/{category}/{product}', [ShopController::class, 'ProductView'])
        ->where('category', '[a-z_-]{3,}')
        ->name('product_index');


    Route::get('/ru/product/{product}', [ShopController::class, 'ProductRuOld']);

    Route::get('/product/{product}', [ShopController::class, 'ProductEnOld']);


});
