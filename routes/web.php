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
    // Author Test 
    // $router->get('authors', ['uses' => 'AuthorController@showAllAuthors']);
    // $router->get('authors/{id}', ['uses' => 'AuthorController@showOneAuthor']);
    // $router->post('authors', ['uses' => 'AuthorController@create']);
    // $router->delete('authors/{id}', ['uses' => 'AuthorController@delete']);
    // $router->put('authors/{id}', ['uses' => 'AuthorController@update']);

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
});