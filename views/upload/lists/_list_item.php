<?php
// YOUR_APP/views/upload/lists/_list_item.php

use yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<article class="list-item col-sm-12" data-key="<?= $model['id'] ?>">
    <h4><?= Html::encode($model['id']); ?></h4>
    <h5>
        <?= $model['title'] ?> <?= $model['link'] ?>  <?= $model['link1'] ?> <img src="<?= $model['image'] ?>" alt="<?= $model['id'] ?>" width="30"> <?= $model['buttonC'] ?> <?= $model['buttonR'] ?> <?= $model['buttonU'] ?> <?= $model['buttonD'] ?>
        <?php
            Modal::begin([
                'header' => '<h2>End Form</h2>',
                'toggleButton' => ['label' => 'End',  'class' => 'btn btn-sm btn-warning'],
            ]);

            echo '<div class="row">
                    <div class="col-lg-5">
        
                        <?php $form = ActiveForm::begin(["id" => "read-form"]); ?>
        
                            <?= $form->field($model, "id")->textInput(["autofocus" => true]) ?>
        
                            <?= $form->field($model, "url") ?>
        
                            <?= $form->field($model, "verifyCode")->widget(Captcha::className(), [
                                "template" => "<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>",
                            ]) ?>
        
                            <div class="form-group">
                                <?= Html::submitButton("Submit", ["class" => "btn btn-primary", "name" => "read-button"]) ?>
                            </div>
        
                        <?php ActiveForm::end(); ?>
        
                    </div>
                </div>';

            Modal::end();
        ?>
    </h5>
</article>

