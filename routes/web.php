<?php

use App\Http\Controllers\AuthOtpController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\FinalBudgetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskDependencyController;
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
    return view('welcome');
});

Route::controller(AuthOtpController::class)->group(function () {
    Route::get('otp', 'index')->name('otp.index');
    Route::post('/otp-generate', 'generateOtp')->name('otp.generate');
    Route::get('/otp-generate', 'verifyOtp')->name('register.otp.verify');
    Route::post('/otp-verification-check', 'checkVarification')->name('check.otp.verification');
    Route::get('/email/verified', 'verified')->name('email.verified');
    Route::post('otp-resend', 'resend')->name('otp.resend');
});

// Project routes
Route::resource('projects', ProjectController::class);

// Task routes (nested under projects)
Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

// Task dependency routes
Route::get('tasks/{task}/dependencies', [TaskDependencyController::class, 'index'])->name('tasks.dependencies.index');
Route::post('tasks/{task}/dependencies', [TaskDependencyController::class, 'store'])->name('tasks.dependencies.store');

// Budget routes
Route::get('projects/{project}/draft-budget', [BudgetController::class, 'draft'])->name('budgets.draft');
Route::post('projects/{project}/draft-budget/recalculate', [BudgetController::class, 'recalculate'])->name('budgets.draft.recalculate');
Route::post('tasks/{task}/actual-cost', [BudgetController::class, 'updateActualCost'])->name('tasks.actualcost.update');

// Final budget (EVM) route
Route::get('projects/{project}/final-budget', [FinalBudgetController::class, 'show'])->name('budgets.final');
