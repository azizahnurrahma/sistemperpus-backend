<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return 'Hello World';
});

// --- FITUR LOGIN (Bisa diakses tanpa login) ---
$router->post('api/login', 'AuthController@login');


// --- FITUR CRUD BUKU (DIKUNCI: Harus Autentikasi/Login Dulu) ---
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('api/buku', 'BookController@index');
    $router->post('api/buku', 'BookController@store');
    $router->get('api/buku/{id}', 'BookController@show');
    $router->put('api/buku/{id}', 'BookController@update');
    $router->delete('api/buku/{id}', 'BookController@destroy');
});