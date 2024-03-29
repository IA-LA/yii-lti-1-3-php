<?php
// YOUR_APP/views/lti/lists/index.php

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Lists LTI Activity';
$this->params['breadcrumbs'][] = $this->title;

// Remember current URL
Url::remember();

?>
<div class="header">
    <h3><?= Html::encode($title); ?> Actividades LTI</h3>
</div>
<div class="row">
    <h4><pre>ID</pre></h4>
    <h5><pre>User-Fecha                 Actividad Launch (Links)                    Acción</pre></h5>
    <?= ListView::widget([
        'options' => [
            'tag' => 'div',
        ],
        'dataProvider' => $listDataProvider,
        'itemView' => function ($model, $key, $index, $widget) {
            $itemContent = $this->render('_list_item',['model' => $model]);

            /* Display an Advertisement after the first list item */
            if ($index == 0) {
                $adContent = $this->render('_ad');
                $itemContent .= $adContent;
            }

            return $itemContent;

            /* Or if you just want to display the list item only: */
            // return $this->render('_list_item',['model' => $model]);
        },
        'itemOptions' => [
            'tag' => false,
        ],
        'summary' => '',

        /* do not display {summary} */
        'layout' => '{items}{pager}',

        'pager' => [
            'firstPageLabel' => 'First',
            'lastPageLabel' => 'Last',
            'maxButtonCount' => 4,
            'options' => [
                'class' => 'pagination col-xs-12'
            ]
        ],

    ]);
    ?>
</div>
<hr/>
<p><a class="btn btn-lg btn-warning" href="index.php?r=<?= strtolower(Html::encode($controller)); ?>%2F<?= Html::encode($back); ?>">Atrás</a></p>
<div class="footer">
</div>

