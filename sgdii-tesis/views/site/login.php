<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Iniciar Sesión';
?>
<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Iniciar Sesión</h3>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'col-form-label'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Usuario'
                    ])->label('Usuario') ?>

                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Contraseña'
                    ])->label('Contraseña') ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
                    ])->label('Recordarme') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Ingresar', [
                            'class' => 'btn btn-primary w-100',
                            'name' => 'login-button'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Credenciales de prueba -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Credenciales de Prueba</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Contraseña</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>admin</code></td>
                                    <td><code>admin123</code></td>
                                    <td><span class="badge bg-danger">Admin</span></td>
                                </tr>
                                <tr>
                                    <td><code>prof.martinez</code></td>
                                    <td><code>prof123</code></td>
                                    <td><span class="badge bg-success">Profesor</span></td>
                                </tr>
                                <tr>
                                    <td><code>prof.gonzalez</code></td>
                                    <td><code>prof123</code></td>
                                    <td><span class="badge bg-success">Profesor</span></td>
                                </tr>
                                <tr>
                                    <td><code>prof.silva</code></td>
                                    <td><code>prof123</code></td>
                                    <td><span class="badge bg-warning">Comisión</span></td>
                                </tr>
                                <tr>
                                    <td><code>alumno.perez</code></td>
                                    <td><code>alumno123</code></td>
                                    <td><span class="badge bg-primary">Alumno</span></td>
                                </tr>
                                <tr>
                                    <td><code>alumno.rojas</code></td>
                                    <td><code>alumno123</code></td>
                                    <td><span class="badge bg-primary">Alumno</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
