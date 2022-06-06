<?php

use Illuminate\Support\Facades\Route;
use Hyperpay\ConnectIn\Controllers\ConnectInController;
use Hyperpay\ConnectIn\Middlewares\LogRoute;

Route::middleware(LogRoute::class)->group(function(){
    Route::post('v1/connectin/payment', [ConnectInController::class , 'payment']);
    Route::post('v1/connectin/payment/{transaction}', [ConnectInController::class , 'refund']);
});
