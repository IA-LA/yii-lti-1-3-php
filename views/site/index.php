<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->params['yiiapp'];

//TODO mostar listados con Yii ListView

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LTI Server</h1>

        <?php
        if (Yii::$app->user->isGuest) {
        ?>
            <p class="lead">Autentifícate para acceder al panel de Actividades (Tools).</p>

            <p><a class="btn btn-lg btn-success" href="index.php?r=site%2Flogin">Login</a></p>
        <?php
        }
        else{
        ?>
            <p class="lead">Bienvenido <?= ' "<i>' . Yii::$app->user->identity->username . '</i>" ' ?>, accede al panel de Actividades (Tools).</p>

            <form action="/index.php?r=site%2Flogout" method="post">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                <button type="submit" class="btn btn-lg btn-danger">Logout <?= '(' . Yii::$app->user->identity->username . ')'?></button>
            </form>
            <!--
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>"><button type="submit" class="btn btn-link logout">Logout (admin)</button>
            <p><a class="btn btn-lg btn-danger" href="index.php?r=site%2Flogin">Logout</a></p>
            -->
        <?php
        }
        ?>
    </div>

    <div class="body-content">

        <div class="row">

        </div>

    </div>
</div>
