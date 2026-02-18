<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SolicitudTemaTesis $model */

$this->title = 'STT ' . $model->correlativo;
$this->params['breadcrumbs'][] = ['label' => 'Solicitudes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Check permissions once at the top
$user = Yii::$app->user->identity;
$canUpdate = false;

if (in_array($model->estado, [SolicitudTemaTesis::ESTADO_ENVIADA, SolicitudTemaTesis::ESTADO_EN_REVISION])) {
    if ($user->rol === 'admin') {
        $canUpdate = true;
    } elseif ($user->rol === 'alumno') {
        $alumno = \app\models\Alumno::findOne(['user_id' => $user->id]);
        if ($alumno) {
            foreach ($model->sttAlumnos as $sttAlumno) {
                if ($sttAlumno->alumno_id == $alumno->id) {
                    $canUpdate = true;
                    break;
                }
            }
        }
    } elseif ($user->rol === 'profesor') {
        $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
        if ($profesor && $model->profesor_curso_id == $profesor->id) {
            $canUpdate = true;
        }
    }
}

$canReview = ($user->rol === 'admin' || $user->rol === 'comision_evaluadora') && $model->puedeSerResuelta();
?>

<div class="stt-view">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?php if ($canUpdate): ?>
                <?= Html::a('<i class="bi bi-pencil"></i> Corregir STT', ['update', 'id' => $model->id], [
                    'class' => 'btn btn-warning'
                ]) ?>
            <?php endif; ?>
            
            <?php if ($canReview): ?>
                <?= Html::a('<i class="bi bi-check-circle"></i> Revisar STT', ['/comision/review', 'id' => $model->id], [
                    'class' => 'btn btn-success'
                ]) ?>
            <?php endif; ?>
            
            <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Correlativo:</strong> <?= Html::encode($model->correlativo) ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-success"><?= Html::encode($model->estado) ?></span></p>
                    <p><strong>Origen:</strong> <?= Html::encode($model->origen->nombre) ?></p>
                    <p><strong>Modalidad:</strong> <?= Html::encode($model->modalidad->nombre) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Profesor de Curso:</strong> <?= Html::encode($model->profesorCurso->nombre) ?></p>
                    <p><strong>Nota:</strong> <?= Html::encode($model->nota) ?></p>
                    <p><strong>Fecha de Creación:</strong> <?= Yii::$app->formatter->asDatetime($model->fecha_creacion) ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p><strong>Título:</strong></p>
                    <p><?= Html::encode($model->titulo) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Alumnos</h5>
        </div>
        <div class="card-body">
            <?php if ($model->sttAlumnos): ?>
                <?php foreach ($model->sttAlumnos as $index => $sttAlumno): ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Alumno <?= $index + 1 ?>:</strong> 
                            <?= Html::encode($sttAlumno->alumno->nombre) ?> 
                            (<?= Html::encode($sttAlumno->alumno->rut) ?>)
                        </div>
                        <div class="col-md-6">
                            <strong>Carrera:</strong> 
                            <?= Html::encode($sttAlumno->carreraMalla->nombre) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No hay alumnos asociados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5>Profesores Propuestos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Profesor Guía:</strong></p>
                    <p><?= $model->profesorGuiaPropuesto ? Html::encode($model->profesorGuiaPropuesto->nombre) : '<span class="text-muted">No propuesto</span>' ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Profesor Revisor 1:</strong></p>
                    <p><?= $model->profesorRevisor1Propuesto ? Html::encode($model->profesorRevisor1Propuesto->nombre) : '<span class="text-muted">No propuesto</span>' ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Profesor Revisor 2:</strong></p>
                    <p><?= $model->profesorRevisor2Propuesto ? Html::encode($model->profesorRevisor2Propuesto->nombre) : '<span class="text-muted">No propuesto</span>' ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($model->empresa): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5>Información de Empresa (Pasantía)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Empresa:</strong> <?= Html::encode($model->empresa->nombre) ?></p>
                        <p><strong>RUT:</strong> <?= Html::encode($model->empresa->rut) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Supervisor:</strong> <?= Html::encode($model->empresa->supervisor_nombre) ?></p>
                        <p><strong>Cargo:</strong> <?= Html::encode($model->empresa->supervisor_cargo) ?></p>
                        <p><strong>Correo:</strong> <?= Html::encode($model->empresa->supervisor_correo) ?></p>
                        <p><strong>Teléfono:</strong> <?= Html::encode($model->empresa->supervisor_telefono) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group mt-3">
        <?php if ($canUpdate): ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Corregir STT', ['update', 'id' => $model->id], [
                'class' => 'btn btn-warning'
            ]) ?>
        <?php endif; ?>
        
        <?php if ($canReview): ?>
            <?= Html::a('<i class="bi bi-check-circle"></i> Revisar STT', ['/comision/review', 'id' => $model->id], [
                'class' => 'btn btn-success'
            ]) ?>
        <?php endif; ?>
        
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
