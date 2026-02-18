<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

use yii\bootstrap5\Html;

$this->title = 'Reportes y Estadísticas';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <p class="lead">Accede a reportes detallados y estadísticas según tu rol en el sistema.</p>
    
    <div class="row mt-4">
        <?php if ($user->rol === 'profesor' || $user->rol === 'admin'): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-person-badge"></i> Reporte de Profesor
                    </h5>
                    <p class="card-text">
                        Visualiza todas las tesis bajo tu supervisión como profesor guía o revisor.
                    </p>
                    <ul class="text-muted small">
                        <li>Tesis como guía</li>
                        <li>Tesis como revisor</li>
                        <li>Estadísticas de carga académica</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <?= Html::a('Ver Reporte <i class="bi bi-arrow-right"></i>', ['/report/profesor'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php
        $canAccessComision = false;
        if ($user->rol === 'admin') {
            $canAccessComision = true;
        } elseif ($user->rol === 'profesor' || $user->rol === 'comision_evaluadora') {
            $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
            $canAccessComision = $profesor && $profesor->es_comision_evaluadora == 1;
        }
        ?>
        
        <?php if ($canAccessComision): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-list-check"></i> Reporte de Comisión
                    </h5>
                    <p class="card-text">
                        Acceso completo a todas las solicitudes y tesis con filtros avanzados.
                    </p>
                    <ul class="text-muted small">
                        <li>Todas las STT con filtros</li>
                        <li>Estados globales</li>
                        <li>Análisis por modalidad y categoría</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <?= Html::a('Ver Reporte <i class="bi bi-arrow-right"></i>', ['/report/comision'], ['class' => 'btn btn-success btn-sm']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($user->rol === 'alumno' || $user->rol === 'admin'): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-journal-text"></i> Reporte de Estudiante
                    </h5>
                    <p class="card-text">
                        Seguimiento de tu progreso personal desde la solicitud hasta la finalización.
                    </p>
                    <ul class="text-muted small">
                        <li>Estado de solicitudes</li>
                        <li>Progreso de tesis activa</li>
                        <li>Historial completo</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <?= Html::a('Ver Reporte <i class="bi bi-arrow-right"></i>', ['/report/estudiante'], ['class' => 'btn btn-info btn-sm']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up"></i> Estadísticas Generales
                    </h5>
                    <p class="card-text">
                        Visualizaciones interactivas con gráficas y análisis detallados del sistema.
                    </p>
                    <ul class="text-muted small">
                        <li>5 gráficas clave</li>
                        <li>Indicadores de desempeño</li>
                        <li>Análisis de tendencias</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <?= Html::a('Ver Estadísticas <i class="bi bi-arrow-right"></i>', ['/report/estadisticas'], ['class' => 'btn btn-warning btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Información sobre Reportes</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Exportación</h6>
                            <p class="small text-muted">
                                Todos los reportes pueden ser exportados en formato <strong>Excel (.xlsx)</strong> y <strong>PDF</strong> 
                                mediante los botones disponibles en cada vista.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Filtros Avanzados</h6>
                            <p class="small text-muted">
                                Los reportes de comisión incluyen filtros por modalidad, estado, fecha, y profesor guía 
                                para un análisis más detallado.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
