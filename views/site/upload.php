<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UploadForm */

use yii\helpers\Html;
/* use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Upload';
$this->params['breadcrumbs'][] = $this->title;

// ini_set('upload_max_filesize', '10M');

?>
<div class="site-upload">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('uploadFormSubmitted')): ?>

        <div>
            <p/>
            <p/>
            <p/>
            <p class="alert alert-success">Archivo ´<b><i><?= $file ?></i></b>´ subido correctamente</p>
            <?php

                // Carpeta de publicación
                $output = shell_exec(escapeshellcmd('ls -lart uploads/ | mkdir uploads/publicacion'));
                //echo "<pre>$output</pre>";
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

                    // Descomprime .zip
                    // outputs the username that owns the running php/httpd process
                    // (on a system with the "unzip" executable in the path)
                    $output=null;
                    $retval=null;
                    //'unzip uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'
                    //exec(escapeshellcmd('unzip uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
                    //exec(escapeshellcmd('unzip uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
                    exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
                    // UNZIP sin errores
                    if($retval === 0) {
                ?>
                        <p/>
                        <p/>
                        <p/>
                        <p class="alert alert-success">Fichero ´<b><i><?= $file ?></i></b>´ descomprimido correctamente.<br/>URL de la Actividad ´<b><i><a href="uploads/publicacion/<?= Html::encode($namedir); ?>" target="_blank"><?= $namedir ?></a></i></b>´ publicada correctamente</p>
                <?php
                        //echo "Returned with status $retval and output:\n";
                        echo "<i> " . count($output) . " archivos descomprimidos. Status y resultado " . ($retval === 0 ? 'correctos' : 'erróneos') . ":\n</i>";
                        echo "<p><pre>";
                        print_r($output);
                        echo "</pre></p>";

                        // TODO Crea proyecto Git repo_$namedir.git ID=$namedir y URL='uploads/publicacion/$namedir/'
                        // ??????????????????????
                        ////////////////////////////////

                        // TODO Registra ID=$namedir y URL='uploads/publicacion/$namedir/'
                        // REGISTRO
                        ////////////////////////////////
                        echo '<div class="row alert alert-success"><div class="col-lg-6">Puede registrarse esta actividad con el ID: <b><i>`' . $namedir . '`</i></b> y la dirección URL: ´<b><i><a href="uploads/publicacion/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´.</div>' .
                             '<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=site%2Fregister">Registrar</a></div></div>';
                             //$this->render('_list_item',['model' => $model])

                        // Boton Atras
                        echo '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fupload">Atrás</a></p>';
                    }
                    else {
                        echo '<p class="alert error-summary">Error al descomprimir fichero <i>`' . $file . '`</i></p>' .
                             '<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2Fupload">Atrás</a></p>';
                    }

                }
                else {
                    echo '<p class="alert error-summary"><i>Error al crear carpeta <i>`' . $namedir . '`</i></p>' .
                         '<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2Fupload">Atrás</a></p>';
                }
            ?>

        </div>

    <?php else: ?>

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            Puede subirse un fichero comprimido en .zip de cada vez,
            sin tener espacios en blanco, tildes o eñes en el nombre.
        </p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'upload-form']); ?>

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
                        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'upload-button']) ?>
                    </div>

                <?php ActiveForm::end() ?>

            </div>
        </div>

    <?php endif; ?>

</div>
