<?php
require_once('conexion.php');

class Rutina{

	function getRutinaByIdSocio($idSocio){ // Esta función nos regresa la rutina activa de un socio especifico
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idSocio!=0)
		{
			$sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Instructor FROM Rutina where Estatus=1  and id_Socio=$idSocio order  by FechaInicio desc  LIMIT 1";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Rutina"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["R_ID"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["FechaInicio"]=$row["FechaInicio"];
                            $item["NumeroSemanas"]=$row["NumeroSemanas"];
                            $item["Estatus"]=$row["Estatus"];
                            $item["Objetivo"]=$row["Objetivo"];
                            $item["id_Socio"]=$row["id_Socio"];
                            $item["id_Instructor"]=$row["id_Instructor"];

                            array_push($response["Rutina"], $item);
                        }
                        $response["success"]=1;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=0;
                        $response["message"]='El socio no cuenta con una rutina activa';
                    }

                }
                else
                    {
                        $response["success"]=0;
                        $response["message"]='El socio no cuenta con una rutina activa';
                    }
            }
            else
            {
                $response["success"]=0;
                $response["message"]='Se presento un error al ejecutar la consulta';
            }

        }
		else
		{
                $response["success"]=0;
                $response["message"]='El id del usuario debe ser diferente de cero';
		}
		desconectar($conexion); //desconectamos la base de datos
		return ($response); //devolvemos el array
	}

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function duplicarRutina($idRutina, $idSocio, $fecha, $numeroSemanas, $objetivo, $idInstructor){

        //Creamos la conexión
        $conexion = obtenerConexion();

        mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        if ($conexion){ //Verificamos la conexión, en caso de fallar regresamos el error de conexión NO EXITOSA

            /* deshabilitar autocommit para poder hacer un rollback*/
            mysqli_autocommit($conexion, FALSE);

            //Lo primero que vamos a hacer es duplicar la rutina

            if($objetivo==NULL or $objetivo==''){
                $sql="INSERT INTO Rutina
                        SELECT NULL as R_ID, Nombre, '$fecha' as FechaInicio, '$numeroSemanas' NumeroSemanas, 1 as Estatus, Objetivo, '$idSocio' as id_Socio,NULL as id_Sucursal, '$idInstructor' as Id_Instructor FROM Rutina
                        where R_ID=$idRutina;";
            }
            else{
                $sql="INSERT INTO Rutina
                    SELECT NULL as R_ID, Nombre, '$fecha' as FechaInicio, '$numeroSemanas' NumeroSemanas, 1 as Estatus, '$objetivo' as Objetivo, '$idSocio' as id_Socio,NULL as id_Sucursal, '$idInstructor' as Id_Instructor FROM Rutina
                    where R_ID=$idRutina;";
            }

            echo '*******'.$sql.'*****************';
                if($result = mysqli_query($conexion, $sql)){ //Ejecutamos la consulta para duplicar la rutina y verificamos si se ejecutó correctamente

                        $idRutinaNueva=mysqli_insert_id($conexion);

                        //Procedemos a hacer una consulta, para obtener los días de la rutina a clonar, y poder duplicar cada uno de los días.
                        $sql2="SELECT SR_ID, Orden, IdRutina, Nombre  FROM Subrutina where IdRutina = $idRutina;";

                        if($result2 = mysqli_query($conexion, $sql2)){
                            $seDuplicoTodo=1;
                            while($row = mysqli_fetch_array($result2)) //Recorremos cada uno de los días, para proceder a dar de alta cada registro.
                            {

                                $idSubrutinaOrigen=$row["SR_ID"];
                                $orden=$row["Orden"];
                                $nombreSubrutina=$row["Nombre"];

                                $sqlSubrutina="INSERT INTO `Subrutina` (`Orden`, `IdRutina`, `Nombre`) VALUES ('$orden', '$idRutinaNueva', '$nombreSubrutina');" ;

                                if($resultSubrutina = mysqli_query($conexion, $sqlSubrutina)){
                                    $idSubrutinaNueva=mysqli_insert_id($conexion); // Si la subrutina se insertó correctamente procedemos a obtener el Id de la nueva subrutina

                                    //Una vez que tenemos la subrutina, vamos a proceder a duplicar los registros de la tabla de cardio.
                                    $sqlCardio="INSERT INTO SubRutinaEjercicioCardio
                                        (SELECT NULL as SEC_ID, '$idSubrutinaNueva' as Id_Subrutina, Id_EjercicioCardio, Tiempototal, Velocidadpromedio, TipoDeVelocidad, DistanciaTotal, TipoDistancia, Ritmocardiaco, Nivel, Observaciones, Orden FROM SubRutinaEjercicioCardio where Id_Subrutina=$idSubrutinaOrigen);";

                                    if($resultCardio = mysqli_query($conexion, $sqlCardio)){

                                        //Si se ejecutó correctamente la duplicidad de los ejercicios de cárdio, procedemos con la duplicidad de los ejercicios de pesas
                                        $sqlPesa="SELECT SEP_ID, Id_Subrutina, Id_EjercicioPeso, Circuito, TiempoDescansoEntreSerie, Observaciones, Orden
                                        FROM SubRutinaEjercicioPeso where Id_Subrutina=$idSubrutinaOrigen ;"; //Ya que necesitamos obtener el id de cada registro ingresado, vamos a proceder a recorrer cada ejercicio de pesas de la serie, para irlos registrando.



                                        if($resultPesa = mysqli_query($conexion, $sqlPesa)){

                                            while($rowPesa = mysqli_fetch_array($resultPesa)) //Recorremos cada uno de los ejercicios de pesas, para proceder a dar de alta cada registro.
                                            {
                                                $idEjercicioPesasOrigen=$rowPesa["SEP_ID"];
                                                $idSubrutinaPesas=$rowPesa["Id_Subrutina"];
                                                $ejercicioPesas=$rowPesa["Id_EjercicioPeso"];
                                                $circuitoPesas=$rowPesa["Circuito"];
                                                if ($circuitoPesas==NULL or $circuitoPesas==""){$circuitoPesas=0;}

                                                $tiempoDescansoEntreSerie=$rowPesa["TiempoDescansoEntreSerie"];
                                                if ($tiempoDescansoEntreSerie==NULL or $tiempoDescansoEntreSerie==''){$tiempoDescansoEntreSerie=0;}

                                                $observacionesPesas=$rowPesa["Observaciones"];

                                                $ordenPesas=$rowPesa["Orden"];
                                                if($ordenPesas==NULL or $ordenPesas==''){$ordenPesas=0;}

                                                $sqlPesas2="INSERT INTO `SubRutinaEjercicioPeso` (`Id_Subrutina`, `Id_EjercicioPeso`, `Circuito`, `TiempoDescansoEntreSerie`, `Observaciones`, `Orden`)
                                                VALUES ('$idSubrutinaNueva', '$ejercicioPesas', '$circuitoPesas', '$tiempoDescansoEntreSerie', '$observacionesPesas', '$ordenPesas');";

                                                if($resultPesas2 = mysqli_query($conexion, $sqlPesas2)){ // Ejecutamos la consulta, para insertar los ejecicios de pesas
                                                    $idEjercicioPesas=mysqli_insert_id($conexion); //Obtenemos el id del registro de pesas
                                                    //Una vez que registramos el ejercicio de pesas, procedemos a duplicar las series

                                                    $sqlSeries="INSERT INTO Serie
                                                    (SELECT NULL as Sr_ID, NumeroSerie, Repeticiones, id_TipoSerie, PesoPropuesto, '$idEjercicioPesas' as id_SubrutinaEjercicio, Observaciones, TipoPeso
                                                    FROM Serie where id_SubrutinaEjercicio=$idEjercicioPesasOrigen);";

                                                    if ($resultSeries=mysqli_query($conexion, $sqlSeries)){ //Ejecutamos la consulta para duplicar las diferentes series del ejercicio

                                                    }
                                                    else{
                                                        $seDuplicoTodo=0;
                                                        $response["success"]=8;
                                                        $response["message"]='Se presentó un error al duplicar las series del ejercicio: '.$idEjercicioPesasOrigen." ";
                                                        /* Revertir */
                                                        mysqli_rollback($conexion);
                                                    }

                                                }
                                                else{
                                                    $seDuplicoTodo=0;
                                                    $response["success"]=8;
                                                    $response["message"]='Se presentó un error al duplicar el ejercicio con id: '.$idEjercicioPesasOrigen." ";
                                                    /* Revertir */
                                                    echo "La consulta que falló es la: ".$sqlPesas2.' ******';
                                                    mysqli_rollback($conexion);
                                                }

                                            }
                                        }
                                        else{
                                                $seDuplicoTodo=0;
                                                $response["success"]=8;
                                                $response["message"]='Se presentó un error al consultar los ejercicios de pesas de la subrutina con Id: '.$idSubrutinaOrigen." ";
                                                /* Revertir */
                                                 mysqli_rollback($conexion);
                                        }

                                    }
                                    else{
                                        $seDuplicoTodo=0;
                                        $response["success"]=7;
                                        $response["message"]='Se presentó un error al duplicar los ejercicios de cardio de la subrutina con Id: '.$idSubrutinaOrigen." ";
                                        /* Revertir */
                                        mysqli_rollback($conexion);
                                    }


                                }
                                else{
                                    $seDuplicoTodo=0;
                                    $response["success"]=6;
                                    $response["message"]='Se presentó un error al duplicar la subrutina con Id: '.$idSubrutinaOrigen." ";
                                    /* Revertir */
                                    mysqli_rollback($conexion);
                                }

                            }

                            if ($seDuplicoTodo==1){
                                mysqli_commit($conexion);
                                $response["success"]=0;
                                $response["message"]='Rutina clonada correctamente';
                            }
                        }
                        else{

                            $response["success"]=5;
                            $response["message"]='Se presentó un error al consultar los días de la rutina (subrutinas)';
                            /* Revertir */
                            mysqli_rollback($conexion);
                        }
                        }
                        else{

                            $response["success"]=4;
                            $response["message"]='Se presentó un error al duplicar la rutina';
                            /* Revertir */
                            mysqli_rollback($conexion);
                        }
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presento un error al realizar la conexión';

        }
        return $response;
    }



}

  $Rutina = new Rutina();
  $RutinaR=$Rutina->duplicarRutina(1,7,'2016-04-08',4,'',1);
  echo json_encode ($RutinaR);


?>
