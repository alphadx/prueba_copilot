<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-sm mt-5">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Por favor ingrese sus credenciales:</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-3',
                                'wrapper' => 'col-sm-9',
                                'error' => '',
                                'hint' => '',
                            ],
                        ],
                    ]); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                        <?= $form->field($model, 'password')->passwordInput() ?>

                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'template' => "<div class=\"offset-sm-3 col-sm-9\">{input} {label}</div>\n<div class=\"col-sm-12\">{error}</div>",
                        ]) ?>

                        <div class="form-group">
                            <div class="offset-sm-3 col-sm-9">
                                <?= Html::submitButton('Iniciar Sesión', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                            </div>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>
                <div class="card-footer text-muted">
                    <small>Usuario de prueba: <strong>admin</strong> / Contraseña: <strong>admin123</strong></small>
                </div>
            </div>
        </div>
    </div>
</div>
