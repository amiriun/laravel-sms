<?php


Route::group(['prefix'=>'amiriun-sms','namespace'=>'\Amiriun\SMS\Http\Controllers'], function () {
    Route::post('kavenegar', 'HookController@receiveKavenegar');
});
