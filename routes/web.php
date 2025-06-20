<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalleePaymentController;
use App\Http\Controllers\TokenPaymentController;
use App\Http\Controllers\WalleeWebhookController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//datatrans
Route::get('/pay', [PaymentController::class, 'showPaymentForm']);
Route::post('/tokenize-card', [PaymentController::class, 'tokenizeCard']);
Route::post('/process-payment', [PaymentController::class, 'processPayment']);
Route::post('/clear-cards', [PaymentController::class, 'clearAliases']); // NEW
Route::post('/init-payment',[PaymentController::class,'initPayment']);


//walle
Route::get('/wallee-payment', [WalleePaymentController::class, 'showForm'])->name('wallee.form');
Route::post('/wallee-payment/start', [WalleePaymentController::class, 'startPayment'])->name('wallee.start');
Route::get('/wallee-payment/success', [WalleePaymentController::class, 'paymentSuccess'])->name('wallee.success');
Route::get('/wallee-payment/fail', [WalleePaymentController::class, 'paymentFail'])->name('wallee.fail');

//tokenized payment
Route::get('/card/register', [TokenPaymentController::class, 'registerCard'])->name('token.register');
Route::get('/card/charge', [TokenPaymentController::class, 'chargeForm'])->name('token.charge.form');
Route::post('/card/charge', [TokenPaymentController::class, 'charge'])->name('token.charge');
Route::get('/payment/success', [TokenPaymentController::class, 'success'])->name('token.success');
Route::get('/payment/fail', [TokenPaymentController::class, 'fail'])->name('token.failed');
Route::post('/wallee/webhook', [WalleeWebhookController::class, 'handle'])->name('wallee.webhook');
Route::get('/payment/status', [TokenPaymentController::class, 'transactionStatus'])->name('token.status');