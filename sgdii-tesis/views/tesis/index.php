<?php

/** @var yii\web\View $this */
/** @var app\models\Tesis[] $tesis */

use yii\bootstrap5\Html;

$this->title = 'Gestión de Tesis';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tesis-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <p class="lead">Gestión del flujo de trabajo de tesis: desarrollo, revisión, evaluación y finalización.</p>

    <?php if (empty($tesis)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No hay tesis asignadas en este momento.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($tesis as $t): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong><?= Html::encode($t->stt->correlativo) ?></strong>
                            <span class="badge <?= $t->getEstadoBadgeClass() ?>">
                                <?= Html::encode($t->estado) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><?= Html::encode($t->stt->titulo) ?></h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> 
                                    <?php 
                                    $alumnos = array_map(fn($a) => $a->nombre, $t->stt->alumnos);
                                    echo Html::encode(implode(', ', $alumnos));
                                    ?>
                                </small>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person-badge"></i> Guía: 
                                    <?= Html::encode($t->profesorGuia->nombre ?? 'N/A') ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-folder"></i> 
                                    <?= Html::encode($t->categoria->nombre ?? 'Sin categoría') ?>
                                </small>
                            </div>

                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar <?= $t->getPorcentaje() >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                     role="progressbar" 
                                     style="width: <?= $t->getPorcentaje() ?>%"
                                     aria-valuenow="<?= $t->getPorcentaje() ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?= $t->getPorcentaje() ?>%
                                </div>
                            </div>
                            <small class="text-muted"><?= $t->getEtapaLabel() ?></small>
                        </div>
                        <div class="card-footer">
                            <?= Html::a('<i class="bi bi-eye"></i> Ver Detalles', 
                                ['view', 'id' => $t->id], 
                                ['class' => 'btn btn-sm btn-primary']) ?>
                            
                            <?php if (Yii::$app->user->identity->rol !== 'alumno' && $t->puedeActualizar()): ?>
                                <?= Html::a('<i class="bi bi-pencil"></i> Gestionar', 
                                    ['view', 'id' => $t->id], 
                                    ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
