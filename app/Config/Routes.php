<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Tugas 2 link url
//$routes->get('/kategori', 'Kategori::index');
//$routes->get('/kategori/(:segment)', 'Kategori::detail/$1');

$routes->get('/home', 'Home::index');
$routes->get('/keranjang', 'TransaksiController::index');
$routes->get('/faq', 'FaqController::index');
$routes->get('/kontak', 'KontakController::index');




//CRUD Product
$routes->group('produk', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProdukController::index');
    $routes->post('', 'ProdukController::create');
    $routes->post('edit/(:any)', 'ProdukController::edit/$1');
    $routes->get('delete/(:any)', 'ProdukController::delete/$1');
    $routes->get('download', 'ProdukController::download');
});

// CRUD Kategori Produk
$routes->group('kategoriproduct', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'KategoriProduct::index');
    $routes->post('', 'KategoriProduct::create');
    $routes->post('edit/(:any)', 'KategoriProduct::edit/$1');
    $routes->get('delete/(:any)', 'KategoriProduct::delete/$1');
});

// Untuk Login & Logout
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransaksiController::index');
    $routes->post('', 'TransaksiController::cart_add');
    $routes->post('edit', 'TransaksiController::cart_edit');
    $routes->get('profile', 'Home::profile', ['filter' => 'auth']);
    $routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
    $routes->post('buy', 'TransactionController::buy', ['filter' => 'auth']);
    $routes->get('delete/(:any)', 'TransaksiController::cart_delete/$1');
    $routes->get('clear', 'TransaksiController::cart_clear');

    $routes->get('get-location', 'TransaksiController::getLocation', ['filter' => 'auth']);
    $routes->get('get-cost', 'TransaksiController::getCost', ['filter' => 'auth']);
});
