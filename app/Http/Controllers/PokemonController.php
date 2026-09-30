<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    /**
     * Muestra el listado de Pokémon consumiendo la PokéAPI.
     * Incluye funcionalidad de búsqueda con validación.
     */
    public function index(Request $request)
    {
        $searchResult = null;
        $searchError = null;
        $searchQuery = null;

        // Validamos si el usuario envió el parámetro de búsqueda
        if ($request->has('search')) {
            $rawSearch = $request->query('search');

            if ($rawSearch === null || trim($rawSearch) === '') {
                // Validación obligatoria: No permitir búsqueda vacía
                $searchError = 'El campo de búsqueda no puede estar vacío. Ingrese el nombre de un Pokémon.';
            } else {
                $searchQuery = strtolower(trim($rawSearch));

                try {
                    // Consultamos el Pokémon directamente en la PokéAPI
                    $searchResponse = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon/{$searchQuery}");

                    if ($searchResponse->successful()) {
                        $data = $searchResponse->json();
                        $searchResult = [
                            'id' => $data['id'],
                            'name' => $data['name'],
                            'sprite' => $data['sprites']['other']['official-artwork']['front_default']
                                ?? $data['sprites']['front_default'],
                            'types' => array_map(fn ($t) => $t['type']['name'], $data['types']),
                            'stats' => [
                                'hp' => $this->extractStat($data['stats'], 'hp'),
                                'attack' => $this->extractStat($data['stats'], 'attack'),
                                'defense' => $this->extractStat($data['stats'], 'defense'),
                            ],
                        ];
                    } else {
                        $searchError = "No se encontró ningún Pokémon con el nombre \"{$rawSearch}\". Verifica que esté bien escrito.";
                    }
                } catch (\Exception $e) {
                    $searchError = 'Ocurrió un error al consultar la PokéAPI. Por favor intenta de nuevo.';
                }
            }
        }

        // Obtenemos los primeros 20 Pokémon desde la PokéAPI para el listado principal
        $pokemons = [];
        try {
            $response = Http::timeout(5)->get('https://pokeapi.co/api/v2/pokemon?limit=20');

            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];

                foreach ($results as $item) {
                    // Extraemos el ID a partir de la URL de la API
                    preg_match('/\/pokemon\/(\d+)\//', $item['url'], $matches);
                    $id = isset($matches[1]) ? (int) $matches[1] : null;

                    $pokemons[] = [
                        'id' => $id,
                        'name' => $item['name'],
                        'sprite' => $id ? "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png" : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Si la API falla, no rompemos el sitio
            $pokemons = [];
        }

        return view('pokemon.index', compact('pokemons', 'searchResult', 'searchError', 'searchQuery'));
    }

    /**
     * Muestra el detalle de un Pokémon específico consumiendo la PokéAPI.
     * Si no existe o falla la conexión, muestra una vista de error amigable.
     */
    public function show($name)
    {
        $normalizedName = strtolower(trim($name));

        try {
            $response = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon/{$normalizedName}");

            if ($response->failed() || ! $response->successful()) {
                return response()->view('pokemon.error', [
                    'name' => $name,
                    'message' => 'El Pokémon solicitado no fue encontrado en la base de datos de PokéAPI.',
                ], 404);
            }

            $data = $response->json();

            $pokemon = [
                'id' => $data['id'],
                'name' => $data['name'],
                'height' => $data['height'] / 10, // decímetros a metros
                'weight' => $data['weight'] / 10, // hectogramos a kilogramos
                'sprite' => $data['sprites']['other']['official-artwork']['front_default']
                    ?? $data['sprites']['front_default'],
                'sprite_pixel' => $data['sprites']['front_default'],
                'types' => array_map(fn ($t) => $t['type']['name'], $data['types']),
                'stats' => [
                    'hp' => $this->extractStat($data['stats'], 'hp'),
                    'attack' => $this->extractStat($data['stats'], 'attack'),
                    'defense' => $this->extractStat($data['stats'], 'defense'),
                    'special_attack' => $this->extractStat($data['stats'], 'special-attack'),
                    'special_defense' => $this->extractStat($data['stats'], 'special-defense'),
                    'speed' => $this->extractStat($data['stats'], 'speed'),
                ],
            ];

            return view('pokemon.show', compact('pokemon'));
        } catch (\Exception $e) {
            return response()->view('pokemon.error', [
                'name' => $name,
                'message' => 'Hubo un error de conexión al consultar el servicio de PokéAPI.',
            ], 500);
        }
    }

    /**
     * Extrae el valor numérico de una estadística específica.
     */
    private function extractStat(array $stats, string $statName): int
    {
        foreach ($stats as $stat) {
            if ($stat['stat']['name'] === $statName) {
                return (int) $stat['base_stat'];
            }
        }

        return 0;
    }
}
