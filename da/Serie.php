<?php

	// JELM
	// 15/03/2016
	// Se define la clase serie
    //

require_once('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos


class Serie{

    function obtenerSerieByID($idSerie)
    {
              //Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idSerie!=0)
		{
			$sql= "SELECT Sr_ID, NumeroSerie, ( SELECT ts.Nombre FROM tiposerie ts WHERE ts.TSr_ID = s.id_TipoSerie ) AS TipoSerie,
                            Repeticiones, PesoPropuesto,
                            (SELECT Abreviatura FROM unidadespeso up WHERE up.UP_ID = s.TipoPeso ) AS TipoPeso, Observaciones FROM serie s
                    WHERE Sr_ID =$idSerie;";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){


                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Sr_ID"]=$row["Sr_ID"];

                            $item["NumeroSerie"]=$row["NumeroSerie"];
                            if ($item["NumeroSerie"]==NULL){$item["NumeroSerie"]=0;}

                            $item["TipoSerie"]=$row["TipoSerie"];
                            if ($item["TipoSerie"]==NULL){$item["TipoSerie"]='';}

                            $item["Repeticiones"]=$row["Repeticiones"];
                            if ($item["Repeticiones"]==NULL){$item["Repeticiones"]=0;}

                            $item["PesoPropuesto"]=$row["PesoPropuesto"];
                            if ($item["PesoPropuesto"]==NULL){$item["PesoPropuesto"]=0;}

                            $item["TipoPeso"]=$row["TipoPeso"];
                            if ($item["TipoPeso"]==NULL){$item["TipoPeso"]='';}

                            $item["Observaciones"]=$row["Observaciones"];
                            if ($item["Observaciones"]==NULL){$item["Observaciones"]='';}

                        $response["serie"]= $item;
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presento un error al ejecutar la consulta';
            }

        }
		else
		{
                $response["success"]=5;
                $response["message"]='El id de la subrutina debe ser diferente de cero';
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

    function updatePesoEnSerie ($idSerie,$NuevoPeso,$TipoDePeso)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
        if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
            mysqli_autocommit($conexion, FALSE);

            mysqli_begin_transaction($conexion);
		    $sql="UPDATE `serie` SET `PesoPropuesto`='$NuevoPeso', `TipoPeso`='$TipoDePeso' WHERE `Sr_ID`='$idSerie'";

			if($result = mysqli_query($conexion, $sql)){

                $fecha = new DateTime();
                $hoy = $fecha->getTimestamp();

                $sql2="INSERT INTO pesoavances (`Peso`, `TipoPeso`, `id_Serie`,`Fecha`) VALUES ($NuevoPeso, $TipoDePeso, $idSerie, $hoy)";

                if($result = mysqli_query($conexion, $sql2)){
                    mysqli_commit($conexion);
                   // mysqli_close($conexion);

                    $serieDatos=$this->obtenerSerieByID($idSerie);

                    $response["serie"]=$serieDatos["serie"];
                    $response["success"]=0;
				    $response["message"]='Peso almacenado correctamente';
                }

                else{
                mysqli_rollback($conexion);
               // mysqli_close($conexion);
                $response["success"]=5;
				$response["message"]='El peso no pudo ser almacenado correctamente en el histórico';

                }


			}
			else{
                mysqli_rollback($conexion);
                //mysqli_close($conexion);
				$response["success"]=4;
				$response["message"]='El peso no pudo ser actualizado correctamente';

            }
		 desconectar($conexion); //desconectamos la base de datos
        }
        else{
            $response["success"]=3;
			$response["message"]='Se presentó un error al realizar la conexión con la base de datos';

        }
		return  ($response); //devolvemos el array
	}


    function saveNewSerie ($NumeroSerie, $Repeticiones, $TipoSerie, $Peso, $idSubrutinaEjercicio, $Observaciones, $TipoPeso)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado correctamente

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
            mysqli_autocommit($conexion, FALSE); //Desactivamos la opción de autocomit, para verificar que se actualice tanto la tabla serie como la tabla avances de peso

            mysqli_begin_transaction($conexion); // Iniciamos con la transacción

            if ($Observaciones==NULL){$Observaciones='';}

		    $sql="INSERT INTO `serie` (`NumeroSerie`, `Repeticiones`, `id_TipoSerie`, `PesoPropuesto`, `id_SubrutinaEjercicio`, `Observaciones`, `TipoPeso`)
                VALUES ($NumeroSerie, $Repeticiones, $TipoSerie, $Peso, $idSubrutinaEjercicio,'$Observaciones', $TipoPeso);";


        	if($result = mysqli_query($conexion, $sql)){

                $fecha = new DateTime();
                $hoy = $fecha->getTimestamp();
                $idSerie=mysqli_insert_id($conexion);
                $sql2="INSERT INTO pesoavances (`Peso`, `TipoPeso`, `id_Serie`,`Fecha`) VALUES ($Peso, $TipoPeso, $idSerie, $hoy)";

                if($result = mysqli_query($conexion, $sql2)){
                    mysqli_commit($conexion);
                   // mysqli_close($conexion);

                    $serieDatos=$this->obtenerSerieByID($idSerie);
                    $response["serie"]=$serieDatos["serie"];
                    $response["success"]=0;
				    $response["message"]='Serie almacenada correctamente';
                }

                else{
                mysqli_rollback($conexion);
               // mysqli_close($conexion);
                $response["success"]=5;
				$response["message"]='El peso no pudo ser almacenado correctamente en el histórico';

                }


			}
			else{
                mysqli_rollback($conexion);
                //mysqli_close($conexion);
				$response["success"]=4;
				$response["message"]='La serie no se pudo registrar correctamente';

            }
		 desconectar($conexion); //desconectamos la base de datos
        }
        else{
            $response["success"]=3;
			$response["message"]='Se presentó un error al realizar la conexión con la base de datos';

        }
		return  ($response); //devolvemos el array
	}

    function deleteSerie($idSerie,$idEjercicio,$numeroSerie){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();



        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


            //Procedemos a armar las consultas

                $sql= "DELETE FROM `serie` WHERE `Sr_ID`=$idSerie;";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {

                            $response["seriesReordenadas"]=$this->reordenarSerie($idEjercicio,$numeroSerie);
                            $response["success"]=0;
                            $response["message"]='La serie se eliminó correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al eliminar la rutina';
                }
                desconectar($conexion); //desconectamos la base de datos
            if ($response["success"]===0){
                $response["getSeries"]=$this->getSerieByEjercicioSubrutina($idEjercicio);
            }
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }


    function reordenarSerie($idEjercicio, $numeroSerie){
        // Esta función nos permite reordenar los ejercicios de una rutina en particular, cuando se haya eliminado un ejercicio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                $sql="UPDATE `serie` SET `NumeroSerie`=(NumeroSerie-1) WHERE  id_SubrutinaEjercicio=$idEjercicio and NumeroSerie >$numeroSerie;";

                if($result = mysqli_query($conexion, $sql))
                {

                                $response["success"]=0;
                                $response["message"]='Rutinas actualizadas correctamente';

                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al actualizar las rutinas';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }



    function updateSerie ($idSerie,$NumeroSerie, $Repeticiones, $TipoSerie, $Peso, $idSubrutinaEjercicio, $Observaciones, $TipoPeso)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado correctamente

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
            mysqli_autocommit($conexion, FALSE); //Desactivamos la opción de autocomit, para verificar que se actualice tanto la tabla serie como la tabla avances de peso

            mysqli_begin_transaction($conexion); // Iniciamos con la transacción

            if ($Observaciones==NULL){$Observaciones='';}

		    $sql="UPDATE `serie` SET `NumeroSerie`=$NumeroSerie, `Repeticiones`=$Repeticiones, `id_TipoSerie`=$TipoSerie, `PesoPropuesto`=$Peso, `id_SubrutinaEjercicio`=$idSubrutinaEjercicio, `Observaciones`='$Observaciones', `TipoPeso`=$TipoPeso WHERE `Sr_ID`=$idSerie;";



        	if($result = mysqli_query($conexion, $sql)){

                $fecha = new DateTime();
                $hoy = $fecha->getTimestamp();

                $sql2="UPDATE `pesoavances` SET `Peso`=$Peso, `TipoPeso`=$TipoPeso, `Fecha`=$hoy WHERE id_Serie=$idSerie order by Fecha desc limit 1;";


                if($result = mysqli_query($conexion, $sql2)){
                    mysqli_commit($conexion);
                   // mysqli_close($conexion);

                    $serieDatos=$this->obtenerSerieByID($idSerie);
                    $response["serie"]=$serieDatos["serie"];
                    $response["success"]=0;
				    $response["message"]='Serie actualizada correctamente';
                }

                else{
                mysqli_rollback($conexion);
               // mysqli_close($conexion);
                $response["success"]=5;
				$response["message"]='El nuevo peso no pudo ser almacenado correctamente en el histórico';

                }


			}
			else{
                mysqli_rollback($conexion);
                //mysqli_close($conexion);
				$response["success"]=4;
				$response["message"]='La serie no se pudo actualizar correctamente';

            }
		 desconectar($conexion); //desconectamos la base de datos
        }
        else{
            $response["success"]=3;
			$response["message"]='Se presentó un error al realizar la conexión con la base de datos';

        }
		return  ($response); //devolvemos el array
	}


    function getSerieByEjercicioSubrutina($idEjercicio){
        //Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idEjercicio!=0)
		{
			$sql= "SELECT Sr_ID, NumeroSerie, ( SELECT ts.Nombre FROM tiposerie ts WHERE ts.TSr_ID = s.id_TipoSerie ) AS TipoSerie,
                            Repeticiones, PesoPropuesto,
                            (SELECT Abreviatura FROM unidadespeso up WHERE up.UP_ID = s.TipoPeso ) AS TipoPeso, Observaciones FROM serie s
                    WHERE id_SubrutinaEjercicio =$idEjercicio;";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["series"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Sr_ID"]=$row["Sr_ID"];

                            $item["NumeroSerie"]=$row["NumeroSerie"];
                            if ($item["NumeroSerie"]==NULL){$item["NumeroSerie"]=0;}

                            $item["TipoSerie"]=$row["TipoSerie"];
                            if ($item["TipoSerie"]==NULL){$item["TipoSerie"]='';}

                            $item["Repeticiones"]=$row["Repeticiones"];
                            if ($item["Repeticiones"]==NULL){$item["Repeticiones"]=0;}

                            $item["PesoPropuesto"]=$row["PesoPropuesto"];
                            if ($item["PesoPropuesto"]==NULL){$item["PesoPropuesto"]=0;}

                            $item["TipoPeso"]=$row["TipoPeso"];
                            if ($item["TipoPeso"]==NULL){$item["TipoPeso"]='';}

                            $item["Observaciones"]=$row["Observaciones"];
                            if ($item["Observaciones"]==NULL){$item["Observaciones"]='';}

                        array_push($response["series"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presento un error al ejecutar la consulta';
            }

        }
		else
		{
                $response["success"]=5;
                $response["message"]='El id de la subrutina debe ser diferente de cero';
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



//
//  $S = new Serie();
//  $Ss=$S->deleteSerie (49996,13111,3);
//  echo json_encode ($Ss);


?>
