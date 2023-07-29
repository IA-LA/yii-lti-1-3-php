<?php

namespace app\models\Upload;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UpdateRegisterForm extends Model
{
    /**
     * @var UpdatedFile
     */
    public $id;
    public $url;
    public $zipFile;
    public $verifyCode;

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
            ['id', 'match', 'pattern'=>"/^[a-f,0-9]{24}$/u", 'message'=>'Has to be a valid Mongo ObjectId hexadecimal 24 character address like this: 5fc3860a81740b0ef098a965'],
            // url has to be a valid URL address
            ['url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            //[['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024],
            [['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
            // verifyCode needs to be entered correctly
            //['verifyCode', 'captcha'],
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
     * Sends a zip to the specified folder using the information collected by this model.
     *
     * @param $id
     * @param $url
     * @return array whether the model passes validation
     */
    public function updateregister($id, $url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('read@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $id])
                ->setSubject('Update ' . $this->zipFile->baseName . '.' . $this->zipFile->extension)
                ->setTextBody('Actualización de información de una Upload')
                ->send();
            $this->zipFile->saveAs('uploads/' . $this->zipFile->baseName . '.' . $this->zipFile->extension);
            //@return bool whether the model passes validation
            //return true;
            return ['result' => true, 'file' => $this->zipFile->baseName . '.' . $this->zipFile->extension, 'id' => $id, 'url' => $url];
        } else {
            //@return bool whether the model passes validation
            //return false;
            return ['result' => false, 'file' => $this->zipFile->baseName . '.' . $this->zipFile->extension, 'id' => $this->id, 'url' => $this->url];
        }
    }
}
