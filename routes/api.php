<?php

use Tymon\JWTAuth\Facades\JWTAuth;
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

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::get('posts/{id}', 'PostsController@show');
Route::get('posts', 'PostsController@index');
Route::post('posts/create', 'PostsController@create');
Route::put('posts/{id}/update', 'PostsController@update');
Route::delete('posts/{id}/delete', 'PostsController@delete');
Route::get('user', 'AuthController@validateUser');
