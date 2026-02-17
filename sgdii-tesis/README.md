# SGDII - Módulo de Tesis

Sistema de Gestión del Departamento de Ingeniería Industrial (SGDII) de la USACH - Módulo de Gestión de Tesis y Trabajos de Título.

## Descripción

Este es un prototipo de desarrollo local que permite gestionar el proceso completo de:
- Solicitud de inscripción de tema de tesis (STT)
- Evaluación y resolución de STT
- Reportes y seguimiento

## Requisitos Previos

- Docker
- Docker Compose

## Inicio Rápido

1. **Iniciar el contenedor:**
   ```bash
   docker-compose up
   ```

2. **Acceder a la aplicación:**
   - URL: http://localhost:8080
   - Usuario: `admin`
   - Contraseña: `admin123`

3. **Detener el contenedor:**
   ```bash
   docker-compose down
   ```

## Estructura del Proyecto

```
sgdii-tesis/
├── docker-compose.yml          # Configuración Docker
├── Dockerfile                  # Imagen PHP 8.4
├── setup.sh                    # Script de inicialización
├── composer.json               # Dependencias PHP/Yii2
├── yii                         # CLI de Yii2
├── config/                     # Configuración
│   ├── db.php                  # SQLite config
│   ├── web.php                 # Yii2 web config
│   └── params.php
├── controllers/                # Controladores
│   └── SiteController.php
├── models/                     # Modelos
│   ├── User.php
│   └── LoginForm.php
├── commands/                   # Comandos de consola
│   └── ShellController.php
├── migrations/                 # Migraciones de BD
│   └── m260217_000001_create_user_table.php
├── views/                      # Vistas
│   ├── site/
│   │   ├── index.php
│   │   ├── login.php
│   │   └── error.php
│   └── layouts/
│       └── main.php
├── assets/                     # Asset bundles
│   └── AppAsset.php
├── runtime/                    # SQLite DB, logs, cache
├── web/                        # Web root
│   ├── index.php               # Entry point
│   ├── css/
│   └── assets/
```

## Stack Tecnológico

- **PHP:** 8.4-cli
- **Framework:** Yii2 (v2.0.45)
- **UI:** Bootstrap 5 (incluido con yii2-bootstrap5)
- **Base de Datos:** SQLite3
- **Servidor:** PHP built-in server

## Funcionalidades Implementadas

### Sprint 0: Setup Inicial ✅

- [x] Entorno Docker completo con PHP 8.4
- [x] Configuración de Yii2 con SQLite3
- [x] Sistema de autenticación básica
- [x] Modelo de usuario con IdentityInterface
- [x] Página de login con validación
- [x] Página de bienvenida
- [x] Layout principal con Bootstrap 5
- [x] Migraciones automáticas
- [x] Seed de usuario administrador

### Próximos Sprints

#### Sprint 1: Solicitud de Tema de Tesis
- Formulario de solicitud STT
- Validaciones de campos
- Almacenamiento en base de datos

#### Sprint 2: Evaluación y Resolución
- Panel de revisión de STT
- Sistema de observaciones
- Aprobación/Rechazo de solicitudes

#### Sprint 3: Reportes y Seguimiento
- Dashboard con estadísticas
- Generación de reportes
- Exportación de datos

## Desarrollo

### Ejecutar Migraciones Manualmente

```bash
docker-compose exec app php yii migrate
```

### Crear un Nuevo Usuario

```bash
docker-compose exec app php yii shell/create-admin
```

### Ver Logs

```bash
docker-compose logs -f
```

### Acceder al Contenedor

```bash
docker-compose exec app bash
```

## Solución de Problemas

### El contenedor no inicia

1. Verificar que el puerto 8080 no esté en uso:
   ```bash
   lsof -i :8080
   ```

2. Reconstruir la imagen:
   ```bash
   docker-compose down
   docker-compose build --no-cache
   docker-compose up
   ```

### Error de base de datos

1. Eliminar el volumen de datos:
   ```bash
   docker-compose down -v
   docker-compose up
   ```

### Error de permisos

1. Verificar que setup.sh sea ejecutable:
   ```bash
   chmod +x setup.sh
   ```

## Licencia

Proyecto interno de la Universidad de Santiago de Chile (USACH) - Departamento de Ingeniería Industrial.

## Contacto

Para consultas sobre el proyecto, contactar al Departamento de Ingeniería Industrial de la USACH.
