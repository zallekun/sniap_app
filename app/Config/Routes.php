<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/test/simple', 'TestController::simple');
$routes->get('/test/all', 'TestController::testAll');
$routes->get('/test/all-models', 'TestController::testAllModels');
$routes->get('/', 'Home::index');
