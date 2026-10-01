@extends('layouts.app')

@section('content')
<div class="pokedex-wrapper">

    <!-- mensaje de error de validacion o busqueda si aplica -->
    @if ($searchError)
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-2 border-dark rounded-0 font-monospace" role="alert">
            <strong>ATENCIÓN:</strong> {{ $searchError }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- dispositivo pokedex folder pixel art (rojo fuego / gba) -->
    <div class="pokedex-device">

        <!-- ============================================== -->
        <!-- panel izquierdo: selector de pokemon          -->
        <!-- ============================================== -->
        <div class="pokedex-panel-left">

            <!-- luces e indicadores pixel art -->
            <div class="pokedex-header-lights">
                <div class="camera-lens" id="cameraLens" title="Sensor principal Pokédex"></div>
                <div class="led-lights">
                    <span class="led led-red" title="Sensor auxiliar"></span>
                    <span class="led led-yellow" title="Procesador"></span>
                    <span class="led led-green" title="PokéAPI activa"></span>
                </div>
            </div>

            <!-- marco de la pantalla izquierda -->
            <div class="pokedex-screen-frame">
                <div class="screen-frame-dots">
                    <span class="screen-frame-dot"></span>
                    <span class="screen-frame-dot"></span>
                </div>

                <!-- pantalla interna estilo lcd -->
                <div class="pokedex-inner-screen">
                    <!-- formulario y buscador de pokemon -->
                    <div class="pokedex-search-box">
                        <form action="{{ route('pokemon.index') }}" method="GET" id="searchForm" class="d-flex gap-2">
                            <input 
                                type="text" 
                                name="search" 
                                id="searchInput"
                                class="pokedex-search-input" 
                                placeholder="BUSCAR POKÉMON..."
                                value="{{ $searchQuery }}"
                                autocomplete="off"
                            >
                            <button type="submit" class="pokedex-search-btn">
                                BUSCAR
                            </button>
                        </form>
                    </div>

                    <!-- lista de los 20 pokemon con sprites de 3ra generacion -->
                    <div class="pokemon-select-list" id="pokemonList">
                        @foreach ($pokemons as $p)
                            <a 
                                href="{{ route('pokemon.show', $p['name']) }}"
                                class="pokemon-select-item {{ strtolower($activePokemon['name']) === strtolower($p['name']) ? 'active' : '' }}"
                                data-id="{{ $p['id'] }}"
                                data-name="{{ strtolower($p['name']) }}"
                                onclick="selectPokemon(event, '{{ strtolower($p['name']) }}')"
                            >
                                <div class="pokemon-item-left">
                                    <img 
                                        src="{{ $p['sprite'] }}" 
                                        alt="{{ $p['name'] }}" 
                                        class="pokemon-item-sprite"
                                        loading="lazy"
                                    >
                                    <div>
                                        <div class="pokemon-item-number">#{{ str_pad($p['id'], 3, '0', STR_PAD_LEFT) }}</div>
                                        <div class="pokemon-item-name">{{ $p['name'] }}</div>
                                    </div>
                                </div>
                                <span class="pokemon-item-ver-btn">
                                    VER
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- controles fisicos inferiores pixel art -->
            <div class="pokedex-controls">
                <div class="action-circle-btn" title="Botón de acción"></div>
                <div class="action-pill-buttons">
                    <span class="pill-btn pill-red"></span>
                    <span class="pill-btn pill-blue"></span>
                </div>
                <div class="dpad">
                    <div class="dpad-cross dpad-up-down"></div>
                    <div class="dpad-cross dpad-left-right"></div>
                    <div class="dpad-center"></div>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- bisagra central pixel art                     -->
        <!-- ============================================== -->
        <div class="pokedex-hinge">
            <div class="hinge-segment"></div>
            <div class="hinge-segment"></div>
            <div class="hinge-segment"></div>
            <div class="hinge-segment"></div>
        </div>

        <!-- ============================================== -->
        <!-- panel derecho: pantalla rojo fuego            -->
        <!-- ============================================== -->
        <div class="pokedex-panel-right">

            <!-- pantalla superior: sprite e identificacion -->
            <div class="pokedex-info-screen">
                <div class="pokemon-display-header">
                    <h3 class="pokemon-display-title" id="displayPokemonName">
                        {{ $activePokemon['name'] }}
                    </h3>
                    <span class="pokemon-display-id" id="displayPokemonId">
                        #{{ str_pad($activePokemon['id'], 3, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <div class="pokemon-visual-box">
                    <div class="pokemon-artwork-container">
                        <img 
                            src="{{ $activePokemon['sprite'] }}" 
                            alt="{{ $activePokemon['name'] }}" 
                            class="pokemon-artwork-img"
                            id="displayPokemonArtwork"
                        >
                    </div>

                    <div class="pokemon-meta-info">
                        <div class="pokemon-genus-badge" id="displayPokemonGenus">
                            {{ $activePokemon['genus'] }}
                        </div>

                        <!-- tipos elementales traducidos al espanol -->
                        <div class="pokemon-types-list" id="displayPokemonTypes">
                            @foreach ($activePokemon['types'] as $type)
                                <span class="pixel-type-badge badge-type-{{ strtolower($type['key']) }}">
                                    {{ $type['name'] }}
                                </span>
                            @endforeach
                        </div>

                        <!-- dimensiones fisicas -->
                        <div class="pokemon-dimensions-grid">
                            <div class="dimension-box">
                                <small>ALTURA</small>
                                <span id="displayPokemonHeight">{{ $activePokemon['height'] }} M</span>
                            </div>
                            <div class="dimension-box">
                                <small>PESO</small>
                                <span id="displayPokemonWeight">{{ $activePokemon['weight'] }} KG</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- pantalla verde lcd: descripcion exclusivamente en espanol de rojo fuego -->
            <div class="firered-screen">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="firered-screen-badge">DATOS POKÉDEX (ROJO FUEGO)</span>
                    <span class="badge {{ !empty($activePokemon['from_local_db']) ? 'bg-success' : 'bg-primary' }} font-monospace" style="font-size: 0.6rem; letter-spacing: 0.5px;" id="displayDbBadge">
                        {{ !empty($activePokemon['from_local_db']) ? '💾 BD LOCAL' : '🌐 POKEAPI' }}
                    </span>
                </div>
                <p class="firered-text" id="displayPokemonDesc">
                    {{ $activePokemon['description'] }}
                </p>
            </div>

            <!-- monitor de estadisticas base de combate pixel art -->
            <div class="pokedex-stats-screen">
                <div class="stats-title">ESTADÍSTICAS BASE</div>

                <!-- puntos de salud (hp) -->
                <div class="stat-row">
                    <div class="stat-label-group">
                        <span>PS (HP)</span>
                        <span id="displayHpVal">{{ $activePokemon['stats']['hp'] }}</span>
                    </div>
                    <div class="stat-bar-container">
                        <div 
                            class="stat-bar-fill stat-fill-hp" 
                            id="displayHpBar" 
                            style="width: {{ min(100, ($activePokemon['stats']['hp'] / 255) * 100) }}%;">
                        </div>
                    </div>
                </div>

                <!-- ataque (attack) -->
                <div class="stat-row">
                    <div class="stat-label-group">
                        <span>ATAQUE</span>
                        <span id="displayAtkVal">{{ $activePokemon['stats']['attack'] }}</span>
                    </div>
                    <div class="stat-bar-container">
                        <div 
                            class="stat-bar-fill stat-fill-attack" 
                            id="displayAtkBar" 
                            style="width: {{ min(100, ($activePokemon['stats']['attack'] / 255) * 100) }}%;">
                        </div>
                    </div>
                </div>

                <!-- defensa (defense) -->
                <div class="stat-row">
                    <div class="stat-label-group">
                        <span>DEFENSA</span>
                        <span id="displayDefVal">{{ $activePokemon['stats']['defense'] }}</span>
                    </div>
                    <div class="stat-bar-container">
                        <div 
                            class="stat-bar-fill stat-fill-defense" 
                            id="displayDefBar" 
                            style="width: {{ min(100, ($activePokemon['stats']['defense'] / 255) * 100) }}%;">
                        </div>
                    </div>
                </div>

                <!-- estadisticas secundarias -->
                <div class="stat-secondary-grid">
                    <span>ATQ.ESP: <strong id="displaySpAtkVal">{{ $activePokemon['stats']['special_attack'] }}</strong></span>
                    <span>DEF.ESP: <strong id="displaySpDefVal">{{ $activePokemon['stats']['special_defense'] }}</strong></span>
                    <span>VELOCIDAD: <strong id="displaySpeedVal">{{ $activePokemon['stats']['speed'] }}</strong></span>
                </div>
            </div>

            <!-- teclado numerico pixel art (10 teclas azules) -->
            <div class="pokedex-keypad">
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
                <button class="keypad-key" type="button" onclick="keypadTone()"></button>
            </div>

        </div>

    </div>

</div>

<!-- script de interaccion asincrona de la pokedex -->
<script>
    // filtro instantaneo para el buscador de la lista
    const searchInput = document.getElementById('searchInput');
    const pokemonItems = document.querySelectorAll('.pokemon-select-item');

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        pokemonItems.forEach(item => {
            const name = item.dataset.name;
            const id = item.dataset.id;
            const paddedId = id.padStart(3, '0');

            if (name.includes(query) || id.includes(query) || paddedId.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // actualizacion dinamica del panel derecho al hacer clic
    async function selectPokemon(event, pokemonName) {
        if (event) event.preventDefault();

        const cameraLens = document.getElementById('cameraLens');
        cameraLens.classList.add('active-pulse');

        pokemonItems.forEach(item => {
            if (item.dataset.name === pokemonName) {
                item.classList.add('active');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });

        try {
            const response = await fetch(`/pokemon/${pokemonName}?format=json`);
            if (!response.ok) throw new Error('Error al cargar datos');

            const data = await response.json();
            updateRightPanel(data);

            window.history.pushState({ name: pokemonName }, '', `/pokemon/${pokemonName}`);
        } catch (error) {
            console.error(error);
        } finally {
            setTimeout(() => {
                cameraLens.classList.remove('active-pulse');
            }, 300);
        }
    }

    // renderiza los datos en la pantalla derecha
    function updateRightPanel(p) {
        document.getElementById('displayPokemonName').textContent = p.name;
        document.getElementById('displayPokemonId').textContent = '#' + String(p.id).padStart(3, '0');
        document.getElementById('displayPokemonArtwork').src = p.sprite;
        document.getElementById('displayPokemonArtwork').alt = p.name;
        document.getElementById('displayPokemonGenus').textContent = p.genus;
        document.getElementById('displayPokemonHeight').textContent = p.height + ' M';
        document.getElementById('displayPokemonWeight').textContent = p.weight + ' KG';

        // tipos elementales
        const typesContainer = document.getElementById('displayPokemonTypes');
        typesContainer.innerHTML = '';
        p.types.forEach(t => {
            const badge = document.createElement('span');
            badge.className = `pixel-type-badge badge-type-${t.key.toLowerCase()}`;
            badge.textContent = t.name;
            typesContainer.appendChild(badge);
        });

        // descripcion exclusivamente en espanol
        document.getElementById('displayPokemonDesc').textContent = p.description;

        // indicador de origen de datos (BD Local / PokeAPI)
        const dbBadge = document.getElementById('displayDbBadge');
        if (dbBadge) {
            dbBadge.textContent = p.from_local_db ? '💾 BD LOCAL' : '🌐 POKEAPI';
            dbBadge.className = (p.from_local_db ? 'badge bg-success' : 'badge bg-primary') + ' font-monospace';
        }

        // estadisticas base
        document.getElementById('displayHpVal').textContent = p.stats.hp;
        document.getElementById('displayHpBar').style.width = Math.min(100, (p.stats.hp / 255) * 100) + '%';

        document.getElementById('displayAtkVal').textContent = p.stats.attack;
        document.getElementById('displayAtkBar').style.width = Math.min(100, (p.stats.attack / 255) * 100) + '%';

        document.getElementById('displayDefVal').textContent = p.stats.defense;
        document.getElementById('displayDefBar').style.width = Math.min(100, (p.stats.defense / 255) * 100) + '%';

        document.getElementById('displaySpAtkVal').textContent = p.stats.special_attack;
        document.getElementById('displaySpDefVal').textContent = p.stats.special_defense;
        document.getElementById('displaySpeedVal').textContent = p.stats.speed;
    }

    function keypadTone() {
        const lens = document.getElementById('cameraLens');
        lens.classList.add('active-pulse');
        setTimeout(() => lens.classList.remove('active-pulse'), 200);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const activeItem = document.querySelector('.pokemon-select-item.active');
        if (activeItem) {
            activeItem.scrollIntoView({ behavior: 'auto', block: 'center' });
        }
    });
</script>
@endsection
