<?php
// YOUR_APP/views/upload/lists/_list_item.php

use yii\helpers\Html;
?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h5><?= Html::encode($model['id']); ?></h5>
    <h6>
        <?= $model['title'] ?> <?= $model['link'] ?>  <?= $model['link1'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30"> <?= $model['buttonC'] ?> <?= $model['buttonR'] ?> <?= $model['buttonU'] ?> <?= $model['buttonD'] ?>
    </h6>
</article>

