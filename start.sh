#!/bin/bash

# Ejecutar las migraciones
php artisan migrate --force

# Iniciar el servidor de Laravel
php artisan serve
