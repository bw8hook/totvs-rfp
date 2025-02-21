<?php

use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\KnowledgeRecordsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRecordsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UserProjectController;
use App\Http\Controllers\BundlesController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\MentoriaController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('knowledge.list');
});

Route::get('/mentoria/processar', [MentoriaController::class, 'processarMentoria']);
Route::post('/get-answer', [MentoriaController::class, 'getAnswer']);


// ROTAS BASE DE CONHECIMENTO
Route::middleware('auth')->group(function () {
    // BASE DE CONHECIMENTO
    Route::get('/knowledge', [KnowledgeController::class,'index'])->name('knowledge.list');
    Route::get('/knowledge/add', [KnowledgeController::class,'create'])->name('knowledge.create');
    Route::post('/knowledge/add', [KnowledgeController::class, 'upload'])->name('knowledge.upload_file');
    Route::get('/knowledge/download/{id}', [KnowledgeController::class,'download'])->name('knowledge.download');
    Route::delete('/knowledge/remove/{id}', [KnowledgeController::class, 'destroy'])->name('knowledge.remove');
        //AJAX
        Route::get('/knowledge/filter', [KnowledgeController::class,'filter'])->name('knowledge.filter');
        Route::post('/knowledge/update-infos/{id}', [KnowledgeController::class,'updateInfos'])->name('knowledge.updateInfos');
    //CRON PARA AUTOMATIZAR  SUBIDA PARA IA
    Route::get('/knowledge/cron', [KnowledgeController::class,'cron'])->name('knowledge.cron');

    // REGISTROS DA BASE DE CONHECIMENTO
    Route::get('/knowledge/records/{id}', [KnowledgeRecordsController::class,'index'])->name('knowledge.records');
    Route::get('/knowledge/records/view/{id}', [KnowledgeRecordsController::class,'view'])->name('knowledge.records.view');
    Route::get('/knowledge/records/errors/{id}', [KnowledgeRecordsController::class,'errors'])->name('knowledge.recordsErrors');
    Route::get('/knowledge/records/processing/{id}', [KnowledgeRecordsController::class,'processing'])->name('knowledge.records.processing');
        //AJAX
        Route::get('/knowledge/records/filter/{id}', [KnowledgeRecordsController::class,'filter'])->name('knowledge.recordsFilter');
        Route::delete('/knowledge/records/{id}', [KnowledgeRecordsController::class,'filterRemove'])->name('knowledge.recordsFilterRemove');
        Route::post('/knowledge/update-record/{id}', [KnowledgeRecordsController::class,'updateDetails'])->name('knowledge.records.update');
        Route::get('/knowledge/records-errors/filter/{id}', [KnowledgeRecordsController::class,'filterError'])->name('knowledge.recordsFilterErrors');
});



// ROTAS PROJETOS
Route::middleware('auth')->group(function () {
    // NOVO PROJETO
    Route::get('/projects', [ProjectController::class,'index'])->name(name: 'project.list');
    Route::get('/project/add', [ProjectController::class,'add'])->name('project.add');
    Route::post('/project/add', [ProjectController::class, 'create'])->name('project.create');
    Route::get('/project/{id}/files', [ProjectController::class,'file'])->name('project.file');
    Route::post('/project/{id}/files', [ProjectController::class,'file_upload'])->name('project.file.uploads');
    Route::get('/project/{id}/detail', [ProjectController::class,'detail'])->name('project.detail');

    Route::delete('/project/remove/{id}', [ProjectController::class, 'destroy'])->name('project.remove');
        //AJAX
        Route::get('/project/filter', [ProjectController::class,'filter'])->name('project.filter');
        Route::get('/project/filter-detail', [ProjectController::class,'filterDetail'])->name('project.filterDetail');
        
        Route::post('/project/update-infos/{id}', [ProjectController::class,'updateInfos'])->name('project.updateInfos');
    //CRON PARA AUTOMATIZAR  SUBIDA PARA IA
    Route::get('/project/cron', [ProjectController::class,'cron'])->name('knowledge.cron');

    // REGISTROS DA BASE DE CONHECIMENTO
    Route::get('/project/answers/{id}', [ProjectRecordsController::class,'answer'])->name('project.answer');
    Route::get('/project/records/{id}', [ProjectRecordsController::class,'index'])->name('project.records');
    Route::get('/project/records-errors/{id}', [ProjectRecordsController::class,'errors'])->name('project.recordsErrors');
    Route::get('/project/records/processing/{id}', [ProjectRecordsController::class,'processing'])->name('project.records.processing');
        //AJAX
        Route::get('/project/records/filter/{id}', [ProjectRecordsController::class,'filter'])->name('project.recordsFilter');
        Route::get('/project/records/references/{id}', [ProjectRecordsController::class,'references'])->name('project.records.references');
        Route::get('/project/records/detail/{id}', [ProjectRecordsController::class,'detail'])->name('project.records.detail');
        Route::get('/project/answer/filter/{id}', [ProjectRecordsController::class,'filterAnswer'])->name('project.recordsFilterAnswer');
        Route::delete('/project/records/{id}', [ProjectRecordsController::class,'filterRemove'])->name('project.recordsFilterRemove');
        Route::post('/project/update-record/{id}', [ProjectRecordsController::class,'updateDetails'])->name('project.records.update');
        Route::post('/project/update/history/record/{id}', [ProjectRecordsController::class,'historyUpdate'])->name('project.records.history.update');
        Route::get('/project/records-errors/filter/{id}', [ProjectRecordsController::class,'filterError'])->name('project.recordsFilterErrors');
});




// Route::get('/new-project', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('newproject');
// Route::get('/project-result/{id}', [NewProjectController::class,'result'])->middleware(['auth', 'verified'])->name('result');
// Route::get('/projects', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('projects');
// Route::get('/data', [NewProjectController::class,'index'])->middleware(['auth', 'verified'])->name('data');


Route::get('/upload-mentoria', [NewProjectController::class,'uploadMentoria'])->middleware(['auth', 'verified'])->name('newproject.upload_mentoria');

Route::get('/base/{bundle}/{userid}/{filename?}', [FileController::class, 'getFile']);


// USUÁRIO
Route::get('/users/filter', [UserProjectController::class, 'filter'])->middleware(['auth', 'verified'])->name('users.filter');
Route::get('/users', [UserProjectController::class,'listUsers'])->middleware(['auth', 'verified'])->name('users.list');
Route::get('/users/new', [UserProjectController::class, 'create'])->middleware(['auth', 'verified'])->name('users.register');
Route::post('/users/edit', [UserProjectController::class, 'update'])->middleware(['auth', 'verified'])->name('user.edit');
Route::get('/users/edit/{id}', [UserProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('users.edit');
Route::delete('/users/remove/{id}', [UserProjectController::class, 'remove'])->middleware(['auth', 'verified'])->name('users.remove');
Route::get('/users/new-password/{id}', [UserProjectController::class, 'newpassword'])->middleware(['auth', 'verified'])->name('users.newpassword');

Route::get('/import/{id}', [ImportController::class, 'listRecords'])->name('import.listRecords');
Route::get('/import/erro/{id}', [ImportController::class, 'listErroRecords'])->name('import.listErroRecords');


//BUNDLES (PACOTES E PRODUTOS)
Route::get('/bundles/filter', [BundlesController::class, 'filter'])->middleware(['auth', 'verified'])->name('bundles.filter');
Route::get('/bundles', [BundlesController::class,'list'])->middleware(['auth', 'verified'])->name('bundles.list');
Route::get('/bundles/new', [BundlesController::class, 'create'])->middleware(['auth', 'verified'])->name('bundles.register');
Route::get('/bundles/edit/{id}', [BundlesController::class, 'edit'])->middleware(['auth', 'verified'])->name('bundles.edit');
Route::delete('/bundles/remove/{id}', [BundlesController::class, 'remove'])->middleware(['auth', 'verified'])->name('bundles.remove');
Route::post('/bundles/register', [BundlesController::class, 'register'])->middleware(['auth', 'verified'])->name('bundles.register');
Route::post('/bundles/edit/{id}', [BundlesController::class, 'update'])->middleware(['auth', 'verified'])->name('bundles.update');

// CONTROLE DE PERFIS
Route::get('/users-role', [UserRoleController::class,'index'])->middleware(['auth', 'verified'])->name('roles.list');
Route::post('/users-role', [UserRoleController::class, 'update'])->middleware(['auth', 'verified'])->name('roles.update');
Route::put('/users-role/{id}', [UserRoleController::class, 'edit'])->middleware(['auth', 'verified'])->name('roles.edit');
Route::delete('/users-role/{id}', [UserRoleController::class, 'remove'])->middleware(['auth', 'verified'])->name('roles.remove');


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
    // Route::get('/users', [ProfileController::class, 'listUsers'])->name('users.list');
    // Route::get('/user/{id}', [ProfileController::class, 'editUser'])->name('users.edit');
});


require __DIR__.'/auth.php';