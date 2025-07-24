<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Dashboard routes
$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/export-aktivitas-pdf', 'Dashboard::exportAktivitasPdf');
$routes->get('dashboard/export-aktivitas-excel', 'Dashboard::exportAktivitasExcel');

// Protected routes (need authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Barang Routes
    $routes->get('barang', 'Barang::index');
    $routes->get('barang/create', 'Barang::create');
    $routes->post('barang/store', 'Barang::store');
    $routes->get('barang/edit/(:num)', 'Barang::edit/$1');
    $routes->post('barang/update/(:num)', 'Barang::update/$1');
    $routes->post('barang/delete/(:num)', 'Barang::delete/$1');
    $routes->get('barang/export-pdf', 'Barang::exportPdf');
    $routes->get('barang/export-excel', 'Barang::exportExcel');
    $routes->post('barang/update-stok-langsung', 'Barang::updateStokLangsung');
    $routes->get('barang/sync-stok', 'Barang::syncStok');
    $routes->get('barang/sync-stok/(:num)', 'Barang::syncStok/$1');

    // Barang Masuk Routes
    $routes->get('barang-masuk', 'BarangMasuk::index');
    $routes->get('barang-masuk/create', 'BarangMasuk::create');
    $routes->post('barang-masuk/store', 'BarangMasuk::store');
    $routes->get('barang-masuk/edit/(:num)', 'BarangMasuk::edit/$1');
    $routes->post('barang-masuk/update/(:num)', 'BarangMasuk::update/$1');
    $routes->post('barang-masuk/delete/(:num)', 'BarangMasuk::delete/$1');
    $routes->get('barang-masuk/export-pdf', 'BarangMasuk::exportPdf');
    $routes->get('barang-masuk/export-excel', 'BarangMasuk::exportExcel');

    // Barang Keluar Routes
    $routes->get('barang-keluar', 'BarangKeluar::index');
    $routes->get('barang-keluar/create', 'BarangKeluar::create');
    $routes->post('barang-keluar/store', 'BarangKeluar::store');
    $routes->get('barang-keluar/edit/(:num)', 'BarangKeluar::edit/$1');
    $routes->post('barang-keluar/update/(:num)', 'BarangKeluar::update/$1');
    $routes->post('barang-keluar/delete/(:num)', 'BarangKeluar::delete/$1');
    $routes->get('barang-keluar/export-pdf', 'BarangKeluar::exportPdf');
    $routes->get('barang-keluar/export-excel', 'BarangKeluar::exportExcel');
});
