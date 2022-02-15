<?php
// YOUR_APP/views/upload/lists/index.php
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Upload\ListsForm */

use yii\bootstrap\ActiveForm;

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

// Remember current URL
Url::remember();

?>
<div class="header">
    <h3><?= Html::encode($title); ?> Upload</h3>
</div>
<div class="row">
    <h4><pre>FICHERO</pre></h4>
    <h5><pre>Nombre                                   Publicación            Git            Acción</pre></h5>
    <?php
    if (Yii::$app->session->hasFlash('CrudFormSubmitted')):
        echo 'CrudFormSubmitted';
        $form = ActiveForm::begin(['id' => 'lists-form']);
            $form->field($model, 'id')->textInput(['autofocus' => true]);
            $form->field($model, 'url');
            $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ]);
            Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'lists-button']);
        ActiveForm::end();
    else:?>
    <?=
        ListView::widget([
            'options' => [
                'tag' => 'div',
            ],
            'dataProvider' => $listDataProvider,
            'itemView' => function ($model, $key, $index, $widget) {
                $itemContent = $this->render('_list_item',['model' => $model]);

                /* Display an Advertisement after the first list item */
                if ($index == 0) {
                    $adContent = $this->render('_ad');
                    $itemContent .= $adContent;
                }

                return $itemContent;

                /* Or if you just want to display the list item only: */
                // return $this->render('_list_item',['model' => $model]);
            },
            'itemOptions' => [
                'tag' => false,
            ],
            'summary' => '',

            /* do not display {summary} */
            'layout' => '{items}{pager}',

            'pager' => [
                'firstPageLabel' => 'First',
                'lastPageLabel' => 'Last',
                'maxButtonCount' => 4,
                'options' => [
                    'class' => 'pagination col-xs-12'
                ]
            ],

        ]);
        ?>
    <?php
    endif;
    ?>
</div>
<hr/>
<p><a class="btn btn-lg btn-warning" href="index.php?r=<?= strtolower(Html::encode($controller)); ?>%2F<?= Html::encode($back); ?>">Atrás</a></p>
<div class="footer">
</div>

