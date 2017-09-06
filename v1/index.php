<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: X-Requested-With");
    header('Content-Type: text/html; charset=utf-8');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

    include_once '../include/Config.php';//Importas las configuraciones
    include ('./consultarcedula.php');//lo de php
    require '../libs/Slim/Slim.php'; 
    \Slim\Slim::registerAutoloader(); 
    $app = new \Slim\Slim();
    //Tomar desde GET
    $app->get('/:N/:cedula', function($N,$cedul) {
        $curls = new SearchCurl();
        $cedula = (int)$cedul;
        $nacionalidad= strtoupper($N);
        $curls->SearchCNE($nacionalidad, $cedula);//25607879
        //echoResponse(200, $curls);
    });
    
    /////////////////////////////////////////////////////////////
    $app->run();//Lo corremos
    ////////////////////////////////////////////////////////////
    //Funciones de la API
    function verifyRequiredParams($required_fields) {
        $error = false;
        $error_fields = "";
        $request_params = array();
        $request_params = $_REQUEST;
        // Handling PUT request params
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $app = \Slim\Slim::getInstance();
            parse_str($app->request()->getBody(), $request_params);
        }
        foreach ($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }
     
        if ($error) {
            $response = array();
            $app = \Slim\Slim::getInstance();
            $response["error"] = true;
            $response["message"] = 'Error' . substr($error_fields, 0, -2) . ' !!!!';
            echoResponse(400, $response);
            $app->stop();
        }
    }

    function echoResponse($status_code, $response) {
        $app = \Slim\Slim::getInstance();
        $app->status($status_code);
        $app->contentType('application/json');
        echo json_encode($response);
    }
    
?>