# Pokédex Web (Laravel)

Proyecto de Pokédex Web desarrollado con Laravel y Bootstrap 5 para la materia **Herramientas para acelerar la construcción de software (Unidad III)**.

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
4. Iniciar el servidor local:
   ```bash
   php artisan serve
   ```
5. Acceder a la aplicación en el navegador web:
   ```
   http://127.0.0.1:8000
   ```

## Estructura de Rutas (Avance 1)
- `GET /` : Pantalla de inicio (Home)
- `GET /pokemon` : Listado de Pokémon de prueba en tarjetas
- `GET /pokemon/{name}` : Detalle del Pokémon seleccionado con imagen placeholder
