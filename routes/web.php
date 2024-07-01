<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostControler;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/@/{user:username}', [ProfileController::class, 'index'])->name('profile');

Route::get('/g%/{group:slug}', [GroupController::class, 'profile'])->name('group.profile');

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

    Route::post('/post/{post}/comment', [PostControler::class, 'postComment'])->name('comment.create');
    Route::delete('/comment/{comment}', [PostControler::class, 'deleteComment'])->name('comment.delete');
    Route::put('/comment/{comment}', [PostControler::class, 'updateComment'])->name('comment.update');
    Route::post('/comment/{comment}/reaction', [PostControler::class, 'commentReaction'])->name('comment.reaction');

    Route::post('/group', [GroupController::class, 'store'])->name('group.create');
    Route::post('/group/update-images/{group:slug}', [GroupController::class, 'updateImage'])->name('group.update.images');
    Route::post('/group/invite-users/{group:slug}', [GroupController::class, 'inviteUsers'])->name('group.invite.users');
    Route::post('/group/join-users/{group:slug}', [GroupController::class, 'joinUsers'])->name('group.join.users');
    Route::post('/group/approve-request/{group:slug}', [GroupController::class, 'approveRequests'])->name('group.approve.requests');
});
    Route::get('/group/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])->name('group.approve.invitation');

require __DIR__.'/auth.php';
