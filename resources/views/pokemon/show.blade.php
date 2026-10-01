@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white overflow-hidden">
            <div class="card-header bg-danger text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 fw-bold text-capitalize">
                    {{ $pokemon['name'] }}
                </h2>
                <span class="badge bg-dark fs-6">
                    #{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center mb-4">
                    <!-- imagen del pokemon -->
                    <div class="col-md-5 text-center mb-4 mb-md-0">
                        <div class="bg-light border rounded p-4 d-flex align-items-center justify-content-center mx-auto" style="min-height: 250px;">
                            <img 
                                src="{{ $pokemon['sprite'] }}" 
                                alt="{{ $pokemon['name'] }}" 
                                class="img-fluid" 
                                style="max-height: 220px; object-fit: contain;"
                            >
                        </div>
                        @if (!empty($pokemon['sprite_pixel']) && $pokemon['sprite'] !== $pokemon['sprite_pixel'])
                            <div class="mt-2 text-muted small">
                                <span>Sprite de batalla:</span>
                                <img src="{{ $pokemon['sprite_pixel'] }}" alt="pixel" style="width: 48px; height: 48px; image-rendering: pixelated;">
                            </div>
                        @endif
                    </div>

                    <!-- datos y tipos -->
                    <div class="col-md-7">
                        @if (!empty($pokemon['genus']))
                            <div class="text-muted fw-bold text-uppercase small mb-2">
                                {{ $pokemon['genus'] }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <span class="text-muted d-block small mb-1">Tipos elementales:</span>
                            <div class="d-flex gap-2">
                                @foreach ($pokemon['types'] as $type)
                                    <span class="badge badge-type-{{ strtolower($type) }} px-3 py-2 text-uppercase fs-6">
                                        {{ $type }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted small d-block">Altura</span>
                                    <strong>{{ $pokemon['height'] }} m</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted small d-block">Peso</span>
                                    <strong>{{ $pokemon['weight'] }} kg</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- entrada de la pokedex: edicion rojo fuego -->
                @if (!empty($pokemon['description_firered']))
                    <div class="p-3 mb-4 rounded border" style="background-color: #f8fafc; border-left: 4px solid #dc2626 !important;">
                        <span class="badge bg-danger text-uppercase mb-2">Edición Rojo Fuego</span>
                        <p class="mb-1 font-monospace text-dark" style="font-size: 0.95rem;">
                            {{ $pokemon['description_firered'] }}
                        </p>
                        @if (!empty($pokemon['description_es']))
                            <small class="text-muted d-block mt-2 pt-2 border-top">
                                {{ $pokemon['description_es'] }}
                            </small>
                        @endif
                    </div>
                @endif

                <hr class="my-4">

                <!-- estadisticas base -->
                <div>
                    <h4 class="fw-bold mb-3">Estadísticas Base</h4>

                    <!-- hp -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Puntos de Salud (HP)</span>
                            <span class="fw-bold">{{ $pokemon['stats']['hp'] }}</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div 
                                class="progress-bar bg-success" 
                                role="progressbar" 
                                style="width: {{ min(100, ($pokemon['stats']['hp'] / 255) * 100) }}%;"
                                aria-valuenow="{{ $pokemon['stats']['hp'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="255">
                            </div>
                        </div>
                    </div>

                    <!-- attack -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Ataque (Attack)</span>
                            <span class="fw-bold">{{ $pokemon['stats']['attack'] }}</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div 
                                class="progress-bar bg-danger" 
                                role="progressbar" 
                                style="width: {{ min(100, ($pokemon['stats']['attack'] / 255) * 100) }}%;"
                                aria-valuenow="{{ $pokemon['stats']['attack'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="255">
                            </div>
                        </div>
                    </div>

                    <!-- defense -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Defensa (Defense)</span>
                            <span class="fw-bold">{{ $pokemon['stats']['defense'] }}</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div 
                                class="progress-bar bg-primary" 
                                role="progressbar" 
                                style="width: {{ min(100, ($pokemon['stats']['defense'] / 255) * 100) }}%;"
                                aria-valuenow="{{ $pokemon['stats']['defense'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="255">
                            </div>
                        </div>
                    </div>

                    <!-- estadisticas secundarias -->
                    <div class="row g-2 mt-2 pt-2 border-top">
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block">Atq. Especial</span>
                            <strong class="fs-6">{{ $pokemon['stats']['special_attack'] }}</strong>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block">Def. Especial</span>
                            <strong class="fs-6">{{ $pokemon['stats']['special_defense'] }}</strong>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block">Velocidad</span>
                            <strong class="fs-6">{{ $pokemon['stats']['speed'] }}</strong>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ route('pokemon.index') }}" class="btn btn-secondary px-4">
                        Volver a la Pokédex
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
