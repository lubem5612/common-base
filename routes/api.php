<?php


use Illuminate\Support\Facades\Route;
use Transave\CommonBase\Http\Controllers\PasswordController;
use Transave\CommonBase\Http\Controllers\UserController;

Route::group(['as' => 'transave'], function () {
    Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
        Route::patch('{id}/verify-password', [ PasswordController::class, 'verifyPassword'])->name('verify.password');
        Route::patch('{id}/update-limit', [ UserController::class, 'updateWithdrawalLimit'])->name('withdrawal.update');
        Route::post('{id}/update', [ UserController::class, 'update'])->name('users.update');
    });
});
