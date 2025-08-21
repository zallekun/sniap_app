<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================
// HOMEPAGE
// ==========================================
$routes->get('/', 'Home::index');
$routes->get('test-api', function() {
    return response()->setJSON(['success' => true, 'message' => 'Simple API test working']);
});

// Temporary: Admin API endpoints without auth for testing  
$routes->get('api/admin/dashboard/stats', 'Admin\AdminController::getDashboardStatsApi');
$routes->get('api/admin/dashboard/activity', 'Admin\AdminController::getRecentActivityApi');
$routes->get('api/admin/users', 'Admin\AdminController::getUsersData');
$routes->get('api/admin/events', 'Admin\AdminController::getEventsData');

// User CRUD endpoints without auth for testing
$routes->post('api/admin/users', 'Admin\AdminController::createUser');
$routes->get('api/admin/users/(:num)', 'Admin\AdminController::getUserById/$1');
$routes->post('api/admin/users/(:num)/update', 'Admin\AdminController::updateUser/$1');
$routes->post('api/admin/users/(:num)/delete', 'Admin\AdminController::deleteUser/$1');
$routes->post('api/admin/users/(:num)/toggle-status', 'Admin\AdminController::toggleUserStatus/$1');

// Event CRUD endpoints without auth for testing
$routes->post('api/admin/events', 'Admin\AdminController::createEvent');
$routes->get('api/admin/events/(:num)', 'Admin\AdminController::getEventById/$1');
$routes->post('api/admin/events/(:num)/update', 'Admin\AdminController::updateEvent/$1');
$routes->post('api/admin/events/(:num)/delete', 'Admin\AdminController::deleteEvent/$1');
$routes->post('api/admin/events/(:num)/toggle-status', 'Admin\AdminController::toggleEventStatus/$1');

// MOVED to api/admin to avoid auth filter conflicts

// Test page to verify data sync
$routes->get('test-data', function() {
    $db = \Config\Database::connect();
    $stats = [
        'total_users' => $db->table('users')->countAllResults(),
        'active_events' => $db->table('events')->where('is_active', true)->countAllResults(),
        'total_registrations' => $db->table('registrations')->countAllResults(),
    ];
    
    return view('test_data_sync', ['stats' => $stats]);
});


// ==========================================
// LOAD DEVELOPMENT ROUTES (only in development)
// ==========================================
if (ENVIRONMENT === 'development') {
    require_once APPPATH . 'Config/Routes/Development.php';
}

// ==========================================
// API ROUTES - Clean and Organized
// ==========================================
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    
    // =================
    // SYSTEM HEALTH (no auth)
    // =================
    $routes->get('health', 'HealthApiController::check');                   
    $routes->get('health/database', 'HealthApiController::database');       
    $routes->get('health/jwt', 'HealthApiController::jwt');                 
    $routes->get('version', 'HealthApiController::version');                
    $routes->get('docs', 'HealthApiController::documentation');    
    
    // =================
    // ADMIN SYSTEM ENDPOINTS (no auth for emergency fixes)
    // =================
    $routes->post('admin/fix-registration-status', 'AdminFixController::fixRegistrationStatus');
    $routes->get('admin/check-sync-status', 'AdminFixController::checkSyncStatus');
    $routes->get('admin/enum-values', 'AdminFixController::getEnumValues');
    $routes->get('admin/system-health', 'AdminFixController::systemHealth');

    // =================
    // AUTH ENDPOINTS (no auth)
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
    // PUBLIC EVENT ENDPOINTS (no auth)
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
    // CERTIFICATE VERIFICATION (no auth) - Must be before protected certificates
    // =================
    $routes->get('certificates/verify/(:any)', 'CertificateApiController::verify/$1');

    // =================
    // WEBHOOKS (no auth)
    // =================
    $routes->post('webhooks/midtrans', 'PaymentApiController::midtransWebhook');

    // =================
    // PROTECTED ENDPOINTS (require JWT)
    // =================
    
    // Payments
    $routes->group('payments', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'PaymentApiController::index');                
        $routes->post('/', 'PaymentApiController::create');              
        $routes->get('stats', 'PaymentApiController::stats');            
        $routes->get('(:num)', 'PaymentApiController::show/$1');         
        $routes->post('(:num)/verify', 'PaymentApiController::verify/$1'); 
        $routes->post('(:num)/simulate-success', 'PaymentApiController::simulateSuccess/$1'); 
        $routes->get('(:num)/invoice', 'PaymentApiController::invoice/$1');            
    });

    // Abstract Management
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

    // Review System
    $routes->group('reviews', ['filter' => 'apiauth'], function($routes) {
        $routes->get('assigned', 'ReviewApiController::getAssignedAbstracts');
        $routes->post('/', 'ReviewApiController::submitReview');
        $routes->get('(:num)', 'ReviewApiController::getReview/$1');
        $routes->put('(:num)', 'ReviewApiController::updateReview/$1');
        $routes->get('dashboard', 'ReviewApiController::reviewerDashboard');
    });

    // LOA System
    $routes->group('loa', ['filter' => 'apiauth'], function($routes) {
        $routes->get('generate/(:num)', 'LoaController::generateLoa/$1');
        $routes->get('download/(:num)', 'LoaController::downloadLoa/$1');
        $routes->get('my-loas', 'LoaController::getMyLoas');
        $routes->get('admin/all', 'LoaController::getAllLoas');
    });

    // QR Code System
    $routes->group('qr', ['filter' => 'apiauth'], function($routes) {
        $routes->post('generate', 'QrCodeApiController::generate');
        $routes->get('my-codes', 'QrCodeApiController::myCodes');
        $routes->get('(:num)', 'QrCodeApiController::show/$1');
        $routes->post('scan', 'QrCodeApiController::scan');
        $routes->get('scan-history', 'QrCodeApiController::scanHistory');
    });

    // Certificate System
    $routes->group('certificates', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'CertificateApiController::index');
        $routes->get('(:num)', 'CertificateApiController::show/$1');
        $routes->get('(:num)/download', 'CertificateApiController::download/$1');
        $routes->post('request', 'CertificateApiController::request');
        $routes->put('(:num)/issue', 'CertificateApiController::issue/$1');
    });

    // Voucher System
    $routes->group('vouchers', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'VoucherApiController::index'); // Admin only
        $routes->post('/', 'VoucherApiController::create'); // Admin only
        $routes->post('apply', 'VoucherApiController::apply');
        $routes->get('check/(:any)', 'VoucherApiController::check/$1');
        $routes->get('my-usage', 'VoucherApiController::myUsage');
    });

    // Registration Management
    $routes->group('registrations', ['filter' => 'apiauth'], function($routes) {
        $routes->get('/', 'RegistrationApiController::index');
        $routes->get('(:num)', 'RegistrationApiController::show/$1');
        $routes->post('register', 'RegistrationApiController::register');
        $routes->get('stats', 'RegistrationApiController::stats');
        $routes->get('(:num)/certificate', 'RegistrationApiController::certificate/$1');
        $routes->put('(:num)', 'RegistrationApiController::update/$1');
        $routes->delete('(:num)', 'RegistrationApiController::cancel/$1');
    });

    // Admin Management
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

    // System Configuration
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
    // DEVELOPMENT ONLY ENDPOINTS
    // =================
    if (ENVIRONMENT === 'development') {
        // Test LOA endpoints
        $routes->get('test-loa/generate/(:num)', 'LoaController::generateLoa/$1');
        $routes->get('test-loa/download/(:num)', 'LoaController::downloadLoa/$1');
        $routes->get('test-loa/my-loas', 'LoaController::getMyLoas');
        $routes->get('test-loa/admin/all', 'LoaController::getAllLoas');
        
        // Test Certificate endpoints
        $routes->get('test-certificates/(:num)', 'CertificateApiController::show/$1');
        $routes->get('test-certificates/(:num)/download', 'CertificateApiController::download/$1');
    }
});

// ==========================================
// WEB ROUTES - Clean and Organized
// ==========================================
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    
    // =================
    // AUTHENTICATION
    // =================
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    $routes->get('logout', 'Auth\AuthController::logout');
    
    $routes->get('register', 'Auth\RegisterController::index');
    $routes->post('register', 'Auth\RegisterController::store');
    
    // Alternative auth routes (for clean forms)
    $routes->get('auth/login', 'Auth\AuthController::login');
    $routes->post('auth/login', 'Auth\AuthController::attemptLogin');
    $routes->get('auth/logout', 'Auth\AuthController::logout');
    
    $routes->get('auth/register', 'Auth\RegisterController::index');
    $routes->post('auth/register', 'Auth\RegisterController::store');
    
    // Email Verification (new system)
    $routes->get('auth/verify-code', 'Auth\RegisterController::verifyCodePage');
    $routes->post('auth/verify-code', 'Auth\RegisterController::verifyCode');
    $routes->post('auth/resend-code', 'Auth\RegisterController::resendCode');
    
    // Email Verification (legacy - keep for backward compatibility)
    $routes->get('verify-email/(:any)', 'Auth\RegisterController::verifyEmail/$1');
    $routes->post('auth/resend-verification', 'Auth\RegisterController::resendVerification');
    $routes->get('auth/check-email', 'Auth\RegisterController::checkEmail');

    // =================
    // PROFILE MANAGEMENT
    // =================
    // Note: Main dashboard moved to /audience/dashboard for consistency
    $routes->get('profile/edit', 'User\ProfileController::edit', ['filter' => 'auth']);
    $routes->post('profile/update', 'User\ProfileController::update', ['filter' => 'auth']);
    
    // Dashboard API endpoints
    $routes->get('dashboard/stats', 'DashboardController::stats', ['filter' => 'auth']);
    $routes->get('dashboard/registrations', 'DashboardController::registrations', ['filter' => 'auth']);
    $routes->get('dashboard/events', 'DashboardController::events', ['filter' => 'auth']);
    $routes->get('dashboard/event-schedule', 'DashboardController::eventSchedule', ['filter' => 'auth']);
    $routes->get('dashboard/event-schedule-page', 'DashboardController::eventSchedulePage', ['filter' => 'auth']);
    $routes->post('dashboard/register-event', 'DashboardController::registerEvent', ['filter' => 'auth']);
    
    // =================
    // MAIN DASHBOARD REDIRECT
    // =================
    // Main dashboard entry point - redirects based on user role (must be AFTER specific dashboard routes)
    $routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
    
    // Alternative endpoint for audience registration without CSRF conflicts
    $routes->post('audience/register-event', 'DashboardController::registerEventNoCsrf', ['filter' => 'auth']);
    
    // Alias routes for upcoming events
    $routes->get('events/upcoming', 'DashboardController::events', ['filter' => 'auth']);
    $routes->get('audience/api/events', 'DashboardController::events', ['filter' => 'auth']);
    
    // Legacy profile routes (can be deprecated later)
    $routes->get('dashboard/profile', 'DashboardController::profile', ['filter' => 'auth']);
    $routes->post('dashboard/profile', 'DashboardController::updateProfile', ['filter' => 'auth']);
    $routes->post('dashboard/change-password', 'DashboardController::changePassword', ['filter' => 'auth']);
    
    // Session-based registration endpoints (compatible with Postman collection)
    $routes->get('registrations', 'DashboardController::getMyRegistrations', ['filter' => 'auth']);
    $routes->delete('registrations/(:num)', 'DashboardController::cancelRegistration/$1', ['filter' => 'auth']);

    // =================
    // PAYMENT GATEWAY
    // =================
    $routes->get('payment/(:num)', 'PaymentController::gateway/$1', ['filter' => 'auth']);
    $routes->post('payment/process', 'PaymentController::process', ['filter' => 'auth']);
    $routes->get('payment/simulate/(:num)', 'PaymentController::simulate/$1', ['filter' => 'auth']);
    $routes->post('payment/complete/(:num)', 'PaymentController::complete/$1', ['filter' => 'auth']);

    // =================
    // ROLE-BASED ROUTES
    // =================
    
    // Admin Routes
    $routes->group('admin', ['filter' => 'auth'], function($routes) {
        // Main pages
        $routes->get('/', 'Admin\AdminController::dashboard');
        $routes->get('dashboard', 'Admin\AdminController::dashboard');
        $routes->get('users', 'Admin\AdminController::users');
        $routes->get('events', 'Admin\AdminController::events');
        $routes->get('registrations', 'Admin\AdminController::registrations');
        $routes->get('abstracts', 'Admin\AdminController::abstracts');
        $routes->get('settings', 'Admin\AdminController::settings');
        $routes->get('analytics', 'Admin\AdminController::analytics');
        
        // API endpoints
        $routes->get('api/test', function() {
            return response()->setJSON(['success' => true, 'message' => 'API working']);
        });
        $routes->get('api/stats', 'Admin\AdminController::getDashboardStatsApi');
        $routes->get('api/dashboard/stats', 'Admin\AdminController::getDashboardStatsApi');
        $routes->get('api/dashboard/activity', 'Admin\AdminController::getRecentActivityApi');
        $routes->get('api/users', 'Admin\AdminController::getUsersData');
        $routes->get('api/registrations', 'Admin\AdminController::getRegistrationsData');
        $routes->get('api/abstracts', 'Admin\AdminController::getAbstractsData');
        $routes->get('api/reviewers', 'Admin\AdminController::getReviewersData');
        $routes->get('api/abstract-stats', 'Admin\AdminController::getAbstractStatsApi');
        $routes->post('api/assign-reviewer', 'Admin\AdminController::assignReviewer');
        $routes->post('api/bulk-assign-reviewers', 'Admin\AdminController::bulkAssignReviewers');
        
        // User CRUD endpoints
        $routes->post('api/users', 'Admin\AdminController::createUser');
        $routes->get('api/users/(:num)', 'Admin\AdminController::getUserById/$1');
        $routes->put('api/users/(:num)', 'Admin\AdminController::updateUser/$1');
        $routes->post('api/users/(:num)/update', 'Admin\AdminController::updateUser/$1');
        $routes->delete('api/users/(:num)', 'Admin\AdminController::deleteUser/$1');
        $routes->post('api/users/(:num)/delete', 'Admin\AdminController::deleteUser/$1');
        $routes->post('api/users/(:num)/toggle-status', 'Admin\AdminController::toggleUserStatus/$1');
        
        // Event CRUD endpoints
        $routes->get('api/events', 'Admin\AdminController::getEventsData');
        $routes->post('api/events', 'Admin\AdminController::createEvent');
        $routes->get('api/events/(:num)', 'Admin\AdminController::getEventById/$1');
        $routes->put('api/events/(:num)', 'Admin\AdminController::updateEvent/$1');
        $routes->post('api/events/(:num)/update', 'Admin\AdminController::updateEvent/$1');
        $routes->delete('api/events/(:num)', 'Admin\AdminController::deleteEvent/$1');
        $routes->post('api/events/(:num)/delete', 'Admin\AdminController::deleteEvent/$1');
        $routes->post('api/events/(:num)/toggle-status', 'Admin\AdminController::toggleEventStatus/$1');
    });

    // Presenter Routes
    $routes->group('presenter', ['filter' => 'auth'], function($routes) {
        // Main pages
        $routes->get('/', 'Presenter\PresenterController::dashboard');
        $routes->get('dashboard', 'Presenter\PresenterController::dashboard');
        $routes->get('abstracts', 'Presenter\PresenterController::abstracts');
        $routes->get('presentations', 'Presenter\PresenterController::presentations');
        $routes->get('registrations', 'Presenter\PresenterController::registrations');
        $routes->get('schedule', 'Presenter\PresenterController::schedule');
        
        // API endpoints
        $routes->get('api/stats', 'Presenter\PresenterController::getStatsApi');
        $routes->get('api/abstracts', 'Presenter\PresenterController::getAbstractsApi');
        $routes->post('api/abstracts/submit', 'Presenter\PresenterController::submitAbstract');
        $routes->get('api/abstracts/(:num)', 'Presenter\PresenterController::getAbstractDetails/$1');
        $routes->put('api/abstracts/(:num)', 'Presenter\PresenterController::updateAbstract/$1');
        $routes->get('abstracts/(:num)/download', 'Presenter\PresenterController::downloadAbstract/$1');
        
        // Revision workflow endpoints
        $routes->post('api/abstracts/(:num)/revise', 'Presenter\PresenterController::submitRevision/$1');
        $routes->get('api/abstracts/(:num)/revision-status', 'Presenter\PresenterController::checkRevisionStatus/$1');
        $routes->get('api/revisions/required', 'Presenter\PresenterController::getRevisionRequired');
        
        // Payment system endpoints
        $routes->post('api/payments/create', 'Presenter\PresenterController::createPayment');
        $routes->post('api/payments/(:num)/process', 'Presenter\PresenterController::processPayment/$1');
        $routes->get('api/payments/status/(:num)', 'Presenter\PresenterController::getPaymentStatus/$1');
        $routes->get('api/payments/history', 'Presenter\PresenterController::getPaymentHistory');
        
        // LOA generation endpoints
        $routes->post('api/loa/(:num)/generate', 'Presenter\PresenterController::generateLoa/$1');
        $routes->get('api/loa/(:num)/status', 'Presenter\PresenterController::getLoaStatus/$1');
        $routes->get('loa/(:num)/download', 'Presenter\PresenterController::downloadLoa/$1');
        
        // QR code generation endpoints
        $routes->post('api/qr/(:num)/generate', 'Presenter\PresenterController::generateQRCode/$1');
        $routes->get('api/qr/(:num)/status', 'Presenter\PresenterController::getQRCodeStatus/$1');
        $routes->get('qr/(:num)/download', 'Presenter\PresenterController::downloadQRCode/$1');
        
        // Certificate generation endpoints
        $routes->post('api/certificate/(:num)/generate', 'Presenter\PresenterController::generateCertificate/$1');
        $routes->get('api/certificate/(:num)/status', 'Presenter\PresenterController::getCertificateStatus/$1');
        $routes->get('certificate/(:num)/download', 'Presenter\PresenterController::downloadCertificate/$1');
        $routes->get('api/certificates', 'Presenter\PresenterController::getCertificatesApi');
    });

    // Reviewer Routes
    $routes->group('reviewer', ['filter' => 'auth'], function($routes) {
        // Main pages
        $routes->get('/', 'Reviewer\ReviewerController::dashboard');
        $routes->get('dashboard', 'Reviewer\ReviewerController::dashboard');
        $routes->get('assigned', 'Reviewer\ReviewerController::assigned');
        $routes->get('reviews', 'Reviewer\ReviewerController::reviews');
        $routes->get('review/(:num)', 'Reviewer\ReviewerController::review/$1');
        
        // API endpoints
        $routes->post('submit-review', 'Reviewer\ReviewerController::submitReview');
        $routes->get('api/stats', 'Reviewer\ReviewerController::getStatsApi');
        $routes->get('api/assigned', 'Reviewer\ReviewerController::getAssignedApi');
        $routes->get('api/abstract-details/(:num)', 'Reviewer\ReviewerController::getAbstractDetailsApi/$1');
        $routes->get('api/review-details/(:num)', 'Reviewer\ReviewerController::getReviewDetailsApi/$1');
    });

    // Common Routes (accessible to all authenticated users)
    $routes->get('events', 'DashboardController::eventSchedulePage', ['filter' => 'auth']);
    $routes->get('event-schedule', 'DashboardController::eventSchedulePage', ['filter' => 'auth']);

    // Audience Routes
    $routes->group('audience', ['filter' => 'auth'], function($routes) {
        // Main dashboard - single entry point
        $routes->get('dashboard', 'DashboardController::index');
        $routes->get('registrations', 'DashboardController::audienceRegistrations');
        $routes->get('events', 'DashboardController::eventSchedulePage'); // Legacy route
        $routes->get('certificates', 'DashboardController::certificates');
        $routes->get('payments', 'DashboardController::paymentHistory');
        
        // API endpoints for audience
        $routes->get('api/registrations', 'DashboardController::getAudienceRegistrationsApi');
        $routes->get('api/stats', 'DashboardController::getAudienceStatsApi');
        $routes->get('api/events', 'DashboardController::getUpcomingEventsApi');
        $routes->get('api/certificates', 'DashboardController::getCertificatesApi');
        $routes->get('api/payments', 'DashboardController::getPaymentHistoryApi');
        $routes->get('api/payments/details/(:num)', 'DashboardController::getPaymentDetailsApi/$1');
        $routes->post('cancel-registration', 'DashboardController::cancelRegistration');
    });

    // =================
    // DEVELOPMENT & TESTING ROUTES
    // =================
    if (ENVIRONMENT === 'development') {
        $routes->get('test/qr-generate/(:num)', 'TestController::testQRGenerate/$1');
        $routes->get('test/qr-generate', 'TestController::testQRGenerate');
        $routes->get('test/qr-validate/(:any)', 'TestController::testQRValidate/$1');
        $routes->get('test/qr-scan/(:any)', 'TestController::testQRScan/$1');
        $routes->get('test/qr-data/(:num)', 'TestController::testQRData/$1');
        $routes->get('test/qr-data', 'TestController::testQRData');
        
    }
});