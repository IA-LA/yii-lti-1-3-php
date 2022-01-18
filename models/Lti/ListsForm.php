<?php

namespace app\models\crud\Lti;

use Yii;
use yii\base\Model;

/**
 * QueryForm is the model behind the query form.
 */
class ListsForm extends Model
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
                'when' => function($model) { return empty($model->url); },
                'whenClient' => "function (attribute, value) { return $('#url').val() == 'https://a.a.a'; }"
            ],
            ['url', 'required',
                'message' => 'Either id or url is required.',
                'when' => function($model) { return empty($model->id); },
                'whenClient' => "function (attribute, value) { return $('#id').val() == '00000000000000000000000'; }"
            ],
            // id has to be a valid ID hexadecimal 24 character address
            //['id', 'match', 'pattern'=>"/^[a-f,0-9]{24}$/u", 'message'=>'Has to be a valid Mongo ObjectId hexadecimal 24 character address like this: `5fc3860a81740b0ef098a965`'],
            //['id', 'match', 'pattern'=>"/^([a-f,0-9]{24})|[*]$/u", 'message'=>'Has to be a valid Mongo ObjectId hexadecimal 24 character address like this: `5fc3860a81740b0ef098a965` or the wildcard * to list all registers.'],
            ['id', 'match', 'pattern'=>"/^([a-f0-9]{24})|([a-f0-9]{1:23}[*])|([*][a-f0-9]{1:23})|([*][a-f0-9]{1:22}[*])|([*])$/u", 'message'=>'Has to be a valid Mongo ObjectId hexadecimal 24 character address like this: `5fc3860a81740b0ef098a965` or using the wildcard * to list all registers in this way: *, HH..HH*, *HH..HH, *HH..HH*.'],
            // url has to be a valid URL address
            //['url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['url', 'match', 'pattern'=>"/^(.*)|(.*)[*]|[*](.*)|[*](.*)[*]|([*])$/u", 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/` or using the wildcard * to list all registers in this way: *, HH..HH*, *HH..HH, *HH..HH*.'],
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
    public function lists($url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('query@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject('Lists ' . $url)
                ->setTextBody('Lista de información de una Actividad')
                ->send();

            return true;
        }
        return false;
    }
}
