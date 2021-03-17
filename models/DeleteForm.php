<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * DeleteForm is the model behind the delete form.
 */
class DeleteForm extends Model
{
    public $id;
    public $url;
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // id, url, subject and body are required
            [['id'], 'required'],
            // id has to be a valid ID alphanumerical 24 character address
//            ['id', 'filter', 'filter'=>'length', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965'],
//            ['id', 'in', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965'],
            ['id', 'match', 'pattern'=>"/^[a-z,0-9]{24}$/u", 'message'=>'Has to be a valid ObjectId alphanumerical 24 character address like this: 5fc3860a81740b0ef098a965'],
            // url has to be a valid URL address
            ['url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
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
    public function delete($url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('delete@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject('Delete ' . $url)
                ->setTextBody('Borrado de una Actividad')
                ->send();

            return true;
        }
        return false;
    }
}
