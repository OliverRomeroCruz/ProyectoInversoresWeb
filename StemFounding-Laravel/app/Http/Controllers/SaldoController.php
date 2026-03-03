<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaldoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('gestionarSaldo', compact('user'));
    }

    public function ingresar(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01'
        ]);

        $user = Auth::user();
        $user->dinero += $request->monto;
        $user->save();

        return back()->with('success', "Se ingresaron $request->monto correctamente.");
    }

    public function retirar(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01'
        ]);

        $user = Auth::user();

        if ($request->monto > $user->dinero) {
            return back()->with('error', "No tienes suficiente saldo para retirar.");
        }

        $user->dinero -= $request->monto;
        $user->save();

        return back()->with('success', "Se retiraron $request->monto correctamente.");
    }
}

