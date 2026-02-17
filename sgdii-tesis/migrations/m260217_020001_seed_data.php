<?php

use yii\db\Migration;

/**
 * Seeds initial test data for all master tables and entities
 */
class m260217_020001_seed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Seed Carreras/Mallas
        $this->batchInsert('{{%carrera_malla}}', ['codigo', 'nombre', 'grado'], [
            ['ICI-1472', 'Ingeniería Civil Industrial', 'pregrado'],
            ['IEI-1473', 'Ingeniería en Ejecución Industrial', 'pregrado'],
            ['MII-2001', 'Magíster en Ingeniería Industrial', 'magister'],
        ]);

        // Seed Orígenes
        $this->batchInsert('{{%origen}}', ['nombre', 'descripcion'], [
            ['Curso', 'Solicitud originada en curso de Proyecto de Ingeniería/Título'],
            ['Otro', 'Solicitud de origen externo al curso'],
        ]);

        // Seed Modalidades
        $this->batchInsert('{{%modalidad}}', ['nombre', 'descripcion'], [
            ['TT', 'Trabajo de Título'],
            ['Papers', 'Publicación académica'],
            ['Pasantía', 'Práctica en empresa'],
        ]);

        // Seed Categorías
        $this->batchInsert('{{%categoria}}', ['nombre', 'descripcion'], [
            ['Deporte', 'Proyectos relacionados con actividades deportivas y gestión deportiva'],
            ['Gestión de Operaciones', 'Optimización de procesos, logística y planificación'],
            ['Educacional', 'Proyectos relacionados con educación y metodologías de enseñanza'],
        ]);

        // Get categoria IDs
        $deporteId = $this->db->createCommand('SELECT id FROM {{%categoria}} WHERE nombre = :nombre', [':nombre' => 'Deporte'])->queryScalar();
        $gestionId = $this->db->createCommand('SELECT id FROM {{%categoria}} WHERE nombre = :nombre', [':nombre' => 'Gestión de Operaciones'])->queryScalar();
        $educacionalId = $this->db->createCommand('SELECT id FROM {{%categoria}} WHERE nombre = :nombre', [':nombre' => 'Educacional'])->queryScalar();

        // Seed Subcategorías
        $this->batchInsert('{{%subcategoria}}', ['categoria_id', 'nombre', 'descripcion'], [
            // Deporte
            [$deporteId, 'Futbolito', 'Proyectos relacionados con fútbol amateur'],
            [$deporteId, 'Gestión deportiva', 'Administración y gestión de instalaciones deportivas'],
            [$deporteId, 'Análisis de rendimiento', 'Análisis estadístico y optimización de rendimiento deportivo'],
            // Gestión de Operaciones
            [$gestionId, 'Optimización de horarios', 'Optimización de horarios y calendarios'],
            [$gestionId, 'Logística', 'Gestión de cadena de suministro y distribución'],
            [$gestionId, 'Planificación de producción', 'Planificación y control de producción'],
            // Educacional
            [$educacionalId, 'Detección de problemas tempranos en infantes', 'Herramientas para detectar dificultades de aprendizaje'],
            [$educacionalId, 'Metodologías de enseñanza', 'Nuevas metodologías y técnicas pedagógicas'],
            [$educacionalId, 'Evaluación académica', 'Sistemas de evaluación y seguimiento académico'],
        ]);

        // Seed Profesores (linked to existing users)
        // Get user IDs by username
        $userMartinez = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'prof.martinez'])->queryScalar();
        $userGonzalez = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'prof.gonzalez'])->queryScalar();
        $userRodriguez = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'prof.rodriguez'])->queryScalar();
        $userSilva = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'prof.silva'])->queryScalar();
        $userMorales = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'prof.morales'])->queryScalar();

        $this->batchInsert('{{%profesor}}', ['rut', 'nombre', 'correo', 'user_id', 'es_comision_evaluadora'], [
            ['12345678-9', 'Juan Martínez López', 'jmartinez@usach.cl', $userMartinez, 0],
            ['13456789-0', 'María González Soto', 'mgonzalez@usach.cl', $userGonzalez, 0],
            ['14567890-1', 'Pedro Rodríguez Muñoz', 'prodriguez@usach.cl', $userRodriguez, 0],
            ['15678901-2', 'Ana Silva Vargas', 'asilva@usach.cl', $userSilva, 1],
            ['16789012-3', 'Luis Morales Díaz', 'lmorales@usach.cl', $userMorales, 1],
        ]);

        // Seed Alumnos (linked to existing users)
        // Get carrera IDs
        $carreraICI = $this->db->createCommand('SELECT id FROM {{%carrera_malla}} WHERE codigo = :codigo', [':codigo' => 'ICI-1472'])->queryScalar();
        $carreraIEI = $this->db->createCommand('SELECT id FROM {{%carrera_malla}} WHERE codigo = :codigo', [':codigo' => 'IEI-1473'])->queryScalar();
        $carreraMII = $this->db->createCommand('SELECT id FROM {{%carrera_malla}} WHERE codigo = :codigo', [':codigo' => 'MII-2001'])->queryScalar();

        // Get alumno user IDs
        $userPerez = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'alumno.perez'])->queryScalar();
        $userRojas = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'alumno.rojas'])->queryScalar();
        $userDiaz = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'alumno.diaz'])->queryScalar();
        $userLopez = $this->db->createCommand('SELECT id FROM {{%user}} WHERE username = :username', [':username' => 'alumno.lopez'])->queryScalar();

        $this->batchInsert('{{%alumno}}', ['rut', 'nombre', 'correo', 'carrera_malla_id', 'user_id', 'tipo_ingreso', 'anio_ingreso'], [
            ['20123456-7', 'Carlos Pérez Torres', 'cperez@usach.cl', $carreraICI, $userPerez, 'PAES', 2021],
            ['20234567-8', 'José Rojas Fuentes', 'jrojas@usach.cl', $carreraIEI, $userRojas, 'PAES', 2021],
            ['20345678-9', 'Valentina Díaz Ramos', 'vdiaz@usach.cl', $carreraICI, $userDiaz, 'PAES', 2022],
            ['20456789-0', 'Francisca López Vera', 'flopez@usach.cl', $carreraMII, $userLopez, 'prosecucion', 2023],
        ]);

        // Seed Empresas
        $this->batchInsert('{{%empresa}}', 
            ['rut', 'nombre', 'supervisor_rut', 'supervisor_nombre', 'supervisor_correo', 'supervisor_telefono', 'supervisor_cargo'], 
            [
                ['76123456-7', 'TechCorp SpA', '17123456-4', 'Roberto Méndez', 'rmendez@techcorp.cl', '+56912345678', 'Gerente de Operaciones'],
                ['76234567-8', 'IndustriaSur Ltda', '17234567-5', 'Carmen Flores', 'cflores@industriasur.cl', '+56923456789', 'Jefa de Proyectos'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%empresa}}');
        $this->delete('{{%alumno}}');
        $this->delete('{{%profesor}}');
        $this->delete('{{%subcategoria}}');
        $this->delete('{{%categoria}}');
        $this->delete('{{%modalidad}}');
        $this->delete('{{%origen}}');
        $this->delete('{{%carrera_malla}}');
    }
}
