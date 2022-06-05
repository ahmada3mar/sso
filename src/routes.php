<?php

use Illuminate\Support\Facades\Route;
use Hyperpay\ConnectIn\Controllers\ConnectInController;


Route::middleware( 'ACI_Log')->group(function(){
    Route::post('v1/connectin/payment', [ConnectInController::class , 'payment']);
});
