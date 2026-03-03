<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Stem Founding') }}</title>


    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">


    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand animated-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Stem Founding') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav me-auto"></ul>


                    <ul class="navbar-nav ms-auto">
                        @php use Illuminate\Support\Str; @endphp

                        @guest

                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Iniciar Sesion</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                                </li>
                            @endif
                        @else

                            @if (Str::lower(Auth::user()->rol ?? '') == 'emprendedor')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('mis-proyectos') }}">Mis Proyectos</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('crear-proyecto') }}">Crear Proyecto</a>
                                </li>


                            @endif

                            @if (Str::lower(Auth::user()->rol ?? '') == 'inversor')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('misInversiones') }}">Mis Inversiones</a>
                                </li>
                            @endif


                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('gestionarSaldo') }}">
                                        Saldo: <strong>{{ Auth::user()->dinero }}</strong>
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                     document.getElementById('logout-form').submit();">
                                        Cerrar Sesion
                                    </a>


                                    @if (Str::lower(Auth::user()->rol ?? '') == 'admin')
                                        <a class="dropdown-item" href="{{ route('panel-admin') }}">Panel del Admin</a>
                                    @endif

                                    <form id="logout-form" action="{{ route('logout') }} " method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>


        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>