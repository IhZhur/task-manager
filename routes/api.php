<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Все API-маршруты начинаются с /api

Route::middleware('api')->group(function () {
    // CRUD API для задач
    Route::apiResource('tasks', TaskController::class);
});
