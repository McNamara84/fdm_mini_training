<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminController;

Route::get('/', [QuestController::class, 'showWelcome'])->name('welcome');
Route::get('/quiz', [QuestController::class, 'showQuiz'])->name('quiz');
Route::get('/story', [QuestController::class, 'showStory'])->name('story');
Route::get('/summary', [QuestController::class, 'showSummary'])->name('summary');

// Route für das Speichern von Votes via AJAX oder Livewire.
Route::post('/vote', [VoteController::class, 'store'])->name('vote.store');

// Admin-Dashboard (Zugriff hierüber benötigen Trainingsleiter-Accounts)
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
