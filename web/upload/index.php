<?php

$title = 'Upload Zip Uploader';
$params['breadcrumbs'][] = $title;

// ini_set('upload_max_filesize', '10M');

// Remember current URL
$url=$_SERVER;

## https://www.codehaven.co.uk/php/php-miscellaneous/how-to-catch-errors-and-warnings-in-php/
set_error_handler(
    function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
    {
        throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
    }, E_ALL);

try{
// TODO MULTIPROCESO/MULTITAREA
//  https://medium.com/async-php/multi-process-php-94a4e5a4be05
// Carga, sube y crea o actualiza el contenido de una actividad con el fichero .zip namedir y lo guarda con nombre file
// PARAMETROS
//  file        :   nombre con el que guardar el fichero
//  namedir     :   dirección física o URL donde se localiza el fichero.zip que se va a subir
//                      ../uploads/60378839cafc7625e8333d04_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4f.zip
//                      http://10.201.54.31:8000/uploads/60378839cafc7625e8333d04_5e46670337ebc61534f37c4a_5e46673e37ebc61534f37c4f.zip
//  actividad   :   ID o URL de la actividad a actualizar
//                      00020220724094336000000d
//                      https://ailanto-dev.intecca.uned.es/publicacion/00020220724095827000000d
//
    if ((isset($_REQUEST['file']) && isset($_REQUEST['namedir'])) && ((($_REQUEST['file'] !== null) && ((preg_match('(zip|Zip|ZIP)', $_REQUEST['file'])))) || (($_REQUEST['namedir'] !== null) && (preg_match('(zip|Zip|ZIP)', $_REQUEST['namedir'])))))
    {
        $file='';
        $namedir='';
        $output=null;
        $retval=null;

        $carpetaGit = '';
        $serverGit = '';
        $carpetaPub = '';
        $serverPub = '';
        $serverLti = '';

        // PARAMS
        // SERVIDORES
        $local = false;
        $params = require __DIR__ . '/../../config/params.php';

        // REQUEST[]
        // NOMBRE DL FICHERO file
        $file=$_REQUEST['file'];
        // VALOR DE namedir
        //  - LA URL
        //      http...
        //  - LA CARPETA
        //      /ruta/fichero.zip
        //  - LA ACTIVIDAD
        //      00000000000000000000000a
        //      5e0df19c0c2e74489066b43f
        // desde el Controlador SiteContreoller.php
        // NOMBRE DL FUENTE ZIP
        //$namedir= substr('nombreTrabajo',0, (strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) >=0 ? strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) : 0)) . Yii::$app->user->identity->username . date('YmdHisu') . '00000003';
        //$namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
        //$namedir=$_REQUEST['namedir'] . Yii::$app->user->identity->username . date('YmdHisu') . 'd';
        $namedir = /*explode('.zip', strtolower($file))[0] . "difusion"*/"000" . date('YmdHisu') . 'd';
        // LA ACTIVIDAD
        if (isset($_REQUEST['actividad'])===true)
            $actividad = $_REQUEST['actividad'];
        else
            $actividad = 'zip';

        // Dirección de alojamiento
        // del servidor de Git
        //////////////////////
        /// LOCAL  resto: ej. 'localhost', '127.0.0.1'
        /// GLOBAL `.uned.es` o '10.201.54.31'
        ///
        if ((strpos($_SERVER['HTTP_HOST'], '10.201.54.31')!==false) && (strpos($_SERVER['HTTP_HOST'], '.uned.es'))!==false) {
            $local = true;
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

        // COPIA el archivo .zip en la carpeta de difusion
        /////////////////////////////////////////////////
        ///
        // Carpeta de difusión Actividad
        umask(0000);
        $output = shell_exec(escapeshellcmd('mkdir ../uploads/difusion'));
        //echo "<pre>$output</pre>";

        // Carpeta de difusion cargada y publicada
        // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'd'
        /////////////////////////////////
        // outputs the username that owns the running php/httpd process
        // (on a system with the "mkdir" executable in the path)
        $output=null;
        $retval=null;
        umask(0000);
        exec(escapeshellcmd('mkdir ../uploads/difusion/' . $namedir), $output, $retval);

        // MKDIR difusion Actividad sin errores
        if($retval === 0) {

            //  - LA ACTIVIDAD
            //      ID: 00000000000000000000000a
            //      ID: 5e0df19c0c2e74489066b43f
            //      URL https://ailanto-dev.intecca.uned.es/publicacion/00020220721114124000000d
            // Update Actividad
            /*Yii::$app->session->hasFlash('uploadupdterExistting')*/
            if (((preg_match('(zip|Zip|ZIP)', $actividad)!==false) && (preg_match('([a-f,0-9]{24})', $actividad)))):
                // TODO-NE recuperar Credenciales del Upload LTI por ID
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
                /// LOCAL  resto: ej. 'localhost', '127.0.0.1'
                /// GLOBAL `.uned.es` o '10.201.54.31'
                ///
                if ($local)
                    $url = $params['serverServiciosLti_local'];
                else
                    $url = $params['serverServiciosLti_global'];

                // ID o URL
                if ((preg_match('(http|Http|HTTP)', $_REQUEST['actividad'])!==false) && !preg_match('(publicacion)', $_REQUEST['actividad'])) {
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/collection/id_actividad/5e0df19c0c2e74489066b43g
                    $ruta = '/read/coleccion/Upload/id_actividad/' . $_REQUEST['actividad'];
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/read/coleccion/collection/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                    $ruta = '/read/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode($_REQUEST['actividad']));
                }

                // READ servicio GET Upload
                $arrayFile = json_decode(file_get_contents($url . $ruta), true);
                //print_r($arrayFile);

                // ACTIVIDAD ID/URL EXISTE
                if($arrayFile['result'] === 'ok'){

                    $namedir = $arrayFile['data']['upload']['carpeta'];

                    // Fichero ZIP ya subido!

                    // Copiar ZIP en publicacion

                    // Unzip Actividad .zip

                    //die("Cuando YA existe la Actividad en el Sistema LTI y sólo hay qye subir el fichero .ZIP y actualizar el git");

                    // DEVUELVE DATA
                    //////////
                    header('Content-Type: application/json');
                    echo json_encode($arrayFile);
                    die("YA existe el Upload en el Sistema LTI y hay que actualizarlo");

                }
                // ACTIVIDAD ID/URL NO EXISTE
                else{
                    // DEVUELVE DATA
                    //////////
                    header('Content-Type: application/json');
                    echo json_encode($arrayFile);
                    die("NO existe el Upload en el Sistema LTI y hay que crearlo");
                }
            //  - LA URL
            //      http.../fichero.zip
            //  - LA CARPETA
            //      /ruta/fichero.zip
            //  - LA ACTIVIDAD
            //      No existe o es un .zip y hay que crearla de cero
            /* Upload Actividad */
            else:
                // TODO-NE dar de Alta Actividad LTI
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
                /// LOCAL  resto: ej. 'localhost', '127.0.0.1'
                /// GLOBAL `.uned.es` o '10.201.54.31'
                ///
                if ($local)
                    $url = $params['serverServiciosLti_local'];
                else
                    $url = $params['serverServiciosLti_global'];

                // ID o URL
                if ((preg_match('(http|Http|HTTP)', $namedir)!==false) && !preg_match('(publicacion)', $namedir)) {
                    // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/collection/id_actividad/5e0df19c0c2e74489066b43g
                    $ruta = '/create/coleccion/Lti/id_actividad/' . $namedir;
                } else {
                    // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/collection/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                    $ruta = '/create/coleccion/Lti/url_actividad/' . str_replace('+', '%20', urlencode($namedir));
                }

                // CREATE servicio POST Lti
                $context = stream_context_create(['http' =>
                    [
                        'ignore_errors' => true,
                        'method'  => 'POST',
                        'header'  => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => http_build_query(
                            [
                                "id_actividad" => $namedir,
                                "url_actividad" => $serverPub . '/' . $namedir,
                                "trabajo_actividad" => '',
                                "user" => [
                                    'email' => 'gcono@lti.server',
                                    'nombre' => 'gcono',
                                    'rol' => '000'
                                ],
                                "launch_parameters" => [
                                    'iss' => '00000000000000000000000a',
                                    'login_hint' => '123456',
                                    'target_link_uri' => $serverPub . '/' . $namedir,
                                    'lti_message_hint' => '123456'
                                ],
                                "launch_url"=> $serverLti . '/login.php?iss=00000000000000000000000a&login_hint=123456&target_link_uri=' . $serverPub . '/' . $namedir . '&lti_message_hint=123456'
                            ]
                        )
                    ]
                ]);

                // LLAMADA SERVICIO
                $arrayFilePost = json_decode(file_get_contents($url . $ruta, false, $context), true);
                //print_r($arrayFile);
                //die();

                // ACTIVIDAD LTI ID/URL CREADA
                if($arrayFilePost['result'] === 'ok') {
                    // TODO-NE dar de Alta Upload LTI
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
                    /// LOCAL  resto: ej. 'localhost', '127.0.0.1'
                    /// GLOBAL `.uned.es` o '10.201.54.31'
                    ///
                    if ($local)
                        $url = $params['serverServiciosLti_local'];
                    else
                        $url = $params['serverServiciosLti_global'];

                    // ID o URL
                    if ((preg_match('(http|Http|HTTP)', $namedir) !== false) && !preg_match('(publicacion)', $namedir)) {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/collection/id_actividad/5e0df19c0c2e74489066b43g
                        $ruta = '/create/coleccion/Upload/id_actividad/' . $namedir;
                    } else {
                        // http://10.201.54.31:49151/servicios/lti/lti13/create/coleccion/collection/url_actividad/http:%2f%2f10.201.54.31:9002%2fPlantilla%20Azul_5e0df19c0c2e74489066b43g%2findex_default.html
                        $ruta = '/create/coleccion/Upload/url_actividad/' . str_replace('+', '%20', urlencode($namedir));
                    }

                    // CREATE servicio POST Lti
                    $context = stream_context_create(['http' =>
                        [
                            'ignore_errors' => true,
                            'method' => 'POST',
                            'header' => 'Content-Type: application/x-www-form-urlencoded',
                            'content' => http_build_query(
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
                                    ]
                                ]
                            )
                        ]
                    ]);

                    // LLAMADA SERVICIO
                    $arrayFilePost = json_decode(file_get_contents($url . $ruta, false, $context), true);
                    //print_r($arrayFile);
                    //die();

                    // UPLOAD LTI ID/URL CREADO
                    if ($arrayFilePost['result'] === 'ok') {

                        $namedir = $arrayFilePost['data']['upload']['carpeta'];

                        // die("Cuando NO existe la Upload en el Sistema LTI y hay qye crearla dese cerodo");
                        // Carpeta de publicación Actividad
                        umask(0000);
                        $output = shell_exec(escapeshellcmd('mkdir ../uploads/publicacion'));
                        //echo "<pre>$output</pre>";

                        // Carpeta de Actividad cargada y publicada
                        // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'a'
                        /////////////////////////////////
                        // outputs the username that owns the running php/httpd process
                        // (on a system with the "mkdir" executable in the path)
                        $output = null;
                        $retval = null;
                        umask(0000);
                        exec(escapeshellcmd('mkdir ../uploads/publicacion/' . $namedir), $output, $retval);

                        // MKDIR publicacion sin errores
                        if ($retval === 0) {

                            //echo "Returned with status $retval and output:\n";
                            //print_r($output);
                            // Carpeta de publicaciones
                            //$output = shell_exec(escapeshellcmd('ls -lart ../uploads/publicacion/'));
                            //echo "<pre>$output</pre>";

                            // Crea proyecto Git '../uploads/git/$namedir.git' y URL='../uploads/publicacion/$namedir/'
                            //  ID=$namedir
                            ///////////////////////////////////////////////////////////////////////////////////

                            // Git
                            $output = shell_exec(escapeshellcmd('git --version'));
                            //echo "<pre>1. $output</pre>";

                            // Carpeta de Git
                            // outputs the username that owns the running php/httpd process
                            // (on a system with the "mkdir" executable in the path)
                            $output = null;
                            $retval = null;
                            umask(0000);
                            exec(escapeshellcmd('mkdir ../uploads/git 2>&1'), $output, $retval);
                            //echo "2.Returned with status $retval and output:\n";
                            //echo "<p><pre>2.a.";
                            //print_r($output);
                            //echo "</pre></p>";
                            $output = shell_exec(escapeshellcmd('ls -lart ../uploads/ | mkdir ../uploads/git 2>&1'));
                            //echo "<pre>2.b. $output</pre>";

                            // Carpeta de Actividad Git
                            // Convenio de nombre proyecto (24 hex) y carpeta = 'repo_' + id user + fecha y hora + 'a' + '.git'
                            /////////////////////////////////
                            // outputs the username that owns the running php/httpd process
                            // (on a system with the "mkdir" executable in the path)
                            $output = null;
                            $retval = null;
                            umask(0000);
                            exec(escapeshellcmd('mkdir ../uploads/git/' . $namedir . '.git'), $output, $retval);
                            //echo "3.Returned with status $retval and output:\n";
                            //echo "<p><pre>";
                            //print_r($output);
                            //echo "</pre></p>";

                            // Proyecto Git
                            // Crear Git vacío distribuido (--bare)
                            $output = shell_exec(escapeshellcmd('git --bare -C ../uploads/git/ init ' . $namedir . '.git'));
                            //echo "<pre>4.a. $output</pre>";
                            // Permisos carptetas Git ./hobks
                            $output = shell_exec(escapeshellcmd('cp ../uploads/git/' . $namedir . '.git/hooks/post-update.sample ../uploads/git/' . $namedir . '.git/hooks/post-update'));
                            $output = shell_exec(escapeshellcmd('chmod a+x ../uploads/git/' . $namedir . '.git/hooks/post-update'));
                            //echo "<pre>4.Hooks post update. $output</pre>";
                            // Permisos carpteta Git .git/, ./objects y ./refs
                            //$output = shell_exec(escapeshellcmd('chmod 777 -R ../uploads/git/' . $namedir . '.git/ 2>&1'));
                            //echo "<pre>4.a. $output</pre>";
                            //echo "4.a.PassThru " . passthru('chmod 777 -R ../uploads/git/' . $namedir . '.git/ 2>&1') . "<br/>";
                            // Permisos carptetas Git ./objects
                            $output = shell_exec(escapeshellcmd('chmod 777 -R ../uploads/git/' . $namedir . '.git/objects/'));
                            //echo "<pre>4.b. $output</pre>";
                            //echo "4.b.PassThru " . passthru('chmod 777 -R ../uploads/git/' . $namedir . '.git/objects/ 2>&1') . "<br/>";
                            // Permisos carptetas Git ./refs
                            $output = shell_exec(escapeshellcmd('chmod 777 -R ../uploads/git/' . $namedir . '.git/refs/'));
                            //echo "<pre>4.c. $output</pre>";
                            //echo "4.c.PassThru " . passthru('chmod 777 -R ../uploads/git/' . $namedir . '.git/refs/ 2>&1') . "<br/>";

                            // Clonar Git distribuido (--bare)
                            // outputs the username that owns the running php/httpd process
                            // (on a system with the "git clone" executable in the path)
                            $output = null;
                            $retval = null;
                            exec(escapeshellcmd('git clone ../uploads/git/' . $namedir . '.git ../uploads/publicacion/' . $namedir), $output, $retval);
                            //echo "5.Returned with status $retval and output:\n";
                            //echo "<p><pre>5.a. git clone ../uploads/git/$namedir.git ../uploads/publicacion/$namedir<br/>";
                            //print_r($output);
                            //echo "</pre></p>";
                            //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/ clone ../uploads/git/' . $namedir . '.git ' . $namedir));
                            //$output = shell_exec(escapeshellcmd('git clone ../uploads/git/' . $namedir . '.git ../uploads/publicacion/' . $namedir . ' 2>&1'));
                            //echo "<pre>5.b. $output</pre>";

                            // TODO-NE unzip fichero .ZIP en Carpeta Difusion
                            try {

                                ///
                                // Difundir la Actividad
                                // Descomprime archivo Zip 'unzip -o -X ../uploads/difusion/'
                                //  ID=$namedir
                                ///////////////////////////////////////////////////////////////////////////////////

                                // COPIA el archivo .zip en la carpeta de difusion
                                /////////////////////////////////////////////////
                                $arrayFileGet = file_get_contents($_REQUEST['namedir']);
                                if (($arrayFileGet !== false) && ($arrayFileGet !== '')) {
                                    $arrayFilePut = file_put_contents('../uploads/difusion/' . $namedir . '/' . $file, $arrayFileGet);
                                    if (($arrayFilePut !== false) && ($arrayFilePut !== '')) {

                                        // Descomprime .zip
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "unzip" executable in the path)
                                        //$output=null;
                                        //$retval=null;
                                        //'unzip uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'
                                        //exec(escapeshellcmd('unzip uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
                                        //exec(escapeshellcmd('unzip uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
                                        //exec(escapeshellcmd('unzip uploads/' . $file . ' -d uploads/publicacion/' . $namedir), $output, $retval);
                                        // Unzip Actividad .zip
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "unzip" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        umask(0000);
                                        exec(escapeshellcmd('unzip -o -X ../uploads/difusion/' . $namedir . '/' . $file . ' -d ../uploads/publicacion/' . $namedir), $output, $retval);
                                        exec(escapeshellcmd('chmod 774 -R ../uploads/publicacion/' . $namedir), $output, $retval);
                                        //exec(escapeshellcmd('unzip uploads/cindetechtmlv1_5a5db903d3bd0d7623bc10c0.zip -d uploads/publicacion/' . $namedir), $output, $retval);
                                        //echo "6.Returned with status $retval and output:\n";
                                        //echo "<i> " . count($output) . " archivos descomprimidos. Status y resultado " . ($retval === 0 ? 'correctos' : 'erróneos') . ":\n</i>";
                                        //echo "<p><pre> 6.a. Unzip PassThru " . passthru('unzip -o -X uploads/' . $file . ' -d uploads/publicacion/' . $namedir . ' 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        //$output = shell_exec(escapeshellcmd('unzip -o -X uploads/' . $file . ' -d uploads/publicacion/' . $namedir));
                                        //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
                                        //echo "<pre>6.b.$output</pre>";
                                        //$output = shell_exec(escapeshellcmd('touch uploads/publicacion/' . $namedir . '/HolaMundo.txt 2>&1'));
                                        //echo "<pre>6.c. touch HolaMundo.txt $output</pre>";
                                        //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
                                        //echo "<pre>6.d. $output</pre>";

                                        // Add, Commit y Push clonado
                                        // Add
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "git add" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ reset '), $output, $retval);
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ add . '), $output, $retval);
                                        //echo "7.Returned with status $retval and output:\n";
                                        //echo "<p><pre>7.a. PassThru " . passthru('git -C uploads/publicacion/' . $namedir . '/ add . 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ add .'));
                                        //echo "<pre>7.b. $output</pre>";
                                        //$output = shell_exec(escapeshellcmd('sleep 0.5s'));
                                        //echo "<pre>7.c. $output</pre>";

                                        // Commit Config
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "git add" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        //exec(escapeshellcmd('git config --global user.email "you@example.com"'), $output, $retval);
                                        //exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config --local user.email "you@example.com"'), $output, $retval);
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.email "' . '000' . '@lti.server"'), $output, $retval);
                                        //echo "8.Returned with status $retval and output:\n";
                                        //echo "<p><pre>8.a.<br/>";
                                        //echo "8.a.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.email "you@example.com" 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "git add" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        //exec(escapeshellcmd('git config --global user.name "Your Name"'), $output, $retval);
                                        //exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config --local user.name "Your Name"'), $output, $retval);
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.name "' . 'gcono' . '"'), $output, $retval);
                                        //echo "8.Returned with status $retval and output:\n";
                                        //echo "<p><pre>8.b.<br/>";
                                        //echo "8.b.PassThru " . passthru('git -C uploads/publicacion/' . $namedir . ' config user.name "Your Name" 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.email "' . Yii::$app->user->identity->username . '@lti.server" 2>&1'));
                                        //echo "<pre>8.c. $output</pre>";
                                        //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . ' config user.name "'. Yii::$app->user->identity->id .'" 2>&1'));
                                        //echo "<pre>8.d. $output</pre>";

                                        // Commit -m "Init Commit Server LTI"
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "git add" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ commit -m "Init Commit Server LTI"'), $output, $retval);
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ commit -m "Initial Upload Commit Server LTI"'), $output, $retval);
                                        //echo "9.Returned with status $retval and output:\n";
                                        //echo "<p><pre>9.a. git -C uploads/publicacion/$namedir/ commit -m 'Initial Commit Server LTI' <br/>";
                                        //echo "9.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1'));
                                        //echo "<pre>9.b. $output</pre>";

                                        // Push clonado
                                        // outputs the username that owns the running php/httpd process
                                        // (on a system with the "git add" executable in the path)
                                        $output = null;
                                        $retval = null;
                                        //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
                                        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
                                        //echo "10.Returned with status $retval and output:\n";
                                        //echo "<p><pre>10.a. git -C uploads/publicacion/$namedir/ push origin master<br/>";
                                        //echo "10.PassThru" . passthru('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1') . "<br/>";
                                        //print_r($output);
                                        //echo "</pre></p>";
                                        //$output = shell_exec(escapeshellcmd('git -C uploads/publicacion/' . $namedir . '/ push origin master 2>&1'));
                                        //echo "<pre>10.b. $output</pre>";

                                        // Git, UNZIP y Publicacion sin errores
                                        if ($retval === 0) {

                                            // DEVUELVE DATA
                                            //////////
                                            $data = [
                                                "result" => "ok",
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
                                                        "date" => date('YmdHisu')
                                                    ]
                                            ];
                                            header('Content-Type: application/json');
                                            echo json_encode($data);
                                            die();

                                        } else {

                                            // DEVUELVE DATA
                                            //////////
                                            $data = [
                                                "result" => "error",
                                                "data" => "Error al actualizar la carpeta publicacion " . $namedir
                                            ];
                                            header('Content-Type: application/json');
                                            echo json_encode($data);
                                            die();

                                            echo '<p class="alert error-summary">Error al actualizar la carpeta publicacion <i>`' . $namedir . '`</i> con el fichero ZIP <i>`' . $_REQUEST['namedir'] . '`</i></p>' .
                                                '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
                                            die();
                                        }
                                    } else {

                                        // DEVUELVE DATA
                                        //////////
                                        $data = [
                                            "result" => "error",
                                            "data" => "Error al escribir el fichero ZIP " . $_REQUEST['$namedir']
                                        ];
                                        header('Content-Type: application/json');
                                        echo json_encode($data);
                                        die();

                                        echo '<p class="alert error-summary">Error al escribir el fichero ZIP <i>`' . $_REQUEST['namedir'] . '`</i></p>' .
                                            '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
                                        die();
                                    }
                                } else {

                                    // DEVUELVE DATA
                                    //////////
                                    $data = [
                                        "result" => "error",
                                        "data" => "Error al leer el fichero ZIP " . $_REQUEST['$namedir']
                                    ];
                                    header('Content-Type: application/json');
                                    echo json_encode($data);
                                    die();

                                    echo '<p class="alert error-summary">Error al leer el fichero ZIP <i>`' . $_REQUEST['namedir'] . '`</i></p>' .
                                        '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
                                    die();
                                }
                            } catch (Exception $e2) {

                                // DEVUELVE DATA
                                //////////
                                $data = [
                                    "result" => "error",
                                    "data" => "Excepción E/S " . $e2->getMessage() . "\n"
                                ];
                                header('Content-Type: application/json');
                                echo json_encode($data);
                                die();

                            }

                        } // ACTIVIDAD LTI ID/URL FALLO
                        else {

                            // DEVUELVE DATA
                            //////////
                            $data = [
                                "result" => "error",
                                "data" => "Error al crear carpeta publicacion " . $namedir
                            ];
                            header('Content-Type: application/json');
                            echo json_encode($data);
                            die();

                            echo '<p class="alert error-summary">Error al crear carpeta publicacion <i>`' . $namedir . '`</i></p>' .
                                '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
                            die("Cuando NO existe la Actividad en el Sistema LTI y hay qye crearla dese cerodo");
                        }
                    }
                }
                else {

                    // DEVUELVE DATA
                    //////////
                    $data = [
                        "result"=> "error",
                        "data" => "Error al dar el Alta a la Actividad LTI " . $namedir
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($data);
                    die();

                    echo '<p class="alert error-summary">Error al dar el Alta a la Actividad LTI <i>`' . $namedir . '`</i></p>' .
                        '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
                }
            endif;

            // DEVUELVE DATA
            //////////
            $data = [
                "result"=> "ok",
                "data" =>
                    [
                        'id_actividad' => $namedir,
                        'url_actividad' => $serverPub . '/' . $namedir,
                        "trabajo_actividad" => $file,
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
                            'actualizado' => 1
                        ],
                        "date"=> date('YmdHisu')
                    ]
            ];
            header('Content-Type: application/json');
            echo json_encode($data);
            die();

            ?>

            <p></p>
            <p></p>
            <p></p>
            <div class="alert alert-success">
                <ol>
                    <li>Git URL de la Actividad ´<b><i><a href="<?= $serverGit . '/' . $namedir ?>.git" target="_blank"><?= $namedir ?>.git</a></i></b>´ generado correctamente.<br/></li>
                    <li>Fichero de la Actividad ´<b><i><?= $file ?></i></b>´ descomprimido correctamente.<br/></li>
                    <li>Web URL de la Actividad ´<b><i><a href="<?= $serverPub . '/' . $namedir ?>" target="_blank"><?= $namedir ?></a></i></b>´ publicada correctamente</li>
                </ol>
            </div>

            <?php
            /**
            // Registra ID=$namedir ... y URL='../uploads/publicacion/$namedir/' en Colección BBDD Ltis y Uploads
            // REGISTRO
            ////////////////////////////////
             */
            echo '<p><div class="row alert alert-success">La actividad LTI ha quedado registrada con el ID: <b><i>`' . $namedir . '`</i></b> y la dirección URL: ´<b><i><a href="' . $serverPub . '/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´.</div>' .
                //'<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=site%2Fregister">Registrar LTI</a></div></div>'.
                '<div class="row alert alert-success">El Upload ha sido registrado con el ID: <b><i>`' . $namedir . '`</i></b>, el fichero: ´<b><i><a href="../uploads/' . $file . '" target="_blank">' . $file . '</a></i>´</b>, la carpeta `<b>' . $namedir . '</b>`, la dirección de publicación: ´<b><i><a href="' . $serverPub . '/' . $namedir . '" target="_blank">' . $namedir . '</a></i></b>´ y el proyecto Git: ´<b><i><a href="' . $serverGit . '/' . $namedir . '.git" target="_blank">' . $namedir . '.git</a></i></b>´.</div></p>' .
                //'<div class="col-lg-2"><a class="btn btn-lg btn-primary" href="index.php?r=crud%2Fregister">Registrar Upload</a></div></div>' .
                '';

            // Boton Atras
            echo '<p><a class="btn btn-lg btn-success" href="window.history.back()">Atrás</a></p>';

            // URL
            //  (preg_match('(http|Http|HTTP)', $_REQUEST['namedir']))
            // o
            // CARPETA
            //  else
            ////////////////////
            if ((preg_match("%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i", $_REQUEST['namedir']))){
                // URL de publicación Actividad
                die("Cuando NO existe la Actividad en el Sistema LTI y hay qye crearla de cerodo desde una URL (.zip)");
            }
            else {
                die("Cuando NO existe la Actividad en el Sistema LTI y hay qye crearla de cerodo desde una carpeta NFS (.zip)");
            }
        }
        // MKDIR difusion Actividad con errores
        else {

            // DEVUELVE DATA
            //////////
            $data = [
                "result"=> "error",
                "data" => "Error al crear carpeta difusion " . $namedir
            ];
            header('Content-Type: application/json');
            echo json_encode($data);
            die();

            echo '<p class="alert error-summary"><i>Error al crear carpeta difusion <i>`' . $namedir . '`</i></p>' .
                '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
        }
    }
    // PARAMETROS LLAMADA SERVICIO INCORREXTOS
    else
    {
        // DEVUELVE DATA
        //////////
        $data = [
            "result"=> "ok",
            "data" => "Formulario de subida o actualización de Econtent complejos desde el GICCU.
               Puede subirse un fichero comprimido .zip, de cada vez,
               sin contener espacios en blanco, tildes o eñes en el nombre."
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
        die();

        ?>

        <div class="upload-uploadupdater">

            <h1><?= $title ?></h1>
            <!--
            DONE
                Carga, sube y crea o actualiza el contenido de una actividad con el fichero .zip namedir y lo guarda con nombre file
                $_REQUEST Parámetros de carga
                          file      Nombre del fichero
                          namedir   URL de descarga del .ZIP
                                    carpeta de descarga del .ZIP
                          actividad ID/URL de la Actividad LTI (actualiza .ZIP)
                $_FILES Parámetros del fichero enviado por POST
            -->
            <p>
                Formulario de subida de Econtent complejos desde el GICCU.
                Puede subirse un fichero comprimido .zip, de cada vez,
                sin contener espacios en blanco, tildes o eñes en el nombre.
            </p>

            <div class="row">
                <div class="col-lg-5">
                </div>
            </div>

        </div>
        <?php
    }
}
catch (Exception $e1){

    // DEVUELVE DATA
    //////////
    $data = [
        "result"=> "error",
        "data" => "Excepción Parámetros " . $e1->getMessage() . "\n"
    ];
    header('Content-Type: application/json');
    echo json_encode($data);
    die();

}

//restore the previous error handler
restore_error_handler();

?>
