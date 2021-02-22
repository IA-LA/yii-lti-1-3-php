<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UploadForm */

use yii\helpers\Html;
/* use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;

$this->title = 'Upload';
$this->params['breadcrumbs'][] = $this->title;

// ini_set('upload_max_filesize', '10M');

?>
<div class="site-upload">

    <h1><?= Html::encode($this->title) ?></h1>

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            <br/>
	    Se pueden subir comprimidos en formato .zip
        </p>


    <div class="row">
        <div class="col-lg-5">

	    <?php $form = ActiveForm::begin([
		'id' => 'upload-form',
	    ]); ?>

        	<button class="btn btn-lg btn-success">Submit</button>

	        <div class="form-group">
	            <div class="col-lg-offset-1 col-lg-11">
                    <?= $form->field($model, 'imageFile')->fileInput() ?>

                    <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'upload-button']) ?>
                    </div>
                 </div>

		<!-- UPLOAD Bad Request (#400) Unable to verify your data submission.   -->
		<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

	    <?php ActiveForm::end() ?>

        </div>
    </div>

    <div class="col-lg-offset-1" style="color:#999;">
        You may upload a compressed file <strong>zip</strong> or <strong>rar</strong><br/>
        To modify the type to upload, please check out the code <code>app\models\UploadForm::rules()</code>.
   </div>
</div>
