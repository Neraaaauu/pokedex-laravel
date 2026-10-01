<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    /**
     * mapeo de tipos en espanol para la pokedex retro.
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
     * muestra la pokedex pixel art con los 20 pokemon requeridos de la 1ra generacion,
     * usando sprites de 3ra generacion (rojo fuego) y descripciones exclusivamente en espanol.
     */
    public function index(Request $request)
    {
        $pokemonList = $this->getPokemonList();
        $searchError = null;
        $activeName = 'bulbasaur'; // por defecto iniciamos con bulbasaur (#001)

        // validacion y procesamiento del buscador
        if ($request->has('search')) {
            $rawSearch = $request->query('search');

            if ($rawSearch === null || trim($rawSearch) === '') {
                // validacion obligatoria: no permitir busqueda vacia
                $searchError = 'El campo de búsqueda no puede estar vacío. Ingrese el nombre de un Pokémon.';
            } else {
                $searchQuery = strtolower(trim($rawSearch));

                // verificamos si existe en el listado de los 20 o en la api
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
     * muestra el detalle de un pokemon especifico en la pokedex.
     * si la peticion es ajax/json, devuelve el json directamente para actualizacion dinamica.
     */
    public function show(Request $request, $name)
    {
        $normalizedName = strtolower(trim($name));
        $pokemon = $this->fetchPokemonData($normalizedName);

        // respuesta json para actualizar el panel derecho sin recargar pagina
        if ($request->expectsJson() || $request->ajax() || $request->query('format') === 'json') {
            if (! $pokemon) {
                return response()->json([
                    'error' => 'Pokémon no encontrado en la Pokédex de Rojo Fuego.',
                ], 404);
            }

            return response()->json($pokemon);
        }

        // vista de error si no existe en acceso directo
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
     * obtiene la lista de los 20 pokemon obligatorios con sprites de rojo fuego (3ra generacion).
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

                        // sprite oficial de la 3ra generacion (pokemon rojo fuego / verde hoja)
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
                // respaldo seguro
            }

            return $this->getFallback20List();
        });
    }

    /**
     * obtiene los datos del pokemon con sprite de 3ra generacion (rojo fuego)
     * y descripcion exclusivamente en espanol.
     */
    private function fetchPokemonData(string $nameOrId): ?array
    {
        $cacheKey = 'pokedex_firered_es_'.strtolower(trim($nameOrId));

        return Cache::remember($cacheKey, 86400, function () use ($nameOrId) {
            try {
                $normalized = strtolower(trim($nameOrId));

                // 1. datos tecnicos (tipos, stats, peso, altura)
                $pokemonRes = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon/{$normalized}");
                if (! $pokemonRes->successful()) {
                    return null;
                }
                $poke = $pokemonRes->json();
                $id = $poke['id'];

                // sprite oficial de rojo fuego (gen 3)
                $fireRedSprite = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png";

                // 2. descripcion exclusivamente en espanol y categoria
                $descriptionEs = 'Información no disponible en este momento.';
                $genusEs = 'Pokémon de Kanto';

                $speciesRes = Http::timeout(5)->get("https://pokeapi.co/api/v2/pokemon-species/{$id}");
                if ($speciesRes->successful()) {
                    $species = $speciesRes->json();

                    // buscar la primera descripcion oficial en espanol
                    foreach ($species['flavor_text_entries'] as $entry) {
                        if ($entry['language']['name'] === 'es') {
                            $cleanText = preg_replace("/[\f\n\r]+/", ' ', $entry['flavor_text']);
                            $descriptionEs = trim($cleanText);
                            break;
                        }
                    }

                    // categoria en espanol (ej. "pokemon semilla", "pokemon llama")
                    foreach ($species['genera'] as $g) {
                        if ($g['language']['name'] === 'es') {
                            $genusEs = $g['genus'];
                            break;
                        }
                    }
                }

                // tipos traducidos al espanol
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
     * busca por nombre o numero en la lista.
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
     * extrae el valor numerico de una estadistica.
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
     * respaldo local de los 20 pokemon con sprites de rojo fuego.
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
     * respaldo de bulbasaur con descripcion en espanol.
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
