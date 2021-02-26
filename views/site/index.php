<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->params['yiiapp'];
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LTI Server</h1>

        <?php
        if (Yii::$app->user->isGuest) {
        ?>
            <p class="lead">Autentifícate con tu usuario para acceder al panel de Actividades.</p>

            <p><a class="btn btn-lg btn-success" href="index.php?r=site%2Flogin">Login</a></p>
        <?php
        }
        else{
        ?>
            <p class="lead">Bienvenido "<?php Yii::$app->user->name ?>" ya puedes acceder al panel de Actividades.</p>

            <p><a class="btn btn-lg btn-danger" href="index.php?r=site%2Flogout">Logout</a></p>
        <?php
        }
        ?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Upload</h2>

                <p>Permite subir contenidos en un único archivo .zip que se utilizará como plantilla para crear un nuevo
                    proyecto Git sobre el cual puede trabajarse de forma distribuida.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fupload">Upload &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Register</h2>

                <p>Formulario que permite dar de alta una Actividad (Tool) para su publicación.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fregister">Registro &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Query</h2>

                <p>Formulario de consulta de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fquery">Consulta &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
