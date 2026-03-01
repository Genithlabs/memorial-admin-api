<?php

use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MemorialController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\PurchaseRequestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Memorials
    Route::get('/memorials', [MemorialController::class, 'index'])->name('memorials.index');
    Route::get('/memorials/{id}', [MemorialController::class, 'show'])->name('memorials.show');
    Route::get('/memorials/{id}/edit', [MemorialController::class, 'edit'])->name('memorials.edit');
    Route::patch('/memorials/{id}', [MemorialController::class, 'update'])->name('memorials.update');
    Route::delete('/memorials/{id}', [MemorialController::class, 'destroy'])->name('memorials.destroy');

    // Stories (under memorials)
    Route::get('/memorials/{id}/stories', [StoryController::class, 'index'])->name('memorials.stories.index');
    Route::patch('/memorials/{id}/stories/{storyId}/toggle', [StoryController::class, 'toggle'])->name('memorials.stories.toggle');
    Route::patch('/memorials/{id}/stories/{storyId}', [StoryController::class, 'update'])->name('memorials.stories.update');
    Route::delete('/memorials/{id}/stories/{storyId}', [StoryController::class, 'destroy'])->name('memorials.stories.destroy');

    // Comments (under memorials)
    Route::get('/memorials/{id}/comments', [CommentController::class, 'index'])->name('memorials.comments.index');
    Route::patch('/memorials/{id}/comments/{commentId}/toggle', [CommentController::class, 'toggle'])->name('memorials.comments.toggle');
    Route::patch('/memorials/{id}/comments/{commentId}', [CommentController::class, 'update'])->name('memorials.comments.update');
    Route::delete('/memorials/{id}/comments/{commentId}', [CommentController::class, 'destroy'])->name('memorials.comments.destroy');

    // Purchase Requests
    Route::get('/purchases', [PurchaseRequestController::class, 'index'])->name('purchases.index');
    Route::patch('/purchases/{id}/status', [PurchaseRequestController::class, 'updateStatus'])->name('purchases.updateStatus');
    Route::delete('/purchases/{id}', [PurchaseRequestController::class, 'destroy'])->name('purchases.destroy');

    // Questions
    Route::resource('/questions', QuestionController::class)->except(['show']);

    // Admin Logs
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
