@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card shadow-sm border-0 bg-white p-5">
            <div class="text-danger mb-3">
                <span class="display-4 fw-bold">404</span>
            </div>
            <h2 class="h4 fw-bold mb-3">Pokémon no encontrado</h2>
            <p class="text-muted mb-4">
                {{ $message ?? 'No pudimos encontrar la información del Pokémon solicitado. Por favor verifica que el nombre esté escrito correctamente.' }}
            </p>
            @if (!empty($name))
                <p class="small text-secondary mb-4">
                    Búsqueda realizada: <code>{{ htmlspecialchars($name) }}</code>
                </p>
            @endif
            <div>
                <a href="{{ route('pokemon.index') }}" class="btn btn-primary px-4">
                    Volver a Pokémon
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 ms-2">
                    Ir al Inicio
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
