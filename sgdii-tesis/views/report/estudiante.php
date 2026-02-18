<?php

/** @var yii\web\View $this */
/** @var app\models\Alumno $alumno */
/** @var array $solicitudes */
/** @var app\models\Tesis $tesis */
/** @var array $alumnos */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Reporte de Estudiante';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-estudiante">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    
    <?php if (Yii::$app->user->identity->rol === 'admin'): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Seleccionar Estudiante</h5>
            <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['estudiante']]); ?>
            <div class="row align-items-end">
                <div class="col-md-10">
                    <label for="alumno_id">Estudiante</label>
                    <select name="alumno_id" id="alumno_id" class="form-select">
                        <option value="">-- Seleccionar Estudiante --</option>
                        <?php foreach ($alumnos as $alum): ?>
                            <option value="<?= $alum->id ?>" <?= $alumno && $alumno->id == $alum->id ? 'selected' : '' ?>>
                                <?= Html::encode($alum->nombre . ' - ' . $alum->rut) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <?= Html::submitButton('Ver', ['class' => 'btn btn-primary w-100']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($alumno): ?>
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4><?= Html::encode($alumno->nombre) ?></h4>
                    <p class="mb-1"><strong>RUT:</strong> <?= Html::encode($alumno->rut) ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= Html::encode($alumno->correo) ?></p>
                    <p class="mb-0"><strong>Carrera:</strong> <?= $alumno->carreraMalla ? Html::encode($alumno->carreraMalla->nombre) : 'N/A' ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Thesis Progress -->
    <?php if ($tesis): ?>
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Tesis Actual en Curso</h5>
        </div>
        <div class="card-body">
            <h5><?= Html::encode($tesis->stt->titulo) ?></h5>
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Correlativo:</strong> <?= Html::encode($tesis->stt->correlativo) ?></p>
                    <p><strong>Modalidad:</strong> <span class="badge bg-secondary"><?= Html::encode($tesis->stt->modalidad->nombre) ?></span></p>
                    <p><strong>Categoría:</strong> <?= $tesis->categoria ? Html::encode($tesis->categoria->nombre) : 'N/A' ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-info"><?= Html::encode($tesis->estado) ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Profesor Guía:</strong> <?= $tesis->profesorGuia ? Html::encode($tesis->profesorGuia->nombre) : 'N/A' ?></p>
                    <?php if ($tesis->profesorRevisor1): ?>
                        <p><strong>Revisor 1:</strong> <?= Html::encode($tesis->profesorRevisor1->nombre) ?></p>
                    <?php endif; ?>
                    <?php if ($tesis->profesorRevisor2): ?>
                        <p><strong>Revisor 2:</strong> <?= Html::encode($tesis->profesorRevisor2->nombre) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-3">
                <label><strong>Progreso:</strong></label>
                <div class="progress" style="height: 30px;">
                    <?php $porcentaje = $tesis->getPorcentaje(); ?>
                    <div class="progress-bar <?= $porcentaje >= 100 ? 'bg-success' : 'bg-info' ?>" 
                         role="progressbar" 
                         style="width: <?= $porcentaje ?>%;" 
                         aria-valuenow="<?= $porcentaje ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= $tesis->getEtapaLabel() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- All Solicitudes History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Solicitudes (<?= count($solicitudes) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($solicitudes)): ?>
                <p class="text-muted">No se encontraron solicitudes para este estudiante.</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($solicitudes as $stt): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h6><?= Html::encode($stt->titulo) ?></h6>
                                        <p class="small mb-1"><strong>Correlativo:</strong> <?= Html::encode($stt->correlativo) ?></p>
                                        <p class="small mb-1"><strong>Modalidad:</strong> <span class="badge bg-secondary"><?= Html::encode($stt->modalidad->nombre) ?></span></p>
                                        <p class="small mb-1">
                                            <strong>Profesor Guía:</strong> 
                                            <?= $stt->profesorGuiaPropuesto ? Html::encode($stt->profesorGuiaPropuesto->nombre) : 'N/A' ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($stt->estado === 'Aceptada') $badgeClass = 'bg-success';
                                        if ($stt->estado === 'Rechazada') $badgeClass = 'bg-danger';
                                        if ($stt->estado === 'En revisión') $badgeClass = 'bg-warning';
                                        if ($stt->estado === 'Aceptada con observaciones') $badgeClass = 'bg-info';
                                        ?>
                                        <span class="badge <?= $badgeClass ?> mb-2"><?= Html::encode($stt->estado) ?></span>
                                        <p class="small text-muted mb-0">Creada: <?= Yii::$app->formatter->asDate($stt->fecha_creacion, 'short') ?></p>
                                        <?php if ($stt->fecha_resolucion): ?>
                                            <p class="small text-muted mb-0">Resuelta: <?= Yii::$app->formatter->asDate($stt->fecha_resolucion, 'short') ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($stt->motivo_resolucion): ?>
                                    <div class="mt-2">
                                        <strong class="small">Observaciones/Motivo:</strong>
                                        <p class="small text-muted mb-0"><?= Html::encode($stt->motivo_resolucion) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($stt->historialEstados)): ?>
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#historial-<?= $stt->id ?>">
                                            <i class="bi bi-list-ul"></i> Ver Historial de Estados
                                        </button>
                                        <div class="collapse mt-2" id="historial-<?= $stt->id ?>">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Estado Anterior</th>
                                                            <th>Estado Nuevo</th>
                                                            <th>Motivo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($stt->historialEstados as $historial): ?>
                                                            <tr>
                                                                <td class="small"><?= Yii::$app->formatter->asDatetime($historial->fecha, 'short') ?></td>
                                                                <td class="small"><?= Html::encode($historial->estado_anterior) ?></td>
                                                                <td class="small"><?= Html::encode($historial->estado_nuevo) ?></td>
                                                                <td class="small"><?= Html::encode($historial->motivo ?: 'N/A') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Selecciona un estudiante para ver su reporte.
    </div>
    <?php endif; ?>
</div>
