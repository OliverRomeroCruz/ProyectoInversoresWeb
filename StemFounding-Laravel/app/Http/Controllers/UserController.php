<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function banear($id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return back()->with('error', 'Usuario no encontrado');
        }

        $user->banned = true;
        $user->save();

        $user->projects()->whereIn('estado', ['pendiente', 'activo' , 'completado'])->get()->each(function ($proyecto) {

            foreach ($proyecto->inversiones as $inversion) {
                $inversion->user->dinero += $inversion->monto;
                $inversion->user->save();
            }

            $proyecto->inversiones()->delete();
            $proyecto->estado = 'cancelado';
            $proyecto->save();
        });

        return back()->with('success', 'Usuario baneado y sus proyectos han sido cancelados');
    }

    public function desbanear($id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return back()->with('error', 'Usuario no encontrado');
        }

        $user->banned = false;
        $user->save();

        return back()->with('success', 'Usuario desbaneado correctamente');
    }
}
