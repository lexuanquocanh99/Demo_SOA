<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
// Route::get('/', function () {
//     return view('welcome');
// });
Route::post('/shipping/send', 'ShippingController@sendShipping')->name('soa.shipping.send');
Route::post('/shipping/check', 'ShippingController@checkShippingStatus')->name('soa.shipping.check');
Route::post('/shipping/change', 'ShippingController@changeShippingStatus')->name('soa.shipping.received');