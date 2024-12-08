<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::post('/menus', [MenuController::class, 'create']);
Route::get('/menus', [MenuController::class, 'getAll']);
Route::get('/menus/{id}', [MenuController::class, 'getById'])
    ->where('id', '[0-9]+');
Route::patch('/menus/{id}', [MenuController::class, 'update'])
    ->where('id', '[0-9]+');
Route::delete('/menus/{id}', [MenuController::class, 'delete'])
    ->where('id', '[0-9]+');
