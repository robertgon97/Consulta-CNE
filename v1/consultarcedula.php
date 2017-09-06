<?php
//Clase pulida por robert ;3
class SearchCurl {
    public static function SearchCNE($nac, $ci) {
        $url = "http://www.cne.gov.ve/web/registro_electoral/ce.php?nacionalidad=$nac&cedula=$ci";
        $resource = self::geUrl($url);
        $text = strip_tags($resource);
        $findme = 'SERVICIO ELECTORAL'; // Identifica que si es población Votante
        $pos = strpos($text, $findme);

        $findme2 = 'ADVERTENCIA'; // Identifica que si es población Votante
        $pos2 = strpos($text, $findme2);

        if ($pos == TRUE AND $pos2 == FALSE) {
            // Codigo buscar votante
            $rempl = array('Cédula:', 'Nombre:', 'Estado:', 'Municipio:', 'Parroquia:', 'Centro:', 'Dirección:', 'SERVICIO ELECTORAL', 'Mesa:');
            $r = trim(str_replace($rempl, '|', self::limpiarCampo($text)));
            $resource = explode("|", $r);
            $datos = explode(" ", self::limpiarCampo($resource[2]));
            $datoJson = array('error' => 0, 'nacionalidad' => $nac, 'cedula' => $ci, 'nombres' => $datos[0] . ' ' . $datos[1], 'apellidos' => $datos[2] . ' ' . $datos[3], 'inscrito' => 'SI', 'cvestado' => self::limpiarCampo($resource[3]), 'cvmunicipio' => self::limpiarCampo($resource[4]), 'cvparroquia' => self::limpiarCampo($resource[5]), 'centro' => self::limpiarCampo($resource[6]), 'direccion' => self::limpiarCampo($resource[7]));
        } elseif ($pos == FALSE AND $pos2 == FALSE) {
            // Codigo buscar votante
            $rempl = array('Cédula:', 'Primer Nombre:', 'Segundo Nombre:', 'Primer Apellido:', 'Segundo Apellido:', 'ESTATUS');
            $r = trim(str_replace($rempl, '|', $text));
            $resource = explode("|", $r);
            $datoJson = array('error' => 2, 'nacionalidad' => NULL, 'cedula' => $ci, 'nombres' => NULL, 'apellidos' => NULL, 'inscrito' => 'NO -(de la linea 31)');
        } elseif ($pos == FALSE AND $pos2 == TRUE) {
            $datoJson = array('error' => 1, 'nacionalidad' => $nac, 'cedula' => $ci, 'nombres' => NULL, 'apellidos' => NULL, 'inscrito' => 'NO -(de la linea 33)');
        }
        //Mostramos el resultado
        //echo json_encode($datoJson);
        if($datoJson['error']==0){
            echoResponse(200, $datoJson);
        }else if($datoJson['error']==1){
            //echo "Error en no se";
            echoResponse(500, $datoJson);
        }else if($datoJson['error']==2){
            //echo "Esta cedula no existe";
            echoResponse(404, $datoJson);
        }
        
    }
    public static function geUrl($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // almacene en una variable
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (curl_exec($curl) === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $return = curl_exec($curl);
        }
        curl_close($curl);

        return $return;
    }

    public static function limpiarCampo($valor) {//Con esto limpiamos los errores de la pagina
        $rempl = array('\n', '\t');
        $r = trim(str_replace($rempl, ' ', $valor));
        return str_replace("\r", "", str_replace("\n", "", str_replace("\t", "", $r)));
    }

}

//$curls = new SearchCurl();
//$curls->SearchCNE('V', 20452262);//25607879
?>
