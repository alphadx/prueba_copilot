<?php

/** @var yii\web\View $this */
/** @var app\models\Profesor $profesor */
/** @var app\models\Tesis[] $tesisComoGuia */
/** @var app\models\Tesis[] $tesisComoRevisor1 */
/** @var app\models\Tesis[] $tesisComoRevisor2 */

use yii\bootstrap5\Html;

$totalTesis = count($tesisComoGuia) + count($tesisComoRevisor1) + count($tesisComoRevisor2);
?>

<div class="profesor-theses-summary">
    <div class="alert alert-info">
        <strong><?= Html::encode($profesor->nombre) ?></strong> tiene actualmente 
        <strong><?= $totalTesis ?></strong> tesis vigente(s).
    </div>

    <?php if ($totalTesis === 0): ?>
        <p class="text-muted">Este profesor no tiene tesis vigentes en este momento.</p>
    <?php else: ?>
        
        <?php if (count($tesisComoGuia) > 0): ?>
            <h6 class="mt-3"><i class="bi bi-person-check"></i> Como Profesor Guía (<?= count($tesisComoGuia) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Correlativo</th>
                            <th>Título</th>
                            <th>Alumnos</th>
                            <th>Estado</th>
                            <th>Etapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tesisComoGuia as $tesis): ?>
                            <tr>
                                <td><small><?= Html::encode($tesis->stt->correlativo) ?></small></td>
                                <td><small><?= Html::encode(mb_strimwidth($tesis->stt->titulo, 0, 40, '...')) ?></small></td>
                                <td>
                                    <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                        <small class="d-block"><?= Html::encode($alumno->nombre) ?></small>
                                    <?php endforeach; ?>
                                </td>
                                <td><small><?= Html::encode($tesis->estado) ?></small></td>
                                <td><small><?= $tesis->etapa_actual ?>/<?= $tesis->total_etapas ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (count($tesisComoRevisor1) > 0): ?>
            <h6 class="mt-3"><i class="bi bi-person"></i> Como Revisor 1 (<?= count($tesisComoRevisor1) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Correlativo</th>
                            <th>Título</th>
                            <th>Alumnos</th>
                            <th>Estado</th>
                            <th>Etapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tesisComoRevisor1 as $tesis): ?>
                            <tr>
                                <td><small><?= Html::encode($tesis->stt->correlativo) ?></small></td>
                                <td><small><?= Html::encode(mb_strimwidth($tesis->stt->titulo, 0, 40, '...')) ?></small></td>
                                <td>
                                    <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                        <small class="d-block"><?= Html::encode($alumno->nombre) ?></small>
                                    <?php endforeach; ?>
                                </td>
                                <td><small><?= Html::encode($tesis->estado) ?></small></td>
                                <td><small><?= $tesis->etapa_actual ?>/<?= $tesis->total_etapas ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (count($tesisComoRevisor2) > 0): ?>
            <h6 class="mt-3"><i class="bi bi-person"></i> Como Revisor 2 (<?= count($tesisComoRevisor2) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Correlativo</th>
                            <th>Título</th>
                            <th>Alumnos</th>
                            <th>Estado</th>
                            <th>Etapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tesisComoRevisor2 as $tesis): ?>
                            <tr>
                                <td><small><?= Html::encode($tesis->stt->correlativo) ?></small></td>
                                <td><small><?= Html::encode(mb_strimwidth($tesis->stt->titulo, 0, 40, '...')) ?></small></td>
                                <td>
                                    <?php foreach ($tesis->stt->alumnos as $alumno): ?>
                                        <small class="d-block"><?= Html::encode($alumno->nombre) ?></small>
                                    <?php endforeach; ?>
                                </td>
                                <td><small><?= Html::encode($tesis->estado) ?></small></td>
                                <td><small><?= $tesis->etapa_actual ?>/<?= $tesis->total_etapas ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
