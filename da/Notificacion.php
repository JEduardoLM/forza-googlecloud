<?php

require_once('conexion.php');

class Notificacion{


    function saveNotificacion ($titulo, $descripcion, $idUsuario, $idGimnasio, $idSucursal, $URL, $topic, $color, $sound){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            $hoy = round(microtime(true) * 1000);


            if ($idGimnasio=="" or $idGimnasio==0)
                {
                    $idGimnasio='NULL';
                }

            if ($idSucursal=="" or $idSucursal==0)
                {
                    $idSucursal='NULL';
                }

            //Procedemos a armar las consultas
            $sql= "INSERT INTO `notificacion` (`titulo`, `descripcion`, `fecha`, `idUsuario`, `idGimnasio` ,`idSucursal`, `URL`)
                                       VALUES ('$titulo', '$descripcion', $hoy , $idUsuario,$idGimnasio, $idSucursal, '$URL');";



                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {


                            $response["success"]=0;
                            $response["message"]='Notificación almacenada correctamente';
                            $idNotificacion=mysqli_insert_id($conexion);
                            $response["getNotificacion"]= $this->getNotificacionById($idNotificacion);
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
            'notification' => array('title' => $titulo, 'body' => $mensaje, 'click_action' => 'NOTIFICATIONS', 'icon' => 'ic_notification_forza', 'color' => $color),
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
          $response["Response HTTP Body"]= " - " .$result ." -";
        }

        curl_close($ch);




       // echo json_encode($response); //devolvemos el array
        return ($response); //devolvemos el array


    }

    function getNotificacionesByIdSucursal($idSucursal){
           //Este método es utilizado para poder determinar cuantos socios se pueden agregar a una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
		$sql= "SELECT idnotificacion,titulo, Descripcion,fecha,
                (SELECT concat(Nombre,' ', Apellidos) as Autor FROM usuarioenforma where Id=n.idUsuario) as Autor,
                idSucursal, URL, idGimnasio AS IdGym
		          FROM notificacion n where idSucursal=$idSucursal order by fecha desc";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Notificaciones"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["IdNotificacion"]=$row["idnotificacion"];
                            $item["Titulo"]=$row["titulo"];
                            $item["Descripcion"]=$row["Descripcion"];
                            $item["Fecha"]=$row["fecha"];
                            $item["Autor"]=$row["Autor"];

                            $item["IdSucursal"]=$row["idSucursal"];
                            if ( $item["IdSucursal"]==NULL){ $item["IdSucursal"]=0;}

                            $item["IdGym"]=$row["IdGym"];
                            if ( $item["IdGym"]==NULL){ $item["IdGym"]=0;}

                            $item["URL"]=$row["URL"];

                            array_push($response["Notificaciones"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presentó un error al ejecutar la consulta';
            }


		desconectar($conexion); //desconectamos la base de datos
        }
    else
    {
        $response["success"]=3;
        $response["message"]='Se presentó un error al realizar la conexión';

    }
		return ($response); //devolvemos el array


    }

    function getNotificacionesByIdGym($idGym){
           //Este método es utilizado para poder determinar cuantos socios se pueden agregar a una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
		$sql= "SELECT idnotificacion,titulo, Descripcion,fecha,
                (SELECT concat(Nombre,' ', Apellidos) as Autor FROM usuarioenforma where Id=n.idUsuario) as Autor,
                idSucursal, URL, idGimnasio AS IdGym
		          FROM notificacion n where idGimnasio=$idGym order by fecha desc";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Notificaciones"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["IdNotificacion"]=$row["idnotificacion"];
                            $item["Titulo"]=$row["titulo"];
                            $item["Descripcion"]=$row["Descripcion"];
                            $item["Fecha"]=$row["fecha"];
                            $item["Autor"]=$row["Autor"];

                            $item["IdSucursal"]=$row["idSucursal"];
                            if ( $item["IdSucursal"]==NULL){ $item["IdSucursal"]=0;}

                            $item["IdGym"]=$row["IdGym"];
                            if ( $item["IdGym"]==NULL){ $item["IdGym"]=0;}

                            $item["URL"]=$row["URL"];

                            array_push($response["Notificaciones"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presentó un error al ejecutar la consulta';
            }


		desconectar($conexion); //desconectamos la base de datos
        }
    else
    {
        $response["success"]=3;
        $response["message"]='Se presentó un error al realizar la conexión';

    }
		return ($response); //devolvemos el array


    }

    function getNotificacionById($idNotificacion){

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
		$sql= "SELECT idnotificacion,titulo, Descripcion,fecha,
                (SELECT concat(Nombre,' ', Apellidos) as Autor FROM usuarioenforma where Id=n.idUsuario) as Autor,
                idSucursal, URL, idGimnasio AS IdGym
		          FROM notificacion n where idnotificacion=$idNotificacion";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["IdNotificacion"]=$row["idnotificacion"];
                            $item["Titulo"]=$row["titulo"];
                            $item["Descripcion"]=$row["Descripcion"];
                            $item["Fecha"]=$row["fecha"];
                            $item["Autor"]=$row["Autor"];

                            $item["IdSucursal"]=$row["idSucursal"];
                            if ( $item["IdSucursal"]==NULL){ $item["IdSucursal"]=0;}

                            $item["IdGym"]=$row["IdGym"];
                            if ( $item["IdGym"]==NULL){ $item["IdGym"]=0;}

                            $item["URL"]=$row["URL"];

                            $response["Notificacion"]=$item;
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presentó un error al ejecutar la consulta';
            }


		desconectar($conexion); //desconectamos la base de datos
        }
    else
    {
        $response["success"]=3;
        $response["message"]='Se presentó un error al realizar la conexión';

    }
		return ($response); //devolvemos el array



    }

}



?>
