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


$router->group(['prefix'=>env('API_ROOT_URL').'/auth'], function () use ($router) {
    $router->post('/login', ['uses'=>'Adm\AuthController@login']);
    $router->get('/about', ['uses'=>'Adm\TestController@about']);
});

$router->group(['prefix'=>env('API_ROOT_URL').'/event'], function () use ($router) {
	$router->get('', ['uses'=>'EventController@list']);
    $router->get('/{id:[\d]+}', ['uses'=>'EventController@get']);
    $router->get('/kids', ['uses'=>'EventController@list_kids']);
    $router->get('/summary', ['uses'=>'EventController@list_summary']);
    $router->put('/{id:[\d]+}', ['uses'=>'EventController@update']);
    $router->post('', ['uses'=>'EventController@create']);
    $router->delete('/{id:[\d]+}', ['uses'=>'EventController@delete']);
});

$router->group(['prefix'=>env('API_ROOT_URL').'/eventcode'], function () use ($router) {
	$router->get('', ['uses'=>'EventCodeController@list_active']);
    $router->get('/all', ['uses'=>'EventCodeController@list_all']);
    $router->get('/{id:[\d]+}', ['uses'=>'EventCodeController@get']);
    $router->put('/{id:[\d]+}', ['uses'=>'EventCodeController@update']);
    $router->post('', ['uses'=>'EventCodeController@create']);
    $router->delete('/{id:[\d]+}', ['uses'=>'EventCodeController@delete']);
});

