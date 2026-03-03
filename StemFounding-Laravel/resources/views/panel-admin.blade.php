@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Proyectos Pendientes</h2>
        </div>
    </div>

    @if($proyectosPendientes->isEmpty())
        <p class="text-center text-muted">No hay proyectos pendientes.</p>
    @else
        <table class="table table-striped table-hover mb-2">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Inversión</th>
                    <th>Fecha fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectosPendientes as $proyecto)
                    <tr>
                        <td><a href="{{ route('detalleProyecto', $proyecto->id) }}">{{ $proyecto->nombre }}</a></td>
                        <td>{{ $proyecto->user->name }}</td>
                        <td>{{ $proyecto->inversion_actual }} / {{ $proyecto->min_inversion }} - {{ $proyecto->max_inversion }}</td>
                        <td>{{ $proyecto->fecha_fin }}</td>
                        <td class="d-flex gap-2">
                            <form action="{{ route('proyecto.confirmar', $proyecto->id) }}" method="POST">@csrf
                                <button class="btn btn-success btn-sm">Confirmar</button>
                            </form>
                            <form action="{{ route('proyecto.denegar', $proyecto->id) }}" method="POST">@csrf
                                <button class="btn btn-danger btn-sm">Denegar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mb-4">
            {{ $proyectosPendientes->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Proyectos Activos</h2>
        </div>
    </div>

    @if($proyectosActivos->isEmpty())
        <p class="text-center text-muted">No hay proyectos activos.</p>
    @else
        <table class="table table-striped table-hover mb-2">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Inversión actual</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectosActivos as $proyecto)
                    <tr>
                        <td>{{ $proyecto->nombre }}</td>
                        <td>{{ $proyecto->user->name }}</td>
                        <td>{{ $proyecto->inversion_actual }}</td>
                        <td>
                            <form action="{{ route('proyecto.cancelar.admin', $proyecto->id) }}" method="POST">@csrf
                                <button class="btn btn-warning btn-sm">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mb-4">
            {{ $proyectosActivos->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Proyectos Cancelados</h2>
        </div>
    </div>

    @if($proyectosCancelados->isEmpty())
        <p class="text-center text-muted">No hay proyectos cancelados.</p>
    @else
        <table class="table table-striped table-hover mb-2">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Inversión actual</th>
                    <th>Fecha fin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectosCancelados as $proyecto)
                    <tr>
                        <td>{{ $proyecto->nombre }}</td>
                        <td>{{ $proyecto->user->name }}</td>
                        <td>{{ $proyecto->inversion_actual }}</td>
                        <td>{{ $proyecto->fecha_fin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mb-4">
            {{ $proyectosCancelados->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Proyectos Completados</h2>
        </div>
    </div>

    @if($proyectosCompletados->isEmpty())
        <p class="text-center text-muted">No hay proyectos completados.</p>
    @else
        <table class="table table-striped table-hover mb-2">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Inversión final</th>
                    <th>Fecha fin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectosCompletados as $proyecto)
                    <tr>
                        <td>{{ $proyecto->nombre }}</td>
                        <td>{{ $proyecto->user->name }}</td>
                        <td>{{ $proyecto->inversion_actual }}</td>
                        <td>{{ $proyecto->fecha_fin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mb-4">
            {{ $proyectosCompletados->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Usuarios</h2>
        </div>
    </div>

    <table class="table table-hover mb-2">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->rol }}</td>
                    <td>
                        @if($user->banned)
                            <span class="badge bg-danger">Baneado</span>
                        @else
                            <span class="badge bg-success">Activo</span>
                        @endif
                    </td>
                    <td>
                        @if(!$user->banned)
                            <form action="{{ route('usuario.banear', $user->id) }}" method="POST">@csrf
                                <button class="btn btn-danger btn-sm">Banear</button>
                            </form>
                        @else
                            <form action="{{ route('usuario.desbanear', $user->id) }}" method="POST">@csrf
                                <button class="btn btn-success btn-sm">Desbanear</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mb-4">
        {{ $usuarios->links('pagination::simple-bootstrap-5') }}
    </div>

</div>
@endsection
