# Reporte de Entrega: Pokédex Web en Laravel
**Materia:** Herramientas para acelerar la construcción de software  
**Unidad:** Unidad III  
**Equipo:** Equipo 2  
**Integrantes:** Gerardo Adrián Rodríguez Domínguez & Compañero de Equipo  

---

## 1. Verificación de Cumplimiento de Objetivos

### Objetivo 1: Base MVC y Rutas
- [x] Proyecto Laravel configurado y funcionando localmente con `php artisan serve`.
- [x] Tres rutas base implementadas (`/`, `/pokemon`, `/pokemon/{name}`).
- [x] Controlador `PokemonController` con métodos `index()` y `show($name)`.
- [x] Vistas Blade base (`layouts/app`, `home`, `pokemon/index`, `pokemon/show`).
- [x] Integración de Bootstrap 5 por CDN.

### Objetivo 2: Librerías, Buscador, Mejora Visual y About
- [x] Librería Laravel Debugbar instalada e integrada para inspección de peticiones en entorno local.
- [x] Librería Laravel Pint ejecutada exitosamente para formateo y cumplimiento del estándar de código.
- [x] Buscador por nombre implementado en `/pokemon`.
- [x] Validación obligatoria: si el input está vacío, no se permite la búsqueda y se muestra un mensaje de error en la misma vista.
- [x] Catálogo mejorado visualmente con tarjetas (cards) responsivas de Bootstrap y botón "Ver".
- [x] Ruta y vista `/about` creada con el nombre del equipo, materia y objetivo del proyecto, accesible desde el navbar.

### Objetivo 3: Consumo de PokéAPI y Manejo de Errores
- [x] Consumo real del endpoint `https://pokeapi.co/api/v2/pokemon?limit=20` en `/pokemon` (exactamente los 20 requeridos de Kanto).
- [x] Interfaz 100% Pixel Art estilo folder/carpeta clásico de la serie animada de Pokémon.
- [x] Sprites oficiales de 3ra Generación (Pokémon Edición Rojo Fuego / Verde Hoja) con renderizado nítido `image-rendering: pixelated`.
- [x] Ficha de detalle interactiva con panel izquierdo (selector de los 20 Pokémon) y panel derecho con la información.
- [x] Descripciones oficiales EXCLUSIVAMENTE en español extraídas de la Pokédex de Rojo Fuego.
- [x] Estadísticas base de combate (PS, Ataque y Defensa) con barras de progreso segmentadas estilo retro.
- [x] Manejo de errores implementado: si el Pokémon no existe o falla la API, se muestra una vista amigable sin romper la aplicación.

### Requisito Especial: Base de Datos Local y Funcionamiento Offline (Sin Internet)
- [x] Base de datos local configurada e integrada en Laravel.
- [x] Migración `create_pokemon_table`: Tabla `pokemon` con campos de stats, tipos en JSON, sprites, medidas y descripciones.
- [x] Modelo Eloquent `App\Models\Pokemon` con casting nativo y método `toFrontendArray()`.
- [x] Seeder `PokemonSeeder`: Precarga los 20 Pokémon con datos completos de Rojo Fuego en español.
- [x] Sprites guardados localmente en `public/images/sprites/` para renderizar imágenes aún sin conexión a internet.
- [x] Estrategia Cache/Fallback: El controlador `PokemonController` consulta primero la BD local para máxima velocidad y autonomía offline. Si se consulta un Pokémon nuevo con internet, lo guarda en la BD local permanentemente.
- [x] Indicador visual `💾 BD LOCAL` en pantalla para evidenciar en la demo el origen de los datos.

---

## 2. Respuestas a las Preguntas del Entregable

### ¿Qué aceleró la librería y qué aceleró Laravel? (4–6 líneas)
Laravel aceleró de forma drástica el desarrollo al proveer un sistema de enrutamiento expresivo, un cliente HTTP integrado con soporte de tiempos de espera y respuestas JSON directas, y el motor de plantillas Blade que redujo la duplicación de código mediante layouts. Por su parte, la librería Laravel Debugbar aceleró la detección de tiempos de respuesta y peticiones HTTP en tiempo real directamente en el navegador, mientras que Laravel Pint automatizó por completo la estandarización y limpieza del estilo de código bajo estándares PSR-12 en un solo comando de consola.

### ¿Qué aprendimos? (5 bullets)
- **Rutas:** Aprendimos a estructurar rutas limpias y RESTful en `routes/web.php`, aprovechando parámetros dinámicos `{name}` y nombres de ruta (`->name()`) para generar enlaces desacoplados de URLs fijas.
- **Controlador:** Comprendimos cómo centralizar la lógica de negocio y consumo de APIs en `PokemonController`, coordinando la validación de peticiones entrantes, la extracción de datos de PokéAPI y el paso de variables a las vistas mediante `compact()`.
- **Vistas:** Experimentamos la potencia de Blade para renderizar datos condicionalmente (`@if`, `@foreach`), gestionar formularios con persistencia de valores y mostrar alertas de validación de forma dinámica.
- **Layout:** Asimilamos la importancia de la herencia de plantillas (`@extends` y `@yield`), permitiendo que el navbar, el footer y los recursos externos se definan una sola vez en `layouts/app.blade.php` para todo el sitio.
- **Bootstrap:** Aprendimos a implementar rápidamente un diseño responsivo y consistente mediante el sistema de cuadrícula (grid), tarjetas (cards), barras de progreso para estadísticas y componentes interactivos vía CDN sin requerir compiladores locales pesados.

---

## 3. Repartición para la Demo en Clase

### Integrante 1:
- Explicar la arquitectura general del proyecto, el archivo de rutas (`routes/web.php`) y la configuración del layout base (`layouts/app.blade.php`).
- Demostrar el funcionamiento de la pantalla de bienvenida (`/`), la navegación hacia la sección Acerca de (`/about`) y la ejecución de la herramienta de formateo de código (Laravel Pint).

### Integrante 2:
- Explicar el funcionamiento del controlador (`PokemonController.php`) y el consumo del cliente HTTP para conectarse a la PokéAPI.
- Demostrar el listado con datos reales y sprites (`/pokemon`), la validación del buscador (tanto búsqueda vacía como resultado exitoso), el detalle completo con estadísticas (`/pokemon/{name}`) y el manejo de errores ante nombres inexistentes.

---

## 4. Registro de Capturas Generadas

Todas las capturas requeridas para el reporte se encuentran guardadas en la carpeta `capturas/`:
1. `capturas/01_home.png` - Pantalla de inicio (Home) con descripción y enlaces de navegación.
2. `capturas/02_listado.png` - Catálogo de Pokémon con datos reales y sprites obtenidos de PokéAPI.
3. `capturas/03_detalle.png` - Ficha de detalle de un Pokémon con tipos, dimensiones y estadísticas base.
4. `capturas/04_buscador_resultado.png` - Buscador mostrando un resultado real de Pokémon.
5. `capturas/05_error_busqueda_vacia.png` - Alerta de validación al intentar buscar con el campo vacío.
6. `capturas/06_error_no_encontrado.png` - Pantalla amigable de error 404 al consultar un Pokémon inexistente.
7. `capturas/07_pint.png` - Ejecución y verificación del formateador Laravel Pint en la terminal.
8. `capturas/07_debugbar.png` - Visualización de la barra Laravel Debugbar.
9. `capturas/08_about.png` - Vista de información del proyecto y del equipo (`/about`).
