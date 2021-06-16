<?php

use App\Http\Controllers\Amocrm;
use App\Http\Controllers\ApiRest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EcwidStore;
use App\Http\Controllers\Orders;
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
Route::get('/orders/thanks', [Orders::class, 'orderThanksIcredit']);
Route::post('/orders/response', [Orders::class, 'orderRequestIcredit']);
Route::any('/orders/webecwid', [Orders::class, 'ecwidWebHook']);
Route::get('/amocrm/callback', [Amocrm::class, 'callBack']);
Route::any('/amocrm/amowebhok', [Amocrm::class, 'amoWebhook']);


////////////////////////////////////////////////////////////////////////////
Route::get('/amocrm', [Amocrm::class, 'integrationAmoCrm'])
    ->middleware(['isAdmin'])->name('amocrm');
Route::get('/amocrm/create-lead', [Amocrm::class, 'createAmoLeadBuEcwidId'])
    ->middleware(['isAdmin'])->name('amo.create.lead');




Route::get('/orders', [Controller::class, 'orders'])
    ->middleware(['isAdmin'])->name('orders');

Route::get('/orders/delete', [Controller::class, 'orderDelete'])
    ->middleware(['isAdmin'])->name('delete_order');

Route::get('/orders/webhooks', [Controller::class, 'getWebHookLog'])
    ->middleware(['isAdmin'])->name('webhook.log');

Route::get('/orders/ecwid-log', [Controller::class, 'getEcwidOrderLog'])
    ->middleware(['isAdmin'])->name('ecwid_order_log');

Route::get('/users', [Controller::class, 'allUsers'])
    ->middleware(['isAdmin'])->name('users');




Route::get('/ecwid-store', [EcwidStore::class, 'EcwidShop'])
    ->middleware(['isAdmin'])->name('ecwid.shop');

// test servise container
Route::get('/ecwid-store/index', [EcwidStore::class, 'index'])
    ->middleware(['isAdmin'])->name('ecwid.index');

Route::get('/ecwid-store/products', [EcwidStore::class, 'getEcwidProducts'])
    ->name('ecwid.products');

Route::get('/ecwid-store/product', [EcwidStore::class, 'getEcwidProductById'])
    ->middleware(['isAdmin'])->name('ecwid.product');

Route::get('/ecwid-store/order', [EcwidStore::class, 'getOrderById'])
    ->middleware(['isAdmin'])->name('ecwid.order');

Route::get('/create-order/ecwid-order', [Controller::class, 'createOrderByEcwidId'])
    ->middleware(['isAdmin'])->name('order.create_by_ecwid_id');

Route::get('/api/category', [ApiRest::class, 'getEcwidProductBuCategoryId']);

