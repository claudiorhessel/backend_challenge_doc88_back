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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '/api/client'], function() use ($router){
    $router->get('/', "ClientController@getAll");
    $router->get('/{id}', "ClientController@get");
    $router->post('/', "ClientController@store");
    $router->put('/{id}', "ClientController@update");
    $router->delete('/{id}', "ClientController@destroy");
});
