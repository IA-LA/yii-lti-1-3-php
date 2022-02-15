<?php
// YOUR_APP/views/upload/lists/_list_item.php

use yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h4><?= Html::encode($model['id']); ?></h4>
    <h5>
        <?= $model['title'] ?> <?= $model['link'] ?>  <?= $model['link1'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30"> <?= $model['buttonC'] ?> <?= $model['buttonR'] ?> <?= $model['buttonU'] ?> <?= $model['buttonD'] ?>
        <?php
            Modal::begin([
                'header' => '<h2>End Form</h2>',
                'toggleButton' => ['label' => 'End',  'class' => 'btn btn-sm btn-warning'],
            ]);

            echo '<form action="index.php?r=upload%2Fread" method="post">
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                    <input name="id" value="' . $model['id'] . '">
                    <button type="submit" class="btn btn-md btn-info">Read&nbsp;&nbsp;</button>
                  </form>';

            Modal::end();
        ?>
    </h5>
</article>

