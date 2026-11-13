<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root redirect
$routes->get('/', static function () {
    if (session()->get('isLoggedIn')) {
        return redirect()->to('/dashboard');
    }
    return redirect()->to('/login');
});

// Autentikasi Publik
$routes->match(['GET', 'POST'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Rute Terautentikasi (Admin dan Kasir)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Transaksi
    $routes->get('transactions', 'Transactions::index');
    $routes->get('transactions/new', 'Transactions::new');
    $routes->post('transactions/create', 'Transactions::create');
    $routes->get('transactions/(:num)', 'Transactions::show/$1');
    $routes->post('transactions/(:num)/status', 'Transactions::updateStatus/$1');
    $routes->post('transactions/(:num)/pay', 'Transactions::pay/$1');
    $routes->post('transactions/(:num)/cancel', 'Transactions::cancel/$1');
    $routes->get('transactions/(:num)/print', 'Transactions::printReceipt/$1');

    // Pelanggan
    $routes->get('customers', 'Customers::index');
    $routes->get('customers/new', 'Customers::new');
    $routes->post('customers/create', 'Customers::create');
    $routes->get('customers/(:num)', 'Customers::show/$1');
    $routes->get('customers/(:num)/edit', 'Customers::edit/$1');
    $routes->post('customers/(:num)/update', 'Customers::update/$1');
    $routes->post('customers/(:num)/delete', 'Customers::delete/$1');
});

// Rute Khusus Administrator
$routes->group('', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    // Layanan
    $routes->get('services', 'Services::index');
    $routes->post('services/create', 'Services::create');
    $routes->post('services/(:num)/update', 'Services::update/$1');
    $routes->post('services/(:num)/toggle', 'Services::toggle/$1');
    $routes->post('services/(:num)/delete', 'Services::delete/$1');

    // Pengguna
    $routes->get('users', 'Users::index');
    $routes->post('users/create', 'Users::create');
    $routes->post('users/(:num)/update', 'Users::update/$1');
    $routes->post('users/(:num)/reset-password', 'Users::resetPassword/$1');
    $routes->post('users/(:num)/toggle', 'Users::toggle/$1');

    // Pengaturan
    $routes->match(['GET', 'POST'], 'settings', 'Settings::index');

    // Laporan
    $routes->get('reports', 'Reports::index');
    $routes->get('reports/export', 'Reports::export');

    // Audit Log
    $routes->get('audit-logs', 'AuditLogs::index');
});
