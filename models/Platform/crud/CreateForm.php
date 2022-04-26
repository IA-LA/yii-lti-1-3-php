<?php

namespace app\models\Platform\crud;

use Yii;
use yii\base\Model;

/**
 * CreateForm is the model behind the create form.
 */
class CreateForm extends Model
{
    public $id;
    public $issuer;             // This will usually look something like 'http://example.com'
    public $client_id;          // This is the id received in the 'aud' (Audience) during a launch
    public $auth_login_url;     // The platform's OIDC login endpoint
    public $auth_token_url;     // The platform's service authorization endpoint (OAuth)
    public $key_set_url;        // The platform's JWKS endpoint (https://tools.ietf.org/html/rfc7519)
    public $private_key_file;   // Relative path to the tool's private key
    public $kid;                // Key Identification (optional) Header Parameter with an unspecified structure.
    // MUST be a case-sensitive string. https://stackoverflow.com/questions/43867440/whats-the-meaning-of-the-kid-claim-in-a-jwt-token
    public $deployment;         // The deployment_id passed by the platform during launch
    public $auth_server;        // 3rd part authorization endpoint (OAuth)
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // id, url, subject and body are required
            [['id', 'issuer', 'client_id', 'auth_login_url', 'auth_token_url', 'key_set_url', 'deployment'], 'required'],
            // id has to be a valid ID hexadecimal 24 character address
            // ['id', 'filter', 'filter'=>'length', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965'],
            // ['id', 'in', 'is' => 24, 'tooLong' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965', 'tooShort' => 'Has to be a valid ObjectId hexadecimal 24 character address like this 5fc3860a81740b0ef098a965'],
            ['id', 'match', 'pattern'=>"/^[a-f,0-9]{24}$/u", 'message'=>'Has to be a valid ObjectId hexadecimal 24 character address like this: 5fc3860a81740b0ef098a965'],
            // url has to be a valid URL address
            //['url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['auth_login_url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['auth_token_url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['key_set_url', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
            ['auth_server', 'url', 'message'=>'Has to be a valid URL address like `http://contenido.uned.es/`'],
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
    public function create($url)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo('create@a.a')
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo(['a@a.a' => $this->id])
                ->setSubject('Create ' . $url)
                ->setTextBody('Creación de una Plataforma')
                ->send();

            return true;
        }
        return false;
    }
}
