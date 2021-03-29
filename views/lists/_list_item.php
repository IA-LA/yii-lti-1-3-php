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

<div id=“resp-table”>
    <div id=“resp-table-caption”>
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

    <div id=“resp-table-body”>
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

    <div id=“resp-table-footer”>
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
