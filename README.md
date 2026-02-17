# SGDII - Sistema de Gestión Departamento Ingeniería Industrial

## Módulo Tesis - Prototipo

Sistema de gestión del ciclo de vida de Tesis y Trabajos de Título para el Departamento de Ingeniería Industrial. Este es un prototipo funcional desarrollado con Yii2, PHP 8.4, SQLite3 y Docker.

## Descripción del Proyecto

El SGDII - Módulo Tesis es una aplicación web diseñada para gestionar:
- **Solicitudes de Inscripción de Tema de Tesis (STT)**: Proceso de inscripción de temas por parte de los alumnos
- **Evaluación y Resolución**: Sistema de evaluación por parte de la Comisión de Titulación
- **Seguimiento de tesis**: Tracking de las diferentes etapas del proceso de tesis
- **Reportes**: Generación de reportes para profesores, alumnos y comisión evaluadora

## Stack Tecnológico

- **PHP 8.4** con extensiones: pdo_sqlite, mbstring, intl, gd, zip, tokenizer, ctype, json, xml
- **Yii2 Framework** (versión 2.0.51) - Yii2 Basic Template
- **SQLite3** como base de datos (persistencia local)
- **Bootstrap 5** para la interfaz de usuario
- **Docker + docker-compose** para contenedorización

## Requisitos Previos

- Docker (versión 20.10 o superior)
- Docker Compose (versión 1.29 o superior)

## Instalación y Uso

### Instalación Rápida

1. Clonar el repositorio:
```bash
git clone https://github.com/alphadx/prueba_copilot.git
cd prueba_copilot
```

2. Levantar el proyecto con Docker:
```bash
docker-compose up --build
```

3. Acceder a la aplicación:
```
http://localhost:8080
```

El script de setup se ejecutará automáticamente y:
- Instalará las dependencias de Composer
- Creará los directorios necesarios
- Ejecutará las migraciones de la base de datos
- Cargará los usuarios de prueba

### Credenciales de Prueba

#### Administrador
| Usuario | Contraseña | Rol | Descripción |
|---------|------------|-----|-------------|
| admin | admin123 | Admin | Administrador del sistema |

#### Profesores
| Usuario | Contraseña | Rol | Nombre |
|---------|------------|-----|--------|
| prof.martinez | prof123 | Profesor | Juan Martínez López |
| prof.gonzalez | prof123 | Profesor | María González Soto |
| prof.rodriguez | prof123 | Profesor | Pedro Rodríguez Muñoz |
| prof.silva | prof123 | Comisión Evaluadora | Ana Silva Vargas |
| prof.morales | prof123 | Comisión Evaluadora | Luis Morales Díaz |

#### Alumnos
| Usuario | Contraseña | Rol | Nombre |
|---------|------------|-----|--------|
| alumno.perez | alumno123 | Alumno | Carlos Pérez Torres |
| alumno.rojas | alumno123 | Alumno | José Rojas Fuentes |
| alumno.diaz | alumno123 | Alumno | Valentina Díaz Ramos |
| alumno.lopez | alumno123 | Alumno | Francisca López Vera |

## Estructura del Proyecto

```
prueba_copilot/
├── docker-compose.yml          # Configuración de Docker Compose
├── Dockerfile                  # Imagen Docker PHP 8.4
├── setup.sh                    # Script de inicialización
├── data/                       # Base de datos SQLite (persistente)
├── uploads/                    # Archivos subidos (futuro)
└── sgdii-tesis/               # Aplicación Yii2
    ├── config/                # Configuraciones
    │   ├── db.php            # Configuración SQLite
    │   ├── web.php           # Configuración web
    │   ├── console.php       # Configuración consola
    │   └── params.php        # Parámetros
    ├── controllers/           # Controladores
    │   └── SiteController.php
    ├── models/                # Modelos
    │   ├── User.php          # Modelo de usuario
    │   └── LoginForm.php     # Formulario de login
    ├── migrations/            # Migraciones de BD
    │   ├── m260217_000001_create_user_table.php
    │   └── m260217_000002_seed_users.php
    ├── views/                 # Vistas
    │   ├── layouts/
    │   │   └── main.php      # Layout principal
    │   └── site/
    │       ├── index.php     # Dashboard
    │       ├── login.php     # Login
    │       └── error.php     # Página de error
    ├── runtime/               # Archivos temporales y BD
    ├── web/                   # Public web root
    │   ├── index.php         # Entry point
    │   ├── css/
    │   │   └── site.css      # Estilos personalizados
    │   └── assets/           # Assets de Yii2
    ├── composer.json          # Dependencias PHP
    └── yii                    # Consola Yii2
```

## Características Implementadas

### Sprint 1 (Actual)
- ✅ Setup completo de Docker + Docker Compose
- ✅ Configuración de Yii2 con PHP 8.4 y SQLite3
- ✅ Sistema de autenticación con base de datos
- ✅ Modelo de usuarios con roles (admin, profesor, alumno, comisión_evaluadora)
- ✅ Login funcional con validación
- ✅ Dashboard básico con información del usuario
- ✅ Layout con navbar y información de usuario
- ✅ 10 usuarios de prueba precargados

### Próximos Sprints

#### Sprint 2 - Gestión de Solicitudes de Tesis (STT)
- [ ] Formulario de Solicitud de Inscripción de Tema de Tesis
- [ ] Validación de datos de alumno, carrera y modalidad
- [ ] Asignación de profesor guía y revisores
- [ ] Gestión de datos de empresa (para pasantías)
- [ ] Upload de documentos de respaldo

#### Sprint 3 - Evaluación y Resolución
- [ ] Vista de resolución de STT para Comisión
- [ ] Sistema de categorización (aprobada, rechazada, requiere modificaciones)
- [ ] Confirmación de profesores guía y revisores
- [ ] Historial de estados y resoluciones

#### Sprint 4 - Reportes
- [ ] Reporte de carga académica por profesor
- [ ] Reporte de tesis vigentes por etapa
- [ ] Reporte de seguimiento para memoristas
- [ ] Exportación de reportes (PDF/Excel)

## Comandos Útiles

### Detener el contenedor
```bash
docker-compose down
```

### Ver logs del contenedor
```bash
docker-compose logs -f
```

### Reconstruir el contenedor
```bash
docker-compose up --build
```

### Ejecutar migraciones manualmente
```bash
docker-compose exec sgdii php sgdii-tesis/yii migrate
```

### Acceder a la consola del contenedor
```bash
docker-compose exec sgdii bash
```

## Notas de Desarrollo

- El servidor PHP built-in se usa solo para desarrollo/prototipo
- La base de datos SQLite se persiste en el directorio `./data/`
- Los assets de Yii2 se generan automáticamente en `web/assets/`
- El sistema usa bcrypt para hash de contraseñas
- Las migraciones son idempotentes (se pueden ejecutar múltiples veces)

## Soporte y Contacto

Para reportar problemas o solicitar funcionalidades, por favor crear un issue en el repositorio.

## Licencia

Proyecto propietario - Uso interno del Departamento de Ingeniería Industrial.