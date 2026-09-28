<!DOCTYPE html>
<html lang="es">
<head>
    {{-- definimos la codificacion de caracteres --}}
    <meta charset="UTF-8">
    {{-- configuramos para que la pagina sea responsiva en celulares --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- titulo que aparece en la pestana del navegador --}}
    <title>Pokédex</title>
    {{-- cargamos bootstrap 5 desde cdn para no instalar nada local --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    {{-- barra de navegacion oscura que se repite en todas las paginas --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            {{-- logo o nombre del sitio con enlace al inicio --}}
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Pokédex</a>
            {{-- boton para abrir el menu en pantallas pequenas --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            {{-- contenedor de los enlaces de navegacion --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        {{-- enlace a la pagina de inicio --}}
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        {{-- enlace al listado de pokemon --}}
                        <a class="nav-link {{ request()->is('pokemon*') ? 'active' : '' }}" href="{{ route('pokemon.index') }}">Pokémon</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- contenedor principal donde se inyecta el contenido de cada vista --}}
    <main class="container my-5">
        {{-- aqui se reemplaza con el codigo de la vista hija --}}
        @yield('content')
    </main>

    {{-- script de bootstrap para que funcionen los botones desplegables --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
