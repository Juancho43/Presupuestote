<?php

use App\Http\Controllers\V1\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::group(['as' => 'public.'], function() {
        Route::resource('clients', ClientController::class)->only(['index', 'show'])->names('clients');
    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['as' => 'private.'], function(){
            Route::resource('clients/private',ClientController::class)->only([ 'store', 'update', 'destroy']);
        });
    });

});

