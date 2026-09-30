<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    /**
     * Mapeo de tipos en español para la Pokédex retro.
     */
    private const TYPE_TRANSLATIONS = [
        'normal' => 'NORMAL',
        'fire' => 'FUEGO',
        'water' => 'AGUA',
        'grass' => 'PLANTA',
        'electric' => 'ELÉCTRICO',
        'ice' => 'HIELO',
        'fighting' => 'LUCHA',
        'poison' => 'VENENO',
        'ground' => 'TIERRA',
        'flying' => 'VOLADOR',
        'psychic' => 'PSÍQUICO',
        'bug' => 'BICHO',
        'rock' => 'ROCA',
        'ghost' => 'FANTASMA',
        'dragon' => 'DRAGÓN',
        'steel' => 'ACERO',
        'fairy' => 'HADA',
    ];

    /**
     * Muestra la Pokédex Pixel Art con los 20 Pokémon requeridos de la 1ra generación,
     * usando sprites de 3ra generación (Rojo Fuego) y descripciones exclusivamente en español.
     */
    public function index(Request $request)
    {
        $pokemonList = $this->getPokemonList();
        $searchError = null;
        $activeName = 'bulbasaur'; // Por defecto iniciamos con Bulbasaur (#001)

        // Validación y procesamiento del buscador
        if ($request->has('search')) {
            $rawSearch = $request->query('search');

            if ($rawSearch === null || trim($rawSearch) === '') {
                // Validación obligatoria: no permitir búsqueda vacía
                $searchError = 'El campo de búsqueda no puede estar vacío. Ingrese el nombre de un Pokémon.';
            } else {
                $searchQuery = strtolower(trim($rawSearch));

                // Verificamos si existe en el listado de los 20 o en la API
                $found = $this->findInList($pokemonList, $searchQuery);
                if ($found) {
                    $activeName = $found;
                } else {
                    $pokemonData = $this->fetchPokemonData($searchQuery);
                    if ($pokemonData) {
                        $activeName = $pokemonData['name'];
                    } else {
                        $searchError = "No se encontró ningún Pokémon con el nombre \"{$rawSearch}\". Verifica que esté bien escrito.";
                    }
                }
            }
        }

        $activePokemon = $this->fetchPokemonData($activeName);

        if (! $activePokemon) {
            $activePokemon = $this->getFallbackPokemon($activeName);
        }

        return view('pokemon.index', [
            'pokemons' => $pokemonList,
            'activePokemon' => $activePokemon,
            'searchError' => $searchError,
            'searchQuery' => $request->query('search', ''),
        ]);
    }

    /**
     * Muestra el detalle de un Pokémon específico en la Pokédex.
     * Si la petición es AJAX/JSON, devuelve el JSON directamente para actualización dinámica.
     */
    public function show(Request $request, $name)
    {
        $normalizedName = strtolower(trim($name));
        $pokemon = $this->fetchPokemonData($normalizedName);

        // Respuesta JSON para actualizar el panel derecho sin recargar página
        if ($request->expectsJson() || $request->ajax() || $request->query('format') === 'json') {
            if (! $pokemon) {
                return response()->json([
                    'error' => 'Pokémon no encontrado en la Pokédex de Rojo Fuego.',
                ], 404);
            }

            return response()->json($pokemon);
        }

        // Vista de error si no existe en acceso directo
        if (! $pokemon) {
            return response()->view('pokemon.error', [
                'name' => $name,
                'message' => 'El Pokémon solicitado no fue encontrado en los registros de Rojo Fuego.',
            ], 404);
        }

        $pokemonList = $this->getPokemonList();

        return view('pokemon.index', [
            'pokemons' => $pokemonList,
            'activePokemon' => $pokemon,
            'searchError' => null,
            'searchQuery' => '',
        ]);
    }

    /**
     * Obtiene la lista de los 20 Pokémon obligatorios con sprites de Rojo Fuego (3ra Generación).
     */
    private function getPokemonList(): array
    {
        return Cache::remember('pokedex_firered_list20', 86400, function () {
            try {
                $response = Http::timeout(6)->get('https://pokeapi.co/api/v2/pokemon?limit=20');

                if ($response->successful()) {
                    $results = $response->json()['results'] ?? [];
                    $list = [];

                    foreach ($results as $item) {
                        preg_match('/\/pokemon\/(\d+)\//', $item['url'], $matches);
                        $id = isset($matches[1]) ? (int) $matches[1] : null;

                        // Sprite oficial de la 3ra Generación (Pokémon Rojo Fuego / Verde Hoja)
                        $sprite = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png";

                        $list[] = [
                            'id' => $id,
                            'name' => $item['name'],
                            'sprite' => $sprite,
                        ];
                    }

                    return $list;
                }
            } catch (\Exception $e) {
                // Respaldo seguro
            }

            return $this->getFallback20List();
        });
    }

    /**
     * Obtiene los datos del Pokémon con sprite de 3ra generación (Rojo Fuego)
     * y descripción EXCLUSIVAMENTE en español.
     */
    private function fetchPokemonData(string $nameOrId): ?array
    {
        $cacheKey = 'pokedex_firered_es_'.strtolower(trim($nameOrId));

        return Cache::remember($cacheKey, 86400, function () use ($nameOrId) {
            try {
                $normalized = strtolower(trim($nameOrId));

                // 1. Datos técnicos (tipos, stats, peso, altura)
                $pokemonRes = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon/{$normalized}");
                if (! $pokemonRes->successful()) {
                    return null;
                }
                $poke = $pokemonRes->json();
                $id = $poke['id'];

                // Sprite oficial de Rojo Fuego (Gen 3)
                $fireRedSprite = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png";

                // 2. Descripción exclusivamente en español y categoría
                $descriptionEs = 'Información no disponible en este momento.';
                $genusEs = 'Pokémon de Kanto';

                $speciesRes = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon-species/{$id}");
                if ($speciesRes->successful()) {
                    $species = $speciesRes->json();

                    // Buscar la primera descripción oficial en español
                    foreach ($species['flavor_text_entries'] as $entry) {
                        if ($entry['language']['name'] === 'es') {
                            $cleanText = preg_replace("/[\f\n\r]+/", ' ', $entry['flavor_text']);
                            $descriptionEs = trim($cleanText);
                            break;
                        }
                    }

                    // Categoría en español (ej. "Pokémon Semilla", "Pokémon Llama")
                    foreach ($species['genera'] as $g) {
                        if ($g['language']['name'] === 'es') {
                            $genusEs = $g['genus'];
                            break;
                        }
                    }
                }

                // Tipos traducidos al español
                $types = array_map(function ($t) {
                    $typeKey = strtolower($t['type']['name']);

                    return [
                        'key' => $typeKey,
                        'name' => self::TYPE_TRANSLATIONS[$typeKey] ?? strtoupper($typeKey),
                    ];
                }, $poke['types']);

                return [
                    'id' => $id,
                    'name' => $poke['name'],
                    'genus' => $genusEs,
                    'height' => $poke['height'] / 10, // metros
                    'weight' => $poke['weight'] / 10, // kg
                    'sprite' => $fireRedSprite,
                    'types' => $types,
                    'description' => $descriptionEs,
                    'stats' => [
                        'hp' => $this->extractStat($poke['stats'], 'hp'),
                        'attack' => $this->extractStat($poke['stats'], 'attack'),
                        'defense' => $this->extractStat($poke['stats'], 'defense'),
                        'special_attack' => $this->extractStat($poke['stats'], 'special-attack'),
                        'special_defense' => $this->extractStat($poke['stats'], 'special-defense'),
                        'speed' => $this->extractStat($poke['stats'], 'speed'),
                    ],
                ];
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Busca por nombre o número en la lista.
     */
    private function findInList(array $list, string $query): ?string
    {
        $clean = strtolower(trim($query));
        foreach ($list as $p) {
            if ($p['name'] === $clean || (string) $p['id'] === $clean) {
                return $p['name'];
            }
        }

        return null;
    }

    /**
     * Extrae el valor numérico de una estadística.
     */
    private function extractStat(array $stats, string $statName): int
    {
        foreach ($stats as $item) {
            if ($item['stat']['name'] === $statName) {
                return (int) $item['base_stat'];
            }
        }

        return 0;
    }

    /**
     * Respaldo local de los 20 Pokémon con sprites de Rojo Fuego.
     */
    private function getFallback20List(): array
    {
        $names = [
            1 => 'bulbasaur', 2 => 'ivysaur', 3 => 'venusaur',
            4 => 'charmander', 5 => 'charmeleon', 6 => 'charizard',
            7 => 'squirtle', 8 => 'wartortle', 9 => 'blastoise',
            10 => 'caterpie', 11 => 'metapod', 12 => 'butterfree',
            13 => 'weedle', 14 => 'kakuna', 15 => 'beedrill',
            16 => 'pidgey', 17 => 'pidgeotto', 18 => 'pidgeot',
            19 => 'rattata', 20 => 'raticate',
        ];

        $list = [];
        foreach ($names as $id => $name) {
            $list[] = [
                'id' => $id,
                'name' => $name,
                'sprite' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png",
            ];
        }

        return $list;
    }

    /**
     * Respaldo de Bulbasaur con descripción en español.
     */
    private function getFallbackPokemon(string $name): array
    {
        return [
            'id' => 1,
            'name' => $name,
            'genus' => 'Pokémon Semilla',
            'height' => 0.7,
            'weight' => 6.9,
            'sprite' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/1.png',
            'types' => [
                ['key' => 'grass', 'name' => 'PLANTA'],
                ['key' => 'poison', 'name' => 'VENENO'],
            ],
            'description' => 'Una rara semilla le fue plantada en el lomo al nacer. La planta brota y crece con este Pokémon.',
            'stats' => [
                'hp' => 45,
                'attack' => 49,
                'defense' => 49,
                'special_attack' => 65,
                'special_defense' => 65,
                'speed' => 45,
            ],
        ];
    }
}
