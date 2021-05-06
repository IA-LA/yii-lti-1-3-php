<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\crud\Upload\PublishForm */

use yii\helpers\Html;
/* use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Publish Git Upload';
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
            <p class="alert alert-success">Upload Git ´<b><i><?= $repositorio ?></i></b>´ es correcto</p>

        </div>

    <?php else: ?>

        <p>
            Formulario de subida de Econtent complejos desde el CTU.
            Puede subirse un fichero comprimido .zip, de cada vez,
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
