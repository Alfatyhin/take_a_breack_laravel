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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::get('/test_get_url/', [IcreditController::class, 'testGetPaymentUrl'])->name('test_get_url');


Route::any('crm', array(ShopSettingController::class, 'index'))->name('index');

Route::middleware(['isAdmin', "ShopSetting"])->group(function () {


    Route::any('crm/users', [Controller::class, 'allUsers'])->name('users');


    Route::any('crm/alex-payd', [PaypalController::class, 'alexpayd'])
        ->name('alex_payd');

    Route::get('crm/artisan/migrate', [ShopSettingController::class, 'migration'])
        ->name('artisan_migrate');

    Route::any('crm/shop-settings/categories', array(ShopSettingController::class, 'Categories'))
        ->name('shop_settings_categories');


    Route::get('crm/shop-settings/product-redact/{product}', array(ProductController::class, 'RedactProduct'))
        ->middleware(['isAdmin'])->name('product_redact');

    Route::get('crm/shop-settings/product-delete/{product}', array(ProductController::class, 'deleteProduct'))
        ->middleware(['isAdmin'])->name('product_delete');

    Route::any('crm/shop-settings/product/create', array(ProductController::class, 'createProduct'))
        ->name('shop_settings_product_create');

    Route::any('crm/shop-settings/category/create', array(CategoriesController::class, 'create'))
        ->name('shop_settings_category_create');

    Route::any('crm/shop-settings/category/delete/{category}', array(CategoriesController::class, 'delete'))
        ->name('shop_settings_category_delete');

    Route::any('crm/shop-settings/products', array(ShopSettingController::class, 'Products'))
        ->name('shop_settings_products');

    Route::any('crm/shop-settings/dey-offer', array(ShopSettingController::class, 'DeyOffer'))
        ->name('dey_offer');

    Route::any('crm/shop-settings/save-sortable/', array(ShopSettingController::class, 'sortableSave'))
        ->name('save_sortable');

    Route::any('crm/shop-settings/orders', array(ShopSettingController::class, 'Orders'))
        ->name('shop_settings_orders');

    Route::any('crm/shop-settings/coupons-discount', array(CouponsController::class, 'couponsDiscounts'))
        ->name('coupons_discount');

    Route::any('crm/shop-settings/save/coupon-status', array(CouponsController::class, 'couponStatusSave'))
        ->name('coupon_status_save');

    Route::any('crm/shop-settings/coupon-data-change', array(CouponsController::class, 'dataSave'))
        ->name('coupon_data_change');

    Route::any('crm/shop-settings/image-download', array(ShopSettingController::class, 'imageDownload'))
        ->name('image_download');

    Route::any('crm/shop-settings/image-delete', array(ShopSettingController::class, 'imageDelete'))
        ->name('image_delete');

    Route::any('crm/shop-settings/image-test', array(ShopSettingController::class, 'imageTest'))
        ->name('image_test');

    Route::any('crm/shop-settings/client/{client}', array(ShopSettingController::class, 'clientData'))
        ->name('client_data');

    Route::any('crm/shop-settings/delivery', array(ShopSettingController::class, 'delivery'))
        ->name('delivery');

    Route::any('crm/shop-settings/delivery/save', array(ShopSettingController::class, 'deliverySave'))
        ->name('delivery_save');

    Route::get('crm/shop-settings/invoice-setting', [ShopSettingController::class, 'appInvoiceSetting'])
        ->name('invoice_setting');

    Route::any('crm/shop-settings/banner', [ShopSettingController::class, 'banner'])
        ->name('banner');

    Route::any('crm/shop-settings/update-amo-contact/{client}', [ShopSettingController::class, 'updateAmoContact'])->name('update_amo_contact');

});


Route::any('shop-test', array(ShopController::class, 'test'))
    ->name('test');



// без проверки csrf для интеграций
// (не менять uri)orders/icredit
Route::post('/orders/icredit', [Orders::class, 'getIcreditPaymentUrl'])
    ->name('order.paymenturl');;


////////////////////////////////////////////////////////////////////////////
Route::get('/amocrm/amo', [Amocrm::class, 'amoMirror'])
    ->middleware(['isAdmin']);
Route::get('/amocrm', [Amocrm::class, 'integrationAmoCrm'])
    ->middleware(['isAdmin'])->name('amocrm');
Route::get('/amocrm/order', [Amocrm::class, 'getOrderById'])
    ->middleware(['isAdmin'])->name('amo.get_order');
Route::get('/amocrm/contacts', [Amocrm::class, 'getContacts'])
    ->middleware(['isAdmin'])->name('amo.get_contacts');
Route::get('/amocrm/callback', [Amocrm::class, 'callBack']);
Route::get('/amocrm/pipeline-test', [Amocrm::class, 'pipelineTest']);
Route::any('/amocrm/amowebhok', [Amocrm::class, 'amoWebhook'])
    ->name('amo_webhook');



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


Route::get('/test-tilda', [TildaController::class, 'testTildaInput'])
    ->middleware(['isAdmin'])
    ->name('test_tilda');
Route::get('/tilda-invoice-create', [TildaController::class, 'createInvoiceOrder'])
    ->middleware(['isAdmin'])
    ->name('tilda_invoice_create');





Route::get('/icredit', [IcreditController::class, 'index'])
    ->middleware(['isAdmin'])->name('icredit_index');
Route::any('/orders/thanks', [IcreditController::class, 'orderThanksIcredit'])
    ->name('order_thanks');
Route::any('/orders/response', [IcreditController::class, 'orderRequestIcredit'])
    ->name('icredit_hebhook');




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

Route::get('/paypal/payment', [PaypalController::class, 'getButton'])
    ->name('paypal_button');




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

    Route::any('/change-product-count', [ShopController::class, 'changeProductCount'])->name('change_product_count');


    Route::get('/ru/category/{category}', [ShopController::class, 'categoryRu'])->name('category_ru');

    Route::get('/category/{category}', [ShopController::class, 'categoryEn'])->name('category_en');

    Route::get('/ru', [ShopController::class, 'indexRu'])->name('index_ru');

    Route::get('/', [ShopController::class, 'indexEn'])->name('index_en');

    Route::get('/ru/market', [ShopController::class, 'marketRU'])->name('market_ru');

    Route::get('/market', [ShopController::class, 'marketEn'])->name('market_en');

    Route::get('/ru/short-market', [ShopController::class, 'marketShortRu'])->name('short_market_ru');

    Route::get('/short-market', [ShopController::class, 'marketShortEn'])->name('short_market_en');

    Route::get('/ru/cart/', [ShopController::class, 'Cart'])->name('cart_ru');

    Route::get('/cart/', [ShopController::class, 'CartEn'])->name('cart_en');

    Route::any('/new-order/', [ShopController::class, 'NewOrder'])->name('new_order');

    Route::any('/order_thanks/', [ShopController::class, 'OrderThanksEn'])
        ->middleware('ShopThanks')->name('order_thanks_en');

    Route::any('/ru/order_thanks/', [ShopController::class, 'OrderThanksRu'])
        ->middleware('ShopThanks')->name('order_thanks_ru');

    Route::get('/wholesale/', [ShopController::class, 'WholeSaleEn'])->name('wholesale_en');

    Route::get('/ru/wholesale/', [ShopController::class, 'WholeSaleRU'])->name('wholesale_ru');

    Route::any('/new-short-order/', [ShopController::class, 'NewShortOrder'])->name('new_short_order');

    Route::any('/links/', [ShopController::class, 'links'])->name('shop_links');

    Route::get('/check-promo-code/', [ShopController::class, 'getPromoCode'])->name('check_promo_code');

    Route::post('/send-contact-form/', [ShopController::class, 'contactForm'])->name('send_contact_form');

    Route::post('/send-birthday/', [ShopController::class, 'saveContactBirth'])->name('send_birthday');

    Route::get('/utm', [ShopController::class, 'testUtm']);


    Route::get('/ru/product/{product}', [ShopController::class, 'ProductRuOld']);

    Route::get('/product/{product}', [ShopController::class, 'ProductEnOld']);


    Route::get('/ru/{category}/{product}', [ShopController::class, 'ProductRu'])->name('product_ru');

    Route::get('/{category}/{product}', [ShopController::class, 'ProductEn'])->name('product_en');



});
