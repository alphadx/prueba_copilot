<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \app\models\Notificacion $notificacion */
/** @var \app\models\User $user */

$this->title = $notificacion->tipo;
?>

<h2 style="color: #0d6efd; margin-top: 0;">Estimado/a <?= Html::encode($user->nombre) ?>,</h2>

<div style="margin: 20px 0;">
    <p><?= nl2br(Html::encode($notificacion->contenido)) ?></p>
</div>

<?php if ($notificacion->stt): ?>
<div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin: 20px 0;">
    <strong>Detalles de la Solicitud:</strong><br>
    <ul style="margin: 10px 0;">
        <li><strong>Correlativo:</strong> <?= Html::encode($notificacion->stt->correlativo) ?></li>
        <li><strong>Título:</strong> <?= Html::encode($notificacion->stt->titulo) ?></li>
        <li><strong>Estado:</strong> <?= Html::encode($notificacion->stt->estado) ?></li>
    </ul>
</div>
<?php endif; ?>

<p style="margin-top: 30px;">
    Para más información, por favor ingrese al sistema:
    <br>
    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/site/index']) ?>" 
       style="display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 5px;">
        Acceder al Sistema
    </a>
</p>

<p style="color: #6c757d; font-size: 14px; margin-top: 20px;">
    <em>Fecha de notificación: <?= Yii::$app->formatter->asDatetime($notificacion->created_at, 'long') ?></em>
</p>
