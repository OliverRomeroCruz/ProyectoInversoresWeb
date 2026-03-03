@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Proyecto</h2>

    <form action="{{ route('update', $proyecto->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   value="{{ old('nombre', $proyecto->nombre) }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion"
                      class="form-control"
                      rows="5"
                      required>{{ old('descripcion', $proyecto->descripcion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>
    </form>
</div>
@endsection
