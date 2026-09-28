@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold">Pokémon</h1>
    <p class="text-muted">Selecciona un Pokémon de la lista para ver su detalle.</p>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
    @foreach ($pokemons as $pokemon)
        <div class="col">
            <div class="card h-100 shadow-sm border">
                <div class="card-body text-center d-flex flex-column justify-content-between p-4">
                    <h5 class="card-title text-capitalize mb-3">{{ $pokemon }}</h5>
                    <a href="{{ url('/pokemon/' . strtolower($pokemon)) }}" class="btn btn-outline-primary btn-sm">
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
