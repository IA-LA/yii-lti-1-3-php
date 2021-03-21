<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->params['yiiapp'];

//TODO mostar listados con Yii ListView

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LTI Development</h1>

        <?php
        if (Yii::$app->user->isGuest) {
        ?>
            <p class="lead">Autentifícate para acceder al panel de Actividades (Tools).</p>

            <p><a class="btn btn-primary" href="index.php?r=site%2Flogin">Login</a></p>
        <?php
        }
        else{
        ?>
            <p class="lead">Bienvenido <?= ' "<i>' . Yii::$app->user->identity->username . '</i>" ' ?>, accede al panel de Actividades (Tools).</p>

            <form action="/index.php?r=site%2Flogout" method="post">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                <button type="submit" class="btn btn-danger">Logout <?= '(' . Yii::$app->user->identity->username . ')'?></button>
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

            // Dirección de alojamiento
            // del servidor de Git
            //////////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $git = Yii::$app->params['git2'];
            else
                $git = Yii::$app->params['git1'];

            // Git
            $output = shell_exec(escapeshellcmd('git --version'));
            echo "<pre>1. $output</pre>";
            // Carpeta de Git
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('mkdir uploads/git'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>2.";
            print_r($output);
            echo "</pre></p>";

            // Carpeta de Actividad Git
            // Convenio de nombre proyecto (24 hex) y carpeta = 'repo_' + id user + fecha y hora + 'a' + '.git'
            /////////////////////////////////
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            $namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
            exec(escapeshellcmd('mkdir uploads/git/' . $namedir . '.git'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>3.";
            print_r($output);
            echo "</pre></p>";

            // Proyecto Git
            // Crear Git vacío distribuíble (--bare)
            $output = shell_exec(escapeshellcmd('git --bare -C ' . $git . '/uploads/git/ init ' . $namedir . '.git'));
            echo "<pre>4.$output</pre>";
            // Clonar Git distribuido
            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/ clone uploads/git/' . $namedir . '.git ' . $namedir));
            //$output = shell_exec(escapeshellcmd('git clone uploads/git/' . $namedir . '.git uploads/publicacion/' . $namedir));
            //echo "<pre>5.$output</pre>";
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git clone" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git clone ' . $git . '/uploads/git/' . $namedir . '.git ' . $git . '/uploads/publicacion/' . $namedir), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>5.";
            print_r($output);
            echo "</pre></p>";

            // Unzip Actividad .zip
            // outputs the username that owns the running php/httpd process
            // (on a system with the "unzip" executable in the path)
            //$output=null;
            //$retval=null;
            //exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
            //echo "Returned with status $retval and output:\n";
            //echo "<p><pre>";
            //print_r($output);
            //echo "</pre></p>";
            //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
            //echo "<pre>6.$output</pre>";
            $output = shell_exec(escapeshellcmd('touch uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
            echo "<pre>6a.$output</pre>";
            //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
            //echo "<pre>6b.$output</pre>";

            // Add, Commit y Push publicacion
            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ add .'));
            //echo "<pre>7.$output</pre>";
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git -C ' . $git . '/uploads/publicacion/' . $namedir . '/ add .'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>7.";
            print_r($output);
            echo "</pre></p>";

            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit'));
            //echo "<pre>8.$output</pre>";
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git -C ' . $git . '/uploads/publicacion/' . $namedir . '/ commit'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>8.";
            print_r($output);
            echo "</pre></p>";

            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push'));
            //echo "<pre>9.$output</pre>";
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git -C ' . $git . '/uploads/publicacion/' . $namedir . '/ push'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>9.";
            print_r($output);
            echo "</pre></p>";

            // INICIO
            /////////
            // outputs the username that owns the running php/httpd process
            // (on a system with the "whoami" executable in the path)
            $output=null;
            $retval=null;
            exec('whoami', $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>";
            print_r($output);
            echo "</pre></p>";

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
            $namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'aa';
            exec(escapeshellcmd('mkdir uploads/publicacion/' . $namedir), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>";
            print_r($output);
            echo "</pre></p>";

            // Descomprime .zip
            // outputs the username that owns the running php/httpd process
            // (on a system with the "unzip" executable in the path)
            $output=null;
            $retval=null;
            //'unzip uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'
            //exec(escapeshellcmd('unzip uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
            exec(escapeshellcmd('unzip uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>";
            print_r($output);
            echo "</pre></p>";

            // Registra ID=$namedir y URL='uploads/publicacion/$namedir/'
            ////////////////////////////////

            ?>

        </div>

    </div>
</div>
