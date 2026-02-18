<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SttForm $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\Origen[] $origenes */
/** @var app\models\Modalidad[] $modalidades */
/** @var app\models\Profesor[] $profesores */
/** @var app\models\Alumno[] $alumnos */
/** @var app\models\CarreraMalla[] $carreras */
/** @var app\models\Empresa[] $empresas */

$this->title = 'Crear Solicitud de Tema de Tesis';
$this->params['breadcrumbs'][] = ['label' => 'Solicitudes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="stt-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="stt-form">
        <?php $form = ActiveForm::begin(['id' => 'stt-form']); ?>

        <div class="card mb-3">
            <div class="card-header">
                <h5>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'origen_id')->dropDownList(
                            ArrayHelper::map($origenes, 'id', 'nombre'),
                            ['prompt' => 'Seleccione...']
                        ) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'profesor_curso_id')->dropDownList(
                            ArrayHelper::map($profesores, 'id', 'nombre'),
                            ['prompt' => 'Seleccione...']
                        ) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'nota')->textInput([
                            'type' => 'number',
                            'step' => '0.1',
                            'min' => '1.0',
                            'max' => '7.0'
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'modalidad_id')->dropDownList(
                            ArrayHelper::map($modalidades, 'id', 'nombre'),
                            ['prompt' => 'Seleccione...', 'id' => 'modalidad-select']
                        ) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'titulo')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5>Alumnos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'alumno_1_id')->dropDownList(
                            ArrayHelper::map($alumnos, 'id', function($alumno) {
                                return $alumno->nombre . ' (' . $alumno->rut . ')';
                            }),
                            ['prompt' => 'Seleccione...']
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'carrera_1_id')->dropDownList(
                            ArrayHelper::map($carreras, 'id', function($carrera) {
                                return $carrera->nombre . ' (' . $carrera->codigo . ')';
                            }),
                            ['prompt' => 'Seleccione...']
                        ) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'alumno_2_id')->dropDownList(
                            ArrayHelper::map($alumnos, 'id', function($alumno) {
                                return $alumno->nombre . ' (' . $alumno->rut . ')';
                            }),
                            ['prompt' => 'Seleccione (Opcional)...']
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'carrera_2_id')->dropDownList(
                            ArrayHelper::map($carreras, 'id', function($carrera) {
                                return $carrera->nombre . ' (' . $carrera->codigo . ')';
                            }),
                            ['prompt' => 'Seleccione...']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5>Profesores Propuestos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'profesor_guia_propuesto_id')->dropDownList(
                            ArrayHelper::map($profesores, 'id', 'nombre'),
                            ['prompt' => 'Seleccione (Opcional)...', 'class' => 'form-control profesor-field']
                        )->label('Profesor Guía Propuesto <span class="text-danger guia-required" style="display:none;">*</span>') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'profesor_revisor1_propuesto_id')->dropDownList(
                            ArrayHelper::map($profesores, 'id', 'nombre'),
                            ['prompt' => 'Seleccione (Opcional)...', 'class' => 'form-control profesor-field']
                        ) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'profesor_revisor2_propuesto_id')->dropDownList(
                            ArrayHelper::map($profesores, 'id', 'nombre'),
                            ['prompt' => 'Seleccione (Opcional)...', 'class' => 'form-control profesor-field']
                        ) ?>
                    </div>
                </div>
                <p class="text-muted profesor-note">
                    <small>* Para modalidad <strong>TT</strong>: Los profesores son opcionales.</small><br>
                    <small>* Para modalidad <strong>Papers</strong>: El profesor guía es obligatorio.</small><br>
                    <small>* Para modalidad <strong>Pasantía</strong>: El profesor guía es obligatorio.</small>
                </p>
            </div>
        </div>

        <div class="card mb-3" id="empresa-section" style="display: none;">
            <div class="card-header">
                <h5>Información de Empresa (Pasantía)</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <?= $form->field($model, 'empresa_id')->dropDownList(
                            ArrayHelper::map($empresas, 'id', function($empresa) {
                                return $empresa->nombre . ' (' . $empresa->rut . ')';
                            }),
                            ['prompt' => 'Seleccione empresa existente o ingrese nueva...', 'id' => 'empresa-select']
                        ) ?>
                        <p class="text-muted"><small>Si la empresa no está en la lista, complete los campos a continuación:</small></p>
                    </div>
                </div>

                <div id="nueva-empresa-fields">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'empresa_rut')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'empresa_nombre')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <h6 class="mt-3">Información del Supervisor</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'empresa_supervisor_rut')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'empresa_supervisor_nombre')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'empresa_supervisor_cargo')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'empresa_supervisor_correo')->textInput(['maxlength' => true, 'type' => 'email']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'empresa_supervisor_telefono')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Crear Solicitud', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancelar', ['site/index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$modalidadesData = [];
foreach ($modalidades as $modalidad) {
    $modalidadesData[$modalidad->id] = $modalidad->nombre;
}
$this->registerJs(<<<JS
    // Get modalidad names from PHP
    var modalidades = <?= Json::htmlEncode($modalidadesData) ?>;
    
    // Function to update form based on modalidad
    function updateFormByModalidad() {
        var modalidadId = $('#modalidad-select').val();
        var modalidadName = modalidades[modalidadId];
        
        if (modalidadName === 'Pasantía') {
            $('#empresa-section').show();
            $('.guia-required').show();
        } else {
            $('#empresa-section').hide();
            $('.guia-required').hide();
        }
        
        if (modalidadName === 'Papers' || modalidadName === 'Pasantía') {
            $('.guia-required').show();
        } else if (modalidadName === 'TT') {
            $('.guia-required').hide();
        }
    }
    
    // Trigger on modalidad change
    $('#modalidad-select').on('change', updateFormByModalidad);
    
    // Trigger on empresa select change
    $('#empresa-select').on('change', function() {
        var empresaId = $(this).val();
        if (empresaId) {
            // Hide nueva empresa fields if existing empresa is selected
            $('#nueva-empresa-fields').hide();
        } else {
            // Show nueva empresa fields if no empresa is selected
            $('#nueva-empresa-fields').show();
        }
    });
    
    // Initialize on page load
    updateFormByModalidad();
JS
);
?>
