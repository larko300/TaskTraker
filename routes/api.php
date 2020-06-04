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

Route::post('login', 'API\AuthController@login');
Route::post('register', 'API\AuthController@register');

Route::middleware('auth:api')->group(function () {
    Route::resource('users', 'API\UserController', [
        'except' => [ 'show', 'edit', 'create' ]
    ]);
    Route::resource('tasks', 'API\TaskController', [
        'except' => [ 'show', 'edit', 'create' ]
    ]);
    Route::put('tasks/{task}/status', 'API\TaskController@setStatus');
    Route::put('users/{user}/tasks/{task}', 'API\TaskController@setUserTask');
});
