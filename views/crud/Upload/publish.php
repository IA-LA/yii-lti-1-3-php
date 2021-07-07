<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\crud\Upload\PublishForm */

use yii\helpers\Html;
/* use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Publish Git';
$this->params['breadcrumbs'][] = $this->title;

// ini_set('upload_max_filesize', '10M');

?>
<div class="site-upload">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('publishFormSubmitted')): ?>

    <div>
        <p/>
        <p/>
        <p/>

        <!-- TODO consultar el valor del _ID en la colección Upload de la BBDD antes de actualizar Git-->
        <?php if (Yii::$app->session->hasFlash('publishIsPosible')): ?>

            <p class="alert alert-success">Upload Git ´<b><i><?= $namedir ?>.git</i></b>´ es un proyecto correcto</p>

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

                // Actualiza proyecto Git 'uploads/git/$namedir.git' y URL='uploads/publicacion/$namedir/'
                //  ID=$namedir
                ////////////////////////////////////////////////////////////////////////////////////////
                ///
                // Git
                $output = shell_exec(escapeshellcmd('git --version'));
                //echo "<pre>1. $output</pre>";

                // Pull Repositirio
                $output=null;
                $retval=null;
                $retva=null;
                $retv=null;
                $ret=null;
                //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ pull'), $output, $retval);
                exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Commit Publish Git" .'), $output, $retval);
                echo "10.Returned with status $retval and output:\n";
                echo "<p><pre>10.a. git -C uploads/publicacion/$namedir/ commit -m 'Commit Publish Git' .<br/>";
                print_r($output);
                exec('git -C uploads/publicacion/' . $namedir . '/ pull origin master 2>&1', $output, $retva);
                echo "10.Returned with status $retva and output:\n";
                echo "<p><pre>10.a. git -C uploads/publicacion/$namedir/ pull origin master 2>&1<br/>";
                print(implode(" ", $output));
                //echo "10.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ pull origin master 2>&1', $retv) . "<br/>";
                //print($retva);
                //echo "</pre></p>";
                //$output = shell_exec(escapeshellcmd('$(git -C uploads/publicacion/' . $namedir . '/ pull origin master)'));
                //echo "<pre>10.b. $output</pre>";
                //$output = system(escapeshellcmd('$(git -C uploads/publicacion/' . $namedir . '/ pull origin master)'), $retv);
                //echo "<pre>10.c. $output</pre>";
                //print($ret);

                // Pull Git Publicacion sin errores
                //REPOSITORIO ACTUALIZADO CORRECTAMENTE
                if(($retval === 0) || (strpos(implode(" ", $output), "Merge made by the 'recursive' strategy.")) ) {
            ?>
                    <div class="alert alert-success">
                        <ol>
                            <li>Repositorio ´<b><i><a href="<?= Html::encode($serverGit . '/' . $namedir); ?>.git" target="_blank"><?= $namedir ?></a></i></b>´ publicado correctamente.<br/></li>
                            <!--
                            <li>Fichero de la Actividad ´<b><i><?= $namedir//$file ?></i></b>´ descomprimido correctamente.<br/></li>
                            <li>Web URL de la Actividad ´<b><i><a href="uploads/publicacion/<?= Html::encode($namedir); ?>" target="_blank"><?= $namedir ?></a></i></b>´ publicada correctamente</li>
                            -->
                        </ol>
                    </div>

            <?php
                    // TODO Registra ID=$namedir y URL='uploads/publicacion/$namedir/'
                    // REGISTRO
                    ////////////////////////////////
                    echo '<div class="row alert alert-success"><div class="col-lg-6">La actividad LTI puede ser registrada con el ID: <b><i>`' . $namedir . '`</i></b> y la dirección URL: ´<b><i><a href="uploads/publicacion/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´.</div>' .
                        '<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=site%2Fregister">Registrar</a></div></div>';
                    //$this->render('_list_item',['model' => $model])

                    // Boton Atras
                    echo '<p><a class="btn btn-lg btn-success" href="index.php?r=crud%2Fpublish">Atrás</a></p>';
                }
                else {
                    // REPOSITORIO SIN CAMBIOS
                    if(strpos(json_encode($output), 'Already up to date.')) {
            ?>
                    <div class="alert alert-success">
                        <ol>
                            <li>Repositorio ´<b><i><a href="<?= Html::encode($serverGit . '/' . $namedir); ?>.git" target="_blank"><?= $namedir ?></a></i></b>´ sin cambios.</li>
                            <li>Web de publicación: <b><i><a href="uploads/publicacion/<?= Html::encode($namedir); ?>" target="_blank">´<?= Html::encode($namedir); ?>´</a></i></b> sin modificaciones.</li>
                            <!--
                                        <li>Fichero de la Actividad ´<b><i><?= $namedir//$file ?></i></b>´ descomprimido correctamente.<br/></li>
                                        <li>Web URL de la Actividad ´<b><i><a href="uploads/publicacion/<?= Html::encode($namedir); ?>" target="_blank"><?= $namedir ?></a></i></b>´ publicada correctamente</li>
                                        -->
                        </ol>
                    </div>
            <?php
                        // Boton Atras
                        echo '<p><a class="btn btn-lg btn-primary" href="index.php?r=crud%2Fpublish">Atrás</a></p>';
                    }
                    else {
                        echo '<p class="alert error-summary">Error al publicar el repositorio <i>`' . $namedir . '.git`</i></p>' .
                        '<p><a class="btn btn-lg btn-warning" href="index.php?r=crud%2Fpublish">Atrás</a></p>';
                    }
                }
            ?>

        <?php else: ?>

            <p class="alert alert-danger">Upload Git ´<b><i><?= $namedir ?></i></b>.git´ NO es un proyecto correcto</p>
            <p><a class="btn btn-lg btn-warning" href="index.php?r=crud%2Fpublish">Atrás</a></p>

        <?php endif; ?>

    </div>

    <?php else: ?>

        <p>
            Formulario de publicación de Econtent complejos desde el CTU.
            Puede publicarse un proyecto o repositorio de cada vez,
            sin contener espacios en blanco, tildes o eñes en el nombre.
        </p>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'publish-form']); ?>

                    <?= $form->field($model, 'id')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ]) ?>

                    <!-- PUBLISH Bad Request (#400) Unable to verify your data submission.   -->
                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

                    <!-- <button class="btn btn-lg btn-success">Submit</button> -->
                    <div class="form-group">
                        <?= Html::submitButton('Publish', ['class' => 'btn btn-primary', 'name' => 'publish-button']) ?>
                    </div>

                <?php ActiveForm::end() ?>

            </div>
        </div>

    <?php endif; ?>

</div>
