@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 text-center">
        <div class="card p-5 shadow-sm border-0 bg-white">
            <h1 class="display-5 fw-bold mb-3 text-dark">Pokédex Web</h1>
            <p class="lead text-muted mb-4">
                Aplicación web desarrollada en Laravel para consultar información oficial de Pokémon a través de PokéAPI.
            </p>
            <div class="d-flex justify-content-center gap-3 mb-5">
                <a href="{{ route('pokemon.index') }}" class="btn btn-primary btn-lg px-4">
                    Explorar Pokémon
                </a>
                <a href="{{ route('about') }}" class="btn btn-outline-secondary btn-lg px-4">
                    Acerca de
                </a>
            </div>

            <div class="row text-start g-4 pt-3 border-top">
                <div class="col-md-4">
                    <h5 class="fw-bold">Catálogo Oficial</h5>
                    <p class="text-muted small">
                        Visualización de Pokémon con sus números de registro oficial e imágenes cargadas dinámicamente desde PokéAPI.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Búsqueda Rápida</h5>
                    <p class="text-muted small">
                        Buscador integrado con validación para consultar cualquier Pokémon por su nombre directamente en la API.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Detalle y Estadísticas</h5>
                    <p class="text-muted small">
                        Ficha completa con tipos elementales, peso, altura y estadísticas base de combate (HP, Ataque y Defensa).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
