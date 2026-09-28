<?php

// importamos el controlador de pokemon para poder usar sus metodos
use App\Http\Controllers\PokemonController;
// importamos la clase route de laravel para definir las rutas web
use Illuminate\Support\Facades\Route;

// ruta principal de la aplicacion que responde cuando entran a la diagonal
Route::get('/', function () {
    // retornamos la vista home que esta en resources views
    return view('home');
// le ponemos un nombre a la ruta para poder enlazarla facilmente
})->name('home');

// ruta que muestra la lista de pokemon llamando al metodo index del controlador
Route::get('/pokemon', [PokemonController::class, 'index'])->name('pokemon.index');

// ruta con parametro dinamico name que manda el nombre al metodo show del controlador
Route::get('/pokemon/{name}', [PokemonController::class, 'show'])->name('pokemon.show');
