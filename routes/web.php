<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/@/{user:username}', [ProfileController::class, 'index'])->name('profile');

Route::get('/g%/{group:slug}', [GroupController::class, 'profile'])->name('group.profile');

Route::middleware('auth')->group(function () {
    Route::post('/profile/update-images', [ProfileController::class, 'updateImage'])->name('profile.updateImage');
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/user/follow/{user}', [UserController::class, 'follow'])->name('user.follow');

    // POST
    Route::prefix('/post')->group(function (){
        Route::post('/', [PostController::class ,'store'])->name('post.store');
        Route::put('/{post}', [PostController::class ,'update'])->name('post.update');
        Route::delete('/{post}', [PostController::class ,'destroy'])->name('post.destroy');
        Route::get('/download/{attachment}', [PostController::class, 'downloadAttachment'])->name('post.download');
        Route::post('/{post}/reaction', [PostController::class, 'postReaction'])->name('post.reaction');
        Route::get('/{post}', [PostController::class, 'view'])->name('post.view');
        Route::post('/{post}/comment', [PostController::class, 'postComment'])->name('comment.create');
    });

    // COMMENT
    Route::delete('/comment/{comment}', [PostController::class, 'deleteComment'])->name('comment.delete');
    Route::put('/comment/{comment}', [PostController::class, 'updateComment'])->name('comment.update');
    Route::post('/comment/{comment}/reaction', [PostController::class, 'commentReaction'])->name('comment.reaction');

    // GROUP
    Route::prefix('/group')->group(function (){
        Route::post('/', [GroupController::class, 'store'])->name('group.create');
        Route::post('/update-images/{group:slug}', [GroupController::class, 'updateImage'])->name('group.update.images');
        Route::post('/invite-users/{group:slug}', [GroupController::class, 'inviteUsers'])->name('group.invite.users');
        Route::post('/join-users/{group:slug}', [GroupController::class, 'joinUsers'])->name('group.join.users');
        Route::post('/approve-request/{group:slug}', [GroupController::class, 'approveRequests'])->name('group.approve.requests');
        Route::post('/change-role/{group:slug}', [GroupController::class, 'changeRole'])->name('group.change.role');
        Route::put('/update/{group:slug}', [GroupController::class, 'update'])->name('group.update');
        Route::delete('/remove-user/{group:slug}', [GroupController::class, 'removeUser'])->name('group.remove.user');
    });
});

Route::get('/group/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])->name('group.approve.invitation');

require __DIR__.'/auth.php';
