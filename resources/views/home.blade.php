{{-- heredamos toda la estructura del layout principal --}}
@extends('layouts.app')

{{-- definimos la seccion que se va a meter en el yield del layout --}}
@section('content')
{{-- centramos el contenido con las clases de bootstrap --}}
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        {{-- tarjeta blanca con sombra suave y padding --}}
        <div class="card p-5 shadow-sm border-0">
            {{-- titulo principal de la pantalla de bienvenida --}}
            <h1 class="display-6 fw-bold mb-3">Pokédex Web</h1>
            {{-- texto descriptivo para el usuario --}}
            <p class="text-muted mb-4">
                Bienvenido a la Pokédex. Aquí puedes consultar el listado de Pokémon y ver sus detalles.
            </p>
            <div>
                {{-- boton que te manda directo a la ruta del listado de pokemon --}}
                <a href="{{ route('pokemon.index') }}" class="btn btn-primary btn-lg px-4">
                    Ver Pokémon
                </a>
            </div>
        </div>
    </div>
</div>
{{-- cerramos la seccion del contenido --}}
@endsection
