{{-- heredamos el layout principal con la barra y bootstrap --}}
@extends('layouts.app')

{{-- abrimos la seccion del contenido para la vista de detalle --}}
@section('content')
{{-- centramos la tarjeta en la pantalla con una columna de tamano 6 --}}
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        {{-- tarjeta que contiene los detalles del pokemon --}}
        <div class="card shadow-sm border p-4">
            {{-- mostramos el nombre que llego desde la url --}}
            <h2 class="card-title text-capitalize fw-bold mb-4">{{ $name }}</h2>

            {{-- recuadro gris como placeholder de la imagen para el objetivo 1 --}}
            <div class="bg-body-secondary border rounded d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 200px; height: 200px;">
                <span class="text-muted fw-semibold">Placeholder Imagen</span>
            </div>

            <div>
                {{-- boton para regresar a la lista de pokemon --}}
                <a href="{{ route('pokemon.index') }}" class="btn btn-secondary">
                    Volver a Pokémon
                </a>
            </div>
        </div>
    </div>
</div>
{{-- cerramos la seccion del contenido --}}
@endsection
