# SPRINT 1: Modelos de Datos, Migraciones y Configuración

## Fecha: 2026-02-17

## Objetivo
Crear toda la estructura de base de datos del módulo de Tesis del SGDII, incluyendo migraciones, modelos ActiveRecord con relaciones, datos de prueba (seed), archivos de configuración .env y esta documentación.

## Restricciones de Negocio Abordadas

### Del Documento de Procedimientos Operacionales:
1. **Restricción 1**: Todos los involucrados deben estar registrados en SGDII → Tablas `alumno`, `profesor`, `empresa` con FK a `user`
2. **Restricción 3**: Un alumno puede realizar una sola tesis vigente → Validación en modelo `SolicitudTemaTesis`
3. **Restricción 4**: Dos alumnos de carreras distintas pueden desarrollar una sola tesis → Tabla `stt_alumno` (N:M)
4. **Restricción 5**: El alumno debe tener solo una tesis vigente → Validación bloqueante
5. **Restricción 6**: Solicitudes no-curso deben ser validadas por profesor → Campo `origen_id`
6. **Restricción 7**: Desarrollo por etapas → Campos `total_etapas` y `etapa_actual` en tabla `tesis`

### Roles del Sistema:
- **Profesor**: Rol único. Los sufijos son contextuales:
  - Crea STT → "Profesor de Curso" (para esa STT)
  - Asignado como guía → "Profesor Guía" (para esa TT)
  - Asignado como revisor → "Profesor Revisor" (para esa TT)
- **Comisión Evaluadora**: Flag `es_comision_evaluadora` en tabla `profesor`
- **Alumno**: Solo consulta (no crea ni modifica). Ve su STT/TT y historial.
- **Admin**: Acceso completo

### Permisos:
| Rol | Puede hacer | No puede hacer |
|-----|------------|----------------|
| Profesor (UA) | Crear STT, proponer guía/revisores, ver sus STT | Evaluar/resolver STT |
| Comisión Evaluadora | Revisar STT, resolver, asignar profesores, definir etapas | Crear STT |
| Alumno | Ver su STT/TT y historial (solo lectura) | Crear ni modificar nada |
| Admin | Todo | — |

## Máquina de Estados

```
FASE 1: SOLICITUD (STT)
========================
Solicitada → Recibida → [Comisión Evalúa]
                           ├── Rechazada (fin del proceso)
                           ├── Aceptada → se convierte en TT (estado STT: "Convertida a TT")
                           └── Aceptada con Observaciones → se convierte en TT

FASE 2: DESARROLLO DE TESIS (TT)
==================================
(La comisión define total_etapas al aceptar la STT)

Para cada etapa i (de 1 a total_etapas):
┌─────────────────────────────────────────────────┐
│  En desarrollo (etapa i)                        │
│         ↓                                       │
│  En revisión (etapa i)                          │
│         ├── Rechazada → En desarrollo (etapa i) │◄── LOOP (misma etapa)
│         └── Aceptada → En desarrollo (etapa i+1)│──► AVANZA a siguiente etapa
└─────────────────────────────────────────────────┘

  * La revisión ACEPTADA es lo que gatilla el paso a la siguiente etapa
  * La revisión RECHAZADA devuelve a desarrollo de la MISMA etapa
  * NUNCA se retrocede a una etapa anterior
  * No se "desavanza" de etapa

         ↓ (revisión de última etapa ACEPTADA)

FASE 3: CIERRE
===============
En biblioteca → Antecedentes Personales → Presentación → Finalizada
```

### Cálculo de Porcentaje:
- Fórmula: `porcentaje = (etapa_actual / total_etapas) * 100`
- Ejemplo con 3 etapas:
  - Etapa 1: 33%
  - Etapa 2: 67%
  - Etapa 3: 100%

### Mapeo Etapa ↔ Porcentaje (para reportes):
- **Etapa 1** (desarrollo + revisión) = rango 0% a `(1/total)*100`%
- **Etapa 2** (desarrollo + revisión) = rango anterior a `(2/total)*100`%
- Para el reporte de Carga Académica:
  - "Etapa 1" agrupa estados donde etapa_actual ≤ total_etapas/2
  - "Etapa 2" agrupa estados donde etapa_actual > total_etapas/2

## Modelo Entidad-Relación

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    user      │     │   profesor   │     │   alumno     │
├──────────────┤     ├──────────────┤     ├──────────────┤
│ id (PK)      │◄───┤ user_id (FK) │     │ id (PK)      │
│ username     │     │ id (PK)      │     │ rut          │
│ password_hash│     │ rut          │     │ nombre       │
│ nombre       │     │ nombre       │     │ correo       │
│ rut          │     │ correo       │     │ carrera_id   │──►┌──────────────┐
│ correo       │     │ es_comision  │     │ user_id (FK) │──►│carrera_malla │
│ rol          │     │ activo       │     │ activo       │   ├──────────────┤
│ activo       │     └──────┬───────┘     └──────┬───────┘   │ id (PK)      │
│ auth_key     │            │                    │           │ codigo       │
└──────────────┘            │                    │           │ nombre       │
                            │                    │           │ grado        │
                            ▼                    ▼           └──────────────┘
                 ┌──────────────────────┐  ┌──────────┐
                 │solicitud_tema_tesis  │  │stt_alumno│
                 ├──────────────────────┤  ├──────────┤
                 │ id (PK)              │◄─┤ stt_id   │
                 │ origen_id ──────────►│  │ alumno_id│──►alumno
                 │ profesor_curso_id ──►│  │ carrera_id│──►carrera_malla
                 │ nota                 │  └──────────┘
                 │ modalidad_id ───────►│
                 │ prof_guia_prop_id   │  ┌──────────────┐
                 │ prof_rev1_prop_id   │  │   empresa    │
                 │ prof_rev2_prop_id   │  ├──────────────┤
                 │ empresa_id ─────────┤──►│ id (PK)      │
                 │ titulo              │  │ rut          │
                 │ documento_path      │  │ nombre       │
                 │ estado              │  │ supervisor_* │
                 └──────────┬──────────┘  └──────────────┘
                            │
                            ▼ (1:1, when accepted)
                 ┌──────────────────────┐
                 │      tesis           │
                 ├──────────────────────┤
                 │ id (PK)              │
                 │ stt_id (FK, UNIQUE) ─┤──►STT
                 │ categoria_id ───────►│──►categoria──►subcategoria
                 │ subcategoria_id ────►│
                 │ profesor_guia_id ───►│──►profesor
                 │ profesor_rev1_id ───►│──►profesor
                 │ profesor_rev2_id ───►│──►profesor
                 │ total_etapas         │
                 │ etapa_actual         │
                 │ estado               │
                 │ resolucion_motivo    │
                 └──────────────────────┘

                 ┌──────────────────────┐
                 │  historial_estado    │
                 ├──────────────────────┤
                 │ id (PK)              │
                 │ stt_id (FK, null)   │──►STT
                 │ tesis_id (FK, null) │──►tesis
                 │ estado_anterior      │
                 │ estado_nuevo         │
                 │ etapa                │
                 │ motivo               │
                 │ usuario_id ─────────►│──►user
                 │ fecha                │
                 └──────────────────────┘

┌──────────┐     ┌────────────┐
│ origen   │     │ modalidad  │
├──────────┤     ├────────────┤
│ id (PK)  │     │ id (PK)    │
│ nombre   │     │ nombre     │
└──────────┘     └────────────┘

┌──────────┐     ┌──────────────┐
│categoria │     │ subcategoria │
├──────────┤     ├──────────────┤
│ id (PK)  │◄───┤ categoria_id │
│ nombre   │     │ id (PK)      │
└──────────┘     │ nombre       │
                 └──────────────┘

┌──────────────────┐
│ resolucion_stt   │
├──────────────────┤
│ id (PK)          │
│ stt_id ─────────►│──►STT
│ tipo             │
│ motivo           │
│ usuario_id ─────►│──►user
│ fecha_resolucion │
└──────────────────┘
```

## Tablas Creadas en este Sprint

| # | Tabla | Registros Seed | Descripción |
|---|-------|---------------|-------------|
| 1 | carrera_malla | 3 | Carreras y mallas curriculares |
| 2 | alumno | 4 | Estudiantes (vinculados a users) |
| 3 | profesor | 5 | Profesores (vinculados a users) |
| 4 | empresa | 2 | Empresas para modalidad Pasantía |
| 5 | origen | 2 | Maestro de orígenes (Curso, Otro) |
| 6 | modalidad | 3 | Maestro de modalidades (TT, Papers, Pasantía) |
| 7 | categoria | 3 | Categorías de tesis |
| 8 | subcategoria | 9 | Subcategorías de tesis |
| 9 | solicitud_tema_tesis | 0 | Solicitudes de tema |
| 10 | stt_alumno | 0 | Relación N:M STT-Alumno |
| 11 | tesis | 0 | Trabajos de título |
| 12 | resolucion_stt | 0 | Resoluciones de la comisión |
| 13 | historial_estado | 0 | Historial de cambios de estado |

## Modelos ActiveRecord Creados

| Modelo | Relaciones Principales |
|--------|----------------------|
| CarreraMalla | → Alumno[] |
| Alumno | → CarreraMalla, → User, → SttAlumno[] |
| Profesor | → User, → Tesis[] (guía), → Tesis[] (revisor), → STT[] (curso) |
| Empresa | (independiente) |
| Origen | → STT[] |
| Modalidad | → STT[] |
| Categoria | → Subcategoria[] |
| Subcategoria | → Categoria |
| SolicitudTemaTesis | → Origen, → Modalidad, → Profesor (curso/guía/revisores), → Empresa, → SttAlumno[] |
| SttAlumno | → STT, → Alumno, → CarreraMalla |
| Tesis | → STT, → Categoria, → Subcategoria, → Profesor (guía/revisores) |
| ResolucionStt | → STT, → User |
| HistorialEstado | → STT, → Tesis, → User |

## Archivos .env

### `.env` (Docker)
Variables compartidas entre contenedores: nombre del proyecto, puertos, versión PHP.

### `sgdii-tesis/.env` (Aplicación)
Variables de la aplicación: nombre, idioma, keys de seguridad, configuración de uploads.

## Problemas Encontrados y Soluciones

### 1. SQLite y Migraciones de Yii2
**Problema**: SQLite no soporta ALTER TABLE para agregar foreign keys después de la creación de la tabla.

**Solución**: Usar el método `addForeignKey()` de Yii2 que maneja las diferencias entre motores de bases de datos. Yii2 ejecuta el SQL apropiado para cada motor.

### 2. Carga de Variables de Entorno
**Problema**: Yii2 no incluye un loader de .env por defecto.

**Solución**: Crear un helper simple en `config/env.php` que use `parse_ini_file()` y funciones nativas de PHP para cargar las variables. Se incluye en `config/web.php` y `config/console.php`.

### 3. Timestamps en Migraciones
**Problema**: Las migraciones necesitan ordenarse después de las existentes del Sprint 0.

**Solución**: Usar timestamps `m260217_010001_` a `m260217_010013_` para las tablas y `m260217_020001_` para el seed, asegurando orden correcto.

## Próximos Pasos (Sprint 2)
- Implementar formulario de creación de STT (3 modalidades: TT, Papers, Pasantía)
- Validación de alumno sin tesis vigente
- Lógica para 1 o 2 alumnos en la misma STT
- Upload de documentos
- Flash messages (simulando notificaciones por email)
