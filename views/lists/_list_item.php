<?php
// YOUR_APP/views/lists/_list_item.php

use yii\helpers\Html;
?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h4><?= Html::encode($model['id']); ?></h4>
    <h5>
        <?= $model['title'] ?> <?= $model['link'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30">
    </h5>
</article>

