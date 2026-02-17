<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'SGDII - Módulo Tesis';
?>
<div class="site-index">

    <div class="p-5 mb-4 bg-transparent rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-4">Sistema de Gestión</h1>
            <h2 class="display-6">Departamento de Ingeniería Industrial - USACH</h2>
            <p class="fs-5 fw-light">Módulo de Tesis / Trabajo de Título</p>
            <p class="lead mt-4">
                Bienvenido al sistema de gestión de tesis del Departamento de Ingeniería Industrial. 
                Este módulo permitirá gestionar el proceso completo de inscripción, evaluación y 
                seguimiento de las tesis y trabajos de título.
            </p>
        </div>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <i class="bi bi-file-earmark-text"></i> Solicitud de Inscripción
                        </h3>
                        <p class="card-text">
                            Sistema de solicitud de tema de tesis (STT). Permite a los estudiantes 
                            presentar su propuesta de tesis para revisión y aprobación.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">Próximamente</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <i class="bi bi-check-circle"></i> Evaluación y Resolución
                        </h3>
                        <p class="card-text">
                            Gestión del proceso de evaluación de solicitudes de tema de tesis (STT).
                            Permite la revisión, observaciones y resolución de las propuestas.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">Próximamente</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <i class="bi bi-bar-chart"></i> Reportes
                        </h3>
                        <p class="card-text">
                            Generación de reportes y estadísticas sobre el estado de las tesis,
                            tiempos de evaluación, y seguimiento del proceso.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">Próximamente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
