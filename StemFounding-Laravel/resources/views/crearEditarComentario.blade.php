@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ isset($comment) ? 'Editar comentario' : 'Crear comentario' }} para:
            {{ $project->nombre ?? $comment->project->nombre }}
        </h2>

        <form
            action="{{ isset($comment) ? route('comments.update', $comment->id) : route('comments.store', $project->id) }}"
            method="POST">
            @csrf
            @if(isset($comment))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                    value="{{ old('titulo', $comment->titulo ?? '') }}" required>
                @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion"
                    name="descripcion" rows="4" required>{{ old('descripcion', $comment->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">URL de la imagen (opcional)</label>
                <input type="url" class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen"
                    value="{{ old('imagen', $comment->imagen ?? '') }}">
                @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if(isset($comment) && $comment->imagen)
                    <p class="mt-2">Imagen actual:</p>
                    <img src="{{ $comment->imagen }}" style="max-width: 200px;">
                @endif
            </div>

            <button type="submit" class="btn {{ isset($comment) ? 'btn-primary' : 'btn-success' }}">
                {{ isset($comment) ? 'Actualizar Comentario' : 'Crear Comentario' }}
            </button>
            <a href="{{ route('detalleProyecto', $project->id ?? $comment->project_id) }}"
                class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection