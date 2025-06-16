<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

Route::get('/pay', [PaymentController::class, 'showPaymentForm']);
Route::post('/tokenize-card', [PaymentController::class, 'tokenizeCard']);
Route::post('/process-payment', [PaymentController::class, 'processPayment']);
Route::post('/clear-cards', [PaymentController::class, 'clearAliases']); // NEW
Route::post('/init-payment',[PaymentController::class,'initPayment']);