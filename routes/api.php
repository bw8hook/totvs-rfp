<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('test', function () {
    return response()->json(['message' => 'API está funcionando!']);
})->middleware('auth:sanctum');
