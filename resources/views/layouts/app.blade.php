<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Pokédex') }} - Unidad III</title>
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Pokédex Kanto Custom Style -->
    <link href="{{ asset('css/pokedex.css') }}" rel="stylesheet">

    <style>
        .badge-type-grass { background-color: #78c850; color: #fff; }
        .badge-type-fire { background-color: #f08030; color: #fff; }
        .badge-type-water { background-color: #6890f0; color: #fff; }
        .badge-type-bug { background-color: #a8b820; color: #fff; }
        .badge-type-normal { background-color: #a8a878; color: #fff; }
        .badge-type-poison { background-color: #a040a0; color: #fff; }
        .badge-type-electric { background-color: #f8d030; color: #212529; }
        .badge-type-ground { background-color: #e0c068; color: #212529; }
        .badge-type-fairy { background-color: #ee99ac; color: #212529; }
        .badge-type-fighting { background-color: #c03028; color: #fff; }
        .badge-type-psychic { background-color: #f85888; color: #fff; }
        .badge-type-rock { background-color: #b8a038; color: #fff; }
        .badge-type-ghost { background-color: #705898; color: #fff; }
        .badge-type-ice { background-color: #98d8d8; color: #212529; }
        .badge-type-dragon { background-color: #7038f8; color: #fff; }
        .badge-type-steel { background-color: #b8b8d0; color: #212529; }
        .badge-type-flying { background-color: #a890f0; color: #fff; }
        .badge-type-dark { background-color: #705848; color: #fff; }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Pokédex Web</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pokemon*') ? 'active' : '' }}" href="{{ route('pokemon.index') }}">Pokémon</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Acerca de</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container my-5 flex-grow-1">
        @yield('content')
    </main>

    <!-- Pie de página -->
    <footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
        <div class="container">
            <span>Proyecto Pokédex Web &bull; Unidad III &bull; Herramientas para acelerar la construcción de software</span>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
