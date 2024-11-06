<?php

use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Group
Route::get('/groups', [GroupController::class, 'index'])
    ->middleware('auth')
    ->name('group_index');
Route::post('/group', [GroupController::class, 'store'])
    ->middleware('auth')
    ->name('group_store');
Route::post('/join_group', [GroupController::class, 'join'])
    ->middleware('auth')
    ->name('group_join');
Route::put('/group', [GroupController::class, 'update'])
    ->middleware('auth')
    ->name('group_update');
Route::get('/group/{id}', [GroupController::class, 'show'])
    ->middleware('auth')
    ->name('group_show');
Route::delete('/group/{id}', [GroupController::class, 'destroy'])
    ->middleware('auth')
    ->name('group_destroy');

require __DIR__.'/auth.php';
