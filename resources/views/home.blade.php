@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="card p-5 shadow-sm border-0">
            <h1 class="display-6 fw-bold mb-3">Pokédex Web</h1>
            <p class="text-muted mb-4">
                Bienvenido a la Pokédex. Aquí puedes consultar el listado de Pokémon y ver sus detalles.
            </p>
            <div>
                <a href="{{ route('pokemon.index') }}" class="btn btn-primary btn-lg px-4">
                    Ver Pokémon
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
