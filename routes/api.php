<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::post('/menus', [MenuController::class, 'create']);
