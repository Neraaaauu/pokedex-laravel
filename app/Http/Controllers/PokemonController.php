<?php

// definimos el espacio de nombres donde esta guardado este controlador
namespace App\Http\Controllers;

// creamos la clase del controlador que hereda del controlador base de laravel
class PokemonController extends Controller
{
    // metodo para mostrar la lista con los doce pokemon de prueba
    public function index()
    {
        // creamos un arreglo con los doce nombres que pide el profesor
        $pokemons = [
            'Bulbasaur',
            'Ivysaur',
            'Venusaur',
            'Charmander',
            'Charmeleon',
            'Charizard',
            'Squirtle',
            'Wartortle',
            'Blastoise',
            'Caterpie',
            'Metapod',
            'Butterfree',
        ];

        // mandamos el arreglo a la vista pokemon.index usando la funcion compact
        return view('pokemon.index', compact('pokemons'));
    }

    // metodo para mostrar el detalle del pokemon seleccionado
    // recibe la variable name directamente desde el parametro de la url
    public function show($name)
    {
        // mandamos la variable con el nombre a la vista pokemon.show
        return view('pokemon.show', compact('name'));
    }
}
