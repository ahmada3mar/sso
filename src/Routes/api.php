<?php

use Hyperpay\ConnectIn\Http\Controllers\ConnectInController;
use Illuminate\Support\Facades\Route;
use Hyperpay\ConnectIn\Middlewares\LogRoute;

Route::middleware(LogRoute::class)->group(function(){
    Route::post(config('connect-in.end_points')['payment'], [class_exists('App\Http\Controllers\ConnectInController') ? 'App\Http\Controllers\ConnectInController' : ConnectInController::class , 'payment']);
    Route::post(config('connect-in.end_points')['refund'], [ConnectInController::class , 'refund']);
});
