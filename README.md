# Pokédex Web (Laravel)

Proyecto de Pokédex Web desarrollado con Laravel, Bootstrap 5 y consumo de PokéAPI para la materia **Herramientas para acelerar la construcción de software (Unidad III)**.

## Requisitos previos
- PHP 8.2 o superior
- Composer

## Instalación y ejecución local

1. Clonar o descargar el repositorio.
2. Instalar las dependencias de Composer:
   ```bash
   composer install
   ```
3. Generar la clave de la aplicación (si no existe el archivo `.env`, copiarlo desde `.env.example`):
   ```bash
   php artisan key:generate
   ```
4. Formatear y verificar el estilo de código con Laravel Pint:
   ```bash
   ./vendor/bin/pint --test
   ```
5. Iniciar el servidor local:
   ```bash
   php artisan serve
   ```
6. Acceder a la aplicación en el navegador web:
   ```
   http://127.0.0.1:8000
   ```

## Estructura de Rutas y Vistas

- `GET /` : Pantalla de inicio con bienvenida, accesos rápidos y características del sistema (`home.blade.php`).
- `GET /pokemon` : Catálogo de Pokémon con datos reales desde PokéAPI, sprites individuales, tarjetas de Bootstrap y buscador integrado con validación (`pokemon/index.blade.php`).
- `GET /pokemon/{name}` : Ficha detallada del Pokémon seleccionado con tipos, artwork oficial, sprite clásico y estadísticas base con barras de progreso (`pokemon/show.blade.php`). Si no existe, muestra pantalla amigable de error (`pokemon/error.blade.php`).
- `GET /about` : Información del equipo de desarrollo, integrantes y objetivos del proyecto (`about.blade.php`).

## Documentación de Entrega

Para consultar las respuestas a las preguntas del entregable (qué aceleró Laravel y la librería, aprendizajes por componente y guía para la demo en clase), revisar el archivo:
- [ENTREGA_U3.md](ENTREGA_U3.md)

Las capturas de pantalla requeridas para el reporte final se encuentran en el directorio `capturas/`.
