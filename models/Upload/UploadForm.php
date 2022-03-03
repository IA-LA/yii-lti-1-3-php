<?php

namespace app\models\Upload;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $zipFile;
    public $verifyCode;

    public function rules()
    {
        return [
            //[['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024],
            [['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha']
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
     * @return array whether the model passes validation
     */
    public function upload()
    {
        if ($this->validate()) {
            $this->zipFile->saveAs('uploads/' . $this->zipFile->baseName . '.' . $this->zipFile->extension);
            //@return bool whether the model passes validation
            //return true;
            return ['result' => true, 'file' => $this->zipFile->baseName . '.' . $this->zipFile->extension];
        } else {
            //@return bool whether the model passes validation
            //return false;
            return ['result' => false, 'file' => $this->zipFile->baseName . '.' . $this->zipFile->extension];
        }
    }
}
