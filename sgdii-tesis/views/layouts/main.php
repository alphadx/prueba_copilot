<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\models\User;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?> - SGDII</title>
    <?php $this->head() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/site.css">
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
            <strong>SGDII - Módulo Tesis</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?php
                    /** @var User $user */
                    $user = Yii::$app->user->identity;
                    $unreadCount = \app\models\Notificacion::getUnreadCount($user->id);
                    ?>
                    <li class="nav-item">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/notification/index']) ?>" 
                           class="nav-link position-relative text-white" 
                           title="Notificaciones">
                            <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                      id="notification-badge" 
                                      style="font-size: 0.7rem;">
                                    <?= $unreadCount ?>
                                </span>
                            <?php else: ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                      id="notification-badge" 
                                      style="font-size: 0.7rem; display: none;">
                                    0
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <strong><?= Html::encode($user->nombre) ?></strong>
                            <span class="badge bg-light text-primary ms-2"><?= Html::encode(ucfirst($user->rol)) ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                            . Html::submitButton(
                                'Cerrar Sesión',
                                ['class' => 'btn btn-outline-light']
                            )
                            . Html::endForm() ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main role="main" class="flex-shrink-0">
    <div class="container mt-4">
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="text-muted text-center">
            <strong>SGDII - Departamento de Ingeniería Industrial</strong> - Prototipo
            <br>
            <small>&copy; <?= date('Y') ?> - Sistema de Gestión de Tesis</small>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $this->endPage() ?>
