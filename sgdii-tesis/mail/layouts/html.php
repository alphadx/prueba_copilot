<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \yii\mail\MessageInterface $message */
/** @var string $content */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .email-header {
            background-color: #0d6efd;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 30px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="email-container">
        <div class="email-header">
            <h1 style="margin: 0;">SGDII - Módulo Tesis</h1>
        </div>
        <div class="email-body">
            <?= $content ?>
        </div>
        <div class="email-footer">
            <p style="margin: 0;">
                Sistema de Gestión Departamento Ingeniería Industrial<br>
                &copy; <?= date('Y') ?> - Este es un correo automático, por favor no responder.
            </p>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
