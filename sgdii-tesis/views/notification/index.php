<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Notificacion;

/** @var yii\web\View $this */
/** @var array $notificaciones */
/** @var string $filtro */

$this->title = 'Notificaciones';
?>

<div class="notification-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($filtro !== 'leidas'): ?>
            <?= Html::a('<i class="bi bi-check-all"></i> Marcar todas como leídas', 
                ['mark-all-as-read'], 
                [
                    'class' => 'btn btn-outline-primary',
                    'id' => 'mark-all-read-btn',
                    'data-method' => 'post',
                ]) ?>
        <?php endif; ?>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <?= Html::a('Todas', ['index', 'filtro' => 'todas'], [
                'class' => 'nav-link' . ($filtro === 'todas' ? ' active' : '')
            ]) ?>
        </li>
        <li class="nav-item">
            <?= Html::a('No leídas', ['index', 'filtro' => 'no_leidas'], [
                'class' => 'nav-link' . ($filtro === 'no_leidas' ? ' active' : '')
            ]) ?>
        </li>
        <li class="nav-item">
            <?= Html::a('Leídas', ['index', 'filtro' => 'leidas'], [
                'class' => 'nav-link' . ($filtro === 'leidas' ? ' active' : '')
            ]) ?>
        </li>
    </ul>

    <!-- Notifications List -->
    <?php if (empty($notificaciones)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No hay notificaciones para mostrar.
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <div class="list-group-item list-group-item-action <?= $notificacion->esNoLeida() ? 'border-primary' : '' ?>" 
                     data-notification-id="<?= $notificacion->id ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <?php if ($notificacion->esNoLeida()): ?>
                                    <span class="badge bg-primary me-2">Nueva</span>
                                <?php endif; ?>
                                <h5 class="mb-0">
                                    <?= Html::encode($notificacion->tipo) ?>
                                </h5>
                            </div>
                            <p class="mb-2 text-muted" style="white-space: pre-line;">
                                <?= Html::encode($notificacion->contenido) ?>
                            </p>
                            <?php if ($notificacion->stt): ?>
                                <div class="alert alert-light mb-2 py-2 px-3">
                                    <small>
                                        <strong>STT:</strong> <?= Html::encode($notificacion->stt->correlativo) ?> - 
                                        <?= Html::encode($notificacion->stt->titulo) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                <?= Yii::$app->formatter->asRelativeTime($notificacion->created_at) ?>
                            </small>
                        </div>
                        <div class="ms-3">
                            <?php if ($notificacion->esNoLeida()): ?>
                                <?= Html::a('<i class="bi bi-check"></i>', 
                                    ['mark-as-read', 'id' => $notificacion->id], 
                                    [
                                        'class' => 'btn btn-sm btn-outline-primary mark-read-btn',
                                        'title' => 'Marcar como leída',
                                        'data-method' => 'post',
                                    ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css', [
    'position' => \yii\web\View::POS_HEAD
]);

$markReadUrl = Url::to(['mark-as-read']);
$markAllReadUrl = Url::to(['mark-all-as-read']);

$js = <<<JS
$(document).on('click', '.mark-read-btn', function(e) {
    e.preventDefault();
    var link = $(this);
    var notificationItem = link.closest('.list-group-item');
    var notificationId = notificationItem.data('notification-id');
    
    $.ajax({
        url: link.attr('href'),
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update badge
                $('#notification-badge').text(response.unreadCount);
                if (response.unreadCount === 0) {
                    $('#notification-badge').hide();
                }
                // Reload page to update list
                location.reload();
            }
        }
    });
});

$(document).on('click', '#mark-all-read-btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    
    $.ajax({
        url: '$markAllReadUrl',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update badge
                $('#notification-badge').text(0).hide();
                // Reload page to update list
                location.reload();
            }
        }
    });
});
JS;

$this->registerJs($js);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
