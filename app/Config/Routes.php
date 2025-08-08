<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default CodeIgniter route
$routes->get('/', 'Home::index');

// ==========================================
// API ROUTES - Clean Version
// ==========================================
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    
    // =================
    // SIMPLE TEST ENDPOINTS (no auth required)
    // =================
    $routes->get('test/db', 'SimpleDebugController::testDb');                    
    $routes->get('test/users', 'SimpleDebugController::testUsers');              
    $routes->get('test/events', 'SimpleDebugController::testEvents');            
    $routes->post('test/insert', 'SimpleDebugController::testInsert');           
    $routes->post('test/model-insert', 'SimpleDebugController::testModelInsert');
    $routes->post('test/json', 'SimpleDebugController::testJson');               
    $routes->post('test/simple-register', 'SimpleDebugController::simpleRegister'); 

    // =================
    // HEALTH ENDPOINTS (no auth required)
    // =================
    $routes->get('health', 'HealthApiController::check');                   
    $routes->get('health/database', 'HealthApiController::database');       
    $routes->get('health/jwt', 'HealthApiController::jwt');                 
    $routes->get('version', 'HealthApiController::version');                
    $routes->get('docs', 'HealthApiController::documentation');    
    
    // =================
    // DEBUG ROUTES
    // =================
    $routes->get('debug/profile', 'DebugController::profile');
    $routes->get('debug/jwt', 'DebugController::jwt');
    $routes->get('debug/jwt-keys', 'DebugController::jwtKeys');
    $routes->get('debug/jwt-test-keys', 'DebugController::jwtTestKeys');
    $routes->get('debug/profile-jwt', 'DebugController::profileJwt');

    // =================
    // SIMPLE PROFILE (NEW)
    // =================
    $routes->get('simple/profile', 'SimpleProfileController::profile');

    // =================
    // AUTH API ROUTES (no auth required)
    // =================
    $routes->group('auth', function($routes) {
        $routes->post('login', 'AuthApiController::login');
        $routes->post('register', 'AuthApiController::register');
        $routes->post('verify', 'AuthApiController::verify');
        $routes->post('refresh', 'AuthApiController::refresh');
        $routes->post('logout', 'AuthApiController::logout');
        $routes->get('profile', 'AuthApiController::profile'); // NO FILTER
    });

    // =================
    // EVENTS API ROUTES (no auth required)
    // =================
    $routes->group('events', function($routes) {
        $routes->get('/', 'EventApiController::index');                    
        $routes->get('(:num)', 'EventApiController::show/$1');             
        $routes->get('(:num)/schedule', 'EventApiController::schedule/$1'); 
        $routes->get('search', 'EventApiController::search');              
        $routes->get('upcoming', 'EventApiController::upcoming');          
        $routes->get('categories', 'EventApiController::categories');      
    });

    // =================
    // PROTECTED ROUTES (require JWT)
    // =================
    $routes->group('registrations', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'RegistrationApiController::index');           
        $routes->post('/', 'RegistrationApiController::create');         
        $routes->get('(:num)', 'RegistrationApiController::show/$1');    
        $routes->put('(:num)', 'RegistrationApiController::update/$1');  
        $routes->delete('(:num)', 'RegistrationApiController::cancel/$1'); 
        $routes->get('(:num)/certificate', 'RegistrationApiController::certificate/$1'); 
        $routes->get('stats', 'RegistrationApiController::stats');       
    });

    $routes->group('payments', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'PaymentApiController::index');                
        $routes->post('/', 'PaymentApiController::create');              
        $routes->get('(:num)', 'PaymentApiController::show/$1');         
        $routes->post('(:num)/verify', 'PaymentApiController::verify/$1'); 
        $routes->get('(:num)/invoice', 'PaymentApiController::invoice/$1'); 
        $routes->get('stats', 'PaymentApiController::stats');            
    });

    // =================
    // WEBHOOKS (no auth)
    // =================
    $routes->post('webhooks/midtrans', 'PaymentApiController::midtransWebhook');

}); // <- INI KURUNG KURAWAL YANG MISSING!

// ==========================================
// WEB ROUTES (if needed)
// ==========================================
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    // Auth routes
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    $routes->get('logout', 'Auth\AuthController::logout');
    
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
    
    // Admin routes
    $routes->group('admin', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Admin\DashboardController::index');
        $routes->get('users', 'Admin\UserController::index');
        // Add more admin routes as needed
    });
});