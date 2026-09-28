@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card shadow-sm border p-4">
            <h2 class="card-title text-capitalize fw-bold mb-4">{{ $name }}</h2>

            <!-- Imagen placeholder -->
            <div class="bg-body-secondary border rounded d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 200px; height: 200px;">
                <span class="text-muted fw-semibold">Placeholder Imagen</span>
            </div>

            <div>
                <a href="{{ route('pokemon.index') }}" class="btn btn-secondary">
                    Volver a Pokémon
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
