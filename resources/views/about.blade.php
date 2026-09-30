@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 bg-white p-4 p-md-5">
            <h1 class="h2 fw-bold text-dark mb-3">Acerca del Proyecto</h1>
            <p class="text-muted mb-4">
                Proyecto desarrollado para la materia <strong>Herramientas para acelerar la construcción de software (Unidad III)</strong>.
            </p>

            <hr class="my-4">

            <div class="mb-4">
                <h5 class="fw-bold text-dark">Información del Equipo</h5>
                <ul class="list-group list-group-flush mt-2">
                    <li class="list-group-item px-0">
                        <strong>Equipo:</strong> Equipo 2
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Integrantes:</strong> Gerardo Adrián Rodríguez Domínguez & Compañero de Equipo
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Materia:</strong> Herramientas para acelerar la construcción de software
                    </li>
                    <li class="list-group-item px-0">
                        <strong>Unidad:</strong> Unidad III - Desarrollo acelerado con Laravel, Composer y APIs
                    </li>
                </ul>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark">Objetivo del Proyecto</h5>
                <p class="text-secondary">
                    Construir una aplicación web completa bajo la arquitectura Modelo-Vista-Controlador (MVC) utilizando el framework Laravel, acelerando el ciclo de desarrollo mediante el uso del gestor de dependencias Composer (Laravel Debugbar, Laravel Pint), la integración con servicios externos (PokéAPI) y el diseño responsivo con Bootstrap 5.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark">Tecnologías y Herramientas Empleadas</h5>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-danger">Laravel 11+</span>
                    <span class="badge bg-primary">PHP 8.3</span>
                    <span class="badge bg-dark">Composer</span>
                    <span class="badge bg-secondary">Laravel Pint</span>
                    <span class="badge bg-warning text-dark">Laravel Debugbar</span>
                    <span class="badge bg-info text-dark">PokéAPI REST</span>
                    <span class="badge bg-success">Bootstrap 5.3 CDN</span>
                </div>
            </div>

            <div class="pt-3">
                <a href="{{ route('pokemon.index') }}" class="btn btn-primary">
                    Ir al Catálogo de Pokémon
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
