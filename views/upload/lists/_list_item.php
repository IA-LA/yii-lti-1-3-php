<?php
// YOUR_APP/views/upload/lists/_list_item.php

use app\models\Upload\crud\ReadForm;
use yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h4><?= Html::encode($model['id']); ?></h4>
    <h5>
        <?= $model['title'] ?> <?= $model['link'] ?>  <?= $model['link1'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30"> <?= $model['buttonC'] ?> <?= $model['buttonR'] ?> <?= $model['buttonU'] ?> <?= $model['buttonD'] ?>
        <?php
        $model2 = new ReadForm();
            Modal::begin([
                'header' => '<h2>End Form</h2>',
                'toggleButton' => ['label' => 'End',  'class' => 'btn btn-sm btn-warning'],
            ]);

            echo '
        <p>
            Consulta la información de un Upload ya registrado por su Id o su Url.
            Thank you.
        </p>

        <pre>';
        print_r($model);
        echo '</pre>';

            Modal::end();
        ?>
    </h5>
</article>

