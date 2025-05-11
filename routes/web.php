<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\FlashcardItemController;


use App\Http\Controllers\ChatIaController;
use App\Http\Controllers\CompositionController;
use App\Http\Controllers\WeeklyMissionsController;

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
    Route::post('/new-word', [ FlashcardController::class, 'newWord' ]);
});

Route::prefix('flashcard-item')->group(function () {
    Route::post('/check-text', [ FlashcardItemController::class, 'checkText' ]);
    Route::post('/store-practice', [ FlashcardItemController::class, 'storePractice' ]);
    Route::get('/list/{id}', [ FlashcardItemController::class, 'listByFlashcard' ]);
    Route::post('/favorite/{id}', [ FlashcardItemController::class, 'updateFavorite' ]);

    Route::get('/practice/{flashcard_item_id}/pdf', [FlashcardItemController::class, 'generatePdf']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::get('/list', [CategoryController::class, 'list']);
});

Route::prefix('chat-ia')->group(function () {
    Route::get('/', [ChatIaController::class, 'index'])->name('chat-ia');
    Route::post('/talk-ia', [ChatIaController::class, 'talkIa']);
});

Route::prefix('composition')->group(function () {
    Route::get('/', [CompositionController::class, 'index'])->name('composition');
    Route::post('/check-text', [ CompositionController::class, 'checkText' ]);
    Route::post('/new-idea', [ CompositionController::class, 'newIdea' ]);
    Route::post('/store-practice', [ CompositionController::class, 'storePractice' ]);
    Route::get('/historic', [ CompositionController::class, 'historic' ])->name('composition.historic');
});

Route::prefix('weekly-mission')->group(function () {
    Route::get('/', [WeeklyMissionsController::class, 'index'])->name('weekly-missions');
    Route::get('/create', [ WeeklyMissionsController::class, 'create' ])->name('weekly-missions.create');
    Route::post('/store', [ WeeklyMissionsController::class, 'store' ])->name('weekly-missions.store');
    Route::get('/show/{id}', [ WeeklyMissionsController::class, 'show' ])->name('weekly-missions.show');
    Route::post('/check-text', [ WeeklyMissionsController::class, 'checkText' ]);
});
