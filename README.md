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

### Sprint 1 (Completado)
- ✅ Setup completo de Docker + Docker Compose
- ✅ Configuración de Yii2 con PHP 8.4 y SQLite3
- ✅ Sistema de autenticación con base de datos
- ✅ Modelo de usuarios con roles (admin, profesor, alumno, comisión_evaluadora)
- ✅ Login funcional con validación
- ✅ Dashboard básico con información del usuario
- ✅ Layout con navbar y información de usuario
- ✅ 10 usuarios de prueba precargados
- ✅ Modelos de datos y migraciones para todas las tablas del sistema

### Sprint 2 (Actual) - Formulario de Solicitud de Tema de Tesis (STT)
- ✅ Formulario de creación de STT con soporte para 1-2 alumnos
- ✅ Validación dinámica según modalidad (TT, Papers, Pasantía)
- ✅ Campos específicos para cada modalidad:
  - **TT**: Profesores guías y revisores opcionales
  - **Papers**: Profesor guía obligatorio
  - **Pasantía**: Profesor guía obligatorio + información de empresa requerida
- ✅ Generación automática de correlativo único (formato: STT-YYYY-NNNN)
- ✅ Validación de estudiantes con tesis vigente
- ✅ Estado inicial "Enviada" al crear STT
- ✅ Mensajes flash de confirmación
- ✅ Vista detallada de STT creada

#### Acceso al Formulario de STT
Para acceder al formulario de creación de STT:

1. Iniciar sesión con un usuario con rol **profesor** o **admin**:
   - Usuario: `prof.martinez` | Password: `prof123`
   - Usuario: `admin` | Password: `admin123`

2. En el dashboard, hacer clic en el botón **"Crear Solicitud"** en la tarjeta de STT

3. Completar el formulario según la modalidad seleccionada

4. La URL directa es: `http://localhost:8080/index.php?r=stt/create`

**Nota sobre migraciones**: Las migraciones de Sprint 1 tienen un problema conocido con SQLite y `addForeignKey()`. Se proporciona un script SQL de workaround (`sgdii-tesis/workaround_create_tables.sql`) para crear las tablas manualmente si es necesario.

### Sprint 3 (Completado) - Gestión de STT para Comisión de Titulación
- ✅ Vista de gestión de STT con filtros avanzados para Comisión Evaluadora
- ✅ Sistema de resolución de STT (aceptar, rechazar, aceptar con observaciones)
- ✅ Campos adicionales en BD: `motivo_resolucion`, `fecha_resolucion`
- ✅ Estados de STT: Solicitada, En revisión, Aceptada, Aceptada con observaciones, Rechazada
- ✅ Restricción de acceso para rol `comision_evaluadora`
- ✅ Notificaciones automáticas (simuladas con flash messages)
- ✅ Modal para consultar tesis vigentes por profesor
- ✅ Historial de estados y resoluciones
- ✅ Validación de permisos por rol

#### Acceso al Módulo de Comisión Evaluadora
Para acceder a la gestión de STT:

1. Iniciar sesión con un usuario con rol **comision_evaluadora**:
   - Usuario: `prof.silva` | Password: `prof123`
   - Usuario: `prof.morales` | Password: `prof123`
   - Usuario: `admin` | Password: `admin123` (también tiene acceso)

2. En el dashboard, hacer clic en **"Gestionar STT"** en la tarjeta de Resolución de STT

3. La URL directa es: `http://localhost:8080/index.php?r=comision/index`

#### Funcionalidades del Módulo
- **Filtros avanzados**: Por modalidad, estado, fechas, y profesor guía
- **Resolución de STT**: Aceptar, rechazar o aceptar con observaciones
- **Consulta de carga**: Ver tesis vigentes de cada profesor antes de resolver
- **Notificaciones**: Se envían automáticamente a alumnos y profesores involucrados

### Sprint 4 (Completado) - Reportes, Estadísticas y Exportación
- ✅ Reportes según roles (Profesores, Comisión, Estudiantes)
- ✅ Reporte de carga académica por profesor (tesis como guía y revisor)
- ✅ Reporte de tesis vigentes con filtros avanzados para comisión
- ✅ Reporte de seguimiento personal para estudiantes
- ✅ Gráficas y estadísticas interactivas con Chart.js:
  - Distribución de modalidades en estados (gráfica de barras)
  - Categorías principales de tesis (gráfica de torta)
  - Evolución mensual de STT (gráfica de línea)
  - Tesis agrupadas por modalidad y estado (gráfica apilada)
  - Tiempos de resolución por rol (gráfica agrupada)
- ✅ Indicadores clave del sistema:
  - Tasa de aceptación y rechazo
  - Promedio de tiempo de resolución
  - Promedio de revisores por tesis
  - Distribución por categorías y modalidades
- ✅ Exportación de reportes a Excel (.xlsx) con PhpSpreadsheet
- ✅ Exportación de reportes a PDF con kartik-v/yii2-mpdf
- ✅ Filtros avanzados para reportes de comisión (modalidad, estado, fechas, profesor)
- ✅ Control de acceso por roles para cada tipo de reporte
- ✅ Dashboard de reportes unificado

#### Acceso a los Reportes
Para acceder a los reportes:

1. Iniciar sesión con cualquier usuario válido
2. En el dashboard principal, hacer clic en **"Ver Reportes"** en la tarjeta de Reportes y Estadísticas
3. La URL directa es: `http://localhost:8080/index.php?r=report/index`

**Tipos de reportes disponibles:**
- **Reporte de Profesor**: Solo para profesores y admin. Muestra tesis bajo supervisión.
- **Reporte de Comisión**: Solo para comisión evaluadora y admin. Vista completa con filtros.
- **Reporte de Estudiante**: Solo para alumnos y admin. Seguimiento personal de tesis.
- **Estadísticas Generales**: Disponible para todos. Gráficas interactivas con 5 visualizaciones clave.

### Próximos Sprints

#### Sprint 5 - Mejoras futuras
- [ ] Notificaciones por email reales (actualmente simuladas)

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