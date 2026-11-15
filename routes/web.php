<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ManageItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/annonces/nouvelle', [ItemController::class, 'create'])->name('items.create');
Route::post('/annonces', [ItemController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('items.store');
Route::get('/annonces/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');

Route::post('/annonces/{item}/messages', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('items.messages.store');

Route::prefix('/annonces/{item}/gerer/{token}')
    ->controller(ManageItemController::class)
    ->group(function () {
        Route::get('/', 'show')->name('items.manage');
        Route::patch('/rendu', 'resolve')->name('items.resolve');
        Route::delete('/', 'destroy')->name('items.destroy');
    });
