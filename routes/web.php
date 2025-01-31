<?php

use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UserProjectController;
use App\Http\Controllers\BundlesController;
use App\Http\Controllers\UserRoleController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('knowledge.list');
});

// ROTAS BASE DE CONHECIMENTO
Route::middleware('auth')->group(function () {
    Route::get('/knowledge', [KnowledgeController::class,'index'])->name('knowledge.list');
    Route::get('/knowledge/records/{id}', [KnowledgeController::class,'records'])->name('knowledge.records');
    Route::get('/knowledge/records-errors/{id}', [KnowledgeController::class,'recordsErrors'])->name('knowledge.recordsErrors');
    Route::get('/knowledge/add', [KnowledgeController::class,'createFile'])->name('knowledge.addFile');
    Route::delete('/knowledge/remove/{id}', [KnowledgeController::class, 'destroy'])->name('knowledge.remove');
    Route::post('/knowledge/upload', [KnowledgeController::class, 'uploadFile'])->name('knowledge.upload_file');
    // FILTER AJAX
    Route::get('/knowledge/filter', [KnowledgeController::class,'filter'])->name('knowledge.filter');
    Route::get('/knowledge/records/filter/{id}', [KnowledgeController::class,'recordsFilter'])->name('knowledge.recordsFilter');
    Route::delete('/knowledge/records/{id}', [KnowledgeController::class,'recordsFilterRemove'])->name('knowledge.recordsFilterRemove');
    Route::post('/knowledge/update-infos/{id}', [KnowledgeController::class,'updateInfos'])->name('knowledge.updateInfos');
    //Route::get('/knowledge/add', [KnowledgeController::class,'create'])->middleware(['auth', 'verified'])->name('knowledge.add');
    //Route::get('/knowledge/base-all', [KnowledgeController::class,'listall'])->name('knowledge.listall');
});


Route::get('/new-project', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('newproject');
Route::get('/project-result/{id}', [NewProjectController::class,'result'])->middleware(['auth', 'verified'])->name('result');
Route::get('/projects', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('projects');
Route::get('/data', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('data');


Route::get('/upload-mentoria', [NewProjectController::class,'uploadMentoria'])->middleware(['auth', 'verified'])->name('newproject.upload_mentoria');

Route::get('/base/{bundle}/{userid}/{filename?}', [FileController::class, 'getFile']);


// USUÁRIO
Route::get('/users/filter', [UserProjectController::class, 'filter'])->middleware(['auth', 'verified'])->name('users.filter');
Route::get('/users/list', [UserProjectController::class,'listUsers'])->middleware(['auth', 'verified'])->name('listUsers');
Route::get('/users/new', [UserProjectController::class, 'create'])->middleware(['auth', 'verified'])->name('userproject.register');
Route::post('/users/edit', [UserProjectController::class, 'update'])->middleware(['auth', 'verified'])->name('user.edit');
Route::get('/users/edit/{id}', [UserProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('userproject.edit');
Route::delete('/users/remove/{id}', [UserProjectController::class, 'remove'])->middleware(['auth', 'verified'])->name('userproject.remove');
Route::get('/users/new-password/{id}', [UserProjectController::class, 'newpassword'])->middleware(['auth', 'verified'])->name('userproject.newpassword');

Route::get('/import/{id}', [ImportController::class, 'listRecords'])->name('import.listRecords');
Route::get('/import/erro/{id}', [ImportController::class, 'listErroRecords'])->name('import.listErroRecords');


//BUNDLES (PACOTES E PRODUTOS)
Route::get('/bundles/filter', [BundlesController::class, 'filter'])->middleware(['auth', 'verified'])->name('bundles.filter');
Route::get('/bundles/list', [BundlesController::class,'list'])->middleware(['auth', 'verified'])->name('bundles.list');
Route::get('/bundles/new', [BundlesController::class, 'create'])->middleware(['auth', 'verified'])->name('bundles.register');
Route::get('/bundles/edit/{id}', [BundlesController::class, 'edit'])->middleware(['auth', 'verified'])->name('bundles.edit');
Route::delete('/bundles/remove/{id}', [BundlesController::class, 'remove'])->middleware(['auth', 'verified'])->name('bundles.remove');
Route::post('/bundles/register', [BundlesController::class, 'register'])->middleware(['auth', 'verified'])->name('bundles.register');
Route::post('/bundles/edit', [BundlesController::class, 'edit_user'])->middleware(['auth', 'verified'])->name('bundles.edit_user');

// CONTROLE DE PERFIS
Route::get('/users-role', [UserRoleController::class,'list'])->middleware(['auth', 'verified'])->name('listUsers');
Route::post('/users-role', [UserRoleController::class, 'update'])->middleware(['auth', 'verified'])->name('user.edit');
Route::put('/users-role/{id}', [UserRoleController::class, 'edit'])->middleware(['auth', 'verified'])->name('userproject.edit');
Route::delete('/users-role/{id}', [UserRoleController::class, 'remove'])->middleware(['auth', 'verified'])->name('userproject.remove');


Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/delete-file/{public_folder}/{folder}/{userid}/{type}/{doc}', [AdminController::class,'delete'])->middleware(['auth', 'verified'])->name('delete');

Route::post('/upload', [UploadController::class, 'upload']);
Route::post('/upload-file', [UploadController::class, 'uploadFile'])->name('upload.file');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profileUser', [ProfileController::class, 'updateUser'])->name('profile.updateUser');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [ProfileController::class, 'listUsers'])->name('users.list');
    Route::get('/user/{id}', [ProfileController::class, 'editUser'])->name('users.edit');
});


require __DIR__.'/auth.php';