<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

// Auth Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::process_login');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::process_register');
$routes->get('/logout', 'Auth::logout');

// Protected Routes (Butuh Filter nanti)
$routes->get('/', 'Dokumen::index');
$routes->get('create', 'Dokumen::create');
$routes->get('edit/(:num)', 'Dokumen::edit/$1');
$routes->post('update/(:num)', 'Dokumen::update/$1');
$routes->post('rename/(:num)', 'Dokumen::rename/$1'); // [BARU] Rename
$routes->get('get-content/(:num)', 'Dokumen::get_content/$1');

// Sharing Routes
$routes->post('share/public/(:num)', 'Dokumen::toggle_public/$1'); // [BARU] Public Link
$routes->post('share/invite/(:num)', 'Dokumen::invite_user/$1');   // [BARU] Invite