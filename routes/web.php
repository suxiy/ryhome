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
});

Route::any('/wechat', 'WeChatController@serve');
Route::any('/wechat/active', 'WeChatController@activePage');
Route::any('/wechat/testCreate', 'WeChatController@testCreate');
Route::any('/wechat/testGet', 'WeChatController@testGet');
Route::any('/wechat/testList', 'WeChatController@testList');
Route::any('/wechat/testReceive', 'WeChatController@testReceive');
Route::any('/wechat/testUpdate', 'WeChatController@testUpdate');
Route::any('/wechat/testDelete', 'WeChatController@testDelete');
Route::any('/wechat/testDisable', 'WeChatController@testDisable');
Route::any('/wechat/testOpenidList', 'WeChatController@testOpenidList');
Route::any('/wechat/testGetToken', 'WeChatController@testGetToken');
Route::any('/wechat/getCard', 'WeChatController@getCard');
Route::any('/wechat/getCardByOpenid', 'WeChatController@getCardByOpenid');
Route::any('/api/wechat/getUserCard', 'WeChatController@getUserCard');

