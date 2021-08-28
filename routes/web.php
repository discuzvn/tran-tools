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

$router->group(['prefix' => 'api'], function ($router) {
  $router->get('sources/{code}/translations', 'SourceController@getTranslations');
  $router->get('sources/{code}/translations/{file}', 'SourceController@getTranslation');
  $router->post('translations/{file}', 'SourceController@saveTranslation');
});

$router->get('/{route:.*}/', function ()  {
  return view('app');
});
