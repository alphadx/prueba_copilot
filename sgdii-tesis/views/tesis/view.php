<?php

/** @var yii\web\View $this */
/** @var app\models\Tesis $tesis */
/** @var app\models\HistorialEstado[] $historial */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Tesis: ' . $tesis->stt->correlativo;
$this->params['breadcrumbs'][] = ['label' => 'Gestión de Tesis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$puedeGestionar = $user->rol !== 'alumno' && $tesis->puedeActualizar();
?>

<div class="tesis-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- Main Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Información de la Tesis</h5>
            <span class="badge <?= $tesis->getEstadoBadgeClass() ?> fs-6">
                <?= Html::encode($tesis->estado) ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Correlativo:</dt>
                        <dd class="col-sm-8"><?= Html::encode($tesis->stt->correlativo) ?></dd>

                        <dt class="col-sm-4">Título:</dt>
                        <dd class="col-sm-8"><strong><?= Html::encode($tesis->stt->titulo) ?></strong></dd>

                        <dt class="col-sm-4">Modalidad:</dt>
                        <dd class="col-sm-8"><?= Html::encode($tesis->stt->modalidad->nombre) ?></dd>

                        <dt class="col-sm-4">Categoría:</dt>
                        <dd class="col-sm-8"><?= Html::encode($tesis->categoria->nombre ?? 'Sin categoría') ?></dd>

                        <dt class="col-sm-4">Estudiante(s):</dt>
                        <dd class="col-sm-8">
                            <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                <div><?= Html::encode($alumno->nombre) ?></div>
                            <?php endforeach; ?>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Profesor Guía:</dt>
                        <dd class="col-sm-8"><?= Html::encode($tesis->profesorGuia->nombre ?? 'N/A') ?></dd>

                        <?php if ($tesis->profesorRevisor1): ?>
                            <dt class="col-sm-4">Revisor 1:</dt>
                            <dd class="col-sm-8"><?= Html::encode($tesis->profesorRevisor1->nombre) ?></dd>
                        <?php endif; ?>

                        <?php if ($tesis->profesorRevisor2): ?>
                            <dt class="col-sm-4">Revisor 2:</dt>
                            <dd class="col-sm-8"><?= Html::encode($tesis->profesorRevisor2->nombre) ?></dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Fecha Aceptación:</dt>
                        <dd class="col-sm-8">
                            <?= $tesis->fecha_aceptacion ? Yii::$app->formatter->asDate($tesis->fecha_aceptacion) : 'N/A' ?>
                        </dd>

                        <dt class="col-sm-4">Última Actualización:</dt>
                        <dd class="col-sm-8">
                            <?= Yii::$app->formatter->asDatetime($tesis->fecha_ultima_actualizacion) ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Progreso de la Tesis</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span><?= $tesis->getEtapaLabel() ?></span>
                <?php if ($puedeGestionar && $tesis->puedeAvanzarEtapa()): ?>
                    <?= Html::beginForm(['avanzar-etapa', 'id' => $tesis->id], 'post', ['class' => 'd-inline']) ?>
                    <?= Html::submitButton(
                        '<i class="bi bi-arrow-right-circle"></i> Avanzar Etapa',
                        [
                            'class' => 'btn btn-sm btn-success',
                            'data' => [
                                'confirm' => '¿Está seguro de avanzar a la siguiente etapa?',
                            ],
                        ]
                    ) ?>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar <?= $tesis->getPorcentaje() >= 100 ? 'bg-success' : 'bg-info' ?>" 
                     role="progressbar" 
                     style="width: <?= $tesis->getPorcentaje() ?>%"
                     aria-valuenow="<?= $tesis->getPorcentaje() ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <strong><?= $tesis->getPorcentaje() ?>%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Change State Card (Only for professors/admin) -->
    <?php if ($puedeGestionar): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Cambiar Estado de la Tesis</h5>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['cambiar-estado', 'id' => $tesis->id],
                    'method' => 'post',
                ]); ?>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nuevo Estado</label>
                            <?= Html::dropDownList(
                                'estado',
                                $tesis->estado,
                                app\models\Tesis::getEstados(),
                                ['class' => 'form-select', 'required' => true]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Motivo del Cambio</label>
                            <?= Html::textarea('motivo', '', [
                                'class' => 'form-control',
                                'rows' => 2,
                                'placeholder' => 'Describa brevemente el motivo del cambio de estado...'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <?= Html::submitButton(
                            '<i class="bi bi-check-circle"></i> Cambiar',
                            ['class' => 'btn btn-primary w-100']
                        ) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Professor Review Response (Only for professors) -->
    <?php if (in_array($user->rol, ['profesor', 'comision_evaluadora', 'admin'])): ?>
        <?php 
        $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
        $puedeResponder = $profesor && (
            $tesis->profesor_guia_id === $profesor->id ||
            $tesis->profesor_revisor1_id === $profesor->id ||
            $tesis->profesor_revisor2_id === $profesor->id
        );
        ?>
        
        <?php if ($puedeResponder): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-right-text"></i> Responder Revisión</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'action' => ['responder-revision', 'id' => $tesis->id],
                        'method' => 'post',
                    ]); ?>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Respuesta</label>
                                <?= Html::dropDownList(
                                    'tipo',
                                    'comentario',
                                    [
                                        'comentario' => 'Comentario',
                                        'acepta' => 'Acepta Revisión',
                                        'rechaza' => 'Requiere Cambios'
                                    ],
                                    ['class' => 'form-select', 'required' => true]
                                ) ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Respuesta / Comentarios</label>
                                <?= Html::textarea('respuesta', '', [
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'placeholder' => 'Ingrese su respuesta o comentarios sobre la revisión...',
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <?= Html::submitButton(
                                '<i class="bi bi-send"></i> Enviar',
                                ['class' => 'btn btn-success w-100']
                            ) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <small><i class="bi bi-info-circle"></i> Su respuesta será notificada a todos los involucrados en esta tesis.</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- History Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Cambios</h5>
        </div>
        <div class="card-body">
            <?php if (empty($historial)): ?>
                <p class="text-muted">No hay cambios de estado registrados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado Anterior</th>
                                <th>Estado Nuevo</th>
                                <th>Motivo</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h): ?>
                                <tr>
                                    <td><?= Yii::$app->formatter->asDatetime($h->fecha_cambio) ?></td>
                                    <td>
                                        <?php if ($h->estado_anterior): ?>
                                            <span class="badge bg-secondary"><?= Html::encode($h->estado_anterior) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-primary"><?= Html::encode($h->estado_nuevo) ?></span></td>
                                    <td><?= Html::encode($h->motivo) ?: '-' ?></td>
                                    <td>
                                        <?= $h->usuario ? Html::encode($h->usuario->nombre) : 'Sistema' ?>
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
