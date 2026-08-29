<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
$routes->get('/', 'Public\HomeController::index');
$routes->get('ajukan-permintaan', 'Public\ApplicationController::create');
$routes->post('ajukan-permintaan', 'Public\ApplicationController::store');
$routes->get('pendaftaran-berhasil/(:segment)', 'Public\ApplicationController::success/$1');
$routes->get('unduh-bukti/(:segment)', 'Public\ApplicationController::downloadPdf/$1');
$routes->get('cek-status', 'Public\StatusController::index');
$routes->post('cek-status', 'Public\StatusController::search');
$routes->get('verifikasi-tiket/(:segment)/(:segment)', 'Public\VerifyController::verify/$1/$2');

// ==========================================
// 2. AUTH ROUTES
// ==========================================
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::processLogin');
    $routes->get('logout', 'Auth\AuthController::logout');
});

// ==========================================
// 3. API ROUTES
// ==========================================
$routes->group('api', static function ($routes) {
    $routes->get('schedules/slots', 'Api\ScheduleApiController::getSlots');
    $routes->get('services/(:num)/subcategories', 'Api\ScheduleApiController::getSubcategories/$1');
    $routes->get('notifications/unread', 'Api\NotificationApiController::getUnread');
    $routes->post('notifications/(:num)/read', 'Api\NotificationApiController::markAsRead/$1');
});

// ==========================================
// 4. ADMIN & OFFICER DASHBOARD ROUTES (Protected by AuthFilter)
// ==========================================
$routes->group('admin', ['filter' => 'auth:superadmin,admin,officer,pimpinan'], static function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Applications Management
    $routes->get('applications', 'Admin\ApplicationController::index');
    $routes->get('applications/(:num)', 'Admin\ApplicationController::show/$1');
    $routes->post('applications/(:num)/status', 'Admin\ApplicationController::updateStatus/$1');
    $routes->post('applications/(:num)/resend/(:segment)', 'Admin\ApplicationController::resendNotification/$1/$2');

    // Zoom Meeting Management (Manual Input)
    $routes->post('applications/(:num)/zoom', 'Admin\ZoomController::save/$1');

    // Schedules Management
    $routes->get('schedules', 'Admin\ScheduleController::index');
    $routes->post('schedules', 'Admin\ScheduleController::store');
    $routes->post('schedules/(:num)/toggle', 'Admin\ScheduleController::toggleStatus/$1');
    $routes->post('schedules/(:num)/delete', 'Admin\ScheduleController::delete/$1');
    $routes->post('schedules/holiday', 'Admin\ScheduleController::storeHoliday');
    $routes->post('schedules/holiday/(:num)/delete', 'Admin\ScheduleController::deleteHoliday/$1');

    // Services Catalog
    $routes->get('services', 'Admin\ServiceController::index');
    $routes->post('services', 'Admin\ServiceController::store');
    $routes->post('services/(:num)', 'Admin\ServiceController::update/$1');
    $routes->post('services/subcategory', 'Admin\ServiceController::storeSubcategory');
    $routes->post('services/subcategory/(:num)/delete', 'Admin\ServiceController::deleteSubcategory/$1');

    // Users Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users', 'Admin\UserController::store');
    $routes->post('users/(:num)', 'Admin\UserController::update/$1');

    // Reports
    $routes->get('reports', 'Admin\ReportController::index');
    $routes->get('reports/excel', 'Admin\ReportController::exportExcel');
    $routes->get('reports/pdf', 'Admin\ReportController::exportPdf');

    // Settings
    $routes->get('settings', 'Admin\SettingController::index');
    $routes->post('settings', 'Admin\SettingController::update');
    $routes->post('settings/test-whatsapp', 'Admin\SettingController::testWhatsApp');

    // Audit Logs
    $routes->get('audit-logs', 'Admin\AuditLogController::index');
});

// ==========================================
// 5. OFFICER QUEUE & CHECK-IN ROUTES (Protected)
// ==========================================
$routes->group('officer', ['filter' => 'auth:officer,admin,superadmin'], static function ($routes) {
    $routes->get('checkin', 'Officer\CheckInController::index');
    $routes->post('applications/(:num)/checkin', 'Officer\CheckInController::checkIn/$1');
    $routes->post('applications/(:num)/start', 'Officer\CheckInController::startService/$1');
    $routes->post('applications/(:num)/finish', 'Officer\CheckInController::finishService/$1');
    $routes->post('applications/(:num)/absent', 'Officer\CheckInController::markAbsent/$1');
});
