<?php

namespace app\models\crud\Upload;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form.
 */
class CreateForm extends Model
{
    public $id;
    public $url;
    public $publicacion;
    public $git;
    public $fichero;
    public $carpeta;
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // id and url are required
            [['id', 'fichero', 'carpeta'], 'required'],
            // [['id', 'publicacion', 'git', 'fichero', 'carpeta'], 'required'],
            // id has to be a valid ID hexadecimal 24 character address
//            ['id', 'filter', 'filter'=>'length', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965'],
//            ['id', 'in', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965'],
            ['id', 'match', 'pattern'=>"/^[a-f,0-9]{24}$/u", 'message'=>'Has to be a valid ObjectId hexadecimal 24 character address like this: 5fc3860a81740b0ef098a965'],
            // url has to be a valid URL address
            //['url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['publicacion', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['git', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an url to the specified url address using the information collected by this model.
     * @param string $url the target url address
     * @return bool whether the model passes validation
     */
    public function register($url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('register@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject('Register ' . $url)
                ->setTextBody('Registro de una Actividad')
                ->send();

            return true;
        }
        return false;
    }
}
