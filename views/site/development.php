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

            // Git
            $output = shell_exec(escapeshellcmd('git --version'));
            echo "<pre>$output</pre>";
            // Carpeta de Git
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('mkdir uploads/git'), $output, $retval);
            echo "Returned with status $retval and output:\n";
            echo "<p><pre>";
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
            echo "<p><pre>";
            print_r($output);
            echo "</pre></p>";

            // Proyecto Git
            // Crear Git vacío distribuíble (--bare)
            $output = shell_exec(escapeshellcmd('git --bare -C uploads/git/ init ' . $namedir . '.git'));
            // Clonar Git distribuido
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/ clone uploads/git/' . $namedir . '.git ' . $namedir));

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
            $output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));

            // Add, Commit y Push publicacion
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ add .'));
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit'));
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push'));


            ?>

        </div>

    </div>
</div>
