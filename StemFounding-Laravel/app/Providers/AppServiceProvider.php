<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            return;
        }

        $this->revisarProyectos();
    }

    protected function revisarProyectos()
    {
        $hoy = Carbon::now();

        $proyectos = Project::with('inversiones.user')
            ->whereIn('estado', ['pendiente', 'activo'])
            ->get();

        foreach ($proyectos as $proyecto) {
            DB::transaction(function () use ($proyecto, $hoy) {

                $totalInversion = $proyecto->inversion_actual;
                $min = $proyecto->min_inversion;
                $max = $proyecto->max_inversion;
                $fechaFin = Carbon::parse($proyecto->fecha_fin);

                if ($totalInversion == $max || ($totalInversion >= $min && $hoy->greaterThanOrEqualTo($fechaFin))) {
                    $proyecto->estado = 'completado';
                    $proyecto->save();
                    return;
                }

                if ($totalInversion < $min && $hoy->greaterThanOrEqualTo($fechaFin)) {

                    foreach ($proyecto->inversiones as $inversion) {
                        $usuario = $inversion->user;
                        $usuario->dinero += $inversion->monto;
                        $usuario->save();
                    }

                    $proyecto->inversiones()->delete();
                    $proyecto->estado = 'cancelado';
                    $proyecto->save();
                }
            });
        }
    }
}

