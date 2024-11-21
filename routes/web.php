<?php

use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UserProjectController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('knowledge.list');
});

Route::get('/knowledge-base', [KnowledgeController::class,'index'])->middleware(['auth', 'verified'])->name('knowledge.list');
Route::get('/add-knowledge', [KnowledgeController::class,'create'])->middleware(['auth', 'verified'])->name('knowledge.add');
Route::delete('/remove-knowledge/{id}', [KnowledgeController::class, 'destroy'])->middleware(['auth', 'verified'])->name('knowledge.remove');


Route::get('/new-project', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('newproject');
Route::get('/projects', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('projects');
Route::get('/data', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('data');

// USUÁRIO
Route::get('/list-users', [UserProjectController::class,'listUsers'])->middleware(['auth', 'verified'])->name('listUsers');
Route::get('/new-user', [UserProjectController::class, 'create'])->middleware(['auth', 'verified'])->name('userproject.register');
Route::get('/edit-user/{id}', [UserProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('userproject.edit');
Route::delete('/remove/{id}', [UserProjectController::class, 'remove'])->middleware(['auth', 'verified'])->name('userproject.remove');
Route::get('/new-password/{id}', [UserProjectController::class, 'newpassword'])->middleware(['auth', 'verified'])->name('userproject.newpassword');


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