<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Platform\crud\CreateForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Create Platform';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="platform-crud-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('createFormSubmitted')): ?>

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
            Crea el Registro de una Plataforma con su Identificador y Credenciales.
            Thank you.
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'create-form']); ?>

                <?= $form->field($model, 'id')->textInput(['autofocus' => true, 'value' => isset($id)? $id : ' ']) ?>
                <?= $form->field($model, 'issuer')->textInput(['value' => isset($issuer)? $issuer : ' ']) ?>

                <!-- Credentials -->
                <?= $form->field($model, 'client_id')->textInput(['value' => isset($client_id)? $client_id : ' ']) ?>
                <?= $form->field($model, 'auth_login_url')->textInput(['value' => isset($auth_login_url)? $auth_login_url : ' ']) ?>
                <?= $form->field($model, 'auth_token_url')->textInput(['value' => isset($auth_token_url)? $auth_token_url : ' ']) ?>
                <?= $form->field($model, 'key_set_url')->textInput(['value' => isset($key_set_url)? $key_set_url : ' ']) ?>
                <?= $form->field($model, 'kid')->hiddenInput(['value' => isset($kid)? $kid : ' ']) ?>
                <?= $form->field($model, 'deployment')->textInput(['value' => isset($deployment)? $deployment : ' ']) ?>
                <?= $form->field($model, 'auth_server')->hiddenInput(['value' => isset($auth_server)? $auth_server : ' ']) ?>

                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>

                <!-- <input type="hidden" name="coleccion" value="Platform"> -->

                <div class="form-group">
                    <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'create-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>
