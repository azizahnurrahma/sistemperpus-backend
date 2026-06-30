<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () {
    return 'Sistem Perpus API';
});

// ============================================
// PUBLIC ROUTES
// ============================================
$router->post('login', 'AuthController@login');

$router->post('api/register/admin', 'AuthController@register');

// ============================================
// AUTHENTICATED ROUTES (*Required)
// ============================================
$router->group(['middleware' => 'auth'], function () use ($router) {
    
    $router->post('logout', 'AuthController@logout');
    $router->get('user/profil', 'AuthController@getProfile');
    
    $router->get('dashboard/mahasiswa', 'DashboardController@mahasiswa');
    
    $router->get('buku', 'BookController@index');
    $router->get('buku/{id}', 'BookController@show');
    $router->get('buku/kategori/{id}', 'BookController@getByKategori');
    $router->get('buku/rak/{id}', 'BookController@getByRak');
    $router->get('buku/stok/{id}', 'BookController@cekStok');
    
    $router->get('kategori', 'KategoriController@index');
    
    $router->get('peminjaman', 'PeminjamanController@index');
    $router->get('peminjaman/riwayat', 'PeminjamanController@riwayat');
    $router->get('peminjaman/{id}/aktif', 'PeminjamanController@pinjamanAktif');
    $router->get('peminjaman/{id}', 'PeminjamanController@show');
    $router->post('peminjaman', 'PeminjamanController@pinjamBuku');
    
    $router->get('denda/saya', 'DendaController@saya');
    $router->get('denda/{id}', 'DendaController@show');
    
    // ============================================
    // NOTIFICATION ROUTES
    // ============================================
    $router->get('notifications', 'NotificationController@index');
    $router->patch('notifications/{id}/read', 'NotificationController@markAsRead');
    $router->patch('notifications/read-all', 'NotificationController@markAllAsRead');
    $router->delete('notifications/{id}', 'NotificationController@destroy');
    $router->delete('notifications', 'NotificationController@destroyAll');
    
    // ============================================
    // ADMIN ROUTES (*Admin)
    // ============================================
    $router->group(['middleware' => 'admin'], function () use ($router) {
        $router->get('dashboard/admin', 'DashboardController@admin');
        
        $router->get('admin/user', 'AdminUserController@index');
        $router->post('admin/user', 'AdminUserController@store');
        $router->get('admin/user/{id}', 'AdminUserController@show');
        $router->put('admin/user/{id}', 'AdminUserController@update');
        $router->delete('admin/user/{id}', 'AdminUserController@destroy');
        
        $router->get('program-studi', 'ProgramStudiController@index');
        $router->get('program-studi/{id}', 'ProgramStudiController@show');
        
        $router->post('buku', 'BookController@store');
        $router->put('buku/{id}', 'BookController@update');
        $router->delete('buku/{id}', 'BookController@destroy');
        
        $router->patch('peminjaman/{id}/setujui', 'PeminjamanController@setujui');
        $router->patch('peminjaman/{id}/tolak', 'PeminjamanController@tolak');
        $router->patch('peminjaman/{id}/kembalikan', 'PeminjamanController@kembalikan');
        
        $router->get('denda', 'DendaController@index');
        $router->patch('denda/{id}/bayar', 'DendaController@bayar');
        
        $router->get('laporan/peminjaman', 'LaporanController@peminjaman');
        $router->get('laporan/pengembalian', 'LaporanController@pengembalian');
        $router->get('laporan/denda', 'LaporanController@denda');
        $router->get('laporan/buku', 'LaporanController@buku');
        $router->get('laporan/pengguna', 'LaporanController@pengguna');
        $router->get('laporan/export/pdf', 'LaporanController@exportPdf');
        $router->get('laporan/export/excel', 'LaporanController@exportExcel');
    });
});