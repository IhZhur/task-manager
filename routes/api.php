<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::apiResource('tasks', TaskController::class);
Route::put('/tasks/sort', [TaskController::class, 'sort'])->name('tasks.sort');

