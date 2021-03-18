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
            <p class="alert alert-success">Archivo "<i> <?= $file ?> </i>" subido correctamente</p>
        </div>

        <p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fupload">Volver</a></p>

    <?php else: ?>

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            Puede subirse un fichero comprimido en formato .zip de cada vez.
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
