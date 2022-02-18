<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Platform\crud\UpdateForm */

use app\widgets\EBackButtonWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Update Platform';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="upload-crud-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('updateFormSubmitted')): ?>

        <div class="alert alert-success">
            Thank you for contacting us. We will respond to you as soon as possible.
        </div>

        <p>
            Note that if you turn on the Yii debugger, you should be able
            to view the mail message on the mail panel of the debugger.<br/>
            <?php if (Yii::$app->mailer->useFileTransport): ?>
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.
            <?php endif; ?>
        </p>

    <?php else: ?>

        <p>
            Actualiza una Plataforma por su Identificador y Credenciales.
            Thank you.
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'update-form']); ?>

                    <?= $form->field($model, 'id')->textInput(['autofocus' => true, 'value' => isset($id)? $id : ' ']) ?>

                    <?= $form->field($model, 'publicacion')->textInput(['value' => isset($publicacion)? $publicacion : ' ']) ?>

                    <?= $form->field($model, 'git')->textInput(['value' => isset($git)? $git : ' ']) ?>

                    <?= $form->field($model, 'fichero')->textInput(['value' => isset($fichero)? $fichero : ' ']) ?>

                    <?= $form->field($model, 'carpeta')->textInput(['value' => isset($carpeta)? $carpeta : ' ']) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ]) ?>

                    <!-- <input type="hidden" name="coleccion" value="Upload"> -->

                    <div class="form-group">
                        <?= Html::submitButton('Update', ['class' => 'btn btn-warning', 'name' => 'update-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>
