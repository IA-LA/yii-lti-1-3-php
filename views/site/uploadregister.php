<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UploadRegisterForm */

use yii\helpers\Html;
/* use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Upload & Register';
$this->params['breadcrumbs'][] = $this->title;

// ini_set('upload_max_filesize', '10M');

?>
<div class="site-uploadregister">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('uploadregisterFormSubmitted')): ?>

        <div>
            <p/>
            <p/>
            <p/>
            <!--
            // VALOR DEL NOMBRE D FICHERO
            // enviado desde el Controlador SiteController.php
            -->
            <p class="alert alert-success">Archivo ´<b><i><?= $file ?></i></b>´ subido correctamente</p>

            <?php

                // Carpeta de publicación Actividad
                $output = shell_exec(escapeshellcmd('mkdir uploads/publicacion'));
                //echo "<pre>$output</pre>";

                // Carpeta de Actividad cargada y publicada
                // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'a'
                /////////////////////////////////
                // outputs the username that owns the running php/httpd process
                // (on a system with the "mkdir" executable in the path)
                $output=null;
                $retval=null;
                // VALOR DE LA CARPETA
                // desde el Controlador SiteContreoller.php
                //$namedir= substr('nombreTrabajo',0, (strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) >=0 ? strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) : 0)) . Yii::$app->user->identity->username . date('YmdHisu') . '00000003';
                //$namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
                $namedir=$namefile;
                exec(escapeshellcmd('mkdir uploads/publicacion/' . $namedir), $output, $retval);
                // MKDIR sin errores
                if($retval === 0) {
            ?>

            <p/>
            <p/>
            <p/>
            <p class="alert alert-success">Carpeta ´<b><i><?= $namedir ?></i></b>´ creada correctamente</p>

            <?php
                    //echo "Returned with status $retval and output:\n";
                    //print_r($output);
                    // Carpeta de publicaciones
                    //$output = shell_exec(escapeshellcmd('ls -lart uploads/publicacion/'));
                    //echo "<pre>$output</pre>";

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

                    // Crea proyecto Git 'uploads/git/$namedir.git' y URL='uploads/publicacion/$namedir/'
                    //  ID=$namedir
                    ///////////////////////////////////////////////////////////////////////////////////
                    ///
                    // Git
                    $output = shell_exec(escapeshellcmd('git --version'));
                    //echo "<pre>1. $output</pre>";

                    // Carpeta de Git
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "mkdir" executable in the path)
                    $output=null;
                    $retval=null;
                    exec(escapeshellcmd('mkdir uploads/git 2>&1'), $output, $retval);
                    //echo "2.Returned with status $retval and output:\n";
                    //echo "<p><pre>2.a.";
                    //print_r($output);
                    //echo "</pre></p>";
                    $output = shell_exec(escapeshellcmd('ls -lart uploads/ | mkdir uploads/git 2>&1'));
                    //echo "<pre>2.b. $output</pre>";

                    // Carpeta de Actividad Git
                    // Convenio de nombre proyecto (24 hex) y carpeta = 'repo_' + id user + fecha y hora + 'a' + '.git'
                    /////////////////////////////////
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "mkdir" executable in the path)
                    $output=null;
                    $retval=null;
                    exec(escapeshellcmd('mkdir uploads/git/' . $namedir . '.git'), $output, $retval);
                    //echo "3.Returned with status $retval and output:\n";
                    //echo "<p><pre>";
                    //print_r($output);
                    //echo "</pre></p>";

                    // Proyecto Git
                    // Crear Git vacío distribuido (--bare)
                    $output = shell_exec(escapeshellcmd('git --bare -C uploads/git/ init ' . $namedir . '.git'));
                    //echo "<pre>4.a. $output</pre>";
                    // Permisos carptetas Git ./hobks
                    $output = shell_exec(escapeshellcmd('cp uploads/git/' . $namedir . '.git/hooks/post-update.sample uploads/git/' . $namedir . '.git/hooks/post-update'));
                    $output = shell_exec(escapeshellcmd('chmod a+x uploads/git/' . $namedir . '.git/hooks/post-update'));
                    //echo "<pre>4.Hooks post update. $output</pre>";
                    // Permisos carpteta Git .git/, ./objects y ./refs
                    //$output = shell_exec(escapeshellcmd('chmod 777 -R uploads/git/' . $namedir . '.git/ 2>&1'));
                    //echo "<pre>4.a. $output</pre>";
                    //echo "4.a.PassThru " . passthru('chmod 777 -R uploads/git/' . $namedir . '.git/ 2>&1') . "<br/>";
                    // Permisos carptetas Git ./objects
                    $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/git/' . $namedir . '.git/objects/'));
                    //echo "<pre>4.b. $output</pre>";
                    //echo "4.b.PassThru " . passthru('chmod 777 -R uploads/git/' . $namedir . '.git/objects/ 2>&1') . "<br/>";
                    // Permisos carptetas Git ./refs
                    $output = shell_exec(escapeshellcmd('chmod 777 -R uploads/git/' . $namedir . '.git/refs/'));
                    //echo "<pre>4.c. $output</pre>";
                    //echo "4.c.PassThru " . passthru('chmod 777 -R uploads/git/' . $namedir . '.git/refs/ 2>&1') . "<br/>";

                    // Clonar Git distribuido (--bare)
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "git clone" executable in the path)
                    $output=null;
                    $retval=null;
                    exec(escapeshellcmd('git clone uploads/git/' . $namedir . '.git uploads/publicacion/' . $namedir), $output, $retval);
                    //echo "5.Returned with status $retval and output:\n";
                    //echo "<p><pre>5.a. git clone uploads/git/$namedir.git uploads/publicacion/$namedir<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/ clone uploads/git/' . $namedir . '.git ' . $namedir));
                    //$output = shell_exec(escapeshellcmd('git clone uploads/git/' . $namedir . '.git uploads/publicacion/' . $namedir . ' 2>&1'));
                    //echo "<pre>5.b. $output</pre>";

                    // Descomprime .zip
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "unzip" executable in the path)
                    //$output=null;
                    //$retval=null;
                    //'unzip uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'
                    //exec(escapeshellcmd('unzip uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
                    //exec(escapeshellcmd('unzip uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
                    //exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);

                    // Unzip Actividad .zip
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "unzip" executable in the path)
                    $output=null;
                    $retval=null;
                    exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
                    //exec(escapeshellcmd('unzip uploads/cindetechtmlv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
                    //echo "6.Returned with status $retval and output:\n";
                    //echo "Returned with status $retval and output:\n";
                    //echo "<i> " . count($output) . " archivos descomprimidos. Status y resultado " . ($retval === 0 ? 'correctos' : 'erróneos') . ":\n</i>";
                    //echo "<p><pre> 6.a. Unzip<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
                    //echo "<pre>6.$output</pre>";
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
                    //echo "7.Returned with status $retval and output:\n";
                    //echo "<p><pre>7.a. git -C uploads/publicacion/" . $namedir . "/ add .<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
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
                    //echo "8.Returned with status $retval and output:\n";
                    //echo "<p><pre>8.a.<br/>";
                    //echo "8.a.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.email "you@example.com" 2>&1') . "<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "git add" executable in the path)
                    $output=null;
                    $retval=null;
                    //exec(escapeshellcmd('git config --global user.name "Your Name"'), $output, $retval);
                    exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config --local user.name "Your Name"'), $output, $retval);
                    //echo "8.Returned with status $retval and output:\n";
                    //echo "<p><pre>8.b.<br/>";
                    //echo "8.b.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.name "Your Name" 2>&1') . "<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.email "' . Yii::$app->user->identity->username . '@lti.server" 2>&1'));
                    //echo "<pre>8.c. $output</pre>";
                    $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.name "'. Yii::$app->user->identity->id .'" 2>&1'));
                    //echo "<pre>8.d. $output</pre>";

                    // Commit -m "Init Commit Server LTI"
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "git add" executable in the path)
                    $output=null;
                    $retval=null;
                    //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ commit -m "Init Commit Server LTI"'), $output, $retval);
                    exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI"'), $output, $retval);
                    //echo "9.Returned with status $retval and output:\n";
                    //echo "<p><pre>9.a. git -C uploads/publicacion/$namedir/ commit -m 'Initial Commit Server LTI' <br/>";
                    //echo "9.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1') . "<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1'));
                    //echo "<pre>9.b. $output</pre>";

                    // Push clonado
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "git add" executable in the path)
                    $output=null;
                    $retval=null;
                    //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
                    exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
                    //echo "10.Returned with status $retval and output:\n";
                    //echo "<p><pre>10.a. git -C uploads/publicacion/$namedir/ push origin master<br/>";
                    //echo "10.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1') . "<br/>";
                    //print_r($output);
                    //echo "</pre></p>";
                    $output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1'));
                    //echo "<pre>10.b. $output</pre>";

                    // Git, UNZIP y Publicacion sin errores
                    if($retval === 0) {
            ?>

            <p/>
            <p/>
            <p/>
            <div class="alert alert-success">
                <ol>
                    <li>Git URL de la Actividad ´<b><i><a href="<?= Html::encode($serverGit . '/' . $namedir); ?>.git" target="_blank"><?= $namedir ?></a></i></b>´ generado correctamente.<br/></li>
                    <li>Fichero de la Actividad ´<b><i><?= $file ?></i></b>´ descomprimido correctamente.<br/></li>
                    <li>Web URL de la Actividad ´<b><i><a href="uploads/publicacion/<?= Html::encode($namedir); ?>" target="_blank"><?= $namedir ?></a></i></b>´ publicada correctamente</li>
                </ol>
            </div>

        <?php

                        // TODO Registra ID=$namedir ... y URL='uploads/publicacion/$namedir/' en Colección BBDD
                        // REGISTRO
                        ////////////////////////////////
                        echo '<div class="row alert alert-success"><div class="col-lg-6">La actividad LTI ha quedado registrada con el ID: <b><i>`' . $namedir . '`</i></b> y la dirección URL: ´<b><i><a href="uploads/publicacion/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´.</div>' .
                            //'<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=site%2Fregister">Registrar LTI</a></div></div>'.
                            '<div class="row alert alert-success"><div class="col-lg-6">El Upload puede ser registrado con el ID: <b><i>`' . $namedir . '`</i></b>, el fichero: ´<b><i><a href="uploads/' . $file . '" target="_blank">' . $file . '</a></i></b>, la carpeta `<b>' . $namedir . '</b>`, la dirección de publicación: ´<b><i><a href="uploads/publicacion/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´ y el proyecto Git: ´<b><i><a href="uploads/git/' . $namedir . '.git" target="_blank">' . $namedir . '.git</a></i></b>´,</div>' .
                            '<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=crud%2Fregister">Registrar Upload</a></div></div>';
                             //$this->render('_list_item',['model' => $model])

                        // Boton Atras
                        echo '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fuploadregister">Atrás</a></p>';
                    }
                    else {
                        echo '<p class="alert error-summary">Error al descomprimir fichero <i>`' . $file . '`</i></p>' .
                             '<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2Fuploadregister">Atrás</a></p>';
                    }

                }
                else {
                    echo '<p class="alert error-summary"><i>Error al crear carpeta <i>`' . $namedir . '`</i></p>' .
                         '<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2Fuploadregister">Atrás</a></p>';
                }
        ?>

        </div>

    <?php else: ?>

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            Puede subirse un fichero comprimido .zip, de cada vez,
            sin contener espacios en blanco, tildes o eñes en el nombre.
        </p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'uploadregister-form']); ?>

                    <!-- Modal de Upload -->
                    <?= $form->field($model, 'zipFile')->fileInput() ?>

                    <!--
                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ]) ?>
                    -->

                    <!-- UPLOAD Bad Request (#400) Unable to verify your data submission.   -->
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

                    <!-- <button class="btn btn-lg btn-success">Submit</button> -->
                    <div class="form-group">
                        <?= Html::submitButton('Upload & Register', ['class' => 'btn btn-primary', 'name' => 'uploadregister-button']) ?>
                    </div>

                <?php ActiveForm::end() ?>

            </div>
        </div>

    <?php endif; ?>

</div>
