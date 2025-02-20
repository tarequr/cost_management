<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthOtpController;

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
    return view('welcome');
});

Route::controller(AuthOtpController::class)->group(function () {
    Route::get('otp','index')->name('otp.index');
    Route::post('/otp-generate', 'generateOtp')->name('otp.generate');
    Route::get('/otp-generate', 'verifyOtp')->name('register.otp.verify');
    Route::post('/otp-verification-check', 'checkVarification')->name('check.otp.verification');
    // Route::get('/check-email', 'checkEmail')->name('email.check');
    Route::post('otp-resend', 'resend')->name('otp.resend');
});
