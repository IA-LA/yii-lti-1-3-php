<?php
// YOUR_APP/views/list/_list_item.php

use yii\helpers\Html;
?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h3><?= Html::encode($model['title']); ?></h3>
    <figure>
        <?= $model['title'] ?> <?= $model['link'] ?>
    </figure>
</article>
