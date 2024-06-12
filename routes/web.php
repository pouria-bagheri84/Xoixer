<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostControler;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/@/{user:username}', [ProfileController::class, 'index'])->name('profile');

Route::middleware('auth')->group(function () {
    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])->name('profile.updateImage');
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/post', [PostControler::class ,'store'])->name('post.store');
    Route::put('/post/{post}', [PostControler::class ,'update'])->name('post.update');
    Route::delete('/post/{post}', [PostControler::class ,'destroy'])->name('post.destroy');
    Route::get('/post/download/{attachment}', [PostControler::class, 'downloadAttachment'])->name('post.download');
    Route::post('/post/{post}/reaction', [PostControler::class, 'postReaction'])->name('post.reaction');
    Route::post('/post/{post}/comment', [PostControler::class, 'postComment'])->name('post.comment.create');
});

require __DIR__.'/auth.php';
