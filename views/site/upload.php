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
            <br/>
	        Se puede subir un fichero comprimido en formato .zip
        </p>


    <div class="row">
        <div class="col-lg-5">
    	    <?php $form = ActiveForm::begin([
	    	    'id' => 'upload-form',
                'layout' => 'horizontal',
                'options' => ['enctype' => 'multipart/form-data'],
	        ]); ?>
                <div class="form-group">
                    <div class="col-lg-offset-1 col-lg-11">
                    <div class="btn btn-default">
                        <?= $form->field($model, 'zipFile')->fileInput([
                            'template' => "<div class=\"btn btn-warning\">{input} {label}</div>\n<div class=\"btn btn-warning\">{error}</div>",
                        ]) ?>
                    </div>
                    <p></p>
                        <!-- UPLOAD Bad Request (#400) Unable to verify your data submission.   -->
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                        <!-- <button class="btn btn-lg btn-success">Submit</button> -->
                        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'upload-button']) ?>
                    </div>
                </div>
            <?php ActiveForm::end() ?>

            <div class="col-lg-offset-1" style="color:#999;">
                To modify the type to upload, please check out the contact Form <!-- <code>app\models\UploadForm::rules()</code> -->.

                You may upload a compressed file <strong>zip</strong><br/>
            </div>
        </div>
    </div>

</div>
