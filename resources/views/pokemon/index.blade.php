@extends('layouts.app')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h2 fw-bold mb-1">Catálogo de Pokémon</h1>
        <p class="text-muted mb-0">Explora la lista de Pokémon o busca uno en específico mediante PokéAPI.</p>
    </div>
    <div class="col-md-6 mt-3 mt-md-0">
        <!-- Formulario de búsqueda -->
        <form action="{{ route('pokemon.index') }}" method="GET" class="d-flex gap-2">
            <input 
                type="text" 
                name="search" 
                class="form-control @if($searchError) is-invalid @endif" 
                placeholder="Buscar Pokémon por nombre (ej. pikachu)"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-primary px-4">
                Buscar
            </button>
            @if(request()->has('search'))
                <a href="{{ route('pokemon.index') }}" class="btn btn-outline-secondary" title="Limpiar búsqueda">
                    Todos
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Alerta de validación o error de búsqueda -->
@if ($searchError)
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <div>
            <strong>Atención:</strong> {{ $searchError }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Resultado de búsqueda exitoso si aplica -->
@if ($searchResult)
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Resultado de la búsqueda</h4>
            <a href="{{ route('pokemon.index') }}" class="btn btn-sm btn-outline-secondary">Ver listado completo</a>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow border-primary h-100">
                    <div class="card-body text-center p-4">
                        <span class="badge bg-dark mb-2">#{{ str_pad($searchResult['id'], 3, '0', STR_PAD_LEFT) }}</span>
                        <div class="my-2" style="min-height: 120px;">
                            <img src="{{ $searchResult['sprite'] }}" alt="{{ $searchResult['name'] }}" style="width: 120px; height: 120px; object-fit: contain;">
                        </div>
                        <h4 class="card-title text-capitalize fw-bold">{{ $searchResult['name'] }}</h4>
                        <div class="mb-3">
                            @foreach ($searchResult['types'] as $type)
                                <span class="badge badge-type-{{ strtolower($type) }} px-2 py-1 text-uppercase">{{ $type }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('pokemon.show', $searchResult['name']) }}" class="btn btn-primary w-100">
                            Ver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="my-5">
@endif

<!-- Listado general de Pokémon -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Listado General (PokéAPI)</h4>
    <span class="badge bg-secondary">{{ count($pokemons) }} Pokémon cargados</span>
</div>

@if (count($pokemons) > 0)
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach ($pokemons as $pokemon)
            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body text-center d-flex flex-column justify-content-between p-3">
                        <div>
                            @if ($pokemon['id'])
                                <span class="badge bg-light text-muted border small mb-2">
                                    #{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                            <div class="d-flex align-items-center justify-content-center my-2" style="height: 100px;">
                                @if ($pokemon['sprite'])
                                    <img src="{{ $pokemon['sprite'] }}" alt="{{ $pokemon['name'] }}" style="width: 96px; height: 96px; object-fit: contain;">
                                @else
                                    <div class="bg-light rounded p-2 text-muted small">Sin imagen</div>
                                @endif
                            </div>
                            <h5 class="card-title text-capitalize fw-bold mt-2 mb-3">{{ $pokemon['name'] }}</h5>
                        </div>
                        <div>
                            <a href="{{ route('pokemon.show', strtolower($pokemon['name'])) }}" class="btn btn-outline-primary btn-sm w-100">
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning text-center p-4">
        No se pudieron cargar los Pokémon en este momento. Verifica tu conexión a Internet o intenta nuevamente.
    </div>
@endif
@endsection
