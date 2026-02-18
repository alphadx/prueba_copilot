<?php

/** @var yii\web\View $this */
/** @var app\models\SolicitudTemaTesis[] $solicitudes */
/** @var app\models\Modalidad[] $modalidades */
/** @var app\models\Profesor[] $profesores */
/** @var array $estados */
/** @var array $filters */

use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

$this->title = 'Gestión de Solicitudes de Tema de Tesis (STT)';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comision-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver al Dashboard', ['/site/index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros Avanzados</h5>
        </div>
        <div class="card-body">
            <?php $form = \yii\bootstrap5\ActiveForm::begin([
                'method' => 'get',
                'action' => ['index'],
            ]); ?>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="modalidad_id">Modalidad</label>
                    <?= Html::dropDownList(
                        'modalidad_id',
                        $filters['modalidad_id'],
                        ArrayHelper::map($modalidades, 'id', 'nombre'),
                        ['class' => 'form-select', 'prompt' => 'Todas las modalidades']
                    ) ?>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="estado">Estado</label>
                    <?= Html::dropDownList(
                        'estado',
                        $filters['estado'],
                        $estados,
                        ['class' => 'form-select', 'prompt' => 'Todos los estados']
                    ) ?>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="fecha_desde">Fecha Desde</label>
                    <?= Html::input('date', 'fecha_desde', $filters['fecha_desde'], ['class' => 'form-control']) ?>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="fecha_hasta">Fecha Hasta</label>
                    <?= Html::input('date', 'fecha_hasta', $filters['fecha_hasta'], ['class' => 'form-control']) ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="profesor_guia_id">Profesor Guía</label>
                    <?= Html::dropDownList(
                        'profesor_guia_id',
                        $filters['profesor_guia_id'],
                        ArrayHelper::map($profesores, 'id', 'nombre'),
                        ['class' => 'form-select', 'prompt' => 'Todos los profesores']
                    ) ?>
                </div>
                
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <?= Html::submitButton('<i class="bi bi-search"></i> Buscar', ['class' => 'btn btn-primary me-2']) ?>
                    <?= Html::a('<i class="bi bi-x-circle"></i> Limpiar', ['index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
            
            <?php \yii\bootstrap5\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Results Section -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-check"></i> 
                Solicitudes (<?= count($solicitudes) ?> encontradas)
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($solicitudes)): ?>
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i> No se encontraron solicitudes con los filtros aplicados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Correlativo</th>
                                <th>Título</th>
                                <th>Modalidad</th>
                                <th>Alumnos</th>
                                <th>Profesor Guía</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $stt): ?>
                                <tr>
                                    <td>
                                        <strong><?= Html::encode($stt->correlativo) ?></strong>
                                    </td>
                                    <td>
                                        <?= Html::encode(mb_strimwidth($stt->titulo, 0, 50, '...')) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= Html::encode($stt->modalidad->nombre) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php foreach ($stt->alumnos as $alumno): ?>
                                            <div class="small"><?= Html::encode($alumno->nombre) ?></div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php if ($stt->profesorGuiaPropuesto): ?>
                                            <?= Html::encode($stt->profesorGuiaPropuesto->nombre) ?>
                                            <?= Html::a(
                                                '<i class="bi bi-info-circle"></i>',
                                                '#',
                                                [
                                                    'class' => 'text-primary ms-1',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#modalProfesorTheses',
                                                    'data-profesor-id' => $stt->profesorGuiaPropuesto->id,
                                                    'data-profesor-nombre' => $stt->profesorGuiaPropuesto->nombre,
                                                    'title' => 'Ver tesis vigentes',
                                                ]
                                            ) ?>
                                        <?php else: ?>
                                            <span class="text-muted">No asignado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = 'secondary';
                                        switch ($stt->estado) {
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
                                            <?= Html::encode($stt->estado) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= Yii::$app->formatter->asDate($stt->fecha_creacion, 'dd/MM/yyyy') ?></small>
                                    </td>
                                    <td>
                                        <?= Html::a(
                                            '<i class="bi bi-eye"></i> Ver',
                                            ['/stt/view', 'id' => $stt->id],
                                            ['class' => 'btn btn-sm btn-outline-primary me-1']
                                        ) ?>
                                        <?php if ($stt->puedeSerResuelta()): ?>
                                            <?= Html::a(
                                                '<i class="bi bi-check-circle"></i> Evaluar',
                                                ['review', 'id' => $stt->id],
                                                ['class' => 'btn btn-sm btn-success']
                                            ) ?>
                                        <?php endif; ?>
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

<!-- Modal for Professor Theses -->
<div class="modal fade" id="modalProfesorTheses" tabindex="-1" aria-labelledby="modalProfesorThesesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProfesorThesesLabel">
                    <i class="bi bi-person-badge"></i> Tesis Vigentes del Profesor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalProfesorThesesBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript for loading professor theses in modal
$baseUrl = \yii\helpers\Url::to(['/']);
$this->registerJs(<<<JS
    var modalProfesorTheses = document.getElementById('modalProfesorTheses');
    modalProfesorTheses.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var profesorId = button.getAttribute('data-profesor-id');
        var profesorNombre = button.getAttribute('data-profesor-nombre');
        
        var modalTitle = modalProfesorTheses.querySelector('.modal-title');
        modalTitle.innerHTML = '<i class="bi bi-person-badge"></i> Tesis Vigentes de ' + profesorNombre;
        
        var modalBody = document.getElementById('modalProfesorThesesBody');
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
        
        // Load content via AJAX
        fetch('$baseUrl' + 'comision/profesor-theses?id=' + profesorId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar los datos.</div>';
        });
    });
JS
);
?>
