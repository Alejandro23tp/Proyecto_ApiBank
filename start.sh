#!/bin/bash

# Obtener el puerto asignado por Render o usar 8000 si no está definido
PORT=${PORT:-8000}

# Ejecutar las migraciones
php artisan migrate --force

# Iniciar el servidor de Laravel en el puerto asignado por Render
php artisan serve --host=0.0.0.0 --port=$PORT
