<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default routes
$routes->get('/', 'Home::index');

// ==================== AUTHENTICATION ROUTES ====================

// Login Routes
$routes->group('auth', function($routes) {
    // Login
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    
    // Logout
    $routes->get('logout', 'Auth\AuthController::logout');
    
    // Forgot Password
    $routes->get('forgot-password', 'Auth\AuthController::forgotPassword');
    $routes->post('forgot-password', 'Auth\AuthController::sendResetEmail');
    
    // Reset Password
    $routes->get('reset-password/(:segment)', 'Auth\AuthController::resetPassword/$1');
    $routes->post('reset-password', 'Auth\AuthController::updatePassword');
});

// Registration Routes
$routes->group('register', function($routes) {
    // Registration
    $routes->get('/', 'Auth\RegisterController::index');
    $routes->post('/', 'Auth\RegisterController::store');
    
    // Email Verification
    $routes->get('verify-email/(:segment)', 'Auth\RegisterController::verifyEmail/$1');
    $routes->post('resend-verification', 'Auth\RegisterController::resendVerification');
    
    // AJAX Routes
    $routes->get('check-email', 'Auth\RegisterController::checkEmail');
    $routes->get('stats', 'Auth\RegisterController::stats');
});

// Alternative login/register routes (for easier access)
$routes->get('login', 'Auth\AuthController::login');
$routes->post('login', 'Auth\AuthController::attemptLogin');
$routes->get('register', 'Auth\RegisterController::index');
$routes->post('register', 'Auth\RegisterController::store');
$routes->get('logout', 'Auth\AuthController::logout');

// ==================== DASHBOARD ROUTES (Protected) ====================

// General Dashboard (will redirect based on role)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Admin Dashboard Routes
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('events', 'Admin\EventController::index');
    $routes->get('abstracts', 'Admin\AbstractController::index');
    $routes->get('settings', 'Admin\SystemController::index');
});

// Reviewer Dashboard Routes
$routes->group('reviewer', ['filter' => 'auth:reviewer'], function($routes) {
    $routes->get('dashboard', 'Reviewer\DashboardController::index');
    $routes->get('abstracts', 'Reviewer\AbstractReviewController::index');
});

// Presenter Dashboard Routes
$routes->group('presenter', ['filter' => 'auth:presenter'], function($routes) {
    $routes->get('dashboard', 'Presenter\DashboardController::index');
    $routes->get('abstracts', 'Presenter\AbstractSubmissionController::index');
    $routes->get('registrations', 'Presenter\RegistrationController::index');
});

// User Profile Routes (All authenticated users)
$routes->group('profile', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'User\ProfileController::index');
    $routes->post('update', 'User\ProfileController::update');
    $routes->post('change-password', 'User\ProfileController::changePassword');
});

// ==================== PUBLIC ROUTES ====================

// Public Event Listing
$routes->get('events', 'Public\EventListController::index');
$routes->get('events/(:segment)', 'Public\EventListController::show/$1');

// Certificate Download (with token)
$routes->get('certificate/(:segment)', 'Public\CertificateController::download/$1');

// ==================== API ROUTES ====================

$routes->group('api', function($routes) {
    // Public API
    $routes->get('events', 'API\EventApiController::list');
    $routes->get('events/(:segment)', 'API\EventApiController::show/$1');
    
    // Protected API (requires authentication)
    $routes->group('auth', ['filter' => 'api_auth'], function($routes) {
        $routes->get('registrations', 'API\RegistrationApiController::list');
        $routes->post('registrations', 'API\RegistrationApiController::create');
        $routes->post('payments', 'API\PaymentApiController::process');
    });
});

// ==================== TESTING ROUTES (Development Only) ====================

// Test Routes - Remove in production
if (ENVIRONMENT !== 'production') {
    $routes->group('test', function($routes) {
        $routes->get('simple', 'TestController::simpleTest');
        $routes->get('all-models', 'TestController::testAllModels');
        $routes->get('debug', 'TestController::debugDatabase');
    });
}

// ==================== ERROR ROUTES ====================

// Remove error controller override for now - use default CodeIgniter error handling
// $routes->set404Override('ErrorController::show404');
$routes->setAutoRoute(false); // Disable auto routing for security