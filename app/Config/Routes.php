<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'MapController::index');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerSave');
$routes->get('logout', 'AuthController::logout');

$routes->get('api/map-data', 'MapController::apiMapData');
$routes->get('api/dashboard-stats', 'AdminController::dashboardStats');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('curah-hujan', 'AdminController::curahHujan');
    $routes->get('curah-hujan/create', 'AdminController::curahHujanCreate');
    $routes->post('curah-hujan/store', 'AdminController::curahHujanStore');
    $routes->get('curah-hujan/edit/(:num)', 'AdminController::curahHujanEdit/$1');
    $routes->post('curah-hujan/update/(:num)', 'AdminController::curahHujanUpdate/$1');
    $routes->get('curah-hujan/delete/(:num)', 'AdminController::curahHujanDelete/$1');
});
