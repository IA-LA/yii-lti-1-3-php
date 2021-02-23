<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form.
 */
class RegisterForm extends Model
{
    public $id;
    public $url;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // id, url, subject and body are required
            [['id', 'url', 'subject', 'body'], 'required'],
            // id has to be a valid ID alphanumerical 24 character address
//            ['id', 'filter', 'filter'=>'length', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965'],
//            ['id', 'in', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId alphanumerical 24 character address like this 5fc3860a81740b0ef098a965'],
            ['id', 'match', 'pattern'=>"/^[a-z,0-9]{24}$/u", 'message'=>'Has to be a valid ObjectId alphanumerical 24 character address like this: 5fc3860a81740b0ef098a965'],
            // url has to be a valid URL address
            ['url', 'url'],
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
                ->setTo('a@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
}
