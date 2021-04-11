<?php
// YOUR_APP/views/lists/index.php

use yii\widgets\ListView;
use yii\helpers\Html;
?>
<div class="header">
    <h3><?= Html::encode($title); ?></h3>
</div>
<div class="row">
    <h5>
        TITULO    ISS        LAUNCH URL                SYMBOL    DATE
    </h5>
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
<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2F<?= Html::encode($return); ?>">Atrás</a></p>
<div class="footer">
</div>

<div class=“resp-table”>
    <div class=“resp-table-caption”>
        Responsive Table without Table tag
    </div>
    <div class=“table-header-cell”>
        Header 1
    </div>
    <div class=“table-header-cell”>
        Header 2
    </div>
    <div class=“table-header-cell”>
        Header 3
    </div>
    <div class=“table-header-cell”>
        Header 4
    </div>
    <div class=“table-header-cell”>
        Header 5
    </div>

    <div class=“resp-table-body”>
        <div class=“resp-table-row”>
            <div class=“table-body-cell”>
                Cell 1–1
            </div>
            <div class=“table-body-cell”>
                Cell 1–2
            </div>
            <div class=“table-body-cell”>
                Cell 1–3
            </div>
            <div class=“table-body-cell”>
                Cell 1–4
            </div>
            <div class=“table-body-cell”>
                Cell 1–5
            </div>
        </div>
    </div>

    <div class=“resp-table-footer”>
        <div class=“table-footer-cell”>
            Footer 1
        </div>
        <div class=“table-footer-cell”>
            Footer 2
        </div>
        <div class=“table-footer-cell”>
            Footer 3
        </div>
        <div class=“table-footer-cell”>
            Footer 4
        </div>
        <div class=“table-footer-cell”>
            Footer 5
        </div>
    </div>
</div>

