<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\BudgetEstimateController;
use App\Http\Controllers\Backend\BudgetCalculatorController;

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

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile','index')->name('profile');
        Route::get('profile/edit','edit')->name('profile.edit');
        Route::post('profile/update','update')->name('profile.update');
        Route::get('/change-password', 'changePassword')->name('change.password');
        Route::post('/password/update', 'passwordUpdate')->name('password.change.update');
    });

    Route::resource('budget-estimate', BudgetEstimateController::class);

    Route::get('plan/{budgetEstimateID}/budget-calculator', [BudgetCalculatorController::class, 'index'])->name('budget-calculator.index');
    Route::post('plan/budget-calculator/store', [BudgetCalculatorController::class, 'store'])->name('budget-calculator.store');
    Route::delete('plan/budget-calculator/delete', [BudgetCalculatorController::class, 'delete'])->name('budget-calculator.delete');
});
