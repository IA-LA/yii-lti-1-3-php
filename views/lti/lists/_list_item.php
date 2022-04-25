<?php
// YOUR_APP/views/lti/lists/_list_item.php

//use app\models\Upload\crud\ReadForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

// Remember current URL
Url::remember();

?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h4><?= Html::encode($model['id']); ?></h4>
    <h5>
        <?= $model['title'] ?> <?= $model['link'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30"> <?= $model['buttonC'] ?>
        <!-- <?= $model['buttonR'] ?> -->
        <?php
        // Modal READ
        //$modelR = new ReadForm();
        Modal::begin([
            'headerOptions' => ['id' => 'modalHeader'],
            'header' => '<h2>'. $model['data']['_id'] . '</h2>',
            'toggleButton' => ['label' => 'Read&nbsp;&nbsp;',  'class' => 'btn btn-md btn-info'],
            'id' => 'modal-r' . $model['data']['_id'],
            //'size' => 'modal-lg',
            //keeps from closing modal with esc key or by clicking out of the modal.
            // user must click cancel or X to close
            //'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
        ]);
        // Reemplaza & y evita que el parámetro `&lti=` sea transformado en `<i=`
        //$model['data']['launch_url'] = str_replace('&', '%26', $model['data']['launch_url']);
        echo    '<div id="modalContent"></div>
                    <p>
                        Consulta la información de una Actividad LTI registrada.
                        Thank you.
                    </p>
                    <pre>' .
                    // Evita que el parámetro `&lti=` sea transformado en `<i=`
                    Html::encode(print_r($model['data'], true)) .
                    '</pre>';
        //$this->render('//upload/crud/read',['model' => $modelR, 'id' => '*']);
        Modal::end();
        ?>
        <?= $model['buttonU'] ?> <?= $model['buttonD'] ?>
    </h5>
</article>

