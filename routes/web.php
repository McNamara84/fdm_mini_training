<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QRCodesController;
use App\Http\Controllers\Admin\ScanController;
use App\Http\Controllers\QuizController;

Route::get('/admin/qrcodes', [QRCodesController::class, 'index'])->name('admin.qrcodes.index');
Route::prefix('admin')->group(function () {
    Route::get('scan', [ScanController::class, 'index'])->name('admin.scan.index');
    Route::post('scan', [ScanController::class, 'store'])->name('admin.scan.store');
});
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::get('/quiz/results/{questionId}', [QuizController::class, 'getResults'])->name('quiz.results.get');
Route::get('/quiz/summary', [QuizController::class, 'summary'])->name('quiz.summary');
Route::post('/quiz/reset', [QuizController::class, 'reset'])->name('quiz.reset');
Route::post('/quiz/active', [QuizController::class, 'updateActiveQuestion'])->name('quiz.active.update');
