{{-- heredamos el layout principal con la barra de navegacion --}}
@extends('layouts.app')

{{-- abrimos la seccion del contenido para esta vista --}}
@section('content')
{{-- encabezado de la pagina con su titulo y descripcion --}}
<div class="mb-4">
    <h1 class="h2 fw-bold">Pokémon</h1>
    <p class="text-muted">Selecciona un Pokémon de la lista para ver su detalle.</p>
</div>

{{-- cuadricula responsiva para mostrar las tarjetas con bootstrap --}}
<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
    {{-- recorremos el arreglo que nos mando el controlador --}}
    @foreach ($pokemons as $pokemon)
        <div class="col">
            {{-- tarjeta individual para cada pokemon --}}
            <div class="card h-100 shadow-sm border">
                <div class="card-body text-center d-flex flex-column justify-content-between p-4">
                    {{-- imprimimos el nombre del pokemon con la primera letra mayuscula --}}
                    <h5 class="card-title text-capitalize mb-3">{{ $pokemon }}</h5>
                    {{-- boton con el enlace dinamico hacia la pantalla de detalle --}}
                    <a href="{{ url('/pokemon/' . strtolower($pokemon)) }}" class="btn btn-outline-primary btn-sm">
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>
    {{-- fin del ciclo foreach --}}
    @endforeach
</div>
{{-- cerramos la seccion del contenido --}}
@endsection
