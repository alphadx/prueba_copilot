<?php

/** @var yii\web\View $this */
/** @var app\models\SolicitudTemaTesis $model */
/** @var app\models\Profesor[] $profesores */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Evaluar STT: ' . $model->correlativo;
$this->params['breadcrumbs'][] = ['label' => 'Gestión STT', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comision-review">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="row">
        <!-- STT Details Section -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Detalles de la Solicitud</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Correlativo:</th>
                            <td><strong><?= Html::encode($model->correlativo) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Estado Actual:</th>
                            <td>
                                <?php
                                $badgeClass = 'secondary';
                                switch ($model->estado) {
                                    case 'Solicitada':
                                        $badgeClass = 'warning';
                                        break;
                                    case 'En revisión':
                                        $badgeClass = 'info';
                                        break;
                                    case 'Aceptada':
                                    case 'Aceptada con observaciones':
                                        $badgeClass = 'success';
                                        break;
                                    case 'Rechazada':
                                        $badgeClass = 'danger';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= Html::encode($model->estado) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Modalidad:</th>
                            <td>
                                <span class="badge bg-info">
                                    <?= Html::encode($model->modalidad->nombre) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Título:</th>
                            <td><?= Html::encode($model->titulo) ?></td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación:</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->fecha_creacion, 'dd/MM/yyyy HH:mm') ?></td>
                        </tr>
                        <tr>
                            <th>Profesor de Curso:</th>
                            <td><?= Html::encode($model->profesorCurso->nombre) ?></td>
                        </tr>
                        <tr>
                            <th>Nota:</th>
                            <td><strong><?= Html::encode($model->nota) ?></strong></td>
                        </tr>
                    </table>

                    <h6 class="mt-3"><i class="bi bi-people"></i> Alumnos</h6>
                    <ul class="list-group">
                        <?php foreach ($model->alumnos as $alumno): ?>
                            <li class="list-group-item">
                                <strong><?= Html::encode($alumno->nombre) ?></strong>
                                <br>
                                <small class="text-muted">
                                    RUT: <?= Html::encode($alumno->rut) ?> | 
                                    Carrera: <?= Html::encode($alumno->carreraMalla->nombre) ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($model->modalidad_id == 1): // TT ?>
                        <h6 class="mt-3"><i class="bi bi-person-badge"></i> Profesores Propuestos</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Profesor Guía:</th>
                                <td><?= $model->profesorGuiaPropuesto ? Html::encode($model->profesorGuiaPropuesto->nombre) : '<em class="text-muted">No especificado</em>' ?></td>
                            </tr>
                            <tr>
                                <th>Revisor 1:</th>
                                <td><?= $model->profesorRevisor1Propuesto ? Html::encode($model->profesorRevisor1Propuesto->nombre) : '<em class="text-muted">No especificado</em>' ?></td>
                            </tr>
                            <tr>
                                <th>Revisor 2:</th>
                                <td><?= $model->profesorRevisor2Propuesto ? Html::encode($model->profesorRevisor2Propuesto->nombre) : '<em class="text-muted">No especificado</em>' ?></td>
                            </tr>
                        </table>
                    <?php elseif ($model->modalidad_id == 2): // Papers ?>
                        <h6 class="mt-3"><i class="bi bi-person-badge"></i> Profesor Propuesto</h6>
                        <p>
                            <strong>Profesor Guía:</strong> 
                            <?= $model->profesorGuiaPropuesto ? Html::encode($model->profesorGuiaPropuesto->nombre) : '<em class="text-muted">No especificado</em>' ?>
                        </p>
                    <?php elseif ($model->modalidad_id == 3): // Pasantía ?>
                        <h6 class="mt-3"><i class="bi bi-building"></i> Información de Empresa</h6>
                        <?php if ($model->empresa): ?>
                            <table class="table table-sm">
                                <tr>
                                    <th>Empresa:</th>
                                    <td><?= Html::encode($model->empresa->nombre) ?></td>
                                </tr>
                                <tr>
                                    <th>RUT:</th>
                                    <td><?= Html::encode($model->empresa->rut) ?></td>
                                </tr>
                                <tr>
                                    <th>Supervisor:</th>
                                    <td><?= Html::encode($model->empresa->supervisor_nombre) ?></td>
                                </tr>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No se especificó empresa</p>
                        <?php endif; ?>
                        
                        <h6 class="mt-3"><i class="bi bi-person-badge"></i> Profesor Propuesto</h6>
                        <p>
                            <strong>Profesor Guía:</strong> 
                            <?= $model->profesorGuiaPropuesto ? Html::encode($model->profesorGuiaPropuesto->nombre) : '<em class="text-muted">No especificado</em>' ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resolution Form -->
        <div class="col-md-4">
            <?php if ($model->puedeSerResuelta()): ?>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle"></i> Emitir Resolución</h5>
                    </div>
                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'resolucion-form',
                            'action' => ['resolve', 'id' => $model->id],
                            'method' => 'post',
                        ]); ?>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Resolución <span class="text-danger">*</span></label>
                            <?= Html::radioList('resolucion', null, [
                                'aceptar' => 'Aceptar',
                                'aceptar_con_observaciones' => 'Aceptar con Observaciones',
                                'rechazar' => 'Rechazar',
                            ], [
                                'class' => 'form-check-input',
                                'item' => function($index, $label, $name, $checked, $value) {
                                    return '<div class="form-check mb-2">' .
                                        Html::radio($name, $checked, [
                                            'value' => $value,
                                            'class' => 'form-check-input',
                                            'id' => 'resolucion-' . $value,
                                        ]) .
                                        '<label class="form-check-label" for="resolucion-' . $value . '">' .
                                        $label .
                                        '</label>' .
                                        '</div>';
                                }
                            ]) ?>
                        </div>

                        <div class="mb-3" id="motivo-container">
                            <label for="motivo" class="form-label">Motivo de Rechazo <span class="text-danger" id="motivo-required" style="display: none;">*</span></label>
                            <?= Html::textarea('motivo', '', [
                                'class' => 'form-control',
                                'id' => 'motivo',
                                'rows' => 5,
                                'placeholder' => 'Ingrese el motivo de rechazo...',
                            ]) ?>
                            <small class="text-muted">
                                Requerido cuando se rechaza la solicitud.
                            </small>
                        </div>

                        <div class="mb-3" id="observaciones-container" style="display: none;">
                            <label for="observaciones" class="form-label">Observaciones <span class="text-danger" id="observaciones-required">*</span></label>
                            <?= Html::textarea('observaciones', '', [
                                'class' => 'form-control',
                                'id' => 'observaciones',
                                'rows' => 5,
                                'placeholder' => 'Ingrese las observaciones para la aceptación...',
                            ]) ?>
                            <small class="text-muted">
                                Requerido cuando se acepta con observaciones.
                            </small>
                        </div>

                        <div class="d-grid">
                            <?= Html::submitButton(
                                '<i class="bi bi-check-circle"></i> Guardar Resolución',
                                ['class' => 'btn btn-success']
                            ) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        Al emitir una resolución, se notificará automáticamente a los alumnos y profesores involucrados.
                    </small>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Resolución Existente</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            Esta solicitud ya ha sido resuelta.
                        </div>
                        
                        <?php if ($model->fecha_resolucion): ?>
                            <p><strong>Fecha de Resolución:</strong><br>
                            <?= Yii::$app->formatter->asDatetime($model->fecha_resolucion, 'dd/MM/yyyy HH:mm') ?></p>
                        <?php endif; ?>
                        
                        <?php if ($model->motivo_resolucion): ?>
                            <p><strong>Motivo de Rechazo:</strong><br>
                            <?= Html::encode($model->motivo_resolucion) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($model->observaciones): ?>
                            <p><strong>Observaciones:</strong><br>
                            <?= Html::encode($model->observaciones) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// JavaScript for form validation and dynamic field display
$this->registerJs(<<<JS
    // Show/hide fields based on resolution type
    $('input[name="resolucion"]').on('change', function() {
        var resolucion = $(this).val();
        
        if (resolucion === 'rechazar') {
            $('#motivo-container').show();
            $('#observaciones-container').hide();
            $('#motivo').attr('required', true);
            $('#observaciones').attr('required', false);
            $('#motivo-required').show();
            $('#observaciones-required').hide();
        } else if (resolucion === 'aceptar_con_observaciones') {
            $('#motivo-container').hide();
            $('#observaciones-container').show();
            $('#motivo').attr('required', false);
            $('#observaciones').attr('required', true);
            $('#motivo-required').hide();
            $('#observaciones-required').show();
        } else {
            $('#motivo-container').hide();
            $('#observaciones-container').hide();
            $('#motivo').attr('required', false);
            $('#observaciones').attr('required', false);
            $('#motivo-required').hide();
            $('#observaciones-required').hide();
        }
    });
    
    $('#resolucion-form').on('beforeSubmit', function(e) {
        var resolucion = $('input[name="resolucion"]:checked').val();
        var motivo = $('#motivo').val().trim();
        var observaciones = $('#observaciones').val().trim();
        
        if (!resolucion) {
            alert('Por favor, seleccione un tipo de resolución.');
            return false;
        }
        
        if (resolucion === 'rechazar' && !motivo) {
            alert('Debe proporcionar el motivo de rechazo.');
            return false;
        }
        
        if (resolucion === 'aceptar_con_observaciones' && !observaciones) {
            alert('Debe proporcionar las observaciones para la aceptación.');
            return false;
        }
        
        if (!confirm('¿Está seguro de emitir esta resolución? Esta acción enviará notificaciones a los involucrados.')) {
            return false;
        }
        
        return true;
    });
JS
);
?>
