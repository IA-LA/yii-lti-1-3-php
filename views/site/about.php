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

        <?php
        // outputs the username that owns the running php/httpd process
        // (on a system with the "whoami" executable in the path)
        $output=null;
        $retval=null;
        exec('whoami', $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);

        $output = shell_exec('ls -lart');
        echo "<pre>$output</pre>";

        $output = shell_exec('ls -lart /');
        echo "<pre>$output</pre>";

        $output = shell_exec(escapeshellcmd('ls -lart uploads'));
        echo "<pre>$output</pre>";

        $output = shell_exec('ls -lart /var/www/html/ | mkdir uploads/publicacion');
        echo "<pre>$output</pre>";


        //mkdir('/var/www/html/lti/publicacion/nombreTrabajoXXX00000001', 0777, true);

        // outputs the username that owns the running php/httpd process
        // (on a system with the "whoami" executable in the path)
        $output=null;
        $retval=null;
        exec('mkdir uploads/publicacion/nombreTrabajoXXX00000001', $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);

        $output = shell_exec('unzip uploads/Plantilla ePub 1_5c4ad1844ffce90a5d17f666.zip	-d uploads/publicacion/nombreTrabajoXXX00000000/');
        echo "<pre>$output</pre>";

        ?>

        <div class="row">
            <div class="col-lg-4">
                <h2>Git Menu</h2>
                <hr/>
                <h3>Upload</h3>

                <p>Permite subir contenidos complejos, en un único archivo .zip, el cual se utilizará como plantilla de un
                    proyecto Git para trabajar de forma distribuida.</p>

                <ol>
                    <li><code>TODO</code> Publicar ZIP eContent lti/publicacion/nombreTrabajoCTU16032021/</li>
                    <li><code>TODO</code> Proyecto publicación en repo_nombreTrabajoCTU16032021.git</li>
                    <li><code>TODO</code> Proveer proyecto en EdX y Moodle</li>
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
