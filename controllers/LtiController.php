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

/*CREATE*/
use app\models\Lti\crud\CreateForm;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/*READ*/
use app\models\Lti\crud\ReadForm;
use yii\helpers\Html;

/* LISTS */
use app\models\Lti\ListsForm;
use yii\data\ArrayDataProvider;

/* DLETE */
use app\models\Lti\crud\DeleteForm;

/* UPDATE */
use app\models\Lti\crud\UpdateForm;

class LtiController extends Controller
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
                    $ruta = '/create/register/coleccion/Lti';
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/Lti/url_actividad/https://www.uned.es
                    $ruta = '/create/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('CreateForm')['url']));
                }

                // Exception POST LTI
                try {
                    // Dirección de alojamiento
                    // del servidor LTI
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
                    // LLAMADA RUTA
                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        ->setMethod('POST')
                        //->setMethod('GET')
                        ->setUrl($url . $ruta) //$_POST['CreateForm']['id']) Parámetros de creación del registro
                        ->setData([
                            'id_actividad' => Yii::$app->request->post('CreateForm')['id'],
                            'url_actividad' => Yii::$app->request->post('CreateForm')['url'],
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
                // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [CreateForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'CreateForm') . print_r($request) . print_r($response) . '</p></div>');
                //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/> . print_r($request) . <br/>RESPONSE:<br/> . print_r($response)' . print_r($request) . '</p></div><br/>');
                // yii\httpclient\Response Object ( [client] => yii\httpclient\Client Object ( [baseUrl] => [formatters] => Array ( [urlencoded] => yii\httpclient\UrlEncodedFormatter Object ( [encodingType] => 1 [charset] => ) ) [parsers] => Array ( ) [requestConfig] => Array ( ) [responseConfig] => Array ( ) [contentLoggingMaxSize] => 2000 [_transport:yii\httpclient\Client:private] => yii\httpclient\StreamTransport Object ( [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => ) [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => Array ( ) ) [_headers:yii\httpclient\Message:private] => Array ( [0] => HTTP/1.1 200 OK [1] => X-Powered-By: Express [2] => Content-Type: application/json; charset=utf-8 [3] => Content-Length: 1180 [4] => ETag: W/"49c-2mm6tdE08PBK3Du9hRlhHVqbw2Y" [5] => Date: Thu, 25 Feb 2021 09:53:53 GMT [6] => Connection: close ) [_cookies:yii\httpclient\Message:private] => [_content:yii\httpclient\Message:private] => {"result":"ok","data":{"user":{"email":"nadie@uned.es","nombre":"Nadie","rol":"Administrador"},"launch_parameters":{"iss":"5fc3860a81740b0ef098a983","login_hint":"123456","target_link_uri":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","lti_message_hint":"123456"},"credentials":{"deployment":["8c49a5fa-f955-405e-865f-3d7e959e809f"],"client_id":"d42df408-70f5-4b60-8274-6c98d3b9468d","auth_login_url":"http://10.201.54.31:9002/platform/login.php","auth_token_url":"http://10.201.54.31:9002/platform/token.php","key_set_url":"http://10.201.54.31:9002/platform/jwks.php","private_key_file":"/private.key","auth_server":"http://10.201.54.31:9002/platform/login.php","kid":"58f36e10-c1c1-4df0-af8b-85c857d1634f"},"_id":"5fc3860a81740b0ef098a983","id_actividad":"5fc3860a81740b0ef098a983","url_actividad":"http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html","launch_url":"http://10.201.54.31:9002/login.php?iss=5fc3860a81740b0ef098a983&login_hint=123456&target_link_uri=http://10.201.54.31:9002/Plantilla Azul_5e0df19c0c2e74489066b43g/index_default.html<i_message_hint=123456","zf":"2020-12-17T09:01:03.889Z"}} [_data:yii\httpclient\Message:private] => [_format:yii\httpclient\Message:private] => [_events:yii\base\Component:private] => Array ( ) [_eventWildcards:yii\base\Component:private] => Array ( ) [_behaviors:yii\base\Component:private] => )
                if ($response->isOk && $response->data['result'] === 'ok' && $response->data['data']['result'] != 'Existe') {
                    $content = '<div><p/><p/><p/><p class="alert alert-success"> Registro: ' . $response->data['result'] . '</p>';
                    $content .= '<div class="jumbotron">
                        <h1>Creación de registro</h1>
                        <p class="lead">de Actividad LTI realizado correctamente.</p>
                        <p class="lead">Copia las credenciales de acceso a la Actividad LTI.</p>' .
                        'URL: <code>' . Html::encode($response->data['data']['launch_url']) . '</code><br/>' .
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
                        <p class="lead">Las credenciales de Creación del registro son erróneas.</p>' .
                        'ID:  <code>' . Yii::$app->request->post('CreateForm')['id'] . '</code><br/>' .
                        'URL: <code>' . Yii::$app->request->post('CreateForm')['url'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }

            // Parámetros llamada a la Vista create desde los listados Lists
            $params = Yii::$app->request->post();

            return $this->render('crud/create', [
                'model' => $model,
                'id' => isset($params['id'])? $params['id'] :' ',
                'url'=> isset($params['url'])? $params['url'] :' ',
            ]);
        }
    }

    /*READ*/
    /**
     * Displays read page.
     *
     * @return Response|string
     */
    public function actionRead()
    {
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
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Lti/id_actividad/5e0df19c0c2e74489066b43g
                    $ruta = '/read/coleccion/Lti/id_actividad/' . Yii::$app->request->post('ReadForm')['id'];
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Lti/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                    $ruta = '/read/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('ReadForm')['url']));
                }
                // Exception GET LTI1
                try {
                    $response = $client->createRequest()
                        ->setFormat(Client::FORMAT_JSON)
                        //->setMethod('POST')
                        ->setMethod('GET')
                        ->setUrl($url . $ruta) //$_POST['ListForm']['id'])
                        ->setData(['name' => Yii::$app->user->identity->username, 'email' => Yii::$app->user->identity->username . '@lti.server'])
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
                        <p class="lead">Copia las credenciales de acceso a la Actividad LTI.</p>' .
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

            // Parámetros llamada a la Vista read desde los listados Lists
            $params = Yii::$app->request->post();

            return $this->render('crud/read', [
                'model' => $model,
                'id' => isset($params['id'])? $params['id'] :' ',
                'url'=> isset($params['url'])? $params['url'] :' ',
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

                // PUT UPDATE (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
                $client = new Client();

                if (Yii::$app->request->post('UpdateForm')['id'] !== '') {
                    // http://10.201.54.31:49151/servicios/lti/lti13/update/register/coleccion/:coleccion
                    $ruta = '/update/coleccion/Lti/id_actividad/' . urlencode(Yii::$app->request->post('UpdateForm')['id']);
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/update/coleccion/:coleccion/url_actividad/https://www.uned.es
                    $ruta = '/update/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('UpdateForm')['publicacion']));
                }

                // Exception PUT Lti
                try {
                    // Dirección de alojamiento
                    // del servidor de Git
                    //////////////////////
                    /// LOCAL puerto :90 ó 9000
                    /// GLOBAL puerto:80 u 8000 o dominio: `.uned.es`
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
                                'url_actividad' => Yii::$app->request->post('UpdateForm')['url'],
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
                    // Exception POST Lti2
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
                        <p class="lead">de Lti realizado correctamente.</p>' .
                        'ID: <code>' .
                        Html::encode($response->data['data']['register']['id_actividad']) .
                        '</code><br/>' .
                        'URL: <code>' .
                        Html::encode($response->data['data']['register']['url_actividad']) .
                        '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-success" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';

                    // Array de respuesta
                    // a 3 niveles de profudidad
                    $content .= "<pre><br/>";
                    foreach ($response->data['data']['register'] as $key => $value) {
                        if(! is_array($value)){
                            $content .= "{$key} => {$value}<br/>";
                        }
                        else{
                            $content .= "{$key} => [<br/>";
                            foreach ($value as $key => $value) {
                                if (!is_array($value)) {
                                    $content .= "&nbsp;&nbsp;&nbsp;&nbsp;{$key} => {$value}<br/>";
                                } else {
                                    $content .= "&nbsp;&nbsp;&nbsp;&nbsp;{$key} => [<br/>";
                                    foreach ($value as $key => $value) {
                                        if (!is_array($value)) {
                                            $content .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$key} => {$value}<br/>";
                                        } else {
                                            $content .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$key} => {array[]}<br/>";
                                        }
                                    }
                                    $content .= "&nbsp;&nbsp;&nbsp;&nbsp;]<br/>";
                                }
                            }
                            $content .= "]<br/>";
                        }
                    }
                    $content .= "</pre>";

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
                        'URL: <code>' . Yii::$app->request->post('UpdateForm')['url'] . '</code><br/>' .
                        '<p/><p/><p/>' .
                        '<p><a class="btn btn-lg btn-warning" href="' . Url::previous() . '">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }

            // Parámetros llamada a la Vista update desde los listados Lists
            $params = Yii::$app->request->post();

            return $this->render('crud/update', [
                'model' => $model,
                'id' => isset($params['id'])? $params['id'] :' ',
                'url'=> isset($params['url'])? $params['url'] :' ',
            ]);
        }
    }

    /*DELETE*/
    /**
     * Displays delete page.
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
                    $ruta = '/delete/coleccion/Lti/id_actividad/' . Yii::$app->request->post('DeleteForm')['id'];
                } else { // Buscar por URL
                    // http://10.201.54.31:49151/servicios/lti/lti13/delete/5e0df19c0c2e74489066b43g
                    $ruta = '/delete/coleccion/Lti/id_actividad/' . str_replace('+', '%20', urlencode(Yii::$app->request->post('deleteForm')['id']));
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
                            <p class="lead">Borradas las credenciales de acceso a la Actividad LTI.</p>' .
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

            // Parámetros llamada a la Vista delete desde los listados Lists
            $params = Yii::$app->request->post();

            return $this->render('crud/delete', [
                'model' => $model,
                'id' => isset($params['id'])? $params['id'] :' ',
            ]);
        }
    }

    /*LISTs*/
    /**
     * Displays lists page.
     *
     * @return Response|string
     *
     */
    public function actionLists()
    {
        // Remember current URL
        Url::remember();

        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model2 = new CreateForm();

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->render('lti/lists', [
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
                    return $this->redirect(array('lti/index',
                        'title' => 'Listado',
                        'back' => 'lists',
                        'controller' => 'Lti',
                        'formulario' => 'ListsForm',
                        'model' => $model,
                        'id' => Yii::$app->request->post('ListsForm')['id'],
                        'url' => Yii::$app->request->post('ListsForm')['url'],
                    ));
                    // View from another Controller
                    return $this->render('//lists/index', [
                        'title' => 'Listado',
                        'back' => 'lists',
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
                        '<p><a class="btn btn-lg btn-warning" href="index.php?r=site%2Flists">Atrás</a></p>
                    </div>';
                    $content .= '</div>';
                }

                return $this->renderContent($content);
                //return $this->refresh();
            }

            return $this->render('lists', [
                'model' => $model,
                'id' => '*',
            ]);
        }
    }

    /*INDEX LISTs*/
    /**
     * Displays index lists page.
     *
     * @return Response|string
     *
     */
    public function actionIndex()
    {

        // function to generate faked models, don't care about this.
        function getFakedModels()
    {
        $fakedModels = [];

        for ($i = 1; $i < 18; $i++) {
            $fakedItem = [
                //'list' => 'Listado',
                'id' => $i,
                'title' => 'Actividad ' . $i,
                'image' => 'https://place-hold.it/1x1/',
                'link'  => '<a href="https://place-hold.it/1x1/" target="_blank">URL</a>'
            ];

            $fakedModels[] = $fakedItem;
        }

        return $fakedModels;
    }

        // GET params
        $params = Yii::$app->request->getQueryParams();
        //$title = Yii::$app->request->getQuery('title');
        if (!isset($params['title'])){
            $provider = new ArrayDataProvider([
                'allModels' => getFakedModels(),
                'pagination' => [
                    'pageSize' => 5
                ],
                'sort' => [
                    'attributes' => ['id'],
                ],
            ]);

            return $this->render('lists/index', [
                'title' => 'FakedModels',
                'back' => 'lists',
                'listDataProvider' => $provider
            ]);
        }
        else{

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

            // GET (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
            $client = new Client();

            switch($params['formulario']){
                case 'ListsForm':
                    if ($params['id']) {
                        // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/controller/id_actividad/5e0df19c0c2e74489066b43g
                        $ruta = '/read/all/coleccion/' . $params['controller'] . '/id_actividad/' . $params['id'];
                    } else {
                        // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/controller/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                        $ruta = '/read/all/coleccion/' . $params['controller'] . '/url_actividad/' . str_replace('+', '%20', urlencode($params['url']));
                    }

                    // Exception GET LTI1
                    try {
                        $response = $client->createRequest()
                            ->setFormat(Client::FORMAT_JSON)
                            //->setMethod('POST')
                            ->setMethod('GET')
                            ->setUrl($url . $ruta) //$_POST['ListsForm']['id'])
                            ->setData(['name' => Yii::$app->user->identity->username, 'email' => Yii::$app->user->identity->username . '@lti.server'])
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
                    if ($response->isOk && $response->data['result'] === 'ok') {
                        // Crea ARRAY con todas las respuestas
                        // TODO separar en una función 'crearListDataProvider();'
                        $responseModels = [];

                        // Actividad múltiple/única
                        if(!array_key_exists('_id', $response->data['data'])) {
                            //foreach ($request as $key => $value){
                            //    echo "{$key} => {$value} ";
                            foreach ($response->data['data'] as $index => $value){
                                //print(json_decode($index['data'], true));
                                //if($index >= 0) {
                                $responseItem = [
                                    //'list' => $index,//'Listado',
                                    'id' => $value['_id'],
                                    'title' => $value['user']['email'] . ' ' . substr($value['zf'], 0, 10),
                                    'link'  => '<a href="' . $value['url_actividad'] . '" target="_blank">Actividad</a>',
                                    'link1'  => '<a href="' . $value['launch_url'] . '" target="_blank">Launch</a>',
                                    'image' => 'https://place-hold.it/1x1/',
                                    'data'  => $value,
                                    //'buttonC' => '<a href="index.php?r=lti%2Fcreate" class="btn btn-lg btn-primary">Create</a>',
                                    'buttonC' => '<form action="index.php?r=lti%2Fcreate" method="post" style="display: inline; white-space: nowrap">
                                                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                                                    <input type="hidden" name="id" value="' . $value['_id'] . '">
                                                    <input type="hidden" name="url" value="' . $value['url_actividad'] . '">
                                                    <button type="submit" class="btn btn-lg btn-primary">Create</button>
                                                  </form>',
                                    'buttonR' => '<a href="index.php?r=lti%2Fread" class="btn btn-md btn-info">Read&nbsp;&nbsp;</a>',
                                    //'buttonU' => '<a href="index.php?r=lti%2Fupdate" class="btn btn-sm btn-warning">Update</a> ',
                                    'buttonU' => '<form action="index.php?r=lti%2Fupdate" method="post" style="display: inline; white-space: nowrap">
                                                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                                                    <input type="hidden" name="id" value="' . $value['_id'] . '">
                                                    <input type="hidden" name="url" value="' . $value['url_actividad'] . '">
                                                    <button type="submit" class="btn btn-sm btn-warning">Update</button>
                                                  </form>',
                                    //'buttonD' => '<a href="index.php?r=lti%2Fdelete" class="btn btn-xs btn-danger">Delete</a> '
                                    'buttonD' => '<form action="index.php?r=lti%2Fdelete" method="post" style="display: inline; white-space: nowrap">
                                                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                                                    <input type="hidden" name="id" value="' . $value['_id'] . '">
                                                    <button type="submit" class="btn btn-lg btn-xs btn-danger">Delete</button>
                                                  </form>',
                                ];
                                $responseModels[] = $responseItem;
                                //}
                                //else{
                                //    echo "{$index} => " . $value;
                                //    echo "{$index} => " . $value['user']['email'];

                                //}
                            }
                        }
                        else{
                            $responseItem = [
                                //'list' => 'Listado',
                                'id' => $response->data['data']['_id'],
                                'title' => $response->data['data']['user']['email'] . ' ' . substr($response->data['data']['zf'], 0, 10),
                                'link'  => '<a href="' . $response->data['data']['url_actividad'] . '" target="_blank">Launch URL</a>',
                                'image' => 'https://place-hold.it/1x1/',
                            ];
                            $responseModels[] = $responseItem;
                        }

                        return $this->render('lists/index', [
                            'title' => $params['title'],
                            'back' => $params['back'],
                            'controller' => $params['controller'],
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
                    }

                    break;
                default:
                    return $this->render('lists/index', [
                        'title' => $params['title'],
                        'back' => $params['back'],
                    ]);
            }
        }
    }

}