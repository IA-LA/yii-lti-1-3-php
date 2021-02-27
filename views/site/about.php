<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

<!--
    <code><?= __FILE__ ?></code>
-->
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
