<?php
// YOUR_APP/controllers/ListsController.php

/* change namespace in your app */
//namespace frontend\controllers;
namespace app\controllers;

use Yii;
use yii\web\Controller;

/*REGISTER*/
//use app\models\RegisterForm;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/*QUERY*/
//use app\models\QueryForm;
use yii\helpers\Html;

/* LISTS */
//use app\models\ListsForm;
use yii\data\ArrayDataProvider;

/* DLETE */
//use app\models\DeleteForm;

class ListsController extends Controller
{
    public function actionIndex()
    {
        // GET params
        $params = Yii::$app->request->getQueryParams();
        //$title = Yii::$app->request->getQuery('title');
        if (!isset($params['title'])){
            $provider = new ArrayDataProvider([
                'allModels' => $this->getFakedModels(),
                'pagination' => [
                    'pageSize' => 5
                ],
                'sort' => [
                    'attributes' => ['id'],
                ],
            ]);

            return $this->render('index', [
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
                        // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Upload/id_actividad/5e0df19c0c2e74489066b43g
                        $ruta = '/read/all/coleccion/Lti/id_actividad/' . $params['id'];
                    } else {
                        // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/Upload/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                        $ruta = '/read/all/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode($params['url']));
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
                        // TODO crear ARRAY con todas las respuestas
                        // TODO crearListDataProvider();
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
                                    'title' => 'Actividad ' . $value['launch_parameters']['iss'],
                                    'image' => 'https://place-hold.it/1x1/',
                                    'link'  => '<a href="' . $value['launch_url'] . '" target="_blank">Launch URL</a>'
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
                                'title' => 'Actividad ' . $response->data['data']['launch_parameters']['iss'],
                                'image' => 'https://place-hold.it/1x1/',
                                'link'  => '<a href="' . $response->data['data']['launch_url'] . '" target="_blank">Launch URL</a>'
                            ];
                            $responseModels[] = $responseItem;
                        }

                        return $this->render('index', [
                            'title' => $params['title'],
                            'back' => $params['back'],
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
                    return $this->render('index', [
                        'title' => $params['title'],
                        'back' => $params['back'],
                    ]);
            }
        }
    }

    // function to generate faked models, don't care about this.
    private function getFakedModels()
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
}
?>
