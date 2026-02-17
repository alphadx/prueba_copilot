# Copilot Testing Space

This repository serves as a dedicated space for testing Copilot functionalities, performing file analysis, and leveraging atomic tasks. It is designed for prototyping tools and applications that can interact with each other seamlessly across multiple languages and programs.

## Key Features:
- **Copilot Testing**: Experiment with various Copilot capabilities to enhance development productivity.
- **File Analysis**: Analyze different file types and structures for better coding practices.
- **Atomic Tasks**: Break down tasks into manageable parts for efficient coding and testing.
- **Prototyping Tools**: Utilize tools that allow for rapid prototyping and integration.

---

## Proyecto: SGDII - Módulo de Tesis

### Descripción
Sistema de Gestión del Departamento de Ingeniería Industrial (SGDII) de la USACH - Módulo de Gestión de Tesis y Trabajos de Título.

Este es un prototipo de desarrollo local que permite gestionar el proceso completo de:
- Solicitud de inscripción de tema de tesis (STT)
- Evaluación y resolución de STT
- Reportes y seguimiento

### Requisitos Previos
- Docker
- Docker Compose

### Instrucciones de Uso

```bash
cd sgdii-tesis
docker-compose up
```

Una vez que el contenedor esté en ejecución:
1. Abrir navegador en: **http://localhost:8080**
2. Usar credenciales:
   - **Usuario:** `admin`
   - **Contraseña:** `admin123`

### Estructura del Proyecto

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

### Stack Tecnológico
- **PHP:** 8.4-cli
- **Framework:** Yii2
- **Base de Datos:** SQLite3
- **UI:** Bootstrap 5 (incluido con Yii2)
- **Servidor:** PHP built-in server

### Plan de Sprints

#### Sprint 0: Setup Inicial ✅
- Entorno Docker completo
- Autenticación básica
- Página de bienvenida

#### Sprint 1: Solicitud de Tema de Tesis (Próximamente)
- Formulario de solicitud STT
- Validaciones
- Almacenamiento en BD

#### Sprint 2: Evaluación y Resolución (Próximamente)
- Panel de revisión
- Sistema de observaciones
- Aprobación/Rechazo

#### Sprint 3: Reportes y Seguimiento (Próximamente)
- Dashboard con estadísticas
- Generación de reportes
- Exportación de datos

---

This README will evolve as more functionalities are added and tested.