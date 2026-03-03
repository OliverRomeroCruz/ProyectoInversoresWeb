@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-center mb-4">Gestionar Saldo</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-4 text-center">
            <div class="card-body">
                <h4>Saldo actual: <strong>${{ number_format($user->dinero, 2) }}</strong></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Ingresar Dinero</div>
                    <div class="card-body">
                        <form action="{{ route('saldo.ingresar') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="ingresar" class="form-label">Monto a ingresar</label>
                                <input type="number" step="0.01" class="form-control" name="monto" id="ingresar" required>
                            </div>
                            <button type="submit" class="btn btn-success">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Retirar Dinero</div>
                    <div class="card-body">
                        <form action="{{ route('saldo.retirar') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="retirar" class="form-label">Monto a retirar</label>
                                <input type="number" step="0.01" class="form-control" name="monto" id="retirar" required>
                            </div>
                            <button type="submit" class="btn btn-danger">Retirar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection