<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

// Ruta 1: Home
Route::get('/', function () {
    return view('home');
})->name('home');

// Ruta 2: Listado (con datos prueba de 12 pokémon)
Route::get('/pokemon', [PokemonController::class, 'index'])->name('pokemon.index');

// Ruta 3: Detalle (con datos prueba)
Route::get('/pokemon/{name}', [PokemonController::class, 'show'])->name('pokemon.show');
