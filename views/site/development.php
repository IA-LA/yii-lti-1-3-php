<?php

/* @var $this yii\web\View */

$this->title = 'Development ' . Yii::$app->params['yiiapp'];

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
            <p class="lead">Hola <?= ' "<i>' . Yii::$app->user->identity->username . '</i>" ' ?>, accede al panel de Actividades (Tools).</p>

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

            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
                $carpetaGit = Yii::$app->params['carpetaGit_local'];
                $serverGit = Yii::$app->params['serverGit_local'];
                $serverLti = Yii::$app->params['serverLti_local'];
            }
            else {
                $carpetaGit = Yii::$app->params['carpetaGit_global'];
                $serverGit = Yii::$app->params['serverGit_global'];
                $serverLti = Yii::$app->params['serverLti_global'];
            }

            // Git
            $output = shell_exec(escapeshellcmd('git --version'));
            echo "<pre>1. $output</pre>";

            // Carpeta de Git
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('mkdir uploads/git 2>&1'), $output, $retval);
            echo "2.Returned with status $retval and output:\n";
            echo "<p><pre>2.a.";
            print_r($output);
            echo "</pre></p>";
            $output = shell_exec(escapeshellcmd('ls -lart uploads/ | mkdir uploads/git 2>&1'));
            echo "<pre>2.b. $output</pre>";

            // Carpeta de Actividad Git
            // Convenio de nombre proyecto (24 hex) y carpeta = 'repo_' + id user + fecha y hora + 'a' + '.git'
            /////////////////////////////////
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            $namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
            exec(escapeshellcmd('mkdir ' . $serverGit . '/' . $namedir . '.git'), $output, $retval);
            echo "3.Returned with status $retval and output:\n";
            echo "<p><pre>3.</br>";
            echo "3.PassThru " . passthru('mkdir ' . $serverGit . '/' . $namedir . '.git 2>&1') . "<br/>";
            print_r($output);
            echo "</pre></p>";

            // Proyecto Git
            // Crear Git vacío distribuido (--bare --shared) con post-update hook (https://git-scm.com/book/en/v2/Git-on-the-Server-The-Protocols)
            $output = shell_exec(escapeshellcmd('git --bare -C  ' . $serverGit . '/  init ' . $namedir . '.git'));
            //$output = shell_exec(escapeshellcmd('git --bare --shared -C  ' . $serverGit . '/  init ' . $namedir . '.git'));
            echo "<pre>4.Init --bare. $output</pre>";
            // Post update
            //$output = shell_exec(escapeshellcmd('mv ' . $serverGit . '/' . $namedir . '.git/hooks/post-update.sample ' . $serverGit . '/' . $namedir . '.git/hooks/post-update'));
            // Error stat
            $output = shell_exec(escapeshellcmd('cp ' . $serverGit . '/' . $namedir . '.git/hooks/post-update.sample ' . $serverGit . '/' . $namedir . '.git/hooks/post-update'));
            $output = shell_exec(escapeshellcmd('chmod a+x ' . $serverGit . '/' . $namedir . '.git/hooks/post-update'));
            echo "<pre>4.Hooks post update. $output</pre>";
            //echo "4.a.PassThru " . passthru('mv ' . $serverGit . '/' . $namedir . '.git/hooks/post-update.sample ' . $serverGit . '/' . $namedir . '.git/hooks/post-update 2>&1') . "<br/>";
            // Error stat
            echo "4.PassThru " . passthru('cp ' . $serverGit . '/' . $namedir . '.git/hooks/post-update.sample ' . $serverGit . '/' . $namedir . '.git/hooks/post-update 2>&1') . "<br/>";
            echo "4.PassThru " . passthru('chmod a+x ' . $serverGit . '/' . $namedir . '.git/hooks/post-update 2>&1') . "<br/>";
            // Permisos carpteta Git .git/, ./objects y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/ 2>&1'));
            echo "<pre>4.a. $output</pre>";
            echo "4.a.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/ 2>&1') . "<br/>";
            // Permisos carptetas Git .git/, ./objects y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/objects/ 2>&1'));
            echo "<pre>4.b. $output</pre>";
            echo "4.b.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/objects/ 2>&1') . "<br/>";
            // Permisos carptetas Git .git/, ./objects y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/refs/ 2>&1'));
            echo "<pre>4.c. $output</pre>";
            echo "4.c.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/refs/ 2>&1') . "<br/>";
            // Permisos carptetas Git ./branches
            $output = shell_exec(escapeshellcmd('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/branches/ 2>&1'));
            echo "<pre>4.c. $output</pre>";
            echo "4.c.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/branches/ 2>&1') . "<br/>";
            // Permisos carptetas Git ./hooks
            $output = shell_exec(escapeshellcmd('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/hooks/ 2>&1'));
            echo "<pre>4.c. $output</pre>";
            echo "4.c.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/hooks/ 2>&1') . "<br/>";
            // Permisos carptetas Git ./info
            $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/' . $namedir . '.git/info/ 2>&1'));
            echo "<pre>4.c. $output</pre>";
            echo "4.c.PassThru " . passthru('chmod 777 -R ' . $serverGit . '/' . $namedir . '.git/info/ 2>&1') . "<br/>";

            // Clonar Git distribuido (--bare --shared)
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git clone" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git clone ' . $serverGit . '/' . $namedir . '.git uploads/publicacion/' . $namedir), $output, $retval);
            echo "5.Returned with status $retval and output:\n";
            echo "<p><pre>5.a. git clone $serverGit/$namedir.git uploads/publicacion/$namedir<br/>";
            print_r($output);
            echo "</pre></p>";
            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/ clone ' . $serverGit . '/' . $namedir . '.git ' . $namedir));
            //$output = shell_exec(escapeshellcmd('git clone ' . $serverGit . '/' . $namedir . '.git uploads/publicacion/' . $namedir . ' 2>&1'));
            //echo "<pre>5.b. $output</pre>";

            // Unzip Actividad .zip
            // outputs the username that owns the running php/httpd process
            // (on a system with the "unzip" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
            //exec(escapeshellcmd('unzip uploads/cindetechtmlv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
            exec(escapeshellcmd('umask 0022'), $output, $retval);
            echo "6.Returned with status $retval and output:\n";
            echo "<p><pre> 6.a. Umask<br/>";
            print_r($output);
            echo "6.a.PassThru " . passthru('umask 0000 2>&1') . "<br/>";
            echo "</pre></p>";

            // outputs the username that owns the running php/httpd process
            // (on a system with the "unzip" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
            exec(escapeshellcmd('unzip uploads/cindetechtmlv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
            // exec(escapeshellcmd('unzip -K -X -o uploads/604b26121513a45112903905_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4d.zip -d uploads/publicacion/' . $namedir . ' -o'), $output, $retval);
            echo "7.Returned with status $retval and output:\n";
            echo "<p><pre> 7.a. Unzip<br/>";
            print_r($output);
            //echo "7.a.PassThru " . passthru('unzip -Z uploads/604b26121513a45112903905_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4d.zip -d uploads/publicacion/' . $namedir . ' 2>&1') . "<br/>";
            //echo "7.a.PassThru " . passthru('unzip -K -X uploads/604b26121513a45112903905_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4d.zip -d uploads/publicacion/' . $namedir . ' 2>&1') . "<br/>";
            //echo "7.a.PassThru " . passthru('unzip -o uploads/604b26121513a45112903905_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4d.zip -d uploads/publicacion/' . $namedir . ' 2>&1') . "<br/>";
            echo "</pre></p>";
            //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
            //echo "<pre>6.$output</pre>";
            //TODO añadir index.html en el respositorio .git
            //$output = shell_exec(escapeshellcmd('touch uploads/publicacion/' . $namedir . '/HolaMundo.txt 2>&1'));
            //echo "<pre>6.a. touch HolaMundo.txt $output</pre>";
            //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
            //echo "<pre>6.b. $output</pre>";

            // Add, Commit y Push clonado
            // Add
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ add . '), $output, $retval);
            echo "8.Returned with status $retval and output:\n";
            echo "<p><pre>8.a. git -C uploads/publicacion/" . $namedir . "/ add .<br/>";
            print_r($output);
            echo "</pre></p>";
            //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ add .'));
            //echo "<pre>7.b. $output</pre>";
            //$output = shell_exec(escapeshellcmd('sleep 0.5s'));
            //echo "<pre>7.c. $output</pre>";

            // Commit Config
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('git config --global user.email "you@example.com"'), $output, $retval);
            exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config --local user.email "you@example.com"'), $output, $retval);
            echo "9.Returned with status $retval and output:\n";
            echo "<p><pre>9.a.<br/>";
            echo "9.a.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.email "you@example.com" 2>&1') . "<br/>";
            print_r($output);
            echo "</pre></p>";
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('git config --global user.name "Your Name"'), $output, $retval);
            exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config --local user.name "Your Name"'), $output, $retval);
            echo "9.Returned with status $retval and output:\n";
            echo "<p><pre>9.b.<br/>";
            echo "9.b.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.name "Your Name" 2>&1') . "<br/>";
            print_r($output);
            echo "</pre></p>";
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.email "' . Yii::$app->user->identity->username . '@lti.server" 2>&1'));
            echo "<pre>9.c. $output</pre>";
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.name "'. Yii::$app->user->identity->id .'" 2>&1'));
            echo "<pre>9.d. $output</pre>";

            // Commit -m "Init Commit Server LTI"
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ commit -m "Init Commit Server LTI"'), $output, $retval);
            exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI"'), $output, $retval);
            echo "9.Returned with status $retval and output:\n";
            echo "<p><pre>9.a. git -C uploads/publicacion/$namedir/ commit -m 'Initial Commit Server LTI' <br/>";
            echo "9.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1') . "<br/>";
            print_r($output);
            echo "</pre></p>";
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1'));
            echo "<pre>9.b. $output</pre>";
            // Push clonado
            // outputs the username that owns the running php/httpd process
            // (on a system with the "git add" executable in the path)
            $output=null;
            $retval=null;
            //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
            exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
            echo "10.Returned with status $retval and output:\n";
            echo "<p><pre>10.a. git -C uploads/publicacion/$namedir/ push origin master<br/>";
            echo "10.a.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1') . "<br/>";
            print_r($output);
            echo "</pre></p>";
            $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1'));
            echo "<pre>10.b. $output</pre>";
            echo "10.b.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ init --bare --shared 2>&1') . "<br/>";

            // Crear Git difusión clonado o distribuido (--bare --shared) con post-update hook (https://git-scm.com/book/en/v2/Git-on-the-Server-The-Protocols)
            $output=null;
            $retval=null;
            exec(escapeshellcmd('cp -rf ' . $serverGit . '/' . $namedir . '.git uploads/difusion/' . $namedir . '.git 2>&1'), $output, $retval);
            echo "11.Returned with status $retval and output:\n";
            echo "<p><pre>11. Difusion";
            print_r($output);
            echo "</pre></p>";
            $output = shell_exec(escapeshellcmd('cp -rf ' . $serverGit . '/' . $namedir . '.git uploads/difusion/' . $namedir . '.git'));
            //$output = shell_exec(escapeshellcmd('git --bare --shared -C  ' . $serverGit . '/  init ' . $namedir . '.git'));
            echo "<pre>11. Difusión. $output</pre>";
            $output = shell_exec(escapeshellcmd('cp -rf ' . $serverGit . '/' . $namedir . '.git uploads/difusion/' . $namedir . '.git'));
            echo "<pre>11. $output</pre>";
            echo "11.PassThru " . passthru('cp -rf ' . $serverGit . '/' . $namedir . '.git uploads/difusion/' . $namedir . '.git 2>&1') . "<br/>";
            // Permisos carpteta Git .git/, ./objects y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/difusion/' . $namedir . '.git/ 2>&1'));
            echo "<pre>11.a. $output</pre>";
            echo "11.a.PassThru " . passthru('chmod 777 -R uploads/difusion/' . $namedir . '.git/ 2>&1') . "<br/>";
            // Permisos carptetas Git .git/, ./objects y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/difusion/' . $namedir . '.git/objects/ 2>&1'));
            echo "<pre>11.b. $output</pre>";
            echo "11.b.PassThru " . passthru('chmod 777 -R uploads/difusion/' . $namedir . '.git/objects/ 2>&1') . "<br/>";
            // Permisos carptetas Git .git/, ./object y ./refs
            $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/difusion/' . $namedir . '.git/refs/ 2>&1'));
            echo "<pre>11.c. $output</pre>";
            echo "11.c.PassThru " . passthru('chmod 777 -R uploads/difusion/' . $namedir . '.git/refs/ 2>&1') . "<br/>";

            // INICIO
            /////////
            echo "INICIO:\n";
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
