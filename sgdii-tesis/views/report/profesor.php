<?php

/** @var yii\web\View $this */
/** @var app\models\Profesor $profesor */
/** @var array $tesisComoGuia */
/** @var array $tesisComoRevisor */
/** @var array $estadisticas */
/** @var array $profesores */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Reporte de Profesor';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-profesor">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    
    <?php if (Yii::$app->user->identity->rol === 'admin'): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Seleccionar Profesor</h5>
            <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['profesor']]); ?>
            <div class="row align-items-end">
                <div class="col-md-10">
                    <label for="profesor_id">Profesor</label>
                    <select name="profesor_id" id="profesor_id" class="form-select">
                        <option value="">-- Seleccionar Profesor --</option>
                        <?php foreach ($profesores as $prof): ?>
                            <option value="<?= $prof->id ?>" <?= $profesor && $profesor->id == $prof->id ? 'selected' : '' ?>>
                                <?= Html::encode($prof->nombre) ?>
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
    
    <?php if ($profesor): ?>
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4><?= Html::encode($profesor->nombre) ?></h4>
                    <p class="mb-1"><strong>RUT:</strong> <?= Html::encode($profesor->rut) ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= Html::encode($profesor->correo) ?></p>
                    <?php if ($profesor->es_comision_evaluadora): ?>
                        <span class="badge bg-success">Comisión Evaluadora</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        <?= Html::a('<i class="bi bi-file-earmark-excel"></i> Excel', 
                            ['export-profesor-excel', 'profesor_id' => $profesor->id], 
                            ['class' => 'btn btn-success btn-sm', 'target' => '_blank']) ?>
                        <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> PDF', 
                            ['export-profesor-pdf', 'profesor_id' => $profesor->id], 
                            ['class' => 'btn btn-danger btn-sm', 'target' => '_blank']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?= $estadisticas['total_como_guia'] ?></h3>
                    <p class="text-muted small mb-0">Tesis como Guía</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?= $estadisticas['total_como_revisor'] ?></h3>
                    <p class="text-muted small mb-0">Tesis como Revisor</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info"><?= $estadisticas['tesis_en_curso'] ?></h3>
                    <p class="text-muted small mb-0">Tesis en Curso</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-secondary"><?= $estadisticas['tesis_finalizadas'] ?></h3>
                    <p class="text-muted small mb-0">Tesis Finalizadas</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tesis como Guía -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-check"></i> Tesis como Profesor Guía (<?= count($tesisComoGuia) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($tesisComoGuia)): ?>
                <p class="text-muted">No hay tesis asignadas como profesor guía.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Correlativo</th>
                                <th>Título</th>
                                <th>Modalidad</th>
                                <th>Categoría</th>
                                <th>Alumnos</th>
                                <th>Estado</th>
                                <th>Etapa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tesisComoGuia as $tesis): ?>
                                <tr>
                                    <td><strong><?= Html::encode($tesis->stt->correlativo) ?></strong></td>
                                    <td><?= Html::encode($tesis->stt->titulo) ?></td>
                                    <td><span class="badge bg-secondary"><?= Html::encode($tesis->stt->modalidad->nombre) ?></span></td>
                                    <td><?= $tesis->categoria ? Html::encode($tesis->categoria->nombre) : '<span class="text-muted">N/A</span>' ?></td>
                                    <td>
                                        <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                            <div><?= Html::encode($alumno->nombre) ?></div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($tesis->estado === 'En curso') $badgeClass = 'bg-info';
                                        if ($tesis->estado === 'Finalizada') $badgeClass = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= Html::encode($tesis->estado) ?></span>
                                    </td>
                                    <td><?= Html::encode($tesis->getEtapaLabel()) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Tesis como Revisor -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Tesis como Profesor Revisor (<?= count($tesisComoRevisor) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($tesisComoRevisor)): ?>
                <p class="text-muted">No hay tesis asignadas como profesor revisor.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Correlativo</th>
                                <th>Título</th>
                                <th>Modalidad</th>
                                <th>Categoría</th>
                                <th>Alumnos</th>
                                <th>Estado</th>
                                <th>Etapa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tesisComoRevisor as $tesis): ?>
                                <tr>
                                    <td><strong><?= Html::encode($tesis->stt->correlativo) ?></strong></td>
                                    <td><?= Html::encode($tesis->stt->titulo) ?></td>
                                    <td><span class="badge bg-secondary"><?= Html::encode($tesis->stt->modalidad->nombre) ?></span></td>
                                    <td><?= $tesis->categoria ? Html::encode($tesis->categoria->nombre) : '<span class="text-muted">N/A</span>' ?></td>
                                    <td>
                                        <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                            <div><?= Html::encode($alumno->nombre) ?></div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($tesis->estado === 'En curso') $badgeClass = 'bg-info';
                                        if ($tesis->estado === 'Finalizada') $badgeClass = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= Html::encode($tesis->estado) ?></span>
                                    </td>
                                    <td><?= Html::encode($tesis->getEtapaLabel()) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Selecciona un profesor para ver su reporte.
    </div>
    <?php endif; ?>
</div>
