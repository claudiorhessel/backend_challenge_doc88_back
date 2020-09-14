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

$router->get('/api/v1/', function () use ($router) {
    $path = rtrim(app()->basePath('public/swagger.json'));
    $json = json_decode(file_get_contents($path), true);
    return response()->json($json);
});

$router->group(['prefix' => '/api/v1/client'], function() use ($router) {
    $router->get('/', "ClientController@getPagination");
    $router->get('/all', "ClientController@getAll");
    $router->get('/{id}', "ClientController@getById");
    $router->post('/', "ClientController@store");
    $router->put('/{id}', "ClientController@update");
    $router->delete('/{id}', "ClientController@destroy");
});

$router->group(['prefix' => '/api/v1/product'], function() use ($router) {
    $router->get('/', "ProductController@getPagination");
    $router->get('/all', "ProductController@getAll");
    $router->get('/{id}', "ProductController@getById");
    $router->post('/', "ProductController@store");
    $router->put('/{id}', "ProductController@update");
    $router->delete('/{id}', "ProductController@destroy");
});

$router->group(['prefix' => '/api/v1/type'], function() use ($router) {
    $router->get('/', "TypeController@getPagination");
    $router->get('/all', "TypeController@getAll");
    $router->get('/{id}', "TypeController@get");
    $router->post('/', "TypeController@store");
    $router->put('/{id}', "TypeController@update");
    $router->delete('/{id}', "TypeController@destroy");
});

$router->group(['prefix' => '/api/v1/order'], function() use ($router) {
    $router->get('/', "OrderController@getPagination");
    $router->get('/all', "OrderController@getAll");
    $router->get('/{id}', "OrderController@get");
    $router->post('/', "OrderController@store");
    $router->put('/{id}', "OrderController@update");
    $router->delete('/{id}', "OrderController@destroy");
});

$router->get('/{any:.*}', "HomeController@notFound");
$router->post('/{any:.*}', "HomeController@notFound");
$router->put('/{any:.*}', "HomeController@notFound");
$router->delete('/{any:.*}', "HomeController@notFound");
$router->patch('/{any:.*}', "HomeController@notFound");
$router->options('/{any:.*}', "HomeController@notFound");
