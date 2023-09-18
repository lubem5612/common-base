<?php


use Illuminate\Support\Facades\Route;
use Transave\CommonBase\Http\Controllers\AuthController;
use Transave\CommonBase\Http\Controllers\ConfigController;
use Transave\CommonBase\Http\Controllers\DebitCardController;
use Transave\CommonBase\Http\Controllers\FlutterwaveController;
use Transave\CommonBase\Http\Controllers\KudaAccountController;
use Transave\CommonBase\Http\Controllers\PasswordController;
use Transave\CommonBase\Http\Controllers\ResourceController;
use Transave\CommonBase\Http\Controllers\SupportController;
use Transave\CommonBase\Http\Controllers\SupportReplyController;
use Transave\CommonBase\Http\Controllers\UserController;

Route::group(['as' => 'transave'], function () {
    //authentication routes
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::patch('password-forgot', [PasswordController::class, 'forgotPassword'])->name('password.forgot');
    Route::patch('password-reset', [PasswordController::class, 'resetPassword'])->name('password.reset');
    Route::patch('resend-token', [AuthController::class, 'resendToken'])->name('resend.token');
    Route::patch('verify-account', [AuthController::class, 'verifyAccount'])->name('account.verify');
    Route::middleware('auth:sanctum')->group(function() {
        Route::get('user', [AuthController::class, 'user'])->name('user');
        Route::any('logout', [AuthController::class, 'logout'])->name('logout');
    });

    //users routes
    Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [ UserController::class, 'index'])->name('users.index');
        Route::patch('{id}/verify-password', [ PasswordController::class, 'verifyPassword'])->name('verify.password');
        Route::patch('{id}/account-type', [ UserController::class, 'updateAccountType'])->name('update.account-type');
        Route::patch('{id}/account-status', [ UserController::class, 'updateAccountStatus'])->name('update.account-status');
        Route::post('{id}/update', [ UserController::class, 'update'])->name('users.update');
        Route::patch('change-email', [ UserController::class, 'changeEmail'])->name('users.change-email');
        Route::patch('change-password', [ UserController::class, 'changePassword'])->name('users.change-password');
        Route::patch('set-pin', [ UserController::class, 'setPin'])->name('users.set-pin');
        Route::patch('{id}/verify-pin', [ UserController::class, 'verifyPin'])->name('users.verify-pin');
    });

    //support controller routes
    Route::as('supports.')->prefix('supports')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::post('/', [SupportController::class, 'store'])->name('store');
        Route::get('/{id}', [SupportController::class, 'show'])->name('show');
        Route::match(['POST', 'PATCH', 'PUT'],'/{id}', [SupportController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupportController::class, 'destroy'])->name('delete');
    });

    //support reply controller routes
    Route::as('support-reply.')->prefix('support-replies')->group(function () {
        Route::get('/', [SupportReplyController::class, 'index'])->name('index');
        Route::post('/', [SupportReplyController::class, 'store'])->name('store');
        Route::get('/{id}', [SupportReplyController::class, 'show'])->name('show');
        Route::match(['POST', 'PATCH', 'PUT'],'/{id}', [SupportReplyController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupportReplyController::class, 'destroy'])->name('delete');
    });

    //support reply controller routes
    Route::as('debit-cards.')->prefix('debit-cards')->group(function () {
        Route::get('/', [DebitCardController::class, 'index'])->name('index');
        Route::post('/', [DebitCardController::class, 'store'])->name('store');
        Route::get('/{id}', [DebitCardController::class, 'show'])->name('show');
        Route::match(['POST', 'PATCH', 'PUT'],'/{id}', [DebitCardController::class, 'update'])->name('update');
        Route::delete('/{id}', [DebitCardController::class, 'destroy'])->name('delete');
    });

    //config controller
    Route::get('config/{endpoint}', [ ConfigController::class, 'index'])->name('config.index');

    //flutterwave routes
    Route::as('flutterwave.')->prefix('flutterwave')->group(function () {
        Route::prefix('transfers')->group(function () {
            Route::get('bank-list', [FlutterwaveController::class, 'bankList'])->name('bank-list');
            Route::post('bank-charge', [FlutterwaveController::class, 'initiateBankTransfer'])->name('bank-transfer');
            Route::post('card-charge', [FlutterwaveController::class, 'initiateCardTransaction'])->name('card-transaction');
            Route::post('customer-charge', [FlutterwaveController::class, 'chargeReturningCustomer'])->name('customer');
            Route::post('validate-charge', [FlutterwaveController::class, 'validateCharge'])->name('validate');
        });
        Route::get('redirect/{id}', [FlutterwaveController::class, 'redirect'])->name('redirect');
    });

    //Kuda controller routes
    Route::as('kuda.')->prefix('kuda')->group(function () {
        Route::prefix('transfers')->group(function() {
            Route::get('bank-list', [KudaAccountController::class, 'bankList'])->name('bank-list');
            Route::post('name-enquiry', [KudaAccountController::class, 'nameEnquiry'])->name('name-enquiry');
            Route::post('virtual-account', [KudaAccountController::class, 'virtualAccountTransfer'])->name('virtual-account');
            Route::post('main-account', [KudaAccountController::class, 'mainAccountTransfer'])->name('main-account');
            Route::post('wallet', [KudaAccountController::class, 'walletTransfer'])->name('wallet-transfer');
        });
        Route::post('webhook', [KudaAccountController::class, 'webhook'])->name('webhook');
    });

    //resource controller routes
    Route::as('resources.')->group(function () {
        Route::get('{endpoint}', [ResourceController::class, 'index'])->name('index');
        Route::post('{endpoint}', [ResourceController::class, 'store'])->name('store');
        Route::get('{endpoint}/{id}', [ResourceController::class, 'show'])->name('show');
        Route::match(['POST', 'PATCH', 'PUT'],'{endpoint}/{id}', [ResourceController::class, 'update'])->name('update');
        Route::delete('{endpoint}/{id}', [ResourceController::class, 'destroy'])->name('delete');
    });

});
