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
Route::any('/admin/login','Admin\LoginController@login');
Route::any('/admin/loginDo','Admin\LoginController@loginDo');
 Route::prefix('/admin')->middleware('checklogin')->group(function(){


//Route::any('/index','Admin\LoginController@index');

 });

 Route::any('admin/wechat','Admin\LoginController@wechat');
Route::any('admin/wechatdo','Admin\LoginController@wechatdo');
Route::any('admin/checkWechatLogin','Admin\LoginController@checkWechatLogin');
Route::any('admin/list','Admin\LoginController@list');
Route::any('admin/show','Admin\LoginController@show');
Route::any('admin/getcode','Admin\LoginController@getcode');
Route::any('/','Admin\LoginController@index');