@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2 class="text-center">Mis inversiones</h2>
        </div>
    </div>

    <div class="row">
        @forelse($inversiones as $inversion)
            <div class="col-md-4 mb-4">
                <div class="card h-100">

                    <div class="card-header">
                        {{ $inversion->project->nombre ?? 'Proyecto eliminado' }}

                        <span class="float-end badge bg-info">
                            Invertido
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <p>
                            <strong>Monto invertido:</strong>
                            {{ $inversion->monto }} €
                        </p>

                        <p>
                            <strong>Estado del proyecto:</strong>
                            {{ $inversion->project->estado ?? 'N/A' }}
                        </p>

                        <p>
                            <strong>Fecha de inversión:</strong>
                            {{ $inversion->created_at->format('d/m/Y') }}
                        </p>

                        <p>
                            <strong>Inversión total del proyecto:</strong>
                            {{ $inversion->project->inversion_actual ?? 0 }} €
                        </p>

                        @if(!empty($inversion->project->imagen_url))
                            <img src="{{ $inversion->project->imagen_url }}"
                                alt="{{ $inversion->project->nombre }}"
                                class="img-fluid mt-auto">
                        @endif
                    </div>

                    <div class="card-footer text-center">
                        <a href="{{ route('detalleProyecto', ['id' => $inversion->project->id]) }}"
                           class="btn btn-primary btn-sm">
                            Ver proyecto
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-md-12">
                <p class="text-center">Aún no has realizado inversiones.</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $inversiones->links() }}
    </div>
</div>
@endsection
