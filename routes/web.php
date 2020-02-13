<?php

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

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/paypal', function () {
    return view('paypal');
})->name('paypal');

Route::post('/pagar', 'PagamentoController@pagarComPayPal')->name('pagar_com_paypal');

Route::get('/status', 'PagamentoController@statusPagamento')->name('status');