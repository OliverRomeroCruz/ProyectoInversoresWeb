<?php

use App\Http\Controllers\ProjectController;

Route::get('/proyectos', [ProjectController::class, 'indexJson'])->name('proyectos.json');
Route::get('/proyectos/{id}', [ProjectController::class, 'showJson'])->name('proyecto.json');
Route::post('/proyectos', [ProjectController::class, 'storeJson'])->name('proyectos.store.json');
Route::put('/proyectos/{id}', [ProjectController::class, 'updateJson'])->name('proyectos.update.json');
Route::put('/proyectos/{id}/cancelar', [ProjectController::class, 'cancelarProyecto'])->name('proyectos.cancelar.json');
Route::put('/proyectos/{id}/completar', [ProjectController::class, 'completarProyecto'])->name('proyectos.completar.json');
