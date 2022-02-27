<?php

use App\Http\Controllers\Amocrm;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiRest;
use App\Http\Controllers\AppErrorsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EcwidStore;
use App\Http\Controllers\IcreditController;
use App\Http\Controllers\Orders;
use App\Http\Controllers\PaypalController;
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


Route::get('/', [Controller::class, 'index'])->name('index');


// без проверки csrf для интеграций
// (не менять uri)orders/icredit
Route::post('/orders/icredit', [Orders::class, 'getIcreditPaymentUrl'])
    ->name('order.paymenturl');
Route::any('/orders/webecwid', [Orders::class, 'ecwidWebHook'])
    ->name('ecwid.webhook');
Route::get('/amocrm/callback', [Amocrm::class, 'callBack']);
Route::any('/amocrm/amowebhok', [Amocrm::class, 'amoWebhook'])
    ->name('amo_webhook');



Route::post('/api/getIcreditUrl', [ApiController::class, 'getIcreditUrl']);

Route::get('/api/ecwidjs', [EcwidStore::class, 'storeJs']);
Route::get('/api/tilda-ecwid-js-v0.1', [EcwidStore::class, 'tildaJs01']);
Route::get('/api/tilda-ecwid-js-v1', [EcwidStore::class, 'tildaJs']);
Route::get('/api/tilda-ecwid-js-v2', [EcwidStore::class, 'tildaJsv2']);
Route::get('/api/tilda-ecwid-js-v3', [EcwidStore::class, 'tildaJsv3']);
Route::get('/api/tilda-ecwid-cart-js', [EcwidStore::class, 'ecwidCart']);
Route::get('/api/tilda-ecwid-cart-test-js', [EcwidStore::class, 'ecwidCartTest']);
Route::get('/api/tilda-ecwid-order-js', [EcwidStore::class, 'ecwidOrder']);
Route::get('/api/check_promo_code', [EcwidStore::class, 'sheckPromoCode']);
Route::post('/api/add_ekwid_order', [EcwidStore::class, 'createOrder'])
    ->name('add_ekwid_order');
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
Route::any('/api/test_request', [ApiRest::class, 'testRequest']);


Route::get('/test-tilda', [TildaController::class, 'testTildaInput'])
    ->middleware(['isAdmin'])
    ->name('test_tilda');
Route::get('/tilda-invoice-create', [TildaController::class, 'createInvoiceOrder'])
    ->middleware(['isAdmin'])
    ->name('tilda_invoice_create');



////////////////////////////////////////////////////////////////////////////
Route::get('/amocrm', [Amocrm::class, 'integrationAmoCrm'])
    ->middleware(['isAdmin'])->name('amocrm');
Route::get('/amocrm/create-lead', [Amocrm::class, 'createAmoLeadBuEcwidId'])
    ->middleware(['isAdmin'])->name('amo.create.lead');
Route::get('/amocrm/order', [Amocrm::class, 'getOrderById'])
    ->middleware(['isAdmin'])->name('amo.get_order');
Route::get('/amocrm/contacts', [Amocrm::class, 'getContacts'])
    ->middleware(['isAdmin'])->name('amo.get_contacts');


Route::get('/icredit', [IcreditController::class, 'index'])
    ->middleware(['isAdmin'])->name('icredit_index');
Route::any('/orders/thanks', [IcreditController::class, 'orderThanksIcredit'])
    ->name('order_thanks');
Route::any('/orders/response', [IcreditController::class, 'orderRequestIcredit'])
    ->name('icredit_hebhook');



Route::get('/orders', [Orders::class, 'orders'])
    ->middleware(['isAdmin'])->name('orders');

Route::get('/api/orders/send_mail', [Orders::class, 'sendMail']);

Route::get('/api/orders/view_mail', [Orders::class, 'testMail']);

Route::get('/orders/test_mail', [Orders::class, 'testMail'])
    ->middleware(['isAdmin'])->name('orders_test_mail');

Route::get('/orders/delete', [Controller::class, 'orderDelete'])
    ->middleware(['isAdmin'])->name('delete_order');

Route::get('/orders/webhooks', [Orders::class, 'getWebHookLog'])
    ->middleware(['isAdmin'])->name('webhook.log');

Route::get('/orders/ecwid-log', [Controller::class, 'getEcwidOrderLog'])
    ->middleware(['isAdmin'])->name('ecwid_order_log');

Route::get('/users', [Controller::class, 'allUsers'])
    ->middleware(['isAdmin'])->name('users');

Route::get('/clients', [Controller::class, 'allClients'])
    ->middleware(['isAdmin'])->name('clients');

Route::get('/invoice-setting', [Controller::class, 'appInvoiceSetting'])
    ->middleware(['isAdmin'])->name('invoice_setting');

Route::get('/invoice-create', [Orders::class, 'createInvoice'])
    ->middleware(['isAdmin'])->name('invoice_create');

Route::get('/export-db', [Controller::class, 'exportDB'])
    ->middleware(['isAdmin']);

Route::get('/import-db', [Controller::class, 'importDB'])
    ->middleware(['isAdmin']);

Route::get('/test', [Controller::class, 'testServiceProvider'])
    ->middleware(['isAdmin']);





Route::any('/api/ginvoice/webhook', [Orders::class, 'gInvoceWebhook'])
    ->name('api_ginvoice');

Route::get('/paypal', [PaypalController::class, 'index'])
    ->middleware(['isAdmin']);

Route::any('/api/paypal/order/capture', [PaypalController::class, 'orderCapture'])
    ->name('paypal_capture');

Route::get('/api/paypal/button', [PaypalController::class, 'getButton'])
    ->name('paypal_button');




Route::get('/ecwid-store/abandone-baskets', [EcwidStore::class, 'AbandoneBaskets'])
    ->middleware(['isAdmin'])->name('ecwid_abandone_baskets');

Route::get('/ecwid-store', [EcwidStore::class, 'EcwidShop'])
    ->name('ecwid.shop');

Route::get('/ecwid-store/delete-order', [EcwidStore::class, 'deleteOrder'])
    ->middleware(['isAdmin'])
    ->name('ecwid_delete_order');

// test servise container
Route::get('/ecwid-store/index', [EcwidStore::class, 'index'])
    ->middleware(['isAdmin'])->name('ecwid.index');

Route::get('/ecwid-store/products', [EcwidStore::class, 'getEcwidProducts'])
    ->middleware(['isAdmin'])
    ->name('ecwid.products');

Route::get('/ecwid-store/categories', [EcwidStore::class, 'allCategories'])
    ->middleware(['isAdmin'])
    ->name('ecwid.categories');

Route::get('/ecwid-store/product', [EcwidStore::class, 'getEcwidProductById'])
    ->middleware(['isAdmin'])->name('ecwid.product');

Route::get('/ecwid-store/order', [EcwidStore::class, 'getOrderById'])
    ->middleware(['isAdmin'])->name('ecwid.order');

Route::any('/ecwid-store/settings', [EcwidStore::class, 'shopSettings'])
    ->middleware(['isAdmin'])->name('ecwid_settings');



Route::get('/order/check-payment-status/icredit', [Orders::class, 'checkPaymentStatusIcredit'])
    ->middleware(['isAdmin'])->name('order_sheck_payment_status_icredit');

Route::get('/create-order/ecwid-order', [Orders::class, 'createOrderByEcwidId'])
    ->middleware(['isAdmin'])->name('order.create_by_ecwid_id');

Route::get('/app-errors/', [AppErrorsController::class, 'index'])
    ->middleware(['isAdmin'])->name('app_errors');

Route::get('/api/category', [ApiRest::class, 'getEcwidProductBuCategoryId']);
Route::get('/api/categoryes', [ApiRest::class, 'getEcwidCategories']);
Route::get('/api/getIp', [ApiRest::class, 'getIP']);

