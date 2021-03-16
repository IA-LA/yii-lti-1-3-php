<?php
// YOUR_APP/views/lists/_list_item.php

use yii\helpers\Html;
?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h3><?= Html::encode($model['_id']); ?></h3>
    <figure>
        <?= $model['title'] ?> <?= $model['link'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30">
    </figure>
</article>
