-- Workaround script for creating database tables manually
-- This script is needed because Sprint 1 migrations use addForeignKey() which is not supported by SQLite

-- Enable foreign keys in SQLite
PRAGMA foreign_keys = ON;

-- profesor table (includes foreign key in CREATE TABLE)
CREATE TABLE IF NOT EXISTS `profesor` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`rut` VARCHAR(12) NOT NULL UNIQUE,
	`nombre` VARCHAR(255) NOT NULL,
	`correo` VARCHAR(255),
	`telefono` VARCHAR(20),
	`especialidad` VARCHAR(255),
	`es_comision_evaluadora` INTEGER DEFAULT 0,
	`user_id` INTEGER,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-profesor-rut` ON `profesor` (`rut`);
CREATE INDEX IF NOT EXISTS `idx-profesor-user_id` ON `profesor` (`user_id`);

-- alumno table
CREATE TABLE IF NOT EXISTS `alumno` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`rut` VARCHAR(12) NOT NULL UNIQUE,
	`nombre` VARCHAR(255) NOT NULL,
	`correo` VARCHAR(255),
	`telefono` VARCHAR(20),
	`carrera_malla_id` INTEGER NOT NULL,
	`tipo_ingreso` VARCHAR(50),
	`anio_ingreso` INTEGER,
	`user_id` INTEGER,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	FOREIGN KEY (`carrera_malla_id`) REFERENCES `carrera_malla` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-alumno-rut` ON `alumno` (`rut`);
CREATE INDEX IF NOT EXISTS `idx-alumno-carrera_malla_id` ON `alumno` (`carrera_malla_id`);
CREATE INDEX IF NOT EXISTS `idx-alumno-user_id` ON `alumno` (`user_id`);

-- empresa table
CREATE TABLE IF NOT EXISTS `empresa` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`rut` VARCHAR(12) NOT NULL UNIQUE,
	`nombre` VARCHAR(255) NOT NULL,
	`supervisor_rut` VARCHAR(12),
	`supervisor_nombre` VARCHAR(255),
	`supervisor_correo` VARCHAR(255),
	`supervisor_telefono` VARCHAR(20),
	`supervisor_cargo` VARCHAR(255),
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NULL DEFAULT NULL
);
CREATE INDEX IF NOT EXISTS `idx-empresa-rut` ON `empresa` (`rut`);

-- origen table
CREATE TABLE IF NOT EXISTS `origen` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`nombre` VARCHAR(100) NOT NULL UNIQUE,
	`descripcion` TEXT,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- modalidad table
CREATE TABLE IF NOT EXISTS `modalidad` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`nombre` VARCHAR(100) NOT NULL UNIQUE,
	`descripcion` TEXT,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- categoria table
CREATE TABLE IF NOT EXISTS `categoria` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`nombre` VARCHAR(100) NOT NULL UNIQUE,
	`descripcion` TEXT,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- subcategoria table
CREATE TABLE IF NOT EXISTS `subcategoria` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`categoria_id` INTEGER NOT NULL,
	`nombre` VARCHAR(100) NOT NULL,
	`descripcion` TEXT,
	`activo` INTEGER DEFAULT 1,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-subcategoria-categoria_id` ON `subcategoria` (`categoria_id`);

-- solicitud_tema_tesis table (includes correlativo field)
CREATE TABLE IF NOT EXISTS `solicitud_tema_tesis` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`correlativo` VARCHAR(20) UNIQUE,
	`origen_id` INTEGER NOT NULL,
	`profesor_curso_id` INTEGER NOT NULL,
	`nota` DECIMAL(2,1) NOT NULL,
	`modalidad_id` INTEGER NOT NULL,
	`profesor_guia_propuesto_id` INTEGER,
	`profesor_revisor1_propuesto_id` INTEGER,
	`profesor_revisor2_propuesto_id` INTEGER,
	`empresa_id` INTEGER,
	`titulo` VARCHAR(500) NOT NULL,
	`documento_path` VARCHAR(500),
	`estado` VARCHAR(50) NOT NULL DEFAULT 'Solicitada',
	`fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	FOREIGN KEY (`origen_id`) REFERENCES `origen` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_curso_id`) REFERENCES `profesor` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`modalidad_id`) REFERENCES `modalidad` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_guia_propuesto_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_revisor1_propuesto_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_revisor2_propuesto_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-stt-correlativo` ON `solicitud_tema_tesis` (`correlativo`);
CREATE INDEX IF NOT EXISTS `idx-stt-origen_id` ON `solicitud_tema_tesis` (`origen_id`);
CREATE INDEX IF NOT EXISTS `idx-stt-profesor_curso_id` ON `solicitud_tema_tesis` (`profesor_curso_id`);
CREATE INDEX IF NOT EXISTS `idx-stt-modalidad_id` ON `solicitud_tema_tesis` (`modalidad_id`);
CREATE INDEX IF NOT EXISTS `idx-stt-empresa_id` ON `solicitud_tema_tesis` (`empresa_id`);
CREATE INDEX IF NOT EXISTS `idx-stt-estado` ON `solicitud_tema_tesis` (`estado`);

-- stt_alumno table
CREATE TABLE IF NOT EXISTS `stt_alumno` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`stt_id` INTEGER NOT NULL,
	`alumno_id` INTEGER NOT NULL,
	`carrera_malla_id` INTEGER NOT NULL,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (`stt_id`) REFERENCES `solicitud_tema_tesis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`alumno_id`) REFERENCES `alumno` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`carrera_malla_id`) REFERENCES `carrera_malla` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-stt_alumno-stt_id` ON `stt_alumno` (`stt_id`);
CREATE INDEX IF NOT EXISTS `idx-stt_alumno-alumno_id` ON `stt_alumno` (`alumno_id`);
CREATE INDEX IF NOT EXISTS `idx-stt_alumno-carrera_malla_id` ON `stt_alumno` (`carrera_malla_id`);
CREATE UNIQUE INDEX IF NOT EXISTS `uq-stt_alumno-stt_id-alumno_id` ON `stt_alumno` (`stt_id`, `alumno_id`);

-- tesis table
CREATE TABLE IF NOT EXISTS `tesis` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`stt_id` INTEGER NOT NULL UNIQUE,
	`categoria_id` INTEGER,
	`subcategoria_id` INTEGER,
	`profesor_guia_id` INTEGER,
	`profesor_revisor1_id` INTEGER,
	`profesor_revisor2_id` INTEGER,
	`total_etapas` INTEGER DEFAULT 3,
	`etapa_actual` INTEGER DEFAULT 1,
	`estado` VARCHAR(50) NOT NULL DEFAULT 'En desarrollo',
	`resolucion_motivo` TEXT,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	FOREIGN KEY (`stt_id`) REFERENCES `solicitud_tema_tesis` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_guia_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_revisor1_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (`profesor_revisor2_id`) REFERENCES `profesor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-tesis-stt_id` ON `tesis` (`stt_id`);
CREATE INDEX IF NOT EXISTS `idx-tesis-categoria_id` ON `tesis` (`categoria_id`);
CREATE INDEX IF NOT EXISTS `idx-tesis-subcategoria_id` ON `tesis` (`subcategoria_id`);
CREATE INDEX IF NOT EXISTS `idx-tesis-profesor_guia_id` ON `tesis` (`profesor_guia_id`);
CREATE INDEX IF NOT EXISTS `idx-tesis-estado` ON `tesis` (`estado`);
CREATE INDEX IF NOT EXISTS `idx-tesis-etapa_actual` ON `tesis` (`etapa_actual`);

-- resolucion_stt table
CREATE TABLE IF NOT EXISTS `resolucion_stt` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`stt_id` INTEGER NOT NULL,
	`tipo` VARCHAR(50) NOT NULL,
	`motivo` TEXT,
	`usuario_id` INTEGER NOT NULL,
	`fecha_resolucion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (`stt_id`) REFERENCES `solicitud_tema_tesis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-resolucion_stt-stt_id` ON `resolucion_stt` (`stt_id`);
CREATE INDEX IF NOT EXISTS `idx-resolucion_stt-usuario_id` ON `resolucion_stt` (`usuario_id`);
CREATE INDEX IF NOT EXISTS `idx-resolucion_stt-tipo` ON `resolucion_stt` (`tipo`);

-- historial_estado table
CREATE TABLE IF NOT EXISTS `historial_estado` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`stt_id` INTEGER,
	`tesis_id` INTEGER,
	`estado_anterior` VARCHAR(50),
	`estado_nuevo` VARCHAR(50) NOT NULL,
	`etapa` INTEGER,
	`motivo` TEXT,
	`usuario_id` INTEGER NOT NULL,
	`fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (`stt_id`) REFERENCES `solicitud_tema_tesis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`tesis_id`) REFERENCES `tesis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`usuario_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx-historial_estado-stt_id` ON `historial_estado` (`stt_id`);
CREATE INDEX IF NOT EXISTS `idx-historial_estado-tesis_id` ON `historial_estado` (`tesis_id`);
CREATE INDEX IF NOT EXISTS `idx-historial_estado-usuario_id` ON `historial_estado` (`usuario_id`);

-- Update migration table to mark these as applied
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010002_create_profesor_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010003_create_alumno_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010004_create_empresa_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010005_create_origen_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010006_create_modalidad_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010007_create_categoria_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010008_create_subcategoria_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010009_create_solicitud_tema_tesis_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010010_create_stt_alumno_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010011_create_tesis_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010012_create_resolucion_stt_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_010013_create_historial_estado_table', strftime('%s','now'));
INSERT OR IGNORE INTO `migration` VALUES ('m260217_030001_add_correlativo_to_solicitud_tema_tesis', strftime('%s','now'));

-- Seed data (from m260217_020001_seed_data.php)

-- Carreras/Mallas (already exists, skip)

-- Orígenes
INSERT OR IGNORE INTO `origen` (id, nombre, descripcion) VALUES 
(1, 'Curso', 'Solicitud originada en curso de Proyecto de Ingeniería/Título'),
(2, 'Otro', 'Solicitud de origen externo al curso');

-- Modalidades
INSERT OR IGNORE INTO `modalidad` (id, nombre, descripcion) VALUES 
(1, 'TT', 'Trabajo de Título'),
(2, 'Papers', 'Publicación académica'),
(3, 'Pasantía', 'Práctica en empresa');

-- Categorías
INSERT OR IGNORE INTO `categoria` (id, nombre, descripcion) VALUES 
(1, 'Deporte', 'Proyectos relacionados con actividades deportivas y gestión deportiva'),
(2, 'Gestión de Operaciones', 'Optimización de procesos, logística y planificación'),
(3, 'Educacional', 'Proyectos relacionados con educación y metodologías de enseñanza');

-- Subcategorías
INSERT OR IGNORE INTO `subcategoria` (categoria_id, nombre, descripcion) VALUES 
(1, 'Futbolito', 'Proyectos relacionados con fútbol amateur'),
(1, 'Gestión deportiva', 'Administración y gestión de instalaciones deportivas'),
(1, 'Análisis de rendimiento', 'Análisis estadístico y optimización de rendimiento deportivo'),
(2, 'Optimización de horarios', 'Optimización de horarios y calendarios'),
(2, 'Logística', 'Gestión de cadena de suministro y distribución'),
(2, 'Planificación de producción', 'Planificación y control de producción'),
(3, 'Detección de problemas tempranos en infantes', 'Herramientas para detectar dificultades de aprendizaje'),
(3, 'Metodologías de enseñanza', 'Nuevas metodologías y técnicas pedagógicas'),
(3, 'Evaluación académica', 'Sistemas de evaluación y seguimiento académico');

-- Profesores (linked to existing users from migration m260217_000002_seed_users)
INSERT OR IGNORE INTO `profesor` (rut, nombre, correo, user_id, es_comision_evaluadora) VALUES 
('12345678-9', 'Juan Martínez López', 'jmartinez@usach.cl', (SELECT id FROM user WHERE username='prof.martinez'), 0),
('13456789-0', 'María González Soto', 'mgonzalez@usach.cl', (SELECT id FROM user WHERE username='prof.gonzalez'), 0),
('14567890-1', 'Pedro Rodríguez Muñoz', 'prodriguez@usach.cl', (SELECT id FROM user WHERE username='prof.rodriguez'), 0),
('15678901-2', 'Ana Silva Vargas', 'asilva@usach.cl', (SELECT id FROM user WHERE username='prof.silva'), 1),
('16789012-3', 'Luis Morales Díaz', 'lmorales@usach.cl', (SELECT id FROM user WHERE username='prof.morales'), 1);

-- Alumnos
INSERT OR IGNORE INTO `alumno` (rut, nombre, correo, carrera_malla_id, user_id, tipo_ingreso, anio_ingreso) VALUES 
('20123456-7', 'Carlos Pérez Torres', 'cperez@usach.cl', (SELECT id FROM carrera_malla WHERE codigo='ICI-1472'), (SELECT id FROM user WHERE username='alumno.perez'), 'PAES', 2021),
('20234567-8', 'José Rojas Fuentes', 'jrojas@usach.cl', (SELECT id FROM carrera_malla WHERE codigo='IEI-1473'), (SELECT id FROM user WHERE username='alumno.rojas'), 'PAES', 2021),
('20345678-9', 'Valentina Díaz Ramos', 'vdiaz@usach.cl', (SELECT id FROM carrera_malla WHERE codigo='ICI-1472'), (SELECT id FROM user WHERE username='alumno.diaz'), 'PAES', 2022),
('20456789-0', 'Francisca López Vera', 'flopez@usach.cl', (SELECT id FROM carrera_malla WHERE codigo='MII-2001'), (SELECT id FROM user WHERE username='alumno.lopez'), 'prosecucion', 2023);

-- Empresas
INSERT OR IGNORE INTO `empresa` (rut, nombre, supervisor_rut, supervisor_nombre, supervisor_correo, supervisor_telefono, supervisor_cargo) VALUES 
('76123456-7', 'TechCorp SpA', '17123456-4', 'Roberto Méndez', 'rmendez@techcorp.cl', '+56912345678', 'Gerente de Operaciones'),
('76234567-8', 'IndustriaSur Ltda', '17234567-5', 'Carmen Flores', 'cflores@industriasur.cl', '+56923456789', 'Jefa de Proyectos');

-- Mark seed migration as applied
INSERT OR IGNORE INTO `migration` VALUES ('m260217_020001_seed_data', strftime('%s','now'));
