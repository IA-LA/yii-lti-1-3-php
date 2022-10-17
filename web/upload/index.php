<?php

$title = 'Upload Zip Updater';
$params['breadcrumbs'][] = $title;

// ini_set('upload_max_filesize', '10M');

// Remember current URL
$url=$_SERVER;

// TODO MULTIPROCESO/MULTITAREA
//  https://medium.com/async-php/multi-process-php-94a4e5a4be05
if ((isset($_REQUEST['file']) && isset($_REQUEST['namedir'])) && ((($_REQUEST['file'] !== null) && ((preg_match('(zip|Zip|ZIP)', $_REQUEST['file'])))) || (($_REQUEST['namedir'] !== null) && (preg_match('(zip|Zip|ZIP)', $_REQUEST['namedir'])))))
{
    $file='';
    $naedir='';
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
    //$namedir= substr('nombreTrabajo',0, (strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) >=0 ? strlen('nombreTrabajo') - strlen(Yii::$app->user->identity->username) : 0)) . Yii::$app->user->identity->username . date('YmdHisu') . '00000003';
    //$namedir= Yii::$app->user->identity->id . date('YmdHisu') . 'a';
    //$namedir=$_REQUEST['namedir'] . Yii::$app->user->identity->username . date('YmdHisu') . 'd';
    $namedir = /*explode('.zip', strtolower($file))[0] . "difusion"*/"000" . date('YmdHisu') . 'd';
    if (isset($_REQUEST['actividad']))
        $actividad = $_REQUEST['actividad'];
    else
        $actividad = 'zip';

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


    // COPIA el archivo .zip en la carpeta de difusion
    /////////////////////////////////////////////////
    ///
    // Carpeta de difusión Actividad
    umask(0000);
    $output = shell_exec(escapeshellcmd('mkdir ../uploads/difusion'));
    //echo "<pre>$output</pre>";

    // Carpeta de Actividad cargada y publicada
    // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'd'
    /////////////////////////////////
    // outputs the username that owns the running php/httpd process
    // (on a system with the "mkdir" executable in the path)
    $output=null;
    $retval=null;
    umask(0000);
    exec(escapeshellcmd('mkdir ../uploads/difusion/' . $namedir), $output, $retval);

    // Fichero ZIP subido
    // MKDIR difusion Actividad sin errores
    if($retval === 0) {

        //  - LA ACTIVIDAD
        //      ID: 00000000000000000000000a
        //      ID: 5e0df19c0c2e74489066b43f
        //      URL https://ailanto-dev.intecca.uned.es/publicacion/00020220721114124000000d
        /*Yii::$app->session->hasFlash('uploadupdterExistting')*/
        if ((!(preg_match('(zip|Zip|ZIP)', $actividad)) && (preg_match('([a-f,0-9]{24})', $actividad)))):
            // TODO recuperar Credenciales de Actividad LTI por ID
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

            }
            // ACTIVIDAD ID/URL NO EXISTE
            else{
                // DEVUELVE DATA
                //////////
                header('Content-Type: application/json');
                echo json_encode($arrayFile);
                die();
                die("Cuando YA existe la Actividad en el Sistema LTI y sólo hay qye subir el fichero .ZIP y actualizar el git");
            }
        //  - LA URL
        //      http...
        //  - LA CARPETA
        //      /ruta/fichero.zip
        /**/
        else:
            // TODO crear Actividad LTI
            //die("Cuando NO existe la Actividad en el Sistema LTI y hay qye crearla dese cerodo");
            // Carpeta de publicación Actividad
            umask(0000);
            $output = shell_exec(escapeshellcmd('mkdir ../uploads/publicacion'));
            //echo "<pre>$output</pre>";

            // Carpeta de Actividad cargada y publicada
            // Convenio de nombre actividades (24 hex) y carpeta = id user + fecha y hora + 'a'
            /////////////////////////////////
            // outputs the username that owns the running php/httpd process
            // (on a system with the "mkdir" executable in the path)
            $output=null;
            $retval=null;
            umask(0000);
            exec(escapeshellcmd('mkdir ../uploads/publicacion/' . $namedir), $output, $retval);

            // MKDIR publicacion sin errores
            if($retval === 0) {

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
                $output=null;
                $retval=null;
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
                $output=null;
                $retval=null;
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
                $output=null;
                $retval=null;
                exec(escapeshellcmd('git clone ../uploads/git/' . $namedir . '.git ../uploads/publicacion/' . $namedir), $output, $retval);
                //echo "5.Returned with status $retval and output:\n";
                //echo "<p><pre>5.a. git clone ../uploads/git/$namedir.git ../uploads/publicacion/$namedir<br/>";
                //print_r($output);
                //echo "</pre></p>";
                //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/ clone ../uploads/git/' . $namedir . '.git ' . $namedir));
                //$output = shell_exec(escapeshellcmd('git clone ../uploads/git/' . $namedir . '.git ../uploads/publicacion/' . $namedir . ' 2>&1'));
                //echo "<pre>5.b. $output</pre>";

            }
            else {
                echo '<p class="alert error-summary"><i>Error al crear carpeta publicacion <i>`' . $namedir . '`</i></p>' .
                     '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
            }
        endif;

        ///
        // Descomprime archivo Zip 'unzip -o -X ../uploads/difusion/'
        //  ID=$namedir
        ///////////////////////////////////////////////////////////////////////////////////

        // COPIA el archivo .zip en la carpeta de difusion
        /////////////////////////////////////////////////
        $arrayFile = file_get_contents($_REQUEST['namedir']);
        file_put_contents('../uploads/difusion/' . $namedir . '/' . $file, $arrayFile);

        // Descomprime .zip
        // outputs the username that owns the running php/httpd process
        // (on a system with the "unzip" executable in the path)
        //$output=null;
        //$retval=null;
        //'unzip ../uploads/Plantilla\ ePub\ 1_5c4ad1844ffce90a5d17f666.zip -d ../uploads/publicacion/nombreTrabajoXXX00000000/'
        //exec(escapeshellcmd('unzip ../uploads/CANVAS_QTI_IMPORT_UNIT_TEST.zip -d ../uploads/publicacion/nombreTrabajoXXX00000000/'), $output, $retval);
        //exec(escapeshellcmd('unzip ../uploads/cindetececontentv1_5a5db903d3bd0d7623bc10c0.zip -d ../uploads/publicacion/' . $namedir), $output, $retval);
        //exec(escapeshellcmd('unzip ../uploads/' . $file . ' -d ../uploads/publicacion/' . $namedir), $output, $retval);

        // Unzip Actividad .zip
        // outputs the username that owns the running php/httpd process
        // (on a system with the "unzip" executable in the path)
        $output=null;
        $retval=null;
        umask(0000);
        exec(escapeshellcmd('unzip -o -X ../uploads/difusion/' . $namedir . '/' . $_REQUEST['file'] . ' -d ../uploads/publicacion/' . $namedir), $output, $retval);
        exec(escapeshellcmd('chmod 774 -R ../uploads/publicacion/' . $namedir), $output, $retval);
        //exec(escapeshellcmd('unzip ../uploads/cindetechtmlv1_5a5db903d3bd0d7623bc10c0.zip -d ../uploads/publicacion/' . $namedir), $output, $retval);
        //echo "6.Returned with status $retval and output:\n";
        //echo "<i> " . count($output) . " archivos descomprimidos. Status y resultado " . ($retval === 0 ? 'correctos' : 'erróneos') . ":\n</i>";
        //echo "<p><pre> 6.a. Unzip PassThru " . passthru('unzip -o -X ../uploads/' . $file . ' -d ../uploads/publicacion/' . $namedir . ' 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        //$output = shell_exec(escapeshellcmd('unzip -o -X ../uploads/' . $file . ' -d ../uploads/publicacion/' . $namedir));
        //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> ../uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
        //echo "<pre>6.b.$output</pre>";
        //$output = shell_exec(escapeshellcmd('touch ../uploads/publicacion/' . $namedir . '/HolaMundo.txt 2>&1'));
        //echo "<pre>6.c. touch HolaMundo.txt $output</pre>";
        //$output = shell_exec(escapeshellcmd('echo "Hola Mundo Linux" >> ../uploads/publicacion/' . $namedir . '/HolaMundo.txt'));
        //echo "<pre>6.d. $output</pre>";

        // Add, Commit y Push clonado
        // Add
        // outputs the username that owns the running php/httpd process
        // (on a system with the "git add" executable in the path)
        $output=null;
        $retval=null;
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ reset '), $output, $retval);
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ add . '), $output, $retval);
        //echo "7.Returned with status $retval and output:\n";
        //echo "<p><pre>7.a. PassThru " . passthru('git -C ../uploads/publicacion/' . $namedir . '/ add . 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ add .'));
        //echo "<pre>7.b. $output</pre>";
        //$output = shell_exec(escapeshellcmd('sleep 0.5s'));
        //echo "<pre>7.c. $output</pre>";

        // Commit Config
        // outputs the username that owns the running php/httpd process
        // (on a system with the "git add" executable in the path)
        $output=null;
        $retval=null;
        //exec(escapeshellcmd('git config --global user.email "you@example.com"'), $output, $retval);
        //exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.email "you@example.com"'), $output, $retval);
        //exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.email "' . Yii::$app->user->identity->username . '@lti.server"'), $output, $retval);
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.email "' . 'gcono' . '@lti.server"'), $output, $retval);
        //echo "8.Returned with status $retval and output:\n";
        //echo "<p><pre>8.a.<br/>";
        //echo "8.a.PassThru " . passthru('git -C ../uploads/publicacion/' . $namedir . ' config user.email "you@example.com" 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        // outputs the username that owns the running php/httpd process
        // (on a system with the "git add" executable in the path)
        $output=null;
        $retval=null;
        //exec(escapeshellcmd('git config --global user.name "Your Name"'), $output, $retval);
        //exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.name "Your Name"'), $output, $retval);
        //exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.name "' . Yii::$app->user->identity->id . '"'), $output, $retval);
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config --local user.name "' . '000' . '"'), $output, $retval);

        //echo "8.Returned with status $retval and output:\n";
        //echo "<p><pre>8.b.<br/>";
        //echo "8.b.PassThru " . passthru('git -C ../uploads/publicacion/' . $namedir . ' config user.name "Your Name" 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config user.email "' . Yii::$app->user->identity->username . '@lti.server" 2>&1'));
        //echo "<pre>8.c. $output</pre>";
        //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . ' config user.name "'. Yii::$app->user->identity->id .'" 2>&1'));
        //echo "<pre>8.d. $output</pre>";

        // Commit -m "Init Commit Server LTI"
        // outputs the username that owns the running php/httpd process
        // (on a system with the "git add" executable in the path)
        $output=null;
        $retval=null;
        //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ commit -m "Init Commit Server LTI"'), $output, $retval);
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ commit -m "Difusion Commit Server LTI"'), $output, $retval);
        //echo "9.Returned with status $retval and output:\n";
        //echo "<p><pre>9.a. git -C ../uploads/publicacion/$namedir/ commit -m 'Initial Commit Server LTI' <br/>";
        //echo "9.PassThru" . passthru('git -C ../uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ commit -m "Initial Commit Server LTI" 2>&1'));
        //echo "<pre>9.b. $output</pre>";

        // Push clonado
        // outputs the username that owns the running php/httpd process
        // (on a system with the "git add" executable in the path)
        $output=null;
        $retval=null;
        //exec(escapeshellcmd('git -C ' . $carpetaGit . '/uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
        exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ push origin master'), $output, $retval);
        //echo "10.Returned with status $retval and output:\n";
        //echo "<p><pre>10.a. git -C ../uploads/publicacion/$namedir/ push origin master<br/>";
        //echo "10.PassThru" . passthru('git -C ../uploads/publicacion/' . $namedir . '/ push origin master 2>&1') . "<br/>";
        //print_r($output);
        //echo "</pre></p>";
        //$output = shell_exec(escapeshellcmd('git -C ../uploads/publicacion/' . $namedir . '/ push origin master 2>&1'));
        //echo "<pre>10.b. $output</pre>";

        // Git, UNZIP y Publicacion sin errores
        if($retval === 0) {

            // DEVUELVE DATA
            //////////
            $data = [
                "result"=> "ok",
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
        else {
            echo '<p class="alert error-summary">Error al descomprimir, publicar e iniciar y clonar  el proyecto desde el fichero <i>`' . $file . '`</i></p>' .
                '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
        }
    }
    // Fichero ZIP NO subido
    else {
        echo '<p class="alert error-summary"><i>Error al crear carpeta difusion <i>`' . $namedir . '`</i></p>' .
            '<p><a class="btn btn-lg btn-warning" href="window.history.back()">Atrás</a></p>';
    }
 }
else
    {
?>

    <div class="upload-uploadupdater">

        <h1><?= $title ?></h1>

        <!--
        TODO
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
?>