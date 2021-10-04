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
        <h2>TODO</h2>
        <ol>
            <li><code>DONE</code> Subir a `uploads/` y publicar .zip eContent URL `publicacion/IdDateTimea/`</li>
            <li><code>DONE</code> Crear Proyecto Git la Actividad (eContrent) como `git/IdDateTimea.git`</li>
            <li><code>DONE</code> Almcenar los Uploads de .zip, publicación y proyecto Git en la colección `Uploads`de la BBDD.</li>
            <li><code>DONE</code> Almcenar las Actividades publicadas en la colección `Ltis`de la BBDD.</li>
            <li><code>DONE</code> Registrar automáticamente ID + URL de la Actividad (eContent) publicada en el lServidor LTI</li>
            <li><code>DONE</code> Registrar automáticamente ZIP + URL y Proyecto Git de la Upload en el lServidor LTI</li>
            <li><code>TODO</code> Proveer la Actividad (eContent) en entornos LMS (EdX, Moodle, etc)</li>
            <li><code>TODO</code> Proveer el Recurso (LtiResourceLinkRequest) en entornos <a href="https://lti-ri.imsglobal.org/platforms">IMS Global</a> (simulación)</li>
        </ol>
        <div class="row">
            <div class="col-lg-4">
                <h2>Main Menu</h2>
                <hr/>
                <h3>Upload Activity</h3>

                <p>Permite subir y registrar contenidos complejos, en un único archivo .zip sin espacios en blanco, eñes o tildes en
                    el nombre, el cual se utilizará como plantilla para publicarlo en una URL, registrarlo en el Servidor
                    LTI y como base de un proyecto Git que permita trabajar en él de forma distribuida.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fupload">Upload &raquo;</a></p>
                <p><a class="btn btn-default" href="index.php?r=site%2Fuploadregister">Upload & Register &raquo;</a></p>

                <hr/>
                <h3>Publish Project</h3>

                <p>Permite publicar y actualizar contenidos complejos, con un id sin espacios en blanco, eñes o tildes en
                    el nombre, el cual se utilizará como plantilla para publicarlo en una URL, registrarlo en el Servidor
                    LTI y como base de un proyecto Git que permita trabajar en él de forma distribuida.</p>

                <p><a class="btn btn-default" href="index.php?r=crud%2Fpublish">Publish &raquo;</a></p>
                <p><a class="btn btn-default" href="index.php?r=crud%2Fpublishregister">Publish & Register &raquo;</a></p>

            </div>
            <div class="col-lg-4">
                <h2>Git Menu</h2>
                <hr/>
                <h3>Delete</h3>

                <p>Formulario de borrado de Proyectos (Git) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=crud%2Fdelete">Borrado &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Lists</h3>

                <p>Formulario de listados de Proyectos (Git) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=crud%2Flists">Listados &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Query</h3>

                <p>Formulario de consulta de Proyectos (Git) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=crud%2Fquery">Consulta &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Register</h3>

                <p>Formulario que permite dar de alta un Proyecto (Git) para su gestión.</p>

                <p><a class="btn btn-default" href="index.php?r=crud%2Fregister">Registro &raquo;</a></p>
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

                <p>Formulario que permite dar de alta una Actividad (Tool) para su difusión.</p>

                <p><a class="btn btn-default" href="index.php?r=site%2Fregister">Registro &raquo;</a></p>
            </div>
        </div>

    </div>

</div>
