#!/bin/bash

set -e

echo "==================================="
echo "SGDII-Tesis - Inicializando..."
echo "==================================="

# Install dependencies if not already installed
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias con Composer..."
    composer install --no-interaction --prefer-dist
else
    echo "Dependencias ya instaladas."
fi

# Create runtime directory if not exists
if [ ! -d "runtime" ]; then
    echo "Creando directorio runtime..."
    mkdir -p runtime
fi

# Make yii executable
chmod +x yii

# Run migrations
echo "Ejecutando migraciones..."
php yii migrate --interactive=0

# Check if admin user exists
ADMIN_EXISTS=$(php yii shell/check-user admin 2>/dev/null || echo "not_found")

if [ "$ADMIN_EXISTS" = "not_found" ]; then
    echo "Creando usuario administrador..."
    php yii shell/create-admin
else
    echo "Usuario administrador ya existe."
fi

echo "==================================="
echo "Iniciando servidor PHP..."
echo "==================================="
echo "Acceso: http://localhost:8080"
echo "Usuario: admin"
echo "Contraseña: admin123"
echo "==================================="

# Start PHP built-in server
php yii serve 0.0.0.0 --port=8080
