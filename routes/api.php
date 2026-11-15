<?php

use App\Http\Controllers\Api\V1\ContactMessageController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\ManageItemController;
use Illuminate\Support\Facades\Route;

/*
| API de l'application mobile : https://lost.yonkou.info/api/v1/...
| Pas de compte : l'auteur d'une annonce la gère avec son jeton
| (en-tête X-Manage-Token), reçu à la publication.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/meta', [ItemController::class, 'meta'])->name('meta');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('items.store');
    Route::get('/items/{item}', [ItemController::class, 'show'])
        ->whereNumber('item')
        ->name('items.show');

    Route::post('/items/{item}/messages', [ContactMessageController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('items.messages.store');

    Route::controller(ManageItemController::class)
        ->middleware('throttle:30,1')
        ->group(function () {
            Route::get('/items/{item}/manage', 'show')->name('items.manage');
            Route::patch('/items/{item}/resolve', 'resolve')->name('items.resolve');
            Route::delete('/items/{item}', 'destroy')->name('items.destroy');
        });
});
