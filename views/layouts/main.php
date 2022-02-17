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
                    '<li class="dropdown-header">LTI Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'List', 'url' => ['lti/lists']],
                    '<li class="dropdown-header">CRUD</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['lti/create']],
                    ['label' => 'Read', 'url' => ['lti/read']],
                    ['label' => 'Update', 'url' => ['lti/update']],
                    ['label' => 'Delete', 'url' => ['lti/delete']],
                    '</ul>',
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Platform Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'List', 'url' => ['platform/lists']],
                    '<li class="dropdown-header">CRUD</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['platform/create']],
                    ['label' => 'Read', 'url' => ['platform/read']],
                    ['label' => 'Update', 'url' => ['platform/update']],
                    ['label' => 'Delete', 'url' => ['platform/delete']],
                    '</ul>',
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Upload Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Upload Zip', 'url' => ['upload/upload']],
                    ['label' => 'Upload & Register Zip as LTI Activity', 'url' => ['upload/uploadregister']],
                    ['label' => 'Read List', 'url' => ['upload/lists']],
                    '<li class="dropdown-header">CRUD</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Create', 'url' => ['upload/create']],
                    ['label' => 'Read', 'url' => ['upload/read']],
                    ['label' => 'Update', 'url' => ['upload/update']],
                    ['label' => 'Delete', 'url' => ['upload/delete']],
                    '</ul>',
                    '</ul>',
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Publish Menu</li>',
                    '<ul style="list-style-type:none">',
                    ['label' => 'Publish Git', 'url' => ['upload/publish']],
                    ['label' => 'Publish & Register Git Project', 'url' => ['upload/publishregister']],
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
