<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return response()->json(['appName' => 'inamen']);
});

$router->get('/test', function () use ($router) {
    return response()->json(['test' => 'aja']);
});

$router->group(['prefix' => '/api/v1'], function () use ($router) {
    $router->get('/user', 'UserController@find');
    $router->post('/user/signin', 'UserController@signin');
    $router->post('/user/signup', 'UserController@signup');
    $router->patch('/user', 'UserController@update');
    $router->delete('/user', 'UserController@delete');
});
