<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default CodeIgniter route
$routes->get('/', 'Home::index');

// ==========================================
// LOAD DEVELOPMENT ROUTES (only in development)
// ==========================================
if (ENVIRONMENT === 'development') {
    require_once APPPATH . 'Config/Routes/Development.php';
}

// ==========================================
// API ROUTES - Production Clean Version
// ==========================================
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    
    // =================
    // HEALTH ENDPOINTS (no auth required)
    // =================
    $routes->get('health', 'HealthApiController::check');                   
    $routes->get('health/database', 'HealthApiController::database');       
    $routes->get('health/jwt', 'HealthApiController::jwt');                 
    $routes->get('version', 'HealthApiController::version');                
    $routes->get('docs', 'HealthApiController::documentation');    
    
    // =================
    // ADMIN ROUTES (no auth for now)
    // =================
    $routes->post('admin/fix-registration-status', 'AdminFixController::fixRegistrationStatus');
    $routes->get('admin/check-sync-status', 'AdminFixController::checkSyncStatus');
    $routes->get('admin/enum-values', 'AdminFixController::getEnumValues');
    $routes->get('admin/system-health', 'AdminFixController::systemHealth');

    // =================
    // AUTH API ROUTES (no auth required)
    // =================
    $routes->group('auth', function($routes) {
        $routes->post('login', 'AuthApiController::login');
        $routes->post('register', 'AuthApiController::register');
        $routes->post('verify', 'AuthApiController::verify');
        $routes->post('verify-user', 'AuthApiController::verifyUser');
        $routes->post('refresh', 'AuthApiController::refresh');
        $routes->post('logout', 'AuthApiController::logout');
        $routes->get('profile', 'AuthApiController::profile');
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
    $routes->group('payments', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'PaymentApiController::index');                
        $routes->post('/', 'PaymentApiController::create');              
        $routes->get('stats', 'PaymentApiController::stats');            
        $routes->get('(:num)', 'PaymentApiController::show/$1');         
        $routes->post('(:num)/verify', 'PaymentApiController::verify/$1'); 
        $routes->post('(:num)/simulate-success', 'PaymentApiController::simulateSuccess/$1'); 
        $routes->get('(:num)/invoice', 'PaymentApiController::invoice/$1');            
    });

    // =================
    // ABSTRACT MANAGEMENT (require JWT)
    // =================
    $routes->group('abstracts', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'AbstractApiController::index');
        $routes->post('/', 'AbstractApiController::create');
        $routes->get('upload-info', 'AbstractApiController::getUploadInfo');
        $routes->get('categories', 'AbstractApiController::categories');
        $routes->get('stats', 'AbstractApiController::stats');
        $routes->get('(:num)', 'AbstractApiController::show/$1');
        $routes->put('(:num)', 'AbstractApiController::update/$1');
        $routes->get('(:num)/download', 'AbstractApiController::downloadFile/$1');
        $routes->get('(:num)/reviews', 'AbstractApiController::getReviews/$1');
        $routes->post('(:num)/revision', 'AbstractApiController::submitRevision/$1');
    });

    // =================
    // REVIEW SYSTEM (require JWT)
    // =================
    $routes->group('reviews', ['filter' => 'apiauth'], function($routes) {
        $routes->get('assigned', 'ReviewApiController::getAssignedAbstracts');
        $routes->post('/', 'ReviewApiController::submitReview');
        $routes->get('(:num)', 'ReviewApiController::getReview/$1');
        $routes->put('(:num)', 'ReviewApiController::updateReview/$1');
        $routes->get('dashboard', 'ReviewApiController::reviewerDashboard');
    });

    // =================
    // LOA SYSTEM (require JWT)
    // =================
    $routes->group('loa', ['filter' => 'apiauth'], function($routes) {
        $routes->get('generate/(:num)', 'LoaController::generateLoa/$1');
        $routes->get('download/(:num)', 'LoaController::downloadLoa/$1');
        $routes->get('my-loas', 'LoaController::getMyLoas');
        $routes->get('admin/all', 'LoaController::getAllLoas');
    });

    // =================
    // DEVELOPMENT LOA ROUTES (only in development)
    // =================
    if (ENVIRONMENT === 'development') {
        $routes->get('test-loa/generate/(:num)', 'LoaController::generateLoa/$1');
        $routes->get('test-loa/download/(:num)', 'LoaController::downloadLoa/$1');
        $routes->get('test-loa/my-loas', 'LoaController::getMyLoas');
        $routes->get('test-loa/admin/all', 'LoaController::getAllLoas');
        
        // Certificate testing endpoints (development only)
        $routes->get('test-certificates/(:num)', 'CertificateApiController::show/$1');
        $routes->get('test-certificates/(:num)/download', 'CertificateApiController::download/$1');
    }

    // =================
    // QR CODE SYSTEM (require JWT)
    // =================
    $routes->group('qr', ['filter' => 'apiauth'], function($routes) {
        $routes->post('generate', 'QrCodeApiController::generate');
        $routes->get('my-codes', 'QrCodeApiController::myCodes');
        $routes->get('(:num)', 'QrCodeApiController::show/$1');
        $routes->post('scan', 'QrCodeApiController::scan');
        $routes->get('scan-history', 'QrCodeApiController::scanHistory');
    });

    // =================
    // CERTIFICATE SYSTEM (require JWT)
    // =================
    $routes->group('certificates', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'CertificateApiController::index');
        $routes->get('(:num)', 'CertificateApiController::show/$1');
        $routes->get('(:num)/download', 'CertificateApiController::download/$1');
        $routes->post('request', 'CertificateApiController::request');
        $routes->put('(:num)/issue', 'CertificateApiController::issue/$1');
    });

    // =================
    // CERTIFICATE VERIFICATION (no auth required)
    // =================
    $routes->get('certificates/verify/(:any)', 'CertificateApiController::verify/$1');

    // =================
    // VOUCHER SYSTEM (require JWT)
    // =================
    $routes->group('vouchers', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'VoucherApiController::index'); // Admin only
        $routes->post('/', 'VoucherApiController::create'); // Admin only
        $routes->post('apply', 'VoucherApiController::apply');
        $routes->get('check/(:any)', 'VoucherApiController::check/$1');
        $routes->get('my-usage', 'VoucherApiController::myUsage');
    });

    // =================
    // ADMIN MANAGEMENT (require JWT + admin role)
    // =================
    $routes->group('admin', ['filter' => 'apiauth'], function($routes) {
        $routes->get('dashboard', 'AdminApiController::dashboard');
        $routes->get('users', 'AdminApiController::users');
        $routes->get('abstracts', 'AdminApiController::abstracts');
        $routes->put('abstracts/(:num)/assign', 'AdminApiController::assignReviewer/$1');
        $routes->get('export/(:segment)', 'AdminApiController::export/$1');
        $routes->get('settings', 'AdminApiController::getSettings');
        $routes->put('settings', 'AdminApiController::updateSettings');
        $routes->get('presenter-progress', 'AdminApiController::presenterProgress');
    });

    // =================
    // SYSTEM CONFIGURATION (require JWT + admin role)
    // =================
    $routes->group('system', ['filter' => 'apiauth'], function($routes) {
        $routes->get('config', 'SystemApiController::getConfig');
        $routes->put('config', 'SystemApiController::updateConfig');
        $routes->put('registration/(:segment)', 'SystemApiController::toggleRegistration/$1');
        $routes->put('abstract/(:segment)', 'SystemApiController::toggleAbstract/$1');
        $routes->put('event-mode/(:segment)', 'SystemApiController::setEventMode/$1');
        $routes->put('deadlines', 'SystemApiController::setDeadlines');
        $routes->put('email-config', 'SystemApiController::configureEmail');
        $routes->get('status', 'SystemApiController::status');
    });

    // =================
    // ENHANCED REGISTRATION ENDPOINTS (require JWT)
    // =================
    $routes->group('registrations', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'RegistrationApiController::index'); // Get user registrations
        $routes->get('(:num)', 'RegistrationApiController::show/$1'); // Get specific registration
        $routes->post('register', 'RegistrationApiController::register'); // New presenter registration
        $routes->get('stats', 'RegistrationApiController::stats');
        $routes->get('(:num)/certificate', 'RegistrationApiController::certificate/$1');
        $routes->put('(:num)', 'RegistrationApiController::update/$1');
        $routes->delete('(:num)', 'RegistrationApiController::cancel/$1');
    });

    // =================
    // ENHANCED EVENT ENDPOINTS (no auth required for schedule)
    // =================
    $routes->get('events/(:num)/schedule', 'EventApiController::schedule/$1');

    // =================
    // WEBHOOKS (no auth)
    // =================
    $routes->post('webhooks/midtrans', 'PaymentApiController::midtransWebhook');
});

// ==========================================
// WEB ROUTES - Production Clean Version
// ==========================================
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    
    // =================
    // AUTHENTICATION ROUTES
    // =================
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    $routes->get('logout', 'Auth\AuthController::logout');
    
    // =================
    // REGISTRATION ROUTES
    // =================
    $routes->get('register', 'Auth\RegisterController::index');
    $routes->post('register', 'Auth\RegisterController::store');
    $routes->get('auth/verify-email/(:any)', 'Auth\RegisterController::verifyEmail/$1');
    $routes->post('auth/resend-verification', 'Auth\RegisterController::resendVerification');
    $routes->get('auth/check-email', 'Auth\RegisterController::checkEmail');

    // =================
    // DASHBOARD ROUTES
    // =================
    $routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
    
    // =================
    // ADMIN ROUTES
    // =================
    $routes->group('admin', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'Admin\DashboardController::index');
        $routes->get('users', 'Admin\UserController::index');
        // Add more admin routes as needed
    });

    $routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    // ... existing routes ...
    
    // QR Test Routes
    $routes->get('test/qr-generate/(:num)', 'TestController::testQRGenerate/$1');
    $routes->get('test/qr-generate', 'TestController::testQRGenerate');
    $routes->get('test/qr-validate/(:any)', 'TestController::testQRValidate/$1');
    $routes->get('test/qr-scan/(:any)', 'TestController::testQRScan/$1');
    $routes->get('test/qr-data/(:num)', 'TestController::testQRData/$1');
    $routes->get('test/qr-data', 'TestController::testQRData');
});
});