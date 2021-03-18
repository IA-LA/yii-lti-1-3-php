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

            <?php
            // outputs the username that owns the running php/httpd process
            // (on a system with the "whoami" executable in the path)
            $output=null;
            $retval=null;
            exec('whoami', $output, $retval);
            echo "Returned with status $retval and output:\n";
            print_r($output);

            //$output = shell_exec(escapeshellcmd('ls -lart'));
            //echo "<pre>$output</pre>";

            //$output = shell_exec('ls -lart /');
            //echo "<pre>$output</pre>";

            // Carpeta de cargas
            $output = shell_exec(escapeshellcmd('ls -lart uploads'));
            echo "<pre>$output</pre>";
            //$output = shell_exec(escapeshellcmd('touch uploads/index.html'));
            //echo "<pre>$output</pre>";

            // Carpeta de publicación
            $output = shell_exec(escapeshellcmd('ls -lart /var/www/html/ | mkdir uploads/publicacion'));
            echo "<pre>$output</pre>";
            $output = shell_exec(escapeshellcmd('ls -lart uploads/publicacion'));
            echo "<pre>$output</pre>";
            //$output = shell_exec(escapeshellcmd('touch uploads/publicacion/index.html'));
            //echo "<pre>$output</pre>";

            //mkdir('/var/www/html/lti/publicacion/nombreTrabajoXXX00000001', 0777, true);

            // Carpeta de Actividad cargada y publicada
            // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'a'
            /////////////////////////////////
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            //$namedir= substr('nombreTrabajo',0, (strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) >=0 ? strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) : 0)) . Yii::$app->user->identity->username . date('YmdHisu') . '00000003';
            $namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
            exec(escapeshellcmd('mkdir uploads/publicacion/' . $namedir), $output, $retval);
            echo "Returned with status $retval and output:\n";
            print_r($output);

            // Descomprime .zip
            // outputs the username that owns the running php/httpd process
            // (on a system with the "unzip" executable in the path)
            $output=null;
            $retval=null;
            //'unzip uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'
            //exec(escapeshellcmd('unzip uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
            exec(escapeshellcmd('unzip uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><code>";
            print_r($output);
            echo "</code></p>";

            // Registra ID=$namedir y URL='uploads/publicacion/$namedir/'
            ////////////////////////////////

            ?>
        </div>

    </div>
</div>
