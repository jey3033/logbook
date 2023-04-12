<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/csrf', function() {
    return json_encode(['CSRF' => csrf_token()]);
});

Route::controller(AuthController::class)->group(function() {
    Route::get('/auth/redirect', 'redirectToProvider');
    Route::get('/auth/callback', 'handleProviderCallback');
});

Route::controller(PageController::class)->group(function() {
    Route::get('/dashboard', 'dashboard')->name('home');
    Route::get('/log', 'logList');
    Route::get('/user/logout', 'logout');
    Route::get('/user/profile', 'profile');
    Route::get('user/{uuid}/edit', 'edituser');
    Route::get('/user', 'user');
    
    Route::get('/division', 'divisionlist');
    Route::get('/division/create', 'divisioncreate');
});

Route::controller(UserController::class)->group(function() {
    Route::post('/login', 'auth');
    Route::get('/getuser', 'get_logged_user');
    Route::get('/logout', 'logout');
    Route::get('/listuser', 'get_list_user');
    Route::post('/user/create', 'create_user');
    Route::post('/user/update', 'update_user');
    Route::get('/user/{id}/deactuser', 'deactivate_user');
    Route::get('/user/{id}/actuser', 'activate_user');
    Route::post('/setsupervisor', 'set_supervisor');
    Route::get('/user/checkpassword', 'check_password');
    Route::post('/user/setphoto', 'edit_profile');
    Route::post('/user/verifyOTP', 'otp_verification');
    Route::post('/user/password/change', 'change_password');

    Route::post('/bypasschange', 'change_pass_bypass');
});

Route::controller(LogController::class)->group(function () {
    Route::get('/log/list', 'index');
    Route::post('/log/store', 'store');
    Route::get('/log/{id}', 'view')->name("viewlog");
    Route::post('/log/response/{id}', 'response');
    Route::post('/log/update/{id}', 'amend');

    Route::get("/get-outstanding-log", "getLogOutstanding");
    Route::get("/get-personal-log", "getLogPersonal");
});

Route::controller(DivisionController::class)->group(function () {
    Route::get('/division/list', 'index');
    Route::post('/division/store', "store");
});