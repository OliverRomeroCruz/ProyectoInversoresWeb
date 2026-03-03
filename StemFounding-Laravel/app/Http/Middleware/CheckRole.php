<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Debes iniciar sesión.');
        }

        $userRole = Auth::user()->rol; 
        if (!in_array($userRole, $roles)) {
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
