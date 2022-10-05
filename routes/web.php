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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });
$router->group(['prefix' => 'api'], function () use ($router) {
    //Authenticate
    $router->post('/login', ['uses' => 'UserController@login']);

    // User 
    $router->get('users', ['uses' => 'UserController@index']);
    // $router->post('users', ['uses' => 'UserController@create']);
    // $router->delete('users', ['uses' => 'UserController@delete']);
    // $router->put('users', ['uses' => 'UserController@update']);

    // Category
    $router->get('categories', ['uses' => 'CategoryController@index']);
    $router->post('categories', ['uses' => 'CategoryController@create']);
    $router->put('categories', ['uses' => 'CategoryController@update']);
    $router->delete('categories', ['uses' => 'CategoryController@delete']);

    // Product 
    $router->get('products', ['uses' => 'ProductController@index']);
    $router->post('products', ['uses' => 'ProductController@create']);
    $router->put('products', ['uses' => 'ProductController@update']);
    $router->delete('products', ['uses' => 'ProductController@delete']);

    // Table 
    $router->get('tables', ['uses' => 'TableController@index']);
    $router->post('tables', ['uses' => 'TableController@create']);
    $router->put('tables', ['uses' => 'TableController@update']);
    $router->delete('tables', ['uses' => 'TableController@delete']);

    // Cart
    $router->post('carts', ['uses' => 'CartController@createCartDetail']);
});