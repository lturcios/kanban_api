<?php
use App\Http\Controllers\Api\ColumnaController;
use App\Http\Controllers\Api\TableroController;
use App\Http\Controllers\Api\TareaController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    // Esto generará las rutas RESTful para los controladores
    Route::apiResource('tableros', TableroController::class);
    Route::apiResource('columnas', ColumnaController::class);
    Route::apiResource('tareas', TareaController::class);

    Route::put('columnas/actualizar-orden', [ColumnaController::class, 'actualizarOrden']);
    Route::put('tareas/actualizar-orden', [TareaController::class, 'actualizarOrden']);
    // Endpoint para mover/reordenar una tarea
    Route::post('tareas/{tarea}/move', [TareaController::class, 'move']);

});