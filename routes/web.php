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

$router->group(['prefix' => '/api/v1/client'], function() use ($router){
    $router->get('/', "ClientController@getAll");
    $router->get('/{id}', "ClientController@getById");
    $router->post('/', "ClientController@store");
    $router->put('/{id}', "ClientController@update");
    $router->delete('/{id}', "ClientController@destroy");
});

$router->group(['prefix' => '/api/v1/pastel'], function() use ($router){
    $router->get('/', "PastelController@getAll");
    $router->get('/{id}', "PastelController@get");
    $router->post('/', "PastelController@store");
    $router->put('/{id}', "PastelController@update");
    $router->delete('/{id}', "PastelController@destroy");
});

$router->group(['prefix' => '/api/v1/product'], function() use ($router){
    $router->get('/', "ProductController@getAll");
    $router->get('/{id}', "ProductController@get");
    $router->post('/', "ProductController@store");
    $router->put('/{id}', "ProductController@update");
    $router->delete('/{id}', "ProductController@destroy");
});

$router->group(['prefix' => '/api/v1/type'], function() use ($router){
    $router->get('/', "TypeController@getAll");
    $router->get('/{id}', "TypeController@get");
    $router->post('/', "TypeController@store");
    $router->put('/{id}', "TypeController@update");
    $router->delete('/{id}', "TypeController@destroy");
});
