<?php

/** @var yii\web\View $this */
/** @var Exception $exception */

use yii\bootstrap5\Html;

$this->title = 'Error';
?>
<div class="site-error">
    <div class="alert alert-danger">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= nl2br(Html::encode($exception->getMessage())) ?>
        </p>
    </div>
</div>
