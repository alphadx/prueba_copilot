<?php

/** @var yii\web\View $this */
/** @var array $solicitudes */
/** @var array $modalidades */
/** @var array $profesores */
/** @var array $estados */
/** @var array $estadisticas */
/** @var array $filters */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Reporte de Comisión Evaluadora';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-comision">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    
    <!-- Export Buttons -->
    <div class="mb-3 text-end">
        <div class="btn-group" role="group">
            <?php
            $exportParams = http_build_query($filters);
            ?>
            <?= Html::a('<i class="bi bi-file-earmark-excel"></i> Exportar Excel', 
                ['export-comision-excel?' . $exportParams], 
                ['class' => 'btn btn-success', 'target' => '_blank']) ?>
            <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> Exportar PDF', 
                ['export-comision-pdf?' . $exportParams], 
                ['class' => 'btn btn-danger', 'target' => '_blank']) ?>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?= $estadisticas['total_stt'] ?></h3>
                    <p class="text-muted small mb-0">Total STT</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?= $estadisticas['tasa_aceptacion'] ?>%</h3>
                    <p class="text-muted small mb-0">Tasa de Aceptación</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-danger"><?= $estadisticas['tasa_rechazo'] ?>%</h3>
                    <p class="text-muted small mb-0">Tasa de Rechazo</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info"><?= $estadisticas['promedio_tiempo_resolucion'] ?></h3>
                    <p class="text-muted small mb-0">Días Prom. Resolución</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros Avanzados</h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['comision']]); ?>
            <div class="row">
                <div class="col-md-3">
                    <label for="modalidad_id">Modalidad</label>
                    <select name="modalidad_id" id="modalidad_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($modalidades as $modalidad): ?>
                            <option value="<?= $modalidad->id ?>" <?= $filters['modalidad_id'] == $modalidad->id ? 'selected' : '' ?>>
                                <?= Html::encode($modalidad->nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($estados as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $filters['estado'] == $key ? 'selected' : '' ?>>
                                <?= Html::encode($value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="fecha_desde">Fecha Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="<?= $filters['fecha_desde'] ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="<?= $filters['fecha_hasta'] ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-9">
                    <label for="profesor_guia_id">Profesor Guía</label>
                    <select name="profesor_guia_id" id="profesor_guia_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($profesores as $profesor): ?>
                            <option value="<?= $profesor->id ?>" <?= $filters['profesor_guia_id'] == $profesor->id ? 'selected' : '' ?>>
                                <?= Html::encode($profesor->nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <?= Html::submitButton('<i class="bi bi-search"></i> Filtrar', ['class' => 'btn btn-primary w-100']) ?>
                    <?= Html::a('Limpiar', ['comision'], ['class' => 'btn btn-outline-secondary w-100 ms-2']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    
    <!-- Results -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Solicitudes de Tema de Tesis (<?= count($solicitudes) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($solicitudes)): ?>
                <p class="text-muted">No se encontraron solicitudes con los filtros aplicados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Correlativo</th>
                                <th>Título</th>
                                <th>Modalidad</th>
                                <th>Alumnos</th>
                                <th>Profesor Guía</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Fecha Resolución</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $stt): ?>
                                <tr>
                                    <td><strong><?= Html::encode($stt->correlativo) ?></strong></td>
                                    <td style="max-width: 300px;">
                                        <div class="text-truncate" title="<?= Html::encode($stt->titulo) ?>">
                                            <?= Html::encode($stt->titulo) ?>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= Html::encode($stt->modalidad->nombre) ?></span></td>
                                    <td>
                                        <?php foreach ($stt->alumnos as $alumno): ?>
                                            <div class="small"><?= Html::encode($alumno->nombre) ?></div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?= $stt->profesorGuiaPropuesto ? Html::encode($stt->profesorGuiaPropuesto->nombre) : '<span class="text-muted">N/A</span>' ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($stt->estado === 'Aceptada') $badgeClass = 'bg-success';
                                        if ($stt->estado === 'Rechazada') $badgeClass = 'bg-danger';
                                        if ($stt->estado === 'En revisión') $badgeClass = 'bg-warning';
                                        if ($stt->estado === 'Aceptada con observaciones') $badgeClass = 'bg-info';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= Html::encode($stt->estado) ?></span>
                                    </td>
                                    <td class="small"><?= Yii::$app->formatter->asDatetime($stt->fecha_creacion, 'short') ?></td>
                                    <td class="small">
                                        <?= $stt->fecha_resolucion ? Yii::$app->formatter->asDatetime($stt->fecha_resolucion, 'short') : '<span class="text-muted">Pendiente</span>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
