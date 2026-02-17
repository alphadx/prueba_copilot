<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

use yii\bootstrap5\Html;

$this->title = 'Dashboard';
$user = Yii::$app->user->identity;
?>
<div class="site-index">
    <div class="jumbotron text-center bg-light p-5 rounded">
        <h1 class="display-4">Bienvenido al SGDII - Módulo Tesis</h1>
        <p class="lead">Hola, <strong><?= Html::encode($user->nombre) ?></strong></p>
        <p>Rol: <span class="badge bg-primary fs-5"><?= Html::encode(ucfirst($user->rol)) ?></span></p>
        <hr class="my-4">
        <p class="text-muted">Este es un prototipo funcional del módulo de gestión de Tesis del Departamento de Ingeniería Industrial</p>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Módulos Disponibles</h2>
            <p class="text-muted">Los siguientes módulos estarán disponibles próximamente:</p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-earmark-text"></i> Solicitud de Inscripción de Tema de Tesis (STT)
                    </h5>
                    <p class="card-text">Formulario para que los profesores creen solicitudes de inscripción de tema de tesis.</p>
                    <?php if (Yii::$app->user->identity->rol === 'profesor' || Yii::$app->user->identity->rol === 'admin'): ?>
                        <?= Html::a('Crear Solicitud', ['/stt/create'], ['class' => 'btn btn-primary']) ?>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Solo Profesores</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-check-circle"></i> Resolución de STT
                    </h5>
                    <p class="card-text">Evaluación y resolución de solicitudes por parte de la Comisión de Titulación.</p>
                    <?php
                    $canAccessComision = false;
                    if (Yii::$app->user->identity->rol === 'admin') {
                        $canAccessComision = true;
                    } elseif (Yii::$app->user->identity->rol === 'profesor' || Yii::$app->user->identity->rol === 'comision_evaluadora') {
                        $profesor = \app\models\Profesor::findOne(['user_id' => Yii::$app->user->id]);
                        $canAccessComision = $profesor && $profesor->es_comision_evaluadora == 1;
                    }
                    ?>
                    <?php if ($canAccessComision): ?>
                        <?= Html::a('Gestionar STT', ['/comision/index'], ['class' => 'btn btn-success']) ?>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Solo Comisión Evaluadora</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-bar-chart"></i> Reportes
                    </h5>
                    <p class="card-text">Reportes de carga académica, seguimiento de tesis y estadísticas generales.</p>
                    <button class="btn btn-secondary" disabled>Próximamente</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                <strong>Nota:</strong> Este es un prototipo en desarrollo. Próximamente se habilitarán los módulos funcionales.
            </div>
        </div>
    </div>
</div>
