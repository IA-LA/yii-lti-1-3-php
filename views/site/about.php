<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

<!--
    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code>
-->
    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Git Menu</h2>
                <hr/>
                <h3>Upload</h3>

                <p>Permite subir contenidos complejos, en un único archivo .zip sin espacios en blanco, eñes o tildes en
                    el nombre, el cual se utilizará como plantilla para publicarlo en una URL, registrarlo en el Servidor
                    LTI y como base de un proyecto Git que permita trabajar en él de forma distribuida.</p>

                <ol>
                    <li><pre>DONE</pre> Publicar ZIP eContent uploads/publicacion/idDateTimea/</li>
                    <li><code>TODO</code> Registrar URL publicación eContent en Servidor LTI</li>
                    <li><code>TODO</code> Proyectar en Git la Actividad registrada mesiante repo_idDateTimea.git</li>
                    <li><code>TODO</code> Proveer la Actividad registrada en EdX y Moodle</li>
                </ol>

                <p><a class="btn btn-default" href="index.php?r=site%2Fupload">Upload &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>LTI Menu</h2>
                <hr/>
                <h3>Delete</h3>

                <p>Formulario de borrado de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fdelete">Borrado &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Lists</h3>

                <p>Formulario de listados de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Flists">Listados &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Query</h3>

                <p>Formulario de consulta de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fquery">Consulta &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Register</h3>

                <p>Formulario que permite dar de alta una Actividad (Tool) para su publicación.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fregister">Registro &raquo;</a></p>
            </div>
        </div>

    </div>

</div>
