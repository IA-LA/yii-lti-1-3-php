<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $zipFile;

    public function rules()
    {
        return [
            //[['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024],
            [['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->zipFile->saveAs('uploads/' . $this->zipFile->baseName . '.' . $this->zipFile->extension);
            return true;
        } else {
            return false;
        }
    }
}
