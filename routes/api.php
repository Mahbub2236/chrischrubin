<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/client/users', [ClientController::class, 'getUsers']);
Route::get('/client/beneficiaries/{userId}', [ClientController::class, 'getBeneficiaries']);
Route::get('/user-accounts/{userId}', [ClientController::class, 'getUserAccounts']);
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/transaction-summary', [PaymentController::class, 'getTransactionSummary']);
Route::get('/filtered-beneficiaries', [ClientController::class, 'getFilteredBeneficiaries']);

Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment']);