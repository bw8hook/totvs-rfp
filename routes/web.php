<?php

use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('newproject');
});
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::get('/newproject', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('newproject');
Route::get('/projects', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('projects');
Route::get('/data', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('data');
Route::get('/subusers', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('subusers');

Route::get('/delete-file/{public_folder}/{folder}/{userid}/{type}/{doc}', [AdminController::class,'delete'])->middleware(['auth', 'verified'])->name('delete');

Route::post('/upload', [UploadController::class, 'upload']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profileUser', [ProfileController::class, 'updateUser'])->name('profile.updateUser');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [ProfileController::class, 'listUsers'])->name('users.list');
    Route::get('/user/{id}', [ProfileController::class, 'editUser'])->name('users.edit');
});

require __DIR__.'/auth.php';