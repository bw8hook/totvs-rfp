<?php

use App\Exceptions\MagicWriteAPI\Agents;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AgentsController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\LineOfProductController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\KnowledgeRecordsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRecordsController;
use App\Http\Controllers\SAMLController;
use App\Http\Controllers\SegmentsController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UserProjectController;
use App\Http\Controllers\BundlesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\MentoriaController;


use App\Models\KnowledgeRecord;
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
    Route::get('/knowledge/cron', [KnowledgeController::class,'cron2'])->name('knowledge.cron');
    Route::get('/knowledge/inserir-produtos', [KnowledgeController::class,'InserirProdutos'])->name('knowledge.produtos');


    // REGISTROS DA BASE DE CONHECIMENTO
        //AJAX
        Route::get('/knowledge/records/filter/{id?}', [KnowledgeRecordsController::class,'filter'])->name('knowledge.recordsFilter');
        Route::delete('/knowledge/records/{id}', [KnowledgeRecordsController::class,'filterRemove'])->name('knowledge.recordsFilterRemove');
        Route::post('/knowledge/update-record/{id}', [KnowledgeRecordsController::class,'updateDetails'])->name('knowledge.records.update');
        Route::get('/knowledge/records-errors/filter/{id}', [KnowledgeRecordsController::class,'filterError'])->name('knowledge.recordsFilterErrors');
    Route::get('/knowledge/records/view/{id}', [KnowledgeRecordsController::class,'view'])->name('knowledge.records.view');
    Route::get('/knowledge/records/errors/{id}', [KnowledgeRecordsController::class,'errors'])->name('knowledge.recordsErrors');
    Route::get('/knowledge/records/processing/{id}', [KnowledgeRecordsController::class,'processing'])->name('knowledge.records.processing');
    Route::get('/knowledge/records/references/{id}', [KnowledgeRecordsController::class,'references'])->name('knowledge.records.references');


    // Rota genérica com parâmetro opcional por último
    Route::get('/knowledge/records/', [KnowledgeRecordsController::class, 'ListAllReferences'])->name('knowledge.records.all');
    Route::get('/knowledge/records/{id}/{record_id?}', [KnowledgeRecordsController::class, 'index'])->name('knowledge.records')->where(['id' => '[0-9]+', 'record_id' => '[0-9]+']); // Restringe id e record_id a números
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
    Route::get('/project/cron', [ProjectController::class,'cron'])->name('project.cron');
    Route::get('/project/cron2', [ProjectController::class,'cron2'])->name('project.cron2');


    Route::get('/project/{id}/export', [ProjectController::class,'projectExport'])->name('project.export');



    // REGISTROS DA BASE DE CONHECIMENTO
    Route::get('/project/answers/{id}', [ProjectRecordsController::class,'answer'])->name('project.answer');
    Route::get('/project/answers-errors/{id}', [ProjectRecordsController::class,'answerErrors'])->name('project.answer.errors');
    Route::get('/project/records/{id}', [ProjectRecordsController::class,'index'])->name('project.records');
    Route::get('/project/records-errors/{id}', [ProjectRecordsController::class,'errors'])->name('project.recordsErrors');
    Route::get('/project/records/processing/{id}', [ProjectRecordsController::class,'processing'])->name('project.records.processing');
    Route::get('/project/answers/processing/{id}', [ProjectRecordsController::class,'processingAnswer'])->name('project.answer.processing');
        //AJAX
        Route::get('/project/records/filter/{id}', [ProjectRecordsController::class,'filter'])->name('project.recordsFilter');
        Route::get('/project/records/references/{id}', [ProjectRecordsController::class,'references'])->name('project.records.references');
        Route::get('/project/records/detail/{id}', [ProjectRecordsController::class,'detail'])->name('project.records.detail');
        Route::get('/project/answer/filter/{id}', [ProjectRecordsController::class,'filterAnswer'])->name('project.recordsFilterAnswer');
        Route::delete('/project/records/{id}', [ProjectRecordsController::class,'filterRemove'])->name('project.recordsFilterRemove');
        Route::post('/project/update-record/{id}', [ProjectRecordsController::class,'updateDetails'])->name('project.records.update');
        Route::post('/project/update/history/record/{id}', [ProjectRecordsController::class,'historyUpdate'])->name('project.records.history.update');
        Route::get('/project/records-errors/filter/{id}', [ProjectRecordsController::class,'filterError'])->name('project.recordsFilterErrors');
        Route::get('/project/answers-errors/filter/{id}', [ProjectRecordsController::class,'filterAnswerError'])->name('project.answer.filter.errors');
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
Route::post('/users/new', [UserProjectController::class, 'store'])->middleware(['auth', 'verified'])->name('users.store');
Route::post('/users/edit', [UserProjectController::class, 'update'])->middleware(['auth', 'verified'])->name('user.edit');
Route::get('/users/edit/{id}', [UserProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('users.edit');
Route::delete('/users/remove/{id}', [UserProjectController::class, 'remove'])->middleware(['auth', 'verified'])->name('users.remove');
Route::get('/users/new-password/{id}', [UserProjectController::class, 'newpassword'])->middleware(['auth', 'verified'])->name('users.newpassword');
Route::post('/users/status/{id}', [UserProjectController::class, 'status'])->middleware(['auth', 'verified'])->name('users.status');


Route::get('/import/{id}', [ImportController::class, 'listRecords'])->name('import.listRecords');
Route::get('/import/erro/{id}', [ImportController::class, 'listErroRecords'])->name('import.listErroRecords');


//BUNDLES (PACOTES E PRODUTOS)
Route::middleware('auth')->group(function () {
    Route::get('/bundles/filter', [BundlesController::class, 'filter'])->name('bundles.filter');
    Route::get('/bundles', [BundlesController::class,'list'])->name('bundles.list');
    Route::get('/bundles/new', [BundlesController::class, 'create'])->name('bundles.new');
    Route::get('/bundles/edit/{id}', [BundlesController::class, 'edit'])->name('bundles.edit');
    Route::delete('/bundles/remove/{id}', [BundlesController::class, 'remove'])->name('bundles.remove');
    Route::post('/bundles/register', [BundlesController::class, 'register'])->name('bundles.register');
    Route::post('/bundles/edit/{id}', [BundlesController::class, 'update'])->name('bundles.update');
});


Route::middleware('auth')->group(function () {
    Route::get('/process/filter', [ProcessController::class, 'filter'])->name('process.filter');
    Route::get('/process', [ProcessController::class,'list'])->name('process.list');
    Route::get('/process/new', [ProcessController::class, 'create'])->name('process.create');
    Route::get('/process/edit/{id}', [ProcessController::class, 'edit'])->name('process.edit');
    Route::delete('/process/remove/{id}', [ProcessController::class, 'remove'])->name('process.remove');
    Route::post('/process/register', [ProcessController::class, 'register'])->name('process.register');
    Route::post('/process/edit/{id}', [ProcessController::class, 'update'])->name('process.update');
});


// CONTROLE DE PERFIS
Route::get('/roles', [RolesController::class,'index'])->middleware(['auth', 'verified'])->name('roles.list');
Route::get('/roles/filter', [RolesController::class, 'filter'])->name('roles.filter');
Route::get('/roles/new', [RolesController::class,'new'])->middleware(['auth', 'verified'])->name('roles.new');
Route::post('/roles/new', [RolesController::class, 'store'])->middleware(['auth', 'verified'])->name('roles.store');
Route::get('/roles/{id}', [RolesController::class, 'show'])->middleware(['auth', 'verified'])->name('roles.show');
Route::post('/roles/{id}', [RolesController::class, 'update'])->middleware(['auth', 'verified'])->name('roles.update');
Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->middleware(['auth', 'verified'])->name('roles.remove');
Route::get('/roles', [RolesController::class,'index'])->middleware(['auth', 'verified'])->name('roles.list');


// CADASTRO DE PERMISSÕES
Route::get('/permissions', [PermissionsController::class,'index'])->middleware(['auth', 'verified'])->name('permissions.list');

// Teste de Email
Route::get('/teste-email', [PermissionsController::class,'new_email'])->middleware(['auth', 'verified'])->name('new_email');



// Cadastro de Configurações
Route::get('/config', [ConfigController::class,'index'])->middleware(['auth', 'verified'])->name('config.index');


// Listar todos os produtos
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Listar todos os produtos
Route::get('/line-of-products', [LineOfProductController::class, 'index'])->name('line-of-products.index');
Route::get('/line-of-products/filter', [LineOfProductController::class, 'filter'])->name('line-of-products.filter');
Route::get('/line-of-products/new', [LineOfProductController::class, 'create'])->name('line-of-products.create');
Route::post('/line-of-products/new', [LineOfProductController::class, 'store'])->name('line-of-products.store');
Route::get('/line-of-products/{id}', [LineOfProductController::class, 'edit'])->name('line-of-products.edit');
Route::post('/line-of-products/{id}', [LineOfProductController::class, 'update'])->name('line-of-products.update');
Route::delete('/line-of-products/{id}', [LineOfProductController::class, 'destroy'])->name('line-of-products.destroy');

// Listar todos os Segmentos
Route::get('/segments', [SegmentsController::class, 'index'])->name('segments.index');
Route::get('/segments/filter', [SegmentsController::class, 'filter'])->name('segments.filter');
Route::get('/segments/new', [SegmentsController::class, 'create'])->name('segments.create');
Route::post('/segments/new', [SegmentsController::class, 'store'])->name('segments.store');
Route::get('/segments/{id}', [SegmentsController::class, 'edit'])->name('segments.edit');
Route::post('/segments/{id}', [SegmentsController::class, 'update'])->name('segments.update');
Route::delete('/segments/{id}', [SegmentsController::class, 'destroy'])->name('segments.destroy');

// Listar todos os Modules
Route::get('/modules', [ModulesController::class, 'index'])->name('modules.index');
Route::get('/modules/filter', [ModulesController::class, 'filter'])->name('modules.filter');
Route::get('/modules/new', [ModulesController::class, 'create'])->name('modules.create');
Route::post('/modules/new', [ModulesController::class, 'store'])->name('modules.store');
Route::get('/modules/{id}', [ModulesController::class, 'edit'])->name('modules.edit');
Route::post('/modules/{id}', [ModulesController::class, 'update'])->name('modules.update');
Route::delete('/modules/{id}', [ModulesController::class, 'destroy'])->name('modules.destroy');

// Listar todos os produtos
Route::get('/agents', [AgentsController::class, 'index'])->name('agents.index');
Route::get('/agents/filter', [AgentsController::class, 'filter'])->name('agents.filter');
Route::get('/agents/new', [AgentsController::class, 'create'])->name('agents.create');
Route::post('/agents/new', [AgentsController::class, 'store'])->name('agents.store');
Route::get('/agents/{id}', [AgentsController::class, 'edit'])->name('agents.edit');
Route::post('/agents/{id}', [AgentsController::class, 'update'])->name('agents.update');
Route::delete('/agents/{id}', [AgentsController::class, 'destroy'])->name('agents.destroy');


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

//LOGIN TOTVS IDENTITY
Route::get('/saml/login', [SAMLController::class, 'login']);
Route::post('/saml', [SAMLController::class, 'acs']);
Route::get('/saml/metadata', [SAMLController::class, 'metadata']);
Route::get('/nouser', [SAMLController::class, 'nouser'])->name('nouser');
Route::get('/nopermission', [SAMLController::class, 'nopermission'])->name('nopermission');


require __DIR__.'/auth.php';
