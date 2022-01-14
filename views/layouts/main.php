<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- UPLOAD Bad Request (#400) Unable to verify your data submission.   -->
    <?= Html::csrfMetaTags() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        //'brandLabel' => Yii::$app->name,
        'brandLabel' => Yii::$app->params['yiiapp'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            ['label' => Yii::$app->user->isGuest ? ('Tools') : ('Tools (' . Yii::$app->user->identity->username . ')'),
                'items' => Yii::$app->user->isGuest ? ([]) : ([
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Upload Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Upload Zip', 'url' => ['/site/upload']],
                    ['label' => 'Upload & Register Zip as LTI Activity', 'url' => ['/site/uploadregister']],
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Publish Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Publish Git', 'url' => ['/crud/publish']],
                    ['label' => 'Publish & Register Git Project', 'url' => ['/crud/publishregister']],
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">CRUD Upload Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['/crud/register']],
                    ['label' => 'Read', 'url' => ['/crud/query']],
                    ['label' => 'Read List', 'url' => ['/crud/lists']],
                    ['label' => 'Update', 'url' => ['/crud/update']],
                    ['label' => 'Delete', 'url' => ['/crud/delete']],
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">CRUD LTI Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['crud/Lti/register']],
                    ['label' => 'Read', 'url' => ['crud/Lti/query']],
                    ['label' => 'Read List', 'url' => ['crud/Lti/lists']],
                    ['label' => 'Update', 'url' => ['crud/Lti/update']],
                    ['label' => 'Delete', 'url' => ['crud/Lti/delete']],
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">CRUD Platform Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['crud/Platform/register']],
                    ['label' => 'Read', 'url' => ['crud/Platform/query']],
                    ['label' => 'Read List', 'url' => ['crud/Platform/lists']],
                    ['label' => 'Update', 'url' => ['crud/Platform/update']],
                    ['label' => 'Delete', 'url' => ['crud/Platform/delete']],
                    '</ul>',
                ]),
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ),

        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->params['yiiname'] ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
