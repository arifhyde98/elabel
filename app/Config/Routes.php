<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('loan-request', 'LoanController::storePublic');

$routes->group('', ['filter' => 'guest'], static function ($routes): void {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');

    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister');

    $routes->get('forgot-password', 'AuthController::forgotPassword', ['as' => 'forgot-password']);
    $routes->post('forgot-password', 'AuthController::sendResetLink');

    $routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1', ['as' => 'reset-password']);
    $routes->post('reset-password', 'AuthController::attemptResetPassword');
});

$routes->group('', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'dashboard']);
    $routes->get('bpkb', 'BpkbController::index');
    $routes->get('bpkb/(:num)/download', 'BpkbController::download/$1');
    $routes->get('loans', 'LoanController::index');
    $routes->post('loans', 'LoanController::store');
    $routes->post('logout', 'AuthController::logout', ['as' => 'logout']);
    $routes->group('admin', ['filter' => 'admin'], static function ($routes): void {
        $routes->get('/', 'Admin\DashboardController::index', ['as' => 'admin-dashboard']);
        $routes->post('activity-logs/cleanup', 'Admin\DashboardController::cleanupActivityLogs');
        $routes->get('boxes', 'Admin\BoxController::index');
        $routes->get('boxes/r4', 'Admin\BoxController::index/r4');
        $routes->get('boxes/r2', 'Admin\BoxController::index/r2');
        $routes->get('boxes/r4/create', 'Admin\BoxController::create/r4');
        $routes->get('boxes/r2/create', 'Admin\BoxController::create/r2');
        $routes->get('boxes/create', 'Admin\BoxController::create');
        $routes->post('boxes', 'Admin\BoxController::store');
        $routes->get('boxes/(:num)', 'Admin\BoxController::show/$1');
        $routes->get('boxes/(:num)/label', 'Admin\BoxController::label/$1');
        $routes->post('boxes/(:num)/merge', 'Admin\BoxController::merge/$1');
        $routes->post('boxes/(:num)/delete', 'Admin\BoxController::delete/$1');

        $routes->get('sertifikat-boxes', 'Admin\SertifikatBoxController::index');
        $routes->get('sertifikat-boxes/create', 'Admin\SertifikatBoxController::create');
        $routes->post('sertifikat-boxes', 'Admin\SertifikatBoxController::store');
        $routes->get('sertifikat-boxes/(:num)', 'Admin\SertifikatBoxController::show/$1');
        $routes->get('sertifikat-boxes/(:num)/label', 'Admin\SertifikatBoxController::label/$1');
        $routes->post('sertifikat-boxes/(:num)/merge', 'Admin\SertifikatBoxController::merge/$1');
        $routes->post('sertifikat-boxes/(:num)/split', 'Admin\SertifikatBoxController::split/$1');
        $routes->post('sertifikat-boxes/(:num)/delete', 'Admin\SertifikatBoxController::delete/$1');

        $routes->get('surat-penyerahan-boxes', 'Admin\SuratPenyerahanBoxController::index');
        $routes->get('surat-penyerahan-boxes/create', 'Admin\SuratPenyerahanBoxController::create');
        $routes->post('surat-penyerahan-boxes', 'Admin\SuratPenyerahanBoxController::store');
        $routes->get('surat-penyerahan-boxes/(:num)', 'Admin\SuratPenyerahanBoxController::show/$1');
        $routes->get('surat-penyerahan-boxes/(:num)/label', 'Admin\SuratPenyerahanBoxController::label/$1');
        $routes->post('surat-penyerahan-boxes/(:num)/merge', 'Admin\SuratPenyerahanBoxController::merge/$1');
        $routes->post('surat-penyerahan-boxes/(:num)/split', 'Admin\SuratPenyerahanBoxController::split/$1');
        $routes->post('surat-penyerahan-boxes/(:num)/delete', 'Admin\SuratPenyerahanBoxController::delete/$1');

        $routes->get('bpkb', 'Admin\BpkbController::index');
        $routes->get('bpkb/r4', 'Admin\BpkbController::index/r4');
        $routes->get('bpkb/r2', 'Admin\BpkbController::index/r2');
        $routes->get('bpkb/r4/create', 'Admin\BpkbController::create/r4');
        $routes->get('bpkb/r2/create', 'Admin\BpkbController::create/r2');
        $routes->get('bpkb/create', 'Admin\BpkbController::create');
        $routes->get('bpkb/(:num)/edit', 'Admin\BpkbController::edit/$1');
        $routes->get('bpkb/(:num)', 'Admin\BpkbController::show/$1');
        $routes->get('bpkb/(:num)/view', 'Admin\BpkbController::viewPdf/$1');
        $routes->get('bpkb/export', 'Admin\BpkbController::export');
        $routes->get('bpkb/import-template', 'Admin\BpkbController::downloadImportTemplate');
        $routes->post('bpkb/import', 'Admin\BpkbController::import');
        $routes->post('bpkb', 'Admin\BpkbController::store');
        $routes->post('bpkb/(:num)', 'Admin\BpkbController::update/$1');
        $routes->post('bpkb/(:num)/delete', 'Admin\BpkbController::delete/$1');

        $routes->get('bpkb-deleted', 'Admin\DeletedBpkbController::index');
        $routes->get('bpkb-deleted/create', 'Admin\DeletedBpkbController::create');
        $routes->get('bpkb-deleted/(:num)', 'Admin\DeletedBpkbController::show/$1');
        $routes->post('bpkb-deleted/(:num)/restore', 'Admin\DeletedBpkbController::restore/$1');
        $routes->post('bpkb-deleted/(:num)/delete', 'Admin\DeletedBpkbController::destroy/$1');
        $routes->get('bpkb-deleted/export', 'Admin\DeletedBpkbController::exportExcel');

        $routes->get('sertifikat', 'Admin\SertifikatController::index');
        $routes->get('sertifikat/create', 'Admin\SertifikatController::create');
        $routes->post('sertifikat', 'Admin\SertifikatController::store');
        $routes->post('sertifikat/import', 'Admin\SertifikatController::import');
        $routes->get('sertifikat/export', 'Admin\SertifikatController::export');
        $routes->get('sertifikat/import-template', 'Admin\SertifikatController::downloadImportTemplate');
        $routes->get('sertifikat/(:num)', 'Admin\SertifikatController::show/$1');
        $routes->get('sertifikat/(:num)/view', 'Admin\SertifikatController::viewPdf/$1');
        $routes->get('sertifikat/(:num)/edit', 'Admin\SertifikatController::edit/$1');
        $routes->post('sertifikat/(:num)', 'Admin\SertifikatController::update/$1');
        $routes->post('sertifikat/(:num)/delete', 'Admin\SertifikatController::delete/$1');
        $routes->get('surat-penyerahan', 'Admin\SuratPenyerahanController::index');
        $routes->get('surat-penyerahan/create', 'Admin\SuratPenyerahanController::create');
        $routes->post('surat-penyerahan', 'Admin\SuratPenyerahanController::store');
        $routes->post('surat-penyerahan/import', 'Admin\SuratPenyerahanController::import');
        $routes->get('surat-penyerahan/export', 'Admin\SuratPenyerahanController::export');
        $routes->get('surat-penyerahan/import-template', 'Admin\SuratPenyerahanController::downloadImportTemplate');
        $routes->get('surat-penyerahan/(:num)', 'Admin\SuratPenyerahanController::show/$1');
        $routes->get('surat-penyerahan/(:num)/edit', 'Admin\SuratPenyerahanController::edit/$1');
        $routes->post('surat-penyerahan/(:num)', 'Admin\SuratPenyerahanController::update/$1');
        $routes->post('surat-penyerahan/(:num)/delete', 'Admin\SuratPenyerahanController::delete/$1');

        $routes->get('loans', 'Admin\LoanController::index');
        $routes->post('loans/manual', 'Admin\LoanController::storeManual');
        $routes->post('loans/(:num)/approve', 'Admin\LoanController::approve/$1');
        $routes->post('loans/(:num)/reject', 'Admin\LoanController::reject/$1');
        $routes->post('loans/(:num)/return', 'Admin\LoanController::markReturned/$1');

        $routes->get('users', 'Admin\UserController::index');
        $routes->get('users/create', 'Admin\UserController::create');
        $routes->post('users', 'Admin\UserController::store');
        $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
        $routes->post('users/(:num)', 'Admin\UserController::update/$1');
        $routes->post('users/(:num)/toggle', 'Admin\UserController::toggle/$1');
        $routes->post('users/(:num)/delete', 'Admin\UserController::delete/$1');
    });
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
