@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">{{ $proyecto->nombre }}</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Detalles del Proyecto
                    @php
                        $estadoColor = match (strtolower($proyecto->estado)) {
                            'pendiente' => 'warning',
                            'activo' => 'success',
                            'cancelado' => 'danger',
                            'completado' => 'primary',
                            'rechazado' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="float-end badge bg-{{ $estadoColor }}">{{ $proyecto->estado }}</span>
                </div>

                <div class="card-body">

                    @if($proyecto->imagen_url)
                        <img src="{{ $proyecto->imagen_url }}" class="img-fluid mb-3">
                    @endif

                    @if($proyecto->video_url)
                        <div class="mb-3">
                            <iframe width="100%" height="315"
                                src="{{ $proyecto->video_url }}"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Descripción</th>
                                <td>{{ $proyecto->descripcion }}</td>
                            </tr>
                            <tr>
                                <th>Usuario</th>
                                <td>{{ $proyecto->user->name ?? 'Desconocido' }}</td>
                            </tr>
                            <tr>
                                <th>Inversión mínima</th>
                                <td>${{ number_format($proyecto->min_inversion, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Inversión máxima</th>
                                <td>${{ number_format($proyecto->max_inversion, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Inversión actual</th>
                                <td>${{ number_format($proyecto->inversion_actual, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Fecha fin</th>
                                <td>{{ $proyecto->fecha_fin }}</td>
                            </tr>
                            <tr>
                                <th>Progreso</th>
                                <td>
                                    @php
                                        $progreso = $proyecto->max_inversion > 0
                                            ? min(($proyecto->inversion_actual / $proyecto->max_inversion) * 100, 100)
                                            : 0;
                                    @endphp
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $progreso }}%">
                                            {{ number_format($progreso, 2) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($proyecto->estado === 'activo' && Auth::check() && Auth::user()->rol === 'inversor')
                        @php
                            $saldo = Auth::user()->dinero;
                            $faltante = $proyecto->max_inversion - $proyecto->inversion_actual;
                            $maxInvertir = min($saldo, $faltante);
                        @endphp

                        <div class="mt-4">
                            <h5>Invertir en este proyecto</h5>
                            <form action="{{ route('invertir', $proyecto->id) }}" method="POST">
                                @csrf
                                <input type="number" name="monto" class="form-control mb-2"
                                    step="0.01"
                                    max="{{ $maxInvertir }}"
                                    required
                                    @if($maxInvertir <= 0) disabled @endif>

                                <button class="btn btn-success"
                                    @if($maxInvertir <= 0) disabled @endif>
                                    Invertir
                                </button>

                                @if($maxInvertir <= 0)
                                    <small class="text-danger d-block">
                                        No puedes invertir más en este proyecto.
                                    </small>
                                @endif
                            </form>
                        </div>
                    @endif

                    @if(Auth::check() && Auth::user()->rol === 'emprendedor' && Auth::id() === $proyecto->user_id && $proyecto->estado === 'activo' && $proyecto->inversion_actual >= $proyecto->min_inversion)
                        <div class="mt-4">
                            <h5>Completar Proyecto</h5>
                            <form action="{{ route('completar-proyecto', $proyecto->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Completar proyecto?')">Completar Proyecto</button>
                            </form>
                        </div>
                    @endif

                    @if(Auth::check() && Auth::user()->rol === 'emprendedor' && Auth::id() === $proyecto->user_id && $proyecto->estado === 'activo')
                        <div class="mt-4">
                            <h5>Cancelar Proyecto</h5>
                            <form action="{{ route('cancelar-proyecto', $proyecto->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar proyecto?')">Cancelar Proyecto</button>
                            </form>
                        </div>
                    @endif

                </div>

                @if(Auth::check())
                <div class="card-body border-top">
                    <h5>Inversiones</h5>

                    @php
                        if (Auth::user()->rol === 'admin' || Auth::id() === $proyecto->user_id) {
                            $mostrarInversiones = $proyecto->inversiones;
                        } else {
                            $mostrarInversiones = $proyecto->inversiones->where('user_id', Auth::id());
                        }
                    @endphp

                    @if($mostrarInversiones->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostrarInversiones as $inversion)
                                <tr>
                                    <td>{{ $inversion->user->name }}</td>
                                    <td>${{ number_format($inversion->monto, 2) }}</td>
                                    <td>{{ $inversion->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if(
                                            Auth::user()->rol === 'inversor'
                                            && Auth::id() === $inversion->user_id
                                            && $inversion->created_at->addHours(24)->isFuture()
                                            && $inversion->project->estado === 'activo'
                                        )
                                            <form action="{{ route('inversion.retirar', $inversion->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Retirar inversión?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-warning btn-sm">Retirar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No hay inversiones.</p>
                    @endif

                    <hr class="mt-5">
                    <h4>Comentarios del proyecto</h4>

                    @if(Auth::check() && Auth::id() === $proyecto->user_id)
                        <a href="{{ route('comments.create', $proyecto->id) }}" class="btn btn-outline-primary mb-3">
                            ➕ Añadir comentario
                        </a>
                    @endif

                    @forelse($proyecto->comments as $comment)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    @if($comment->imagen)
                                        <div class="col-md-3">
                                            <img src="{{ $comment->imagen }}" class="img-fluid rounded"
                                                style="width: 100%; height: 150px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-9">
                                    @else
                                            <div class="col-md-12">
                                        @endif

                                            <h5>{{ $comment->titulo }}</h5>
                                            <p>{{ $comment->descripcion }}</p>

                                            <div class="mt-2 d-flex gap-2">
                                                @if(Auth::check() && Auth::id() === $comment->user_id)
                                                    <a href="{{ route('comments.edit', $comment->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        Editar
                                                    </a>
                                                @endif

                                                @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->rol === 'admin'))
                                                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST"
                                                        onsubmit="return confirm('¿Eliminar comentario?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @empty
                            <p class="text-muted">Este proyecto aún no tiene comentarios.</p>
                        @endforelse
                    </div>
                </div>
                @endif

            </div>
        </div>


@endsection
