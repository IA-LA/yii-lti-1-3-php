<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

use yii\helpers\Url;

/* LOGIN */
use app\models\LoginForm;

/*UPLOAD*/
use app\models\Upload\UploadForm;
use yii\web\UploadedFile;

/*UPLOADREGISTER*/
use app\models\Upload\UploadRegisterForm;

/*CREATE*/
use app\models\Upload\crud\CreateForm;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/*READ*/
use app\models\Upload\crud\ReadForm;
use yii\helpers\Html;

/* UPDATE */
use app\models\Upload\crud\UpdateForm;

/* LISTS */
use app\models\Upload\ListsForm;
use yii\data\ArrayDataProvider;

/* DLETE */
use app\models\Upload\crud\DeleteForm;

/*PUBLISH*/
use app\models\Upload\PublishForm;

/*PUBLISHREGISTER*/
use app\models\Upload\PublishRegisterForm;

class UploadController extends Controller
{

    /*CREATE*/
    /**
     * Displays create page.
     *
     * @return Response|string
     */
    public function actionCreate()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new CreateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('crud/create', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new CreateForm();

            // Información servidor
            //  https://www.php.net/manual/es/function.header.php
            ///////////////////////
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL
            //$url.= $_SERVER['REQUEST_URI'];
            //echo $_REQUEST['target_link_uri'];

            // Llamadas REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            // al servidor de SERVICIOS
            ///////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $url = Yii::$app->params['serverServiciosLti_local'];
            else
                $url = Yii::$app->params['serverServiciosLti_global'];

            //Envío del Formulario de Creación
            if ($model->load($request = Yii::$app->request->post()) && $model->create(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('createFormSubmitted');

                // POST CREATE (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                $client = new Client();

                if (Yii::$app->request->post('CreateForm')['id'] !== '') {
                    // http://10.201.54.31:49151/servicios/lti/lti13/create/register/coleccion/:coleccion
                    $ruta = '/create/register/coleccion/Upload';
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/coleccion/url_actividad/https://www.uned.es
                    $ruta = '/create/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('CreateForm')['publicacion']));
                }

                // Exception POST Upload
                try {
                    // Dirección de alojamiento
                    // del servidor de Git
                    //////////////////////
                    /// LOCAL puerto :9000
                    /// GLOBAL puerto:8000 o `.uned.es`
                    ///
                    if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
                        $serverGit = Yii::$app->params['serverGit_local'];
                        $carpetaGit = Yii::$app->params['carpetaGit_local'];
                        $serverPub = Yii::$app->params['serverPublicacion_local'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_local'];
                        $serverLti = Yii::$app->params['serverLti_local'];
                    }
                    else {
                        $serverGit = Yii::$app->params['serverGit_global'];
                        $carpetaGit = Yii::$app->params['carpetaGit_global'];
                        $serverPub = Yii::$app->params['serverPublicacion_global'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_global'];
                        $serverLti = Yii::$app->params['serverLti_global'];
                    }

                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        ->setMethod('POST')
                        //->setMethod('GET')
                        ->setUrl($url . $ruta)
                        //$_POST['CreateForm']['id']) Parámetros del registro
                        ->setData([
                            'id_actividad' => Yii::$app->request->post('CreateForm')['id'],
                            'url_actividad' => Yii::$app->request->post('CreateForm')['publicacion'],
                            'fichero' => Yii::$app->request->post('CreateForm')['fichero'],
                            'carpeta' => Yii::$app->request->post('CreateForm')['carpeta'],
                            'publicacion_url' => $serverPub . '/' . Yii::$app->request->post('CreateForm')['carpeta'], //['publicacion']
                            'git_url' => $serverGit . '/' . Yii::$app->request->post('CreateForm')['carpeta'] . '.git' //['git']
                            ]
                        )
                        ->setOptions([
                            //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                            'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                        ])
                        ->send();
                }
                catch (Exception $e1) {
                    // Exception POST Upload2
                    try {
                    }
                    catch (Exception $e2) {
                    }
                }
                //foreach ($request as $key => $value){
                //    echo "{$key} => {$value} ";
                //}
                // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [CreateForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'CreateForm') . print_r($request) . print_r($response) . '</p></div>');
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']['result'] != 'Existe') {
                    $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . '</p>';
                    $content .= '<div class="jumbotron">
                        <h1>Registro</h1>
                        <p class="lead">de Upload realizado correctamente.</p>' .
                        'ID: <code>' .
                        Html::encode($response->data['data']['register']['id_actividad']) .
                        '</code><br/>' .
                        'URL: <code>' .
                        Html::encode($response->data['data']['register']['url_actividad']) .
                        '</code><br/>' .
                        'FILE: <code>' .
                        Html::encode($response->data['data']['register']['upload']['fichero']) .
                        '</code><br/>' .
                        'FOLDER: <code>' .
                        'PUBLISH: <code>' .
                        Html::encode($response->data['data']['register']['upload']['carpeta']) .
                        '</code><br/>' .
                        'PUBLICACION: <code>' .
                        Html::encode($response->data['data']['register']['upload']['publicacion_url']) .
                        '</code><br/>' .
                        'GIT: <code>' .
                        Html::encode($response->data['data']['register']['upload']['git_url']) .
                        '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';

                } else {
                    $content = '<div><p/><p/><p/>';
                    $content .= '<p class="alert error-summary"> Registro: ' . Yii::$app->request->post('CreateForm...', 'error') . '</p>';
                    //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                    //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                    //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                    //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                    $content .= '<div class="jumbotron">
                        <h1>Error</h1>
                        <p class="lead">Las credenciales de Registro son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('CreateForm')['id'] . '</code><br/>' .
                        'URL: <code>' . Yii::$app->request->post('CreateForm')['publicacion'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }
            return $this->render('crud/create', [
                'model' => $model,
            ]);
        }
    }

    /*READ*/
    /**
     * Displays read page.
     *
     * @return Response|string
     */
    public function actionRead(){

        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new CreateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('crud/read', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new ReadForm();

            // Información servidor
            //  https://www.php.net/manual/es/function.header.php
            ///////////////////////
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL
            //$url.= $_SERVER['REQUEST_URI'];
            //echo $_REQUEST['target_link_uri'];

            // Llamadas REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            // al servidor de SERVICIOS
            ///////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $url = Yii::$app->params['serverServiciosLti_local'];
            else
                $url = Yii::$app->params['serverServiciosLti_global'];

            //Envío del Formulario de Consulta
            if ($model->load($request = Yii::$app->request->post()) && $model->read(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('readFormSubmitted');

                // GET Query (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                $client = new Client();

                if (Yii::$app->request->post('ReadForm')['id'] !== '') {
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Upload/id_actividad/5e0df19c0c2e74489066b43g
                    $ruta = '/read/coleccion/Upload/id_actividad/' . Yii::$app->request->post('ReadForm')['id'];
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Upload/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                    $ruta = '/read/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('ReadForm')['url']));
                }
                // Exception GET LTI1
                try {
                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        //->setMethod('POST')
                        ->setMethod('GET')
                        ->setUrl($url . $ruta) //$_POST['ListForm']['id'])
                        ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
                        ->setOptions([
                            //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                            'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                        ])
                        ->send();
                }
                catch (Exception $e1) {
                    // Exception GET LTI2
                    try {
                    }
                    catch (Exception $e2) {
                    }
                }

                //foreach ($request as $key => $value){
                //    echo "{$key} => {$value} ";
                //}
                // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [CreateForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'CreateForm') . print_r($request) . print_r($response) . '</p></div>');
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                if ($response->isOk && $response->data['result'] === 'ok') {
                    $content = '<div><p/><p/><p/><p class="alert alert-success"> Consulta: ' . $response->data['result'] . '</p>';
                    $content .= '<div class="jumbotron">
                        <h1>Consulta</h1>
                        <p class="lead">Copia las credenciales de acceso a la actividad.</p>' .
                        'ID: <code>' . $response->data['data']['launch_parameters']['iss'] . '</code><br/>' .
                        'LAUNCH URL: <code>' . Html::encode($response->data['data']['launch_url']) . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';

                } else {
                    $content = '<div><p/><p/><p/>';
                    $content .= '<p class="alert error-summary"> Consulta: ' . Yii::$app->request->post('ReadForm...', 'error') . '</p>';
                    //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                    //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                    //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                    //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                    $content .= '<div class="jumbotron">
                        <h1>Error</h1>
                        <p class="lead">Las credenciales de Consulta son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('ReadForm')['id'] . '</code><br/>' .
                        'URL: <code>' . Yii::$app->request->post('ReadForm')['url'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }

            return $this->render('crud/read', [
                'model' => $model,
            ]);
        }
    }

    /*UPDATE*/
    /**
     * Displays update page.
     *
     * @return Response|string
     */
    public function actionUpdate()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new UpdateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('crud/update', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new UpdateForm();

            // Información servidor
            //  https://www.php.net/manual/es/function.header.php
            ///////////////////////
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL
            //$url.= $_SERVER['REQUEST_URI'];
            //echo $_REQUEST['target_link_uri'];

            // Llamadas REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            // al servidor de SERVICIOS
            ///////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $url = Yii::$app->params['serverServiciosLti_local'];
            else
                $url = Yii::$app->params['serverServiciosLti_global'];

            //Envío del Formulario de Actualización
            if ($model->load($request = Yii::$app->request->post()) && $model->update(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('updateFormSubmitted');

                // POST UPDATE (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                $client = new Client();

                if (Yii::$app->request->post('UpdateForm')['id'] !== '') {
                    // http://10.201.54.31:49151/servicios/lti/lti13/update/register/coleccion/:coleccion
                    $ruta = '/update/coleccion/Upload/id_actividad/' . urlencode(Yii::$app->request->post('UpdateForm')['id']);
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/update/coleccion/coleccion/url_actividad/https://www.uned.es
                    $ruta = '/update/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('UpdateForm')['publicacion']));
                }

                // Exception POST Upload
                try {
                    // Dirección de alojamiento
                    // del servidor de Git
                    //////////////////////
                    /// LOCAL puerto :9000
                    /// GLOBAL puerto:8000 o `.uned.es`
                    ///
                    if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
                        $serverGit = Yii::$app->params['serverGit_local'];
                        $carpetaGit = Yii::$app->params['carpetaGit_local'];
                        $serverPub = Yii::$app->params['serverPublicacion_local'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_local'];
                        $serverLti = Yii::$app->params['serverLti_local'];
                    }
                    else {
                        $serverGit = Yii::$app->params['serverGit_global'];
                        $carpetaGit = Yii::$app->params['carpetaGit_global'];
                        $serverPub = Yii::$app->params['serverPublicacion_global'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_global'];
                        $serverLti = Yii::$app->params['serverLti_global'];
                    }

                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        ->setMethod('PUT')
                        //->setMethod('DELETE')
                        //->setMethod('POST')
                        //->setMethod('GET')
                        ->setUrl($url . $ruta)
                        //$_POST['UpdateForm']['id']) Parámetros del registro
                        ->setData([
                                'id_actividad' => Yii::$app->request->post('UpdateForm')['id'],
                                'url_actividad' => Yii::$app->request->post('UpdateForm')['publicacion'],
                                "upload" => [
                                    'fichero' => Yii::$app->request->post('UpdateForm')['fichero'],
                                    'carpeta' => Yii::$app->request->post('UpdateForm')['carpeta'],
                                    'publicacion_url' => $serverPub . '/' . Yii::$app->request->post('UpdateForm')['carpeta'], //['publicacion']
                                    'git_url' => $serverGit . '/' . Yii::$app->request->post('UpdateForm')['carpeta'] . '.git', //['git']],
                                    'actualizado' => '1'
                                ],
                                "user" => [
                                    'email' => Yii::$app->user->identity->username . '@lti.server',
                                    'nombre' => Yii::$app->user->identity->username,
                                    'rol' => Yii::$app->user->identity
                                ]
                            ]
                        )
                        ->setOptions([
                            //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                            'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                        ])
                        ->send();
                }
                catch (Exception $e1) {
                    // Exception POST Upload2
                    try {
                    }
                    catch (Exception $e2) {
                    }
                }
                //foreach ($request as $key => $value){
                //    echo "{$key} => {$value} ";
                //}
                // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [UpdateForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'UpdateForm') . print_r($request) . print_r($response) . '</p></div>');
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']['result'] != 'No existe') {
                    $content = '<div><p/><p/><p/><p class="alert alert-success"> Actualización: ' . $response->data['result'] . '</p>';
                    $content .= '<div class="jumbotron">
                        <h1>Actualización</h1>
                        <p class="lead">de Upload realizado correctamente.</p>' .
                        'ID: <code>' .
                        Html::encode($response->data['data']['register']['id_actividad']) .
                        '</code><br/>' .
                        'URL: <code>' .
                        Html::encode($response->data['data']['register']['url_actividad']) .
                        '</code><br/>' .
                        'FILE: <code>' .
                        Html::encode($response->data['data']['register']['upload']['fichero']) .
                        '</code><br/>' .
                        'FOLDER: <code>' .
                        Html::encode($response->data['data']['register']['upload']['carpeta']) .
                        '</code><br/>' .
                        'PUBLICACION: <code>' .
                        Html::encode($response->data['data']['register']['upload']['publicacion_url']) .
                        '</code><br/>' .
                        'GIT: <code>' .
                        Html::encode($response->data['data']['register']['upload']['git_url']) .
                        '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';

                } else {
                    $content = '<div><p/><p/><p/>';
                    $content .= '<p class="alert error-summary"> Actualización: ' . Yii::$app->request->post('UpdateForm...', 'error') . '</p>';
                    //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                    //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                    //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                    //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                    $content .= '<div class="jumbotron">
                        <h1>Error</h1>
                        <p class="lead">Las credenciales de Actualización son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('UpdateForm')['id'] . '</code><br/>' .
                        'URL: <code>' . $response->data['result'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }
            return $this->render('crud/update', [
                'model' => $model,
            ]);
        }
    }

    /*DELETE*/
    /**
     * Displays list page.
     *
     * @return Response|string
     *
     */
    public function actionDelete()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new DeleteForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('crud/delete', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new DeleteForm();

            // Información servidor
            //  https://www.php.net/manual/es/function.header.php
            ///////////////////////
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL
            //$url.= $_SERVER['REQUEST_URI'];
            //echo $_REQUEST['target_link_uri'];

            // Llamadas REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            // al servidor de SERVICIOS
            ///////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $url = Yii::$app->params['serverServiciosLti_local'];
            else
                $url = Yii::$app->params['serverServiciosLti_global'];

            //Envío del Formulario de Borrado
            if ($model->load($request = Yii::$app->request->post()) && $model->delete(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('deleteFormSubmitted');

                // DELETE Register (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                $client = new Client();

                // Buscar por _ID
                if (Yii::$app->request->post('DeleteForm')['id'] !== '') {
                    // http://10.201.54.31:49151/servicios/lti/lti13/delete/5e0df19c0c2e74489066b43g
                    $ruta = '/delete/coleccion/Upload/id_actividad/' . Yii::$app->request->post('DeleteForm')['id'];
                } else { // Buscar por URL
                    // http://10.201.54.31:49151/servicios/lti/lti13/delete/5e0df19c0c2e74489066b43g
                    $ruta = '/delete/coleccion/Upload/id_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('deleteForm')['id']));
                }

                // Exception DELETE LTI1
                try {
                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        ->setMethod('DELETE')
                        //->setMethod('POST')
                        //->setMethod('GET')
                        ->setUrl($url . $ruta) //$_POST['deleteForm']['id'])
                        ->setData(['id_actividad' => Yii::$app->request->post('DeleteForm')['id']])
                        ->setOptions([
                            //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                            'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                        ])
                        ->send();
                }
                catch (Exception $e1) {
                    // Exception DELETE LTI2
                    try {
                    }
                    catch (Exception $e2) {
                    }
                }
                //foreach ($request as $key => $value){
                //    echo "{$key} => {$value} ";
                //}
                // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [deleteForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fdelete [subject] => a [body] => aa [verifyCode] => zuvagi ) [delete-button] => )
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'deleteForm') . print_r($request) . print_r($response) . '</p></div>');
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']) {
                    $content = '<div><p/><p/><p/><p class="alert alert-success"> Borrado: ' . $response->data['result'] . '</p>';
                    $content .= '<div class="jumbotron">
                            <h1>Borrado</h1>
                            <p class="lead">Borradas las credenciales de acceso a la actividad.</p>' .
                        'REGISTRO: ' . Yii::$app->request->post('DeleteForm')['id'] . ' <code>' . Html::encode($response->data['data']) . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                    $content .= '</div>';

                } else {
                    $content = '<div><p/><p/><p/>';
                    $content .= '<p class="alert error-summary"> Borrado: ' . Yii::$app->request->post('DeleteForm...', 'error') . '</p>';
                    //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                    //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                    //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                    //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                    $content .= '<div class="jumbotron">
                            <h1>Error</h1>
                            <p class="lead">Las credenciales de Borrado son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('DeleteForm')['id'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }
            return $this->render('crud/delete', [
                'model' => $model,
            ]);
        }
    }

    /*LISTs*/
    /**
     * Displays list page.
     *
     * @return Response|string
     *
     */
    public function actionLists()
    {

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new CreateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('lists', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new ListsForm();

            // Información servidor
            //  https://www.php.net/manual/es/function.header.php
            ///////////////////////
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL
            //$url.= $_SERVER['REQUEST_URI'];
            //echo $_REQUEST['target_link_uri'];

            // Llamadas REST
            //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
            //  https://www.php.net/manual/en/context.http.php
            // Obtiene la configuración de las actividades con una llamada de lectura `GET`
            // al servidor de SERVICIOS
            ///////////////////
            /// LOCAL puerto :9000
            /// GLOBAL puerto:8000 o `.uned.es`
            ///
            if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                $url = Yii::$app->params['serverServiciosLti_local'];
            else
                $url = Yii::$app->params['serverServiciosLti_global'];

            //Envío del Formulario de Consulta
            if ($model->load($request = Yii::$app->request->post()) && $model->lists(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('listsFormSubmitted');

                if ((Yii::$app->request->post('ListsForm')['id'] !== '') || (Yii::$app->request->post('ListsForm')['url'] !== '')) {
                    // Listado ListView
                    return $this->redirect(array('lists_crud/index',
                        'title' => 'Listado',
                        'return' => 'lists',
                        'model' => $model,
                        'id' => Yii::$app->request->post('ListsForm')['id'],
                        'url' => Yii::$app->request->post('ListsForm')['url'],
                        'formulario' => 'ListsForm',
                    ));
                    // View from another Controller
                    return $this->render('//lists_crud/index', [
                        'title' => 'Listado',
                        'return' => 'lists',
                        'model' => $model,
                        'listDataProvider' => new ArrayDataProvider([
                            'allModels' => $responseModels,
                            'pagination' => [
                                'pageSize' => 5
                            ],
                            'sort' => [
                                'attributes' => ['id'],
                            ],
                        ]),
                    ]);
                } else { // BAD REQUEST
                    $content = '<div><p/><p/><p/>';
                    $content .= '<p class="alert error-summary"> Consulta: ' . Yii::$app->request->post('ListsForm...', 'error') . '</p>';
                    //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                    //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                    //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                    //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                    $content .= '<div class="jumbotron">
                        <h1>Error</h1>
                        <p class="lead">Las credenciales de Consulta son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('ListsForm')['id'] . '</code><br/>' .
                        'URL: <code>' . Yii::$app->request->post('ListsForm')['url'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="index.php?r=crud%2Flists">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }

            return $this->render('lists', [
                'model' => $model,
            ]);
        }
    }

    /*PUBLISH*/
    /**
     * Displays upload page.
     *
     * @return string
     */
    public function actionPublish()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new PublishForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('publish', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new PublishForm();

            // form is send successfully
            if (Yii::$app->request->isPost) {
                Yii::$app->session->setFlash('publishFormSubmitted');

                $publish = $model->publish(Yii::$app->request->post('PublishForm')['id']);
                // publish does successfully
                if ($publish['result']) {
                    Yii::$app->session->setFlash('publishIsPosible');
                    return $this->render('publish', ['model' => $model, "namedir" => $publish['resultado']]);
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo "<i>' . $upload['file'] .'</i>" subido correctamente</p></div>' . '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fupload">Atrás</a></p>');
                    //return $this->render('publish', ['model' => $model]);
                    //return;
                }
                else {
                    Yii::$app->session->setFlash('publishIsNotPosible');
                    return $this->render('publish', ['model' => $model, "namedir" => $publish['resultado']]);
                }
            }

            return $this->render('publish', ['model' => $model]);
        }
    }

    /*PUBLISHREGISTER*/
    /**
     * Displays publishregister page.
     *
     * @return string
     */
    public function actionPublishregister()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new PublishCreateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('publishregister', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new PublishRegisterForm();

            // form is send successfully
            if (Yii::$app->request->isPost) {
                Yii::$app->session->setFlash('publishregisterFormSubmitted');

                $publish = $model->publishregister(Yii::$app->request->post('PublishRegisterForm')['id']);
                // publish does successfully
                if ($publish['result']) {
                    Yii::$app->session->setFlash('publishregisterIsPosible');
                    return $this->render('publishregister', ['model' => $model, "namedir" => $publish['resultado']]);
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo "<i>' . $upload['file'] .'</i>" subido correctamente</p></div>' . '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fupload">Atrás</a></p>');
                    //return $this->render('publishregister', ['model' => $model]);
                    //return;
                }
                else {
                    Yii::$app->session->setFlash('publishregisterIsNotPosible');
                    return $this->render('publishregister', ['model' => $model, "namedir" => $publish['resultado']]);
                }
            }

            return $this->render('publishregister', ['model' => $model]);
        }
    }

    /*UPLOAD*/
    /**
     * Displays upload page.
     *
     * @return string
     */
    public function actionUpload()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new UploadForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('upload', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new UploadForm();

            if (Yii::$app->request->isPost) {
                $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
                $upload = $model->upload();
                if ($upload['result']) {
                    // file is uploaded successfully
                    Yii::$app->session->setFlash('uploadFormSubmitted');
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo "<i>' . $upload['file'] .'</i>" subido correctamente</p></div>' . '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fupload">Atrás</a></p>');
                    return $this->render('upload', ['model' => $model, "file" => $upload['file']]);
                    //return $this->render('upload', ['model' => $model]);
                    //return;
                }
            }

            return $this->render('upload', ['model' => $model]);
        }
    }

    /*UPLOADREGISTER*/
    /**
     * Displays uploadregister page.
     *
     * @return string
     */
    public function actionUploadregister()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new UploadRegisterForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('uploadregister', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new UploadRegisterForm();

            if (Yii::$app->request->isPost) {
                $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
                $uploadregister = $model->uploadregister();

                // If file is uploaded successfully
                ///////////////////////////////////
                if ($uploadregister['result']) {
                    Yii::$app->session->setFlash('uploadregisterFormSubmitted');
                    // Define Nombre de carpeta, de la web de publicación y del git
                    $namefile = Yii::$app->user->identity->id . date('YmdHisu') . 'a';

                    // REGISTER Publicación & LTI
                    // --------------------------
                    // LLAMADA POST CREATE 1
                    // Información servidor
                    //  https://www.php.net/manual/es/function.header.php
                    ///////////////////////
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                        $url = "https://";
                    else
                        $url = "http://";
                    // Append the host(domain name, ip) to the URL.
                    $url .= $_SERVER['HTTP_HOST'];

                    // Append the requested resource location to the URL
                    //$url.= $_SERVER['REQUEST_URI'];
                    //echo $_REQUEST['target_link_uri'];

                    // Llamadas REST
                    //  https://stackoverflow.com/questions/2445276/how-to-post-data-in-php-using-file-get-contents
                    //  https://www.php.net/manual/en/context.http.php
                    // Envía la configuración de las actividades con una llamada de escritura `POST`
                    // al servidor de SERVICIOS
                    ///////////////////
                    /// LOCAL puerto :9000
                    /// GLOBAL puerto:8000 o `.uned.es`
                    ///
                    if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000'))
                        $url = Yii::$app->params['serverServiciosLti_local'];
                    else
                        $url = Yii::$app->params['serverServiciosLti_global'];

                    // Dirección de alojamiento
                    // del servidor de Git
                    //////////////////////
                    /// LOCAL puerto :9000
                    /// GLOBAL puerto:8000 o `.uned.es`
                    ///
                    if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
                        $carpetaGit = Yii::$app->params['carpetaGit_local'];
                        $serverGit = Yii::$app->params['serverGit_local'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_local'];
                        $serverPub = Yii::$app->params['serverPublicacion_local'];
                        $serverLti = Yii::$app->params['serverLti_local'];
                    }
                    else {
                        $carpetaGit = Yii::$app->params['carpetaGit_global'];
                        $serverGit = Yii::$app->params['serverGit_global'];
                        $carpetaPub = Yii::$app->params['carpetaPublicacion_global'];
                        $serverPub = Yii::$app->params['serverPublicacion_global'];
                        $serverLti = Yii::$app->params['serverLti_global'];
                    }

                    // POST
                    // POST CREATE (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                    $client = new Client();

                    // Registro por ID
                    if ($namefile !== '') {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/register/
                        $ruta = '/create/register/coleccion/Lti';
                        // REgistro por URL
                    } else {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/Lti/url_actividad/https://www.uned.es
                        $ruta = '/create/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode($serverPub . '/' . $namefile));
                    }

                    // Exception POST LTI
                    try {
                        $response = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            //->setMethod('GET')
                            ->setUrl($url . $ruta) //$_POST['RegisterForm']['id'])
                            ->setData([
                                'id_actividad' => $namefile,
                                'url_actividad' => $serverPub . '/' . $namefile,
                                "user" => [
                                    'email' => Yii::$app->user->identity->username . '@lti.server',
                                    'nombre' => Yii::$app->user->identity->username,
                                    'rol' => Yii::$app->user->identity->id
                                ]
                            ])
                            ->setOptions([
                                //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                                'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                            ])
                            ->send();
                    }
                    catch (Exception $e1) {
                        // Exception POST LTI2
                        try {
                        }
                        catch (Exception $e2) {
                        }
                    }
                    //foreach ($request as $key => $value){
                    //    echo "{$key} => {$value} ";
                    //}
                    // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [RegisterForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'RegisterForm') . print_r($request) . print_r($response) . '</p></div>');
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                    // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                    if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']['result'] === 'Existe') {
                        $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . '</p>';
                        $content .= '<div class="jumbotron">
                            <h1>Registro</h1>
                            <p class="lead">Copia las credenciales de acceso a la actividad.</p>' .
                            'LAUNCH URL: <code>' . Html::encode($response->data['data']['launch_url']) . '</code><br/>' .
                            '<p/><p/><p/>' .
                            '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                        $content .= '</div>';

                    } else {
                        $content = '<div><p/><p/><p/>';
                        $content .= '<p class="alert error-summary"> Registro: ' . Yii::$app->request->post('RegisterForm...', 'error') . '</p>';
                        //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                        //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                        //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                        //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                        $content .= '<div class="jumbotron">
                            <h1>Error</h1>
                            <p class="lead">Las credenciales de Registro son erróneas.</p>' .
                            'ID:  <code>' . $namefile . '</code><br/>' .
                            'URL: <code>' . $serverPub . '/' . $namefile . '</code><br/>' .
                            '<p/><p/><p/>' .
                            '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                        $content .= '</div>';
                    }

                    // REGISTER Upload & Git
                    // ---------------------
                    // LLAMADA POST CREATE 1
                    // Información servidor
                    //  https://www.php.net/manual/es/function.header.php

                    // POST
                    // POST CREATE (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                    $client = new Client();

                    if ($namefile !== '') {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/register/coleccion/:coleccion
                        $ruta = '/create/register/coleccion/Upload';
                    } else {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/coleccion/url_actividad/https://www.uned.es
                        $ruta = '/create/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('RegisterForm')['url']));
                    }

                    // Exception POST Upload
                    try {
                        // Dirección de alojamiento
                        // del servidor de Git
                        //////////////////////
                        /// LOCAL puerto :9000
                        /// GLOBAL puerto:8000 o `.uned.es`
                        ///
                        if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
                            $serverPublicacion = Yii::$app->params['serverPublicacion_local'];
                            $carpetaPublicacion = Yii::$app->params['carpetaPublicacion_local'];
                            $serverGit = Yii::$app->params['serverGit_local'];
                            $carpetaGit = Yii::$app->params['carpetaGit_local'];
                            $serverLti = Yii::$app->params['serverLti_local'];
                        }
                        else {
                            $serverPublicacion = Yii::$app->params['serverPublicacion_global'];
                            $carpetaPublicacion = Yii::$app->params['carpetaPublicacion_global'];
                            $serverGit = Yii::$app->params['serverGit_global'];
                            $carpetaGit = Yii::$app->params['carpetaGit_global'];
                            $serverLti = Yii::$app->params['serverLti_global'];
                        }

                        $response = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            ->setMethod('POST')
                            //->setMethod('GET')
                            ->setUrl($url . $ruta)
                            //$_POST['RegisterForm']['id']) Parámetros del registro
                            ->setData([
                                    'id_actividad' => $namefile,
                                    'url_actividad' => $serverPublicacion . '/' . $namefile,
                                    "upload" => [
                                        'fichero' => $uploadregister['file'],
                                        'carpeta' => $namefile,
                                        'publicacion_url' => $serverPublicacion . '/' . $namefile, //['publicacion']
                                        'git_url' => $serverGit . '/' . $namefile . '.git', //['git']
                                        'actualizado' => '0'
                                    ],
                                    "user" => [
                                        'email' => Yii::$app->user->identity->username . '@lti.server',
                                        'nombre' => Yii::$app->user->identity->username,
                                        'rol' => Yii::$app->user->identity->id
                                    ]
                                ]
                            )
                            ->setOptions([
                                //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                                'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                            ])
                            ->send();
                    }
                    catch (Exception $e1) {
                        // Exception POST Upload2
                        try {
                        }
                        catch (Exception $e2) {
                        }
                    }
                    //foreach ($request as $key => $value){
                    //    echo "{$key} => {$value} ";
                    //}
                    // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [RegisterForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'RegisterForm') . print_r($request) . print_r($response) . '</p></div>');
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                    // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                    if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']['result'] === 'Existe') {
                        $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . '</p>';
                        $content .= '<div class="jumbotron">
                            <h1>Registro</h1>
                            <p class="lead">de Upload realizado correctamente.</p>' .
                            'ID: <code>' .
                            Html::encode($response->data['data']['register']['id_actividad']) .
                            '</code><br/>' .
                            'URL: <code>' .
                            Html::encode($response->data['data']['register']['url_actividad']) .
                            '</code><br/>' .
                            'FILE: <code>' .
                            Html::encode($response->data['data']['register']['upload']['fichero']) .
                            '</code><br/>' .
                            'FOLDER: <code>' .
                            'PUBLISH: <code>' .
                            Html::encode($response->data['data']['register']['upload']['carpeta']) .
                            '</code><br/>' .
                            'PUBLICACION: <code>' .
                            Html::encode($response->data['data']['register']['upload']['publicacion_url']) .
                            '</code><br/>' .
                            'GIT: <code>' .
                            Html::encode($response->data['data']['register']['upload']['git_url']) .
                            '</code><br/>' .
                            '<p/><p/><p/>' .
                            '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                        $content .= '</div>';

                    } else {
                        $content = '<div><p/><p/><p/>';
                        $content .= '<p class="alert error-summary"> Registro: ' . Yii::$app->request->post('RegisterForm...', 'error') . '</p>';
                        //$content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . ArrayHelper::isAssociative($request) . '</p></div><br/>';
                        //$content.='<div><p/><p/><p/><p class="alert alert-success"> REQUEST : ' . print_r($request) . '</p></div><br/>';
                        //$content .= '<div><p/><p/><p/><p class="alert alert-success">RESPONSE: ' . print_r($response) . '</p></div><br/>';
                        //$content.= '<button class="btn btn-info" onclick="history.go(-1);return false;">Atrás</button>';
                        $content .= '<div class="jumbotron">
                            <h1>Error</h1>
                            <p class="lead">Las credenciales de Registro son erróneas.</p>' .
                            'ID:  <code>' . $namefile . '</code><br/>' .
                            'URL: <code>' . $serverPublicacion . '/' . $namefile . '</code><br/>' .
                            '<p/><p/><p/>' .
                            '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                        </div>';
                        $content .= '</div>';
                    }

                    // Return file is uploaded successfully
                    ///////////////////////////////////////
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo "<i>' . $upload['file'] .'</i>" subido correctamente</p></div>' . '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fuploadregister">Atrás</a></p>');
                    return $this->render('uploadregister', ['model' => $model, "file" => $uploadregister['file'], "namefile" => $namefile]);
                    //return $this->render('uploadregister', ['model' => $model]);
                    //return;
                }
            }

            return $this->render('uploadregister', ['model' => $model]);
        }
    }

    /*UPLOADREGISTER_old*/
    /**
     * Displays uploadregister_old page.
     *
     * @return string
     */
    public function actionUploadregister_old()
    {

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new UploadRegisterForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('uploadregister', [
                    'model' => $model2,
                ]);
            }

            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);

        }
        else {
            $model = new UploadRegisterForm();

            if (Yii::$app->request->isPost) {
                $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
                $uploadregister = $model->uploadregister();
                if ($uploadregister['result']) {
                    // file is uploaded successfully
                    Yii::$app->session->setFlash('uploadregisterFormSubmitted');

                    // Return file is uploaded successfully
                    //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo "<i>' . $upload['file'] .'</i>" subido correctamente</p></div>' . '<p><a class="btn btn-lg btn-success" href="index.php?r=site%2Fuploadregister">Atrás</a></p>');
                    return $this->render('uploadregister', ['model' => $model, "file" => $uploadregister['file']]);
                    //return $this->render('uploadregister', ['model' => $model]);
                    //return;
                }
            }

            return $this->render('uploadregister', ['model' => $model]);
        }
    }

}