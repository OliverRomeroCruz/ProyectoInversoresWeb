<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Models\Project;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\CheckRole;


Route::get('/proyectos', [ProjectController::class, 'indexJson']);       
Route::get('/proyectos/{id}', [ProjectController::class, 'showJson']);   
Route::post('/proyectos', [ProjectController::class, 'storeJson']);      
Route::put('/proyectos/{id}', [ProjectController::class, 'updateJson']); 

Route::get('/', [ProjectController::class, 'home'])->name('home');


Route::get('/detalleProyecto/{id}', [ProjectController::class, 'mostrarProyecto'])->name('detalleProyecto');
Route::post('/proyectos/{id}/invertir', [ProjectController::class, 'invertir'])
    ->middleware('auth', CheckRole::class . ':inversor,emprendedor')
    ->name('invertir');

Route::delete('/inversiones/{id}', [ProjectController::class, 'retirar'])
    ->middleware('auth', CheckRole::class . ':inversor')
    ->name('inversion.retirar');

Auth::routes();


Route::middleware(['auth'])->group(function () {


    Route::middleware([CheckRole::class . ':admin'])->group(function () {

        Route::get('/panel-admin', [ProjectController::class, 'gestionProyectosAdmin'])->name('panel-admin');

        Route::post('/proyecto/{id}/confirmar', [ProjectController::class, 'confirmarProyecto'])->name('proyecto.confirmar');
        Route::post('/proyecto/{id}/denegar', [ProjectController::class, 'denegarProyecto'])->name('proyecto.denegar');
        Route::post('/proyecto/{id}/cancelar', [ProjectController::class, 'cancelarProyectoAdmin'])->name('proyecto.cancelar.admin');

        Route::post('/usuario/{id}/banear', [UserController::class, 'banear'])->name('usuario.banear');
        Route::post('/usuario/{id}/desbanear', [UserController::class, 'desbanear'])->name('usuario.desbanear');
    });


    Route::middleware([CheckRole::class . ':emprendedor'])->group(function () {

        Route::get('/proyectosPersonales', [ProjectController::class, 'misProyectos'])->name('mis-proyectos');
        Route::get('/creacionProyecto', [ProjectController::class, 'formCrearProyecto'])->name('form-crear-proyecto');
        Route::post('/creacionProyecto', [ProjectController::class, 'crearProyecto'])->name('crear-proyecto');

        Route::get('/proyectos/{id}/editar', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/proyectos/{project}', [ProjectController::class, 'update'])->name('update');

        Route::delete('/proyecto/{id}/cancelar', [ProjectController::class, 'cancelarProyecto'])->name('cancelar-proyecto');
        Route::delete('/proyecto/{id}/completar', [ProjectController::class, 'completarProyecto'])->name('completar-proyecto');
    });


    Route::middleware([CheckRole::class . ':inversor'])->group(function () {

        Route::get('/inversionesPersonales', [ProjectController::class, 'misInversiones'])->name('misInversiones');
    });

    Route::middleware([CheckRole::class . ':inversor,emprendedor'])->group(function () {

        Route::get('/gestionar-saldo', [SaldoController::class, 'index'])->name('gestionarSaldo');
        Route::post('/gestionar-saldo/ingresar', [SaldoController::class, 'ingresar'])->name('saldo.ingresar');
        Route::post('/gestionar-saldo/retirar', [SaldoController::class, 'retirar'])->name('saldo.retirar');
    });

    Route::middleware(['auth'])->group(function () {


        Route::middleware([CheckRole::class . ':emprendedor'])->group(function () {


            Route::get('/proyectos/{project}/comentarios/crear', [CommentController::class, 'create'])
                ->name('comments.create');

            Route::post('/proyectos/{project}/comentarios', [CommentController::class, 'store'])
                ->name('comments.store');


            Route::get('/comentarios/{comment}/editar', [CommentController::class, 'edit'])
                ->name('comments.edit');

            Route::put('/comentarios/{comment}', [CommentController::class, 'update'])
                ->name('comments.update');

            Route::delete('/comentarios/{comment}', [CommentController::class, 'destroy'])
                ->name('comments.destroy');
        });

        Route::middleware([CheckRole::class . ':admin'])->group(function () {

            Route::delete('/comentarios/{comment}/admin', [CommentController::class, 'destroyByAdmin'])
                ->name('comments.destroy.admin');
        });

    });
});
