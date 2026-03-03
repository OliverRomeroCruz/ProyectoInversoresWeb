@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2 class="text-center">Mis proyectos</h2>
            </div>
        </div>

        <div class="row">
            @forelse($proyectos as $proyecto)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            {{ $proyecto->nombre }}

                            @php
                                $estadoColor = match (strtolower($proyecto->estado)) {
                                    'pendiente' => 'warning',
                                    'activo' => 'success',
                                    'cancelado' => 'danger',
                                    'completado' => 'primary',
                                    default => 'secondary',
                                    'rechazado' => 'danger',
                                };
                            @endphp

                            <span class="float-end badge bg-{{ $estadoColor }}">
                                {{ $proyecto->estado }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <p><strong>Descripción:</strong> {{ $proyecto->descripcion ?? 'No hay descripción' }}</p>
                            <p><strong>Usuario:</strong> {{ optional($proyecto->user)->name ?? 'Anónimo' }}</p>
                            <p>
                                <strong>inversión:</strong>
                                {{ $proyecto->inversion_actual ?? 0 }} /
                                {{ $proyecto->min_inversion ?? 0 }} -
                                {{ $proyecto->max_inversion ?? 0 }}
                            </p>
                            <p><strong>Fecha fin:</strong> {{ $proyecto->fecha_fin ?? 'Sin fecha' }}</p>



                            @if(!empty($proyecto->imagen_url))
                                <img src="{{ $proyecto->imagen_url }}" alt="{{ $proyecto->nombre ?? 'Imagen del proyecto' }}"
                                    class="img-fluid mt-auto">
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('detalleProyecto', ['id' => $proyecto->id]) }}" class="btn btn-primary">
                                Ver más
                            </a>
                            @if($proyecto->estado === 'activo' && $proyecto->inversion_actual >= $proyecto->min_inversion)
                                <form action="{{ route('completar-proyecto', $proyecto->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-success ms-2" onclick="return confirm('¿Completar proyecto?')">Completar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p class="text-center">No hay proyectos disponibles.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection