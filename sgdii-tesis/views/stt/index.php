<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\SolicitudTemaTesis[] $solicitudes */

$this->title = 'Solicitudes de Tema de Tesis';
$this->params['breadcrumbs'][] = $this->title;

// Perform permission checks once before the loop
$user = Yii::$app->user->identity;
$alumno = null;
$profesor = null;

if ($user->rol === 'alumno') {
    $alumno = \app\models\Alumno::findOne(['user_id' => $user->id]);
} elseif ($user->rol === 'profesor') {
    $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
}

$isAdmin = $user->rol === 'admin';
$isComision = $user->rol === 'comision_evaluadora' || $user->rol === 'admin';
?>

<div class="stt-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if (Yii::$app->user->identity->rol !== 'comision_evaluadora'): ?>
            <?= Html::a('<i class="bi bi-plus-circle"></i> Nueva Solicitud', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No hay solicitudes de tema de tesis disponibles.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Correlativo</th>
                        <th>Título</th>
                        <th>Modalidad</th>
                        <th>Alumnos</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $stt): ?>
                        <tr>
                            <td><?= Html::encode($stt->correlativo) ?></td>
                            <td>
                                <?= Html::encode(strlen($stt->titulo) > 60 ? substr($stt->titulo, 0, 60) . '...' : $stt->titulo) ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= Html::encode($stt->modalidad->nombre) ?></span>
                            </td>
                            <td>
                                <?php 
                                $alumnos = [];
                                foreach ($stt->sttAlumnos as $sttAlumno) {
                                    $alumnos[] = $sttAlumno->alumno->nombre;
                                }
                                echo Html::encode(implode(', ', $alumnos));
                                ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'secondary';
                                switch ($stt->estado) {
                                    case 'Aceptada':
                                        $badgeClass = 'success';
                                        break;
                                    case 'Aceptada con observaciones':
                                        $badgeClass = 'warning';
                                        break;
                                    case 'Rechazada':
                                        $badgeClass = 'danger';
                                        break;
                                    case 'En revisión':
                                        $badgeClass = 'primary';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= Html::encode($stt->estado) ?></span>
                            </td>
                            <td><?= Yii::$app->formatter->asDate($stt->fecha_creacion, 'dd/MM/yyyy') ?></td>
                            <td>
                                <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $stt->id], [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'Ver Detalles',
                                ]) ?>
                                
                                <?php if (in_array($stt->estado, ['Enviada', 'En revisión'])): ?>
                                    <?php 
                                    $canUpdate = false;
                                    
                                    if ($isAdmin) {
                                        $canUpdate = true;
                                    } elseif ($alumno) {
                                        foreach ($stt->sttAlumnos as $sttAlumno) {
                                            if ($sttAlumno->alumno_id == $alumno->id) {
                                                $canUpdate = true;
                                                break;
                                            }
                                        }
                                    } elseif ($profesor && $stt->profesor_curso_id == $profesor->id) {
                                        $canUpdate = true;
                                    }
                                    ?>
                                    
                                    <?php if ($canUpdate): ?>
                                        <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $stt->id], [
                                            'class' => 'btn btn-sm btn-outline-warning',
                                            'title' => 'Corregir STT',
                                        ]) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if ($isComision && $stt->puedeSerResuelta()): ?>
                                    <?= Html::a('<i class="bi bi-check-circle"></i>', ['/comision/review', 'id' => $stt->id], [
                                        'class' => 'btn btn-sm btn-outline-success',
                                        'title' => 'Revisar STT',
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
