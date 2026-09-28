<?php

namespace App\Http\Controllers;

class PokemonController extends Controller
{
    /**
     * Muestra el listado con un arreglo prueba de 12 pokémon (solo nombres).
     */
    public function index()
    {
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

        return view('pokemon.index', compact('pokemons'));
    }

    /**
     * Recibe el nombre por URL y lo muestra en la vista.
     */
    public function show($name)
    {
        return view('pokemon.show', compact('name'));
    }
}
