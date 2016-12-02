<?php

require_once('conexion.php');

class Notificacion{


    function saveNotificacion ($titulo, $descripcion, $idUsuario, $idSucursal, $URL, $topic, $color, $sound){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            $fecha = new DateTime();
            $hoy = $fecha->getTimestamp();

            //Procedemos a armar las consultas
            $sql= "INSERT INTO `notificacion` (`titulo`, `descripcion`, `fecha`, `idUsuario`, `idSucursal`, `URL`)
                                       VALUES ('$titulo', '$descripcion', $hoy , $idUsuario, $idSucursal, '$URL');";


                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {

                            $response["success"]=0;
                            $response["message"]='Notificación almacenada correctamente';
                            $response["notificacionEnviada"]=$this->enviarNotificacionPush($titulo,$descripcion,$topic,$sound,$color);

                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }
                desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    function enviarNotificacionPush($titulo, $mensaje, $topic, $sound, $color ){
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'to' => "/topics/$topic",
            'notification' => array('title' => $titulo, 'body' => $mensaje, 'click_action' => 'OPEN_ACTIVITY_1', 'icon' => 'ic_notification_forza', 'color' => "#63a21d"),
            'data' => array('message' => array('offer' => '.5'))
        );

        $headers = array(
            'Authorization:key=AIzaSyBBXEj5mSDFK-w1HnSfw7yRhrJrZyI7mf0',
            'Content-Type:application/json'
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if(!$result) {
          $response["success"]=3;
          $response["Error"]='Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
        } else {
          $response["success"]=0;
          $response["StatusCode"]= curl_getinfo($ch,     CURLINFO_HTTP_CODE);
          $response["message"]='Notificacion enviada correctamente.';
        }

        curl_close($ch);




       // echo json_encode($response); //devolvemos el array
        return ($response); //devolvemos el array


    }



}



?>
