<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PokemonController extends Controller
{
    /**
     * Mapeo de tipos en espanol para la pokedex retro.
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
     * Muestra la pokedex pixel art con los pokemon requeridos de la 1ra generacion.
     * Utiliza la base de datos local para operar 100% offline sin necesidad de internet.
     */
    public function index(Request $request)
    {
        $pokemonList = $this->getPokemonList();
        $searchError = null;
        $activeName = 'bulbasaur'; // Por defecto iniciamos con bulbasaur (#001)

        // Validacion y procesamiento del buscador
        if ($request->has('search')) {
            $rawSearch = $request->query('search');

            if ($rawSearch === null || trim($rawSearch) === '') {
                // Validacion obligatoria: no permitir busqueda vacia
                $searchError = 'El campo de búsqueda no puede estar vacío. Ingrese el nombre de un Pokémon.';
            } else {
                $searchQuery = strtolower(trim($rawSearch));

                // Verificamos si existe en la lista, base de datos local o API
                $found = $this->findInList($pokemonList, $searchQuery);
                if ($found) {
                    $activeName = $found;
                } else {
                    $pokemonData = $this->fetchPokemonData($searchQuery);
                    if ($pokemonData) {
                        $activeName = $pokemonData['name'];
                        // Refrescar lista por si se guardo un nuevo pokemon en BD
                        $pokemonList = $this->getPokemonList();
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
     * Muestra el detalle de un pokemon especifico en la pokedex.
     * Si la peticion es AJAX/JSON, devuelve el JSON directamente para actualizacion dinamica.
     */
    public function show(Request $request, $name)
    {
        $normalizedName = strtolower(trim($name));
        $pokemon = $this->fetchPokemonData($normalizedName);

        // Respuesta JSON para actualizar el panel derecho sin recargar pagina
        if ($request->expectsJson() || $request->ajax() || $request->query('format') === 'json') {
            if (! $pokemon) {
                return response()->json([
                    'error' => 'Pokémon no encontrado en la base de datos local ni en la Pokédex.',
                ], 404);
            }

            return response()->json($pokemon);
        }

        // Vista de error amigable si no existe en acceso directo
        if (! $pokemon) {
            return response()->view('pokemon.error', [
                'name' => $name,
                'message' => 'El Pokémon solicitado no fue encontrado en los registros de la base de datos ni de la Pokédex.',
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
     * Obtiene la lista de los 20 pokemon obligatorios.
     * Prioridad 1: Base de datos local (MySQL/MariaDB) para funcionamiento 100% offline.
     * Prioridad 2: Respaldo local seguro.
     */
    private function getPokemonList(): array
    {
        try {
            $dbPokemons = Pokemon::orderBy('id')->get();
            if ($dbPokemons->isNotEmpty()) {
                return $dbPokemons->map(function ($p) {
                    $localPath = public_path("images/sprites/{$p->id}.png");
                    $spriteUrl = file_exists($localPath)
                        ? asset("images/sprites/{$p->id}.png")
                        : ($p->sprite ?? asset("images/sprites/{$p->id}.png"));

                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'sprite' => $spriteUrl,
                    ];
                })->toArray();
            }
        } catch (\Exception $e) {
            // Si la base de datos no responde, continuamos al fallback
        }

        return $this->getFallback20List();
    }

    /**
     * Obtiene los datos del pokemon.
     * 1. Consulta la base de datos local (offline).
     * 2. Si no existe, consulta la PokeAPI y lo guarda localmente en la base de datos.
     */
    private function fetchPokemonData(string $nameOrId): ?array
    {
        $normalized = strtolower(trim($nameOrId));

        // 1. Consultar base de datos local (permite trabajar sin internet)
        try {
            $localPokemon = is_numeric($normalized)
                ? Pokemon::find((int) $normalized)
                : Pokemon::where('name', $normalized)->first();

            if ($localPokemon) {
                return $localPokemon->toFrontendArray();
            }
        } catch (\Exception $e) {
            // Continuar si la conexion falla
        }

        // 2. Si no esta en la base de datos local, intentar consultar la PokeAPI externa
        try {
            $pokemonRes = Http::withoutVerifying()
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->timeout(4)
                ->get("https://pokeapi.co/api/v2/pokemon/{$normalized}");

            if ($pokemonRes->successful()) {
                $poke = $pokemonRes->json();
                $id = (int) $poke['id'];

                $fireRedSprite = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png";

                $localSpriteFile = public_path("images/sprites/{$id}.png");
                $spriteDbPath = file_exists($localSpriteFile) ? "/images/sprites/{$id}.png" : $fireRedSprite;

                $descriptionEs = 'Información no disponible en este momento.';
                $genusEs = 'Pokémon de Kanto';

                $speciesRes = Http::withoutVerifying()
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->timeout(4)
                    ->get("https://pokeapi.co/api/v2/pokemon-species/{$id}");

                if ($speciesRes->successful()) {
                    $species = $speciesRes->json();

                    foreach ($species['flavor_text_entries'] as $entry) {
                        if ($entry['language']['name'] === 'es') {
                            $cleanText = preg_replace("/[\f\n\r]+/", ' ', $entry['flavor_text']);
                            $descriptionEs = trim($cleanText);
                            break;
                        }
                    }

                    foreach ($species['genera'] as $g) {
                        if ($g['language']['name'] === 'es') {
                            $genusEs = $g['genus'];
                            break;
                        }
                    }
                }

                $types = array_map(function ($t) {
                    $typeKey = strtolower($t['type']['name']);

                    return [
                        'key' => $typeKey,
                        'name' => self::TYPE_TRANSLATIONS[$typeKey] ?? strtoupper($typeKey),
                    ];
                }, $poke['types']);

                // Guardar en la base de datos local para que quede guardado permanentemente
                try {
                    $savedPokemon = Pokemon::updateOrCreate(['id' => $id], [
                        'name' => $poke['name'],
                        'genus' => $genusEs,
                        'height' => $poke['height'] / 10,
                        'weight' => $poke['weight'] / 10,
                        'sprite' => $spriteDbPath,
                        'types' => $types,
                        'description' => $descriptionEs,
                        'hp' => $this->extractStat($poke['stats'], 'hp'),
                        'attack' => $this->extractStat($poke['stats'], 'attack'),
                        'defense' => $this->extractStat($poke['stats'], 'defense'),
                        'special_attack' => $this->extractStat($poke['stats'], 'special-attack'),
                        'special_defense' => $this->extractStat($poke['stats'], 'special-defense'),
                        'speed' => $this->extractStat($poke['stats'], 'speed'),
                    ]);

                    return $savedPokemon->toFrontendArray();
                } catch (\Exception $e) {
                    return [
                        'id' => $id,
                        'name' => $poke['name'],
                        'genus' => $genusEs,
                        'height' => $poke['height'] / 10,
                        'weight' => $poke['weight'] / 10,
                        'sprite' => $spriteDbPath,
                        'types' => $types,
                        'description' => $descriptionEs,
                        'from_local_db' => false,
                        'stats' => [
                            'hp' => $this->extractStat($poke['stats'], 'hp'),
                            'attack' => $this->extractStat($poke['stats'], 'attack'),
                            'defense' => $this->extractStat($poke['stats'], 'defense'),
                            'special_attack' => $this->extractStat($poke['stats'], 'special-attack'),
                            'special_defense' => $this->extractStat($poke['stats'], 'special-defense'),
                            'speed' => $this->extractStat($poke['stats'], 'speed'),
                        ],
                    ];
                }
            }
        } catch (\Exception $e) {
            // Sin conexion o API inaccesible
        }

        return null;
    }

    /**
     * Busca por nombre o numero en la lista.
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
     * Extrae el valor numerico de una estadistica.
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
     * Respaldo local de los 20 pokemon con sprites de rojo fuego.
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
            $localFile = public_path("images/sprites/{$id}.png");
            $sprite = file_exists($localFile)
                ? asset("images/sprites/{$id}.png")
                : "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/{$id}.png";

            $list[] = [
                'id' => $id,
                'name' => $name,
                'sprite' => $sprite,
            ];
        }

        return $list;
    }

    /**
     * Respaldo de bulbasaur con descripcion en espanol.
     */
    private function getFallbackPokemon(string $name): array
    {
        $localFile = public_path('images/sprites/1.png');
        $sprite = file_exists($localFile)
            ? asset('images/sprites/1.png')
            : 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-iii/firered-leafgreen/1.png';

        return [
            'id' => 1,
            'name' => $name,
            'genus' => 'Pokémon Semilla',
            'height' => 0.7,
            'weight' => 6.9,
            'sprite' => $sprite,
            'from_local_db' => true,
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
