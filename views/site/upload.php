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

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            Puede subirse un fichero comprimido en formato .zip de cada vez.
        </p>

    <div class="row">
        <div class="col-lg-5">
    	    <?php $form = ActiveForm::begin(['id' => 'upload-form']); ?>
                <div class="btn btn-default>"
                    <?= $form->field($model, 'zipFile')->fileInput() ?>
                </div>
                <!-- UPLOAD Bad Request (#400) Unable to verify your data submission.   -->
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <!-- <button class="btn btn-lg btn-success">Submit</button> -->
                <div class="form-group">
                        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'upload-button']) ?>
                </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>

</div>
