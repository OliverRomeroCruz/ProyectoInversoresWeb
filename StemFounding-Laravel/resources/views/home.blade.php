@extends('layouts.app')

@section('content')
    <div class="container">

        @if($ultimosProyectos->isNotEmpty())
            <div id="carouselUltimosProyectos" class="carousel slide mb-5" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ultimosProyectos as $key => $proyecto)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <a href="{{ route('detalleProyecto', ['id' => $proyecto->id]) }}" class="d-block">
                                @if(!empty($proyecto->imagen_url))
                                    <img src="{{ $proyecto->imagen_url }}" class="d-block w-100" style="height:300px; object-fit:cover;"
                                        alt="{{ $proyecto->nombre }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="height:300px; background-color:#ddd;">
                                        <h4>{{ $proyecto->nombre }}</h4>
                                    </div>
                                @endif
                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                    <h5>{{ $proyecto->nombre }}</h5>
                                    <p>{{ Str::limit($proyecto->descripcion, 100) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselUltimosProyectos"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselUltimosProyectos"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        @endif

        <form method="GET" action="{{ route('home') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="busqueda" class="form-label">Buscar</label>
                    <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ request('busqueda') }}"
                        placeholder="Nombre o descripción">
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="min_inversion" class="form-label">Mín. Inversión</label>
                    <input type="number" name="min_inversion" id="min_inversion" class="form-control"
                        value="{{ request('min_inversion') }}" min="0">
                </div>
                <div class="col-md-3">
                    <label for="max_inversion" class="form-label">Máx. Inversión</label>
                    <input type="number" name="max_inversion" id="max_inversion" class="form-control"
                        value="{{ request('max_inversion') }}" min="0">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-12">
                <h2 class="text-center">Lista de Proyectos</h2>
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
                                    'rechazado' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp

                            <span class="float-end badge bg-{{ $estadoColor }}">
                                {{ $proyecto->estado }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <p><strong>Descripción:</strong> {{ $proyecto->descripcion }}</p>
                            <p><strong>Usuario:</strong> {{ $proyecto->user->name }}</p>
                            <p>
                                <strong>Inversión:</strong>
                                {{ $proyecto->inversion_actual }} /
                                {{ $proyecto->min_inversion }} -
                                {{ $proyecto->max_inversion }}
                            </p>
                            <p><strong>Fecha fin:</strong> {{ $proyecto->fecha_fin }}</p>

                            @if(!empty($proyecto->imagen_url))
                                <img src="{{ $proyecto->imagen_url }}" alt="{{ $proyecto->nombre}}" class="img-fluid mt-auto"
                                    style="height:200px; object-fit:cover;">
                            @endif
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('detalleProyecto', ['id' => $proyecto->id]) }}" class="btn btn-primary w-100">
                                Ver más
                            </a>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-md-12">
                    <p class="text-center">No hay proyectos disponibles.</p>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mb-4">
                <ul class="pagination">
                    @foreach ($proyectos->getUrlRange(1, $proyectos->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $proyectos->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>




    </div>
@endsection