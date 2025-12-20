<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\EstimationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('blocks', BlockController::class);
Route::get('estimations/create/step2', [EstimationController::class, 'createStep2'])->name('estimations.create.step2');
Route::resource('estimations', EstimationController::class);
Route::get('estimations/{estimation}/builder', [EstimationController::class, 'builder'])->name('estimations.builder');
Route::get('estimations/{estimation}/pdf', [EstimationController::class, 'exportPdf'])->name('estimations.pdf');

Route::view('settings/setups', 'settings.setups')->name('settings.setups');
Route::view('settings/options', 'settings.options')->name('settings.options');
Route::view('settings/project-types', 'settings.project-types')->name('settings.project-types');
Route::view('settings/translation', 'settings.translation')->name('settings.translation');
