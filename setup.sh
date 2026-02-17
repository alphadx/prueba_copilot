#!/bin/bash

set -e

echo "=========================================="
echo "SGDII - Módulo Tesis - Setup Script"
echo "=========================================="
echo ""

cd sgdii-tesis

# Check if this is first run
if [ ! -d "vendor" ]; then
    echo "[1/5] Primera ejecución detectada. Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo "✓ Dependencias instaladas"
else
    echo "[1/5] Dependencias ya instaladas. Omitiendo composer install..."
fi

# Create runtime directories
echo "[2/5] Creando directorios runtime..."
mkdir -p runtime/db
mkdir -p runtime/uploads
mkdir -p runtime/cache
mkdir -p runtime/logs
chmod -R 777 runtime
echo "✓ Directorios creados"

# Create web assets directory
echo "[3/5] Creando directorio de assets..."
mkdir -p web/assets
chmod -R 777 web/assets
echo "✓ Directorio de assets creado"

# Run migrations
echo "[4/5] Ejecutando migraciones..."
if [ ! -f "runtime/db/sgdii.db" ]; then
    echo "Base de datos no existe. Creando y ejecutando migraciones..."
    php yii migrate --interactive=0
    echo "✓ Migraciones ejecutadas"
else
    echo "Base de datos ya existe. Verificando migraciones pendientes..."
    php yii migrate --interactive=0 || echo "No hay migraciones pendientes"
fi

echo "[5/5] Setup completado"
echo ""
echo "=========================================="
echo "¡SGDII - Módulo Tesis está listo!"
echo "=========================================="
echo ""
echo "URL de acceso: http://localhost:8080"
echo ""
echo "Credenciales de prueba:"
echo "----------------------------------------"
echo "ADMIN:"
echo "  Usuario: admin"
echo "  Password: admin123"
echo ""
echo "PROFESORES:"
echo "  Usuario: prof.martinez | Password: prof123"
echo "  Usuario: prof.gonzalez | Password: prof123"
echo ""
echo "ALUMNOS:"
echo "  Usuario: alumno.perez | Password: alumno123"
echo "  Usuario: alumno.rojas | Password: alumno123"
echo "----------------------------------------"
echo ""
echo "Iniciando servidor PHP en puerto 8080..."
echo ""
