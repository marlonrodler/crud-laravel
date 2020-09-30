<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', "IndexController@api");

Route::post('auth', 'Auth\LoginController@login');

Route::post('auth/password/forgot', 'Auth\PasswordController@forgot');
Route::put('auth/password/forgot', 'Auth\PasswordController@callbackForgot');

Route::middleware(['auth:api'])->group(function () {

    Route::get('auth', 'Auth\LoginController@logged');
    Route::delete('auth', 'Auth\LoginController@logout');

    Route::put('auth/password/reset', 'Auth\PasswordController@reset');

    Route::apiResources([
        'user' => 'UserController',
    ]);
});

Route::apiResources([
    'order' => 'OrderController',
    'product' => 'ProductController',
    'orderItem' => 'OrderItemController',
]);
