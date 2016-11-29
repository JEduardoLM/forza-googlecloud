<?php

require_once('conexion.php');

class Notificacion{


    function saveNotificacion ($titulo, $descripcion, $idUsuario, $idSucursal, $URL){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            $fecha = new DateTime();
            $hoy = $fecha->getTimestamp();

            //Procedemos a armar las consultas
            $sql= "INSERT INTO `notificacion` (`titulo`, `descripcion`, `fecha`, `idUsuario`, `idSucursal`, `URL`)
                                       VALUES ($titulo, $descripcion, $hoy , $idUsuario, $idSucursal, $URL);";


                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {

                            $response["success"]=0;
                            $response["message"]='Notificación almacenada correctamente';

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



}



?>
