<?php

$title = 'LTI Publisher';
$params['breadcrumbs'][] = $title;

// ini_set('upload_max_filesize', '10M');

// Remember current URL
$url=$_SERVER;

// TODO MULTIPROCESO/MULTITAREA
//  https://medium.com/async-php/multi-process-php-94a4e5a4be05
// Presenta vía redirección a la Actividad de id o actividad en la Plataforma iss
// PARAMETROS
//  id      :   identificador de la publicación de la Actividad (00020220724095827000000d)
//  iss     :   URI plataforma
//                      https://ailanto-dev.intecca.uned.es/Moolde
//                      http://10.201.54.31:9002
//  actividad   :   ID o URL de la actividad a visualizar
//                      00020220724094336000000d
//                      https://ailanto-dev.intecca.uned.es/publicacion/00020220724095827000000d
//
if (isset($_REQUEST['id']) || (isset($_REQUEST['id']) && isset($_REQUEST['iss'])) || ((isset($_REQUEST['iss']) && isset($_REQUEST['actividad']))))
{
    $id='';
    $iss='';
    $actividad='';
    $output=null;
    $retval=null;

    $carpetaGit = '';
    $serverGit = '';
    $carpetaPub = '';
    $serverPub = '';
    $serverLti = '';

    // PARAMS
    // SERVIDORES
    $params = require __DIR__ . '/../../config/params.php';

    // REQUEST[]
    // NOMBRE VARIABLES
    // VALORES
    //  - LA id
    //      ID: 00000000000000000000000d
    //      ID: 00020220724095827000000d
    if (isset($_REQUEST['id']))
        $id = $_REQUEST['id'];
    else
        $id = 'no';
    //  - LA iss
    //      URL: http...
    if (isset($_REQUEST['iss']))
        $iss = $_REQUEST['iss'];
    else
        $iss = 'no';
    //  - LA ACTIVIDAD
    //      ID: 00000000000000000000000a
    //      URL: http...
    //
    if (isset($_REQUEST['actividad']))
        $actividad = $_REQUEST['actividad'];
    else
        $actividad = 'no';

    // Dirección de alojamiento
    // del servidor de Git
    //////////////////////
    /// LOCAL puerto :9000
    /// GLOBAL puerto:8000 o `.uned.es`
    ///
    if ((! strpos($_SERVER['HTTP_HOST'], '.uned.es')) && ($_SERVER['REMOTE_PORT'] !== '80') && ($_SERVER['REMOTE_PORT'] !== '8000')) {
        //$carpetaGit = Yii::$app->params['carpetaGit_local'];
        //$serverGit = Yii::$app->params['serverGit_local'];
        //$carpetaPub = Yii::$app->params['carpetaPublicacion_local'];
        //$serverPub = Yii::$app->params['serverPublicacion_local'];
        //$serverLti = Yii::$app->params['serverLti_local'];
        $carpetaGit = $params['carpetaGit_local'];
        $serverGit = $params['serverGit_local'];
        $carpetaPub = $params['carpetaPublicacion_local'];
        $serverPub = $params['serverPublicacion_local'];
        $serverLti = $params['serverLti_local'];
    }
    else {
        //$carpetaGit = Yii::$app->params['carpetaGit_global'];
        //$serverGit = Yii::$app->params['serverGit_global'];
        //$carpetaPub = Yii::$app->params['carpetaPublicacion_global'];
        //$serverPub = Yii::$app->params['serverPublicacion_global'];
        //$serverLti = Yii::$app->params['serverLti_global'];
        $carpetaGit = $params['carpetaGit_global'];
        $serverGit = $params['serverGit_global'];
        $carpetaPub = $params['carpetaPublicacion_global'];
        $serverPub = $params['serverPublicacion_global'];
        $serverLti = $params['serverLti_global'];
    }


    // DONE confirmar que la carpeta id o actividad existe
    // Revisa la carpeta de difusion
    ////////////////////////////////
    ///
    // Carpeta de difusión de actividades
    umask(0111);
    $output = shell_exec(escapeshellcmd('mkdir ../uploads/publicacion'));
    //echo "<pre>$output</pre>";

    // Carpeta de Actividad cargada y publicada
    // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'd'
    /////////////////////////////////
    // outputs the username that owns the running php/httpd process
    // (on a system with the "mkdir" executable in the path)
    $output=null;
    $retval=null;
    umask(0111);
    exec(escapeshellcmd('mkdir ../uploads/publicacion/' . $id), $output, $retval);

    // Caarpeta publicacion de id existe
    // TOUCH publicacion Actividad con errores
    if($retval !== 0) {
        //  - LA ACTIVIDAD
        //      ID: 00000000000000000000000a
        //      ID: 5e0df19c0c2e74489066b43f
        //      URL https://ailanto-dev.intecca.uned.es/publicacion/00020220721114124000000d
        /*Yii::$app->session->hasFlash('uploadupdterExistting')*/
        if (preg_match('([a-f,0-9]{24})', $actividad)):
            //$_REQUEST['actividad'];

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
                $url = $params['serverServiciosLti_local'];
            else
                //$url = $params['serverServiciosLti_global'];
                $url = $params['serverServiciosLti_local'];

            // ID o URL
            if (!(preg_match('(http|Http|HTTP)', $_REQUEST['actividad'])) && !preg_match('(publicacion)', $_REQUEST['actividad'])) {
                // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/collection/id_actividad/5e0df19c0c2e74489066b43g
                $ruta = '/read/coleccion/Lti/id_actividad/' . $_REQUEST['actividad'];
            } else {
                // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/collection/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                $ruta = '/read/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode($_REQUEST['actividad']));
            }

            // READ servicio GET Lti
            $arrayFile = json_decode(file_get_contents($url . $ruta), true);
            //print_r($arrayFile);

            // DONE recuperar Credenciales de Actividad LTI por ID o URL
            // ACTIVIDAD ID/URL EXISTE
            if($arrayFile['result'] === 'ok'){
                //echo ('carpeta exixte!!!!!!!!');
                //echo ('id exixte!!!!!!!!');
                //print_r($arrayFile);

                $namedir = $arrayFile['data']['url_actividad'];
                $file=$arrayFile['data']['launch_parameters']['target_link_uri'];

                // REDIRECTION HEADER
                //header('Location: ' . TOOL_PARAMS_TARGET, true, 302);
                //die;

                // Carpeta existe!

                // Actividad exisste!

                //Redireccionar a la URL Actividad

                // REDIRECTION HEADER
                //header('Location: ' . $namedir, false, 302);
                //header('Location: ' . $namedir, false, 302);
                //die;
                //http://stackoverflow.com/questions/17371785/ddg#17371860
                //<base href="http://www.example.com/news/index.html">

                // Inyección de publicación HTML
                //echo file_get_contents('https://ailanto-dev.intecca.uned.es/lti/publicacion/10220210903095251000000a/xml/');
                //echo file_get_contents('https://ailanto-dev.intecca.uned.es/lti/publicacion/10220210903095251000000a/index.html');
                $html = file_get_contents('https://ailanto-dev.intecca.uned.es/lti/publicacion/10220210903095251000000a/index.html');
                $html= substr_replace($html, '<base href="http://www.example.com/news/index.html">',134, null);
                echo $html;
                die;

                // DEVUELVE DATA
                //////////
                $data = [
                    "result"=> "ok",
                    "data" =>
                        [
                            'id_actividad' => $namedir,
                            'url_actividad' => $serverPub . '/' . $namedir,
                            "user" => [
                                'email' => 'gcono@lti.server',
                                'nombre' => 'gcono',
                                'rol' => '000'
                            ],
                            "upload" => [
                                'fichero' => $file,
                                'carpeta' => $namedir,
                                'publicacion_url' => $serverPub . '/' . $namedir,
                                'git_url' => $serverGit . '/' . $namedir . '.git',
                                'actualizado' => 0
                            ],
                            "date"=> date('YmdHisu')
                        ]
                ];
                header('Content-Type: application/json');
                echo json_encode($data);
                die();

            }
            // ACTIVIDAD ID/URL NO EXISTE
            else{
                // DEVUELVE DATA
                //////////
                header('Content-Type: application/json');
                echo json_encode($arrayFile);
                die();
            }
        //  - LA URL
        //      http...
        //  - LA CARPETA
        //      /ruta/fichero.zip
        /**/
        else:
            // Actividad LTI de id no ObjectId Mongo

            // DEVUELVE DATA
            //////////
            $data = [
                "result"=> "error",
                "data" => "Dengado acceso a carpeta difusion " . $id
            ];
            header('Content-Type: application/json');
            echo json_encode($data);
            die("Cuando NO existe la Actividad en el Sistema LTI y hay qye crearla dese cerodo");
        endif;
    }
    // Actividad NO publicada
    else {

        // Borra Carpeta de Actividad inexistente
        // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'd'
        /////////////////////////////////
        // outputs the username that owns the running php/httpd process
        // (on a system with the "mkdir" executable in the path)
        $output=null;
        $retval=null;
        umask(0111);
        exec(escapeshellcmd('rmdir ../uploads/publicacion/' . $id), $output, $retval);

        // DEVUELVE DATA
        //////////
        $data = [
            "result"=> "error",
            "data" => "Error al acceder a carpeta difusion " . $id
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
        die();

        echo '<p class="alert error-summary"><i>Error al acceder a carpeta difusion <i>`' . $id . '`</i></p>' .
            '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
    }
 }
else
    {
?>

    <div class="publish-publishlti">

        <h1><?= $title ?></h1>

        <!--
        TODO
            Presenta  redirección a la Actividad LTI id o actividad en la Plataforma iss
            $_REQUEST Parámetros de carga
                      id       : identificador de la publicación de la Actividad (00020220724095827000000d)
                      iss      : URI Plataforma
                                    https://ailanto-dev.intecca.uned.es/Moolde
                                    http://10.201.54.31:9002
                      actividad: ID o URL de la Actividad LTI a visualizar
                                    00020220724094336000000d
                                    https://ailanto-dev.intecca.uned.es/publicacion/00020220724095827000000d
        -->
        <p>
            Formulario de redireccion de Econtent complejos desde el GICCU.
            Puede redireccionarse una Actividad de cada vez,
            sin contener espacios en blanco, tildes o eñes en el nombre.
        </p>

        <div class="row">
            <div class="col-lg-5">
            </div>
        </div>

    </div>

<?php
    }
?>
