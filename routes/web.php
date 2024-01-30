<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB Routes
|--------------------------------------------------------------------------
|
| Here is where you can register WEB routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "web" middleware group. Enjoy building your WEB!
|
*/

Route::get('/', function () {
    return response()->json(['status'=>'Available', 'time'=>NOW()]);
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
