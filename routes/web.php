<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::resource('blocks', BlockController::class);
    Route::get('estimations/create/step2', [EstimationController::class, 'createStep2'])->name('estimations.create.step2');
    Route::resource('estimations', EstimationController::class);
    Route::post('estimations/{estimation}/duplicate', [EstimationController::class, 'duplicate'])->name('estimations.duplicate');
    Route::get('estimations/{estimation}/builder', [EstimationController::class, 'builder'])->name('estimations.builder');
    Route::get('estimations/{estimation}/pdf', [EstimationController::class, 'exportPdf'])->name('estimations.pdf');

    Route::view('settings/setups', 'settings.setups')->name('settings.setups');
    Route::view('settings/options', 'settings.options')->name('settings.options');
    Route::view('settings/project-types', 'settings.project-types')->name('settings.project-types');
    Route::view('settings/translation', 'settings.translation')->name('settings.translation');

    Route::get('subscription', function () {
        return view('subscription.index');
    })->name('subscription.index');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::view('plans', 'admin.plans')->name('plans');
        Route::view('coupons', 'admin.coupons')->name('coupons');
        Route::view('users', 'admin.users')->name('users');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
