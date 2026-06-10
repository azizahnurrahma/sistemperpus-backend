<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return 'Hello World';
});

// --- FITUR LOGIN (Bisa diakses tanpa login) ---
$router->post('api/login', 'AuthController@login');
// --- FITUR REGISTER (Bisa diakses tanpa login) ---
$router->post('api/register/admin', 'AuthController@register');




// --- FITUR CRUD BUKU & AKUN (DIKUNCI: Harus Autentikasi/Login Dulu) ---
$router->group(['middleware' => 'auth'], function () use ($router) {

    // RUTE BARU: Register Mahasiswa dikunci di dalam sini!
    $router->post('api/register/mahasiswa', 'AuthController@registerMahasiswa');

    $router->get('api/buku', 'BookController@index');
    $router->post('api/buku', 'BookController@store');
    $router->get('api/buku/{id}', 'BookController@show');
    $router->put('api/buku/{id}', 'BookController@update');
    $router->delete('api/buku/{id}', 'BookController@destroy');

    // Fitur Peminjaman Buku
    $router->post('api/pinjam', 'PeminjamanController@pinjamBuku');
    
    // PERBAIKAN RUTE PROFIL: Ditambahkan api/ di depan dan pakai tanda @
    $router->get('api/user/profile', 'AuthController@getProfile');

    // Fitur Logout
    $router->post('api/logout', 'AuthController@logout');
});