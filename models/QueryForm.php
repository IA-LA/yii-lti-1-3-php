<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * QueryForm is the model behind the query form.
 */
class QueryForm extends Model
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
            // id, url are required
            //[['id', 'url'], 'required'],
            // id, url Either one field is required
            ['id', 'required',
                'message' => 'Either id or url is required.',
                'when' => function($model) { return empty($model->url); }
            ],
            ['url', 'required',
                'message' => 'Either id or url is required.',
                'when' => function($model) { return empty($model->id); }
            ],
            // id has to be a valid ID alphanumerical 24 character address
            ['id', 'match', 'pattern'=>"/^[a-z,0-9]{24}$/u", 'message'=>'Has to be a valid Mongo ObjectId alphanumerical 24 character address like this: 5fc3860a81740b0ef098a965'],
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
    public function query($url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('query@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject('Query')
                ->setTextBody('Consulta de información de una Actividad')
                ->send();

            return true;
        }
        return false;
    }
}
