@extends('layouts.app')

@section('title', 'Crear Proyecto')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Crear Proyecto</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($proyectosActivos >= 2)
        <div class="alert alert-warning">
            No puedes crear más de 2 proyectos pendientes o activos.
        </div>
    @else
        <form action="{{ route('crear-proyecto') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Proyecto</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="imagen_url" class="form-label">URL de la Imagen</label>
                <input type="url" class="form-control" id="imagen_url" name="imagen_url"
                    value="{{ old('imagen_url') }}" required>
            </div>

            <div class="mb-3">
                <label for="video_url" class="form-label">URL del Video (opcional)</label>
                <input type="url" class="form-control" id="video_url" name="video_url"
                    value="{{ old('video_url') }}">
            </div>

            <div class="mb-3">
                <label for="min_inversion" class="form-label">Inversión Mínima</label>
                <input type="number" class="form-control" id="min_inversion" name="min_inversion"
                    value="{{ old('min_inversion') }}" min="0" required>
            </div>

            <div class="mb-3">
                <label for="max_inversion" class="form-label">Inversión Máxima</label>
                <input type="number" class="form-control" id="max_inversion" name="max_inversion"
                    value="{{ old('max_inversion') }}" min="0" required>
            </div>

            <div class="mb-3">
                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                    value="{{ old('fecha_fin') }}" required>
            </div>

            <button type="submit" class="btn btn-success">
                Crear Proyecto
            </button>
        </form>
    @endif
</div>
@endsection
