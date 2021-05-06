<?php

namespace app\models\crud\Upload;

use Yii;
use yii\base\Model;
//use yii\web\UploadedFile;

class PublishForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $id;
    public $zipFile;
    public $verifyCode;

    public function rules()
    {
        return [
            // id is required
            [['id'], 'required'],
            ['id', 'match', 'pattern'=>"/^[a-f,0-9]{24}$/u", 'message'=>'Has to be a valid ObjectId hexadecimal 24 character address like this: 5fc3860a81740b0ef098a965'],
            //[['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'maxSize' => 1024*1024],
            //[['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
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
     * Sends a file to the specified folder using the information collected by this model.
     *
     * @return array whether the model passes validation
     */
    public function publish()
    {
        if ($this->validate() && is_dir('uploads/git/' . $this->id . '.' . 'git')) {
            // @return bool whether the model passes validation
            // return true;
            return ['result' => true, 'repositorio' => $this->id . '.' . 'git'];
        } else {
            // @return bool whether the model passes validation
            // return false;
            return ['result' => false, 'repositorio' => $this->id . '.' . `git`];
        }
    }
}
