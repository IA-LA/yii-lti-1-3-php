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
                <h2>Main Menu</h2>
                <hr/>
                <h3>Upload Activity (Upload Menu)</h3>

                <p>Permite subir y registrar contenidos complejos (eContent), en un único archivo .zip sin espacios en blanco, eñes o tildes en
                    el nombre que se utilizará como plantilla para publicarlo en una URL, registrarlo en el Servidor
                    LTI y como base de un proyecto Git que permita trabajar en él de forma distribuida.</p>

                <p><a class="btn btn-default" href="index.php?r=upload%2Fupload">Upload &raquo;</a></p>
                <p><a class="btn btn-default" href="index.php?r=upload%2Fuploadregister">Upload & Register &raquo;</a></p>

                <hr/>
                <h3>Publish Project (Git Menu)</h3>

                <p>Permite publicar y registrar el proyecto Git de contenidos complejos, con un id sin espacios en blanco, eñes o tildes en
                    el nombre, para trabajar en él de forma distribuida.</p>

                <p><a class="btn btn-default" href="index.php?r=upload%2Fpublish">Publish &raquo;</a></p>
                <p><a class="btn btn-default" href="index.php?r=upload%2Fpublishregister">Publish & Register &raquo;</a></p>

            </div>

            <div class="col-lg-4">
                <h2>LTI Menu</h2>
                <hr/>
            </div>
            <div class="col-lg-4">
                <h3>Lists</h3>

                <p>Formulario de listados de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=lti%2Flists">Listados &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Create</h3>

                <p>Formulario que permite dar de alta una Actividad (Tool) para su difusión.</p>

                <p><a class="btn btn-default" href="index.php?r=lti%2Fcreate">Registro &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Read</h3>

                <p>Formulario de consulta de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=lti%2Fread">Consulta &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Update</h3>

                <p>Formulario que permite actualizar Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=lti%2Fupdate">Registro &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h3>Delete</h3>

                <p>Formulario de borrado de Actividades (Tools) dadas de alta en el servior.</p>

                <p><a class="btn btn-default" href="index.php?r=lti%2Fdelete">Borrado &raquo;</a></p>
            </div>
        </div>

        <div class="col-lg-4">
            <h2>Platform Menu</h2>
            <hr/>
        </div>
        <div class="col-lg-4">
            <h3>Lists</h3>

            <p>Formulario de listados de Plataformas (LMSs) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=platform%2Flists">Listados &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Create</h3>

            <p>Formulario que permite dar de alta una Plataforma (LMS) para su difusión.</p>

            <p><a class="btn btn-default" href="index.php?r=platform%2Fcreate">Registro &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Read</h3>

            <p>Formulario de consulta de Plataformas (LMSs) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=platform%2Fread">Consulta &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Update</h3>

            <p>Formulario que permite actualizar Plataformas (LMSs) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=platform%2Fupdate">Registro &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Delete</h3>

            <p>Formulario de borrado de Plataformas (LMSs) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=platform%2Fdelete">Borrado &raquo;</a></p>
        </div>

        <div class="col-lg-4">
            <h2>Upload Menu</h2>
            <hr/>
        </div>
        <div class="col-lg-4">
            <h3>Lists</h3>

            <p>Formulario de listados de Proyectos (Git) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=upload%2Flists">Listados &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Create</h3>

            <p>Formulario que permite dar de alta un Proyecto (Git) para su gestión.</p>

            <p><a class="btn btn-default" href="index.php?r=crud%2Fcreate">Registro &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Read</h3>

            <p>Formulario de consulta de Proyectos (Git) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=upload%2Fread">Consulta &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Update</h3>

            <p>Formulario que permite actualizar Proyectos (Git) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=upload%2Fupdate">Borrado &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Delete</h3>

            <p>Formulario de borrado de Proyectos (Git) dadas de alta en el servior.</p>

            <p><a class="btn btn-default" href="index.php?r=upload%2Fdelete">Borrado &raquo;</a></p>
        </div>
    </div>
</div>
<div>
    <var>
        <h2><a href="#todo">TODO & DONE</a></h2>
        <ol>
            <b>CLIENTE SERVIDOR LTI</b>
            <li><del><samp>DONE</samp></del> Subir a `uploads/` y publicar .zip eContent URL `publicacion/IdDateTimea/`</li>
            <li><del><samp>DONE</samp></del> Crear Proyecto Git la Actividad (eContrent) como `git/IdDateTimea.git`</li>
            <li><del><samp>DONE</samp></del> Almcenar los Uploads de .zip, publicación y proyecto Git en la colección `Uploads`de la BBDD.</li>
            <li><del><samp>DONE</samp></del> Almcenar las Actividades publicadas en la colección `Ltis`de la BBDD.</li>
            <li><del><samp>DONE</samp></del> Registrar automáticamente ID + URL de la Actividad LTI y mo LTI (eContent) publicada en el Servidor LTI</li>
            <li><del><samp>DONE</samp></del> Registrar automáticamente ZIP + URL y Proyecto Git de un Upload en el lServidor LTI</li>
            <li><del><samp>DONE</samp></del> Platform Menu</li>
            <li><del><samp>DONE</samp></del> Update Form (Git, Lti y Platform)</li>
            <li><del><samp>DONE</samp></del> List con opciones CRUD</li>
            <li><del><samp>DONE</samp></del> List de Upload con opciones de Publish</li>

            <b>PLATAFORMAS LMS</b>
            <li><del><samp>DONE</samp></del> Proveer <a href="http://ailanto-dev.intecca.uned.es:9002/login.php?iss=10120210421153903000000a&login_hint=123456&target_link_uri=https://ailanto-dev.intecca.uned.es/lti/publicacion/10120210421153903000000a&lti_message_hint=123456" target="_blank">una Actividad <b>HTTP</b> no LTI</a> (<a href="https://ailanto-dev.intecca.uned.es/lti/publicacion/10120210421153903000000a" target="_blank">eContent</a>) en entornos LMS (<a href="http://ailanto-dev.intecca.uned.es:8080/courses/course-v1:edX+DemoX+Demo_Course/courseware/17dbb7a0acaa453e9811b67028973ca0/48f40f1eda24409384eb6a1415536c01/1?activate_block_id=block-v1%3AedX%2BDemoX%2BDemo_Course%2Btype%40vertical%2Bblock%407a2650b00f89411792c3ab18fa631aa5" target="_blank">EdX</a>, <a href="https://ailanto-dev.intecca.uned.es/eTrivial/course/view.php?id=52#section-0" target="_blank">Moodle</a>, etc)</li>
            <li><code>TODO</code> Proveer una Actividad <b>HTTPS</b> no LTI (eContent) en entornos LMS (<a href="http://ailanto-dev.intecca.uned.es:8080" target="_blank">EDX</a>, <a href="https://ailanto-dev.intecca.uned.es/eTrivial/" target="_blank">Moodle</a>, etc)</li>
            <li><del><samp>DONE</samp></del> Proveer un <a href="https://lti-ri.imsglobal.org/platforms/2630/resource_links/62041" target="_blank">Recurso LTI 1.3 (LtiResourceLinkRequest)</a> <a href="https://lti-ri.imsglobal.org/platforms/2630/resource_links/62041/rosters" target="_blank">vía OIDC</a> en plataformas <a href="https://lti-ri.imsglobal.org/platforms/2630" target="_blank">IMS Global</a> simuladas</li>
            <li><code>TODO</code> Proveer un Recurso LTI 1.3 (LtiResourceLinkRequest) <a href="">vía OIDC</a> en plataformas <a href="http://ailanto-dev.intecca.uned.es:8080" target="_blank">EDX</a>, etc reales</li>
            <li><code>TODO</code> Paso mensajes LTI Advantage (DeepLinkingRequest DL) en plataformas <a href="https://lti-ri.imsglobal.org/platforms/2630" target="_blank">IMS Global</a> simuladas</li>
            <li><code>TODO</code> Paso mensajes LTI Advantage (DeepLinkingRequest DL) en plataformas <a href="http://ailanto-dev.intecca.uned.es:8080" target="_blank">EdX</a>, etc reales</li>
            <li><code>TODO</code> Paso mensajes LTI Advantage (AGS) en plataformas <a href="http://ailanto-dev.intecca.uned.es:8080" target="_blank">EdX</a>, etc reales</li>
            <li><code>TODO</code> Paso mensajes LTI Advantage (NPRS) en plataformas <a href="http://ailanto-dev.intecca.uned.es:8080" target="_blank">EdX</a>, etc reales</li>
        </ol>
    </var>
</div>
