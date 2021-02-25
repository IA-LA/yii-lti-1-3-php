<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

/*UPLOAD*/
use app\models\UploadForm;
use yii\web\UploadedFile;

/*REGISTER*/
use app\models\RegisterForm;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/*QUERY*/
use app\models\QueryForm;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /*UPLOAD*/
    /**
     * Displays upload page.
     *
     * @return string
     */
    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
		        return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo subido correctamente</p></div>');
		        //return $this->render('upload', ['model' => $model]);
		        //return;
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    /*REGISTER*/
    /**
     * Displays register page.
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        $model = new RegisterForm();

        // Información servidor
        //  https://www.php.net/manual/es/function.header.php
        ///////////////////////
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.
            $url.= $_SERVER['HTTP_HOST'];

        // Append the requested resource location to the URL
        //$url.= $_SERVER['REQUEST_URI'];
        //echo $_REQUEST['target_link_uri'];

        // Llamadas REST
        //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
        //  https://www.php.net/manual/en/context.http.php
        // Obtiene la configuración de las actividades con una llamada de lectura `GET`
        // al servidor de SERVICIOS
        ///////////////////
        if(strpos($url, '10.201.54.'))
            $url = "http://10.201.54.31:49151/servicios/lti/lti13";
        else
            $url= "http://192.168.0.31:49151/servicios/lti/lti13";

        // GET/POST Register (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
        $client = new Client();

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            //->setMethod('POST')
            ->setMethod('GET')
            ->setUrl($url . '/read/5fc3860a81740b0ef098a983')
            ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
            ->setOptions([
                //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
            ])
            ->send();

        if ($model->load($request = Yii::$app->request->post()) && $model->register(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('registerFormSubmitted');

            //foreach ($request as $key => $value){
            //    echo "{$key} => {$value} ";
            //}
            // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [RegisterForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
            //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'RegisterForm') . print_r($request) . print_r($response) . '</p></div>');
            //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
            // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
            if ($response->isOk) {
                $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . ' ID: ' . $response->data['data']['launch_parameters']['iss'] . ' URL: ' . $response->data['data']['launch_url'] . '</p></div><br/>';
            }
            else{
                $content='<div><p/><p/><p/><p class="alert alert-success"> Registro realizado: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                $content.='<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
            }
            return $this->renderContent($content);
            //return $this->refresh();
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /*QUERY*/
    /**
     * Displays query page.
     *
     * @return Response|string
     */
    public function actionQuery()
    {
        $model = new QueryForm();

        // Información servidor
        //  https://www.php.net/manual/es/function.header.php
        ///////////////////////
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.
        $url.= $_SERVER['HTTP_HOST'];

        // Append the requested resource location to the URL
        //$url.= $_SERVER['REQUEST_URI'];
        //echo $_REQUEST['target_link_uri'];

        // Llamadas REST
        //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
        //  https://www.php.net/manual/en/context.http.php
        // Obtiene la configuración de las actividades con una llamada de lectura `GET`
        // al servidor de SERVICIOS
        ///////////////////
        if(strpos($url, '10.201.54.'))
            $url = "http://10.201.54.31:49151/servicios/lti/lti13";
        else
            $url= "http://192.168.0.31:49151/servicios/lti/lti13";

        if ($model->load($request = Yii::$app->request->post()) && $model->query(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('registerFormSubmitted');

            // GET Register (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
            $client = new Client();

            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                //->setMethod('POST')
                ->setMethod('GET')
                ->setUrl($url . '/read/' . $request->get('RegisterForm')->get('.id'))
                ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
                ->setOptions([
                    //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                    'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                ])
                ->send();

            //foreach ($request as $key => $value){
            //    echo "{$key} => {$value} ";
            //}
            // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [RegisterForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
            //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'RegisterForm') . print_r($request) . print_r($response) . '</p></div>');
            //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
            // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
            if ($response->isOk) {
                $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . ' ID: ' . $response->data['data']['launch_parameters']['iss'] . ' URL: ' . $response->data['data']['launch_url'] . '</p></div><br/>';
            } else {
                $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro realizado: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                $content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
            }
            return $this->renderContent($content);
        }

        return $this->render('query', [
            'model' => $model,
        ]);
    }
}
