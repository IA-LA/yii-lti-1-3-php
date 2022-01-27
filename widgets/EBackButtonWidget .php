<?php
namespace app\widgets;

use Yii;

/**
 * A simple go back button widget
We all know that widgets are really useful. We can use the almost everywhere we want, and we can use the same code a lot of times ( Almost OOP ).

In this case we are going to create a really useful and simple widget. A GoBack button.

Why a widget ? Simple, we can extend the variables and make it as complex as we want, but will not be this case ;). We are going to pass just 1 variable if we need it.

Under your ext folder or widget folder ( mine under protected/ext/data ) create the following widget:
 *
 *
 * ```
 *
 * @author http://www.cristiantala.cl/crear-un-widget-en-yii-boton-volver/
 *
 */
class EBackButtonWidget extends CWidget {

    public $width = "150px";

    public function run() {

        echo CHtml::button('Back', array(
                'name' => 'btnBack',
                'class' => 'uibutton loading confirm',
                'style' => 'width:'.$this->width.';',
                'onclick' => "history.go(-1)",
            )
        );
    }

}
