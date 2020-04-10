<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/'], static function ($app) {
    $app->get('storage/test/', 'FileStorageController@test');
//    $app->put('todo/{id}/', 'TodoController@update');
//    $app->delete('todo/{id}/', 'TodoController@delete');
});
