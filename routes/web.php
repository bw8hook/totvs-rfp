<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\GptController;
use App\Http\Controllers\Auth\RegisteredUserController;
use ErlandMuchasaj\LaravelFileUploader\FileUploader; 
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [AdminController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/register', [RegisteredUserController::class, 'create']);

// Route::get('/admin/dashboard', [AdminController::class,'dashboard'])->name('admin.dashboard');

// Route::get('/agent/dashboard', [AgentController::class,'dashboard'])->name('agent.dashboard');

Route::get('/dashboard', [AdminController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/delete-file/{public_folder}/{folder}/{userid}/{type}/{doc}', [AdminController::class,'delete'])->middleware(['auth', 'verified'])->name('delete');

Route::get('/update-ia', [AdminController::class,'update'])->middleware(['auth', 'verified'])->name('update');

Route::get('/assistente/{id}', [AgentController::class,'assistente'])->middleware(['auth', 'verified'])->name('assistente');

Route::get('/gerenciar/{id}', [AgentController::class,'gerenciar'])->middleware(['auth', 'verified'])->name('gerenciar');
Route::post('/diretrizes', [AgentController::class, 'diretriz'])->middleware(['auth', 'verified'])->name('diretriz');

Route::get('/agentes', [AgentController::class,'lista'])->middleware(['auth', 'verified'])->name('lista');
Route::get('/agente/{id}', [AgentController::class,'edit'])->middleware(['auth', 'verified'])->name('edit');


Route::get('/bases-conhecimento', [AdminController::class,'bases'])->middleware(['auth', 'verified'])->name('bases');

Route::get('/criar', [TestController::class,'index'])->middleware(['auth', 'verified'])->name('criar');

// handle the post request
Route::post('/files', function (Request $request) {

    $max_size = (int) ini_get('upload_max_filesize') * 1000;

    $file = $request->file('file');

    if($file == null){
        return redirect()->back()->with('error','Nenhum arquivo foi enviado')->with('file');
    }

    $response = FileUploader::store($file);

    $gptController = new GptController();
    $gptController->upload($file);

    return redirect()->back()->with('success','Upload realizado com sucesso.')->with('file', $response);
})->name('files.store');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profileUser', [ProfileController::class, 'updateUser'])->name('profile.updateUser');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [ProfileController::class, 'listUsers'])->name('users.list');
    Route::get('/user/{id}', [ProfileController::class, 'editUser'])->name('users.edit');

});

require __DIR__.'/auth.php';
