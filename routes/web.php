<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('blocks', BlockController::class);
    Route::resource('estimations', EstimationController::class);
    Route::post('estimations/{estimation}/duplicate', [EstimationController::class, 'duplicate'])->name('estimations.duplicate');
    Route::get('estimations/{estimation}/builder', [EstimationController::class, 'builder'])->name('estimations.builder');
    Route::get('estimations/{estimation}/pdf', [EstimationController::class, 'exportPdf'])->name('estimations.pdf');
    Route::post('estimations/{estimation}/save-as-template', [EstimationController::class, 'saveAsTemplate'])->name('estimations.save-as-template');

    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/builder', [TemplateController::class, 'builder'])->name('templates.builder');
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
    Route::post('/templates/{template}/create-estimation', [TemplateController::class, 'createEstimation'])->name('templates.create-estimation');

    Route::view('settings/setups', 'settings.setups')->name('settings.setups');
    Route::view('settings/options', 'settings.options')->name('settings.options');
    Route::view('settings/project-types', 'settings.project-types')->name('settings.project-types');
    Route::view('settings/translation', 'settings.translation')->name('settings.translation');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::view('plans', 'admin.plans')->name('plans');
        Route::view('coupons', 'admin.coupons')->name('coupons');
        Route::view('users', 'admin.users')->name('users');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/subscription', function () {
        return view('subscription');
    })->name('subscription');

    Route::post('/billing/checkout/{plan}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
});

require __DIR__.'/auth.php';
