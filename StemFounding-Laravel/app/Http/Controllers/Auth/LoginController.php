<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; 

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Evita que usuarios baneados inicien sesión
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->banned) {
            auth()->logout(); // cerrar sesión
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido baneada, no puedes iniciar sesión.'
            ]);
        }
    }
}

