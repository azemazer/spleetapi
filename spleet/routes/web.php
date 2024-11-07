<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;

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

// Receipt
Route::post('/ticket', [ReceiptController::class, 'store'])
    ->middleware('auth')
    ->name('ticket_store');
Route::get('/ticket/{id}', [ReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('ticket_show');
Route::put('/ticket', [ReceiptController::class, 'update'])
    ->middleware('auth')
    ->name('ticket_update');
Route::delete('/ticket/{id}', [ReceiptController::class, 'destroy'])
    ->middleware('auth')
    ->name('ticket_destroy');

// Product
Route::put('/product', [ProductController::class, 'update'])
    ->middleware('auth')
    ->name('product_update');

require __DIR__.'/auth.php';
