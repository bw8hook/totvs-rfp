<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;

use ErlandMuchasaj\LaravelFileUploader\FileUploader; 
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', [AdminController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/admin/dashboard', [AdminController::class,'dashboard'])->name('admin.dashboard');

// Route::get('/agent/dashboard', [AgentController::class,'dashboard'])->name('agent.dashboard');

Route::get('/dashboard', [AdminController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/delete-file/{public_folder}/{folder}/{userid}/{type}/{doc}', [AdminController::class,'delete'])->middleware(['auth', 'verified'])->name('delete');

Route::get('/update-ia', [AdminController::class,'update'])->middleware(['auth', 'verified'])->name('update');


// handle the post request
Route::post('/files', function (Request $request) {

    $max_size = (int) ini_get('upload_max_filesize') * 1000;

    $file = $request->file('file');

    if($file == null){
        return redirect()->back()->with('error','Nenhum arquivo foi enviado')->with('file');
    }

    $response = FileUploader::store($file);

    return redirect()->back()->with('success','Upload realizado com sucesso.')->with('file', $response);
})->name('files.store');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [ProfileController::class, 'listUsers'])->name('users.list');

});

require __DIR__.'/auth.php';
