<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'MapController::index');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::registerSave');
$routes->get('verify-pending', 'AuthController::verifyPending');
$routes->get('verify-email/(:any)', 'AuthController::verifyEmail/$1');
$routes->get('resend-verification', 'AuthController::resendVerification');
$routes->get('logout', 'AuthController::logout');

$routes->get('api/map-data', 'MapController::apiMapData');
$routes->get('api/dashboard-stats', 'AdminController::dashboardStats');
$routes->get('api/comments', 'MapController::getComments');
$routes->post('api/comments', 'MapController::postComment');
$routes->get('api/trend-data', 'AdminController::trendData');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('arsip', 'AdminController::archive');
    $routes->get('komentar', 'AdminController::comments');
    $routes->get('komentar/delete/(:num)', 'AdminController::deleteComment/$1');
    $routes->get('curah-hujan', 'AdminController::curahHujan');
    $routes->get('curah-hujan/create', 'AdminController::curahHujanCreate');
    $routes->post('curah-hujan/store', 'AdminController::curahHujanStore');
    $routes->get('curah-hujan/edit/(:num)', 'AdminController::curahHujanEdit/$1');
    $routes->post('curah-hujan/update/(:num)', 'AdminController::curahHujanUpdate/$1');
    $routes->get('curah-hujan/delete/(:num)', 'AdminController::curahHujanDelete/$1');
});
