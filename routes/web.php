<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\FlashcardItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('dashboard')->group(function () {
    Route::get('/', [ DashboardController::class, 'index' ])->name('dashboard');
});

Route::get('/', [ FlashcardController::class, 'index' ])->name('flashcard');

Route::prefix('flashcard')->group(function () {
    Route::get('/create', [ FlashcardController::class, 'create' ])->name('flashcard.create');
    Route::post('/store', [ FlashcardController::class, 'store' ])->name('flashcard.store');
    Route::get('/show/{id}', [ FlashcardController::class, 'show' ])->name('flashcard.show');

    Route::get('/stats', [FlashcardController::class, 'getStats']);


    Route::get('/practice/{flashcard_id}/pdf', [FlashcardController::class, 'generatePdf'])->name('flashcard.pdf');

    Route::post('/new-idea', [ FlashcardController::class, 'newIdea' ]);
});

Route::prefix('flashcard-item')->group(function () {
    Route::post('/check-text', [ FlashcardItemController::class, 'checkText' ]);
    Route::post('/store-practice', [ FlashcardItemController::class, 'storePractice' ]);
    Route::get('/list/{id}', [ FlashcardItemController::class, 'listByFlashcard' ]);

    Route::get('/practice/{flashcard_item_id}/pdf', [FlashcardItemController::class, 'generatePdf']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::get('/list', [CategoryController::class, 'list']);
});