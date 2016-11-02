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

    function updatePesoEnSerie ($idSerie,$NuevoPeso,$TipoDePeso,$idEjercicio)
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
            // Una vez que se ha insertado un nuevo peso, procedemos a buscar cual es el peso máximo de todo el ejercicio
            // El peso máximo de todo el ejercicio, nos permite saber, cual es el valor máximo para utilizarlo posteriormente en indicadores.

                //El peso máximo siempre se va a guardar en Kilogramos
                $sqlMax="SELECT max(tipoPeso) as PesoMaximo FROM
                (SELECT 	case TipoPeso  when 1 then PesoPropuesto  when 2 then PesoPropuesto*0.453592 end as tipoPeso
                        FROM serie  where  id_SubrutinaEjercicio=$idEjercicio) as Peso;";

                $pesoMaximo=$NuevoPeso;

                if($result = mysqli_query($conexion, $sqlMax))
                    {
                        if($result!=null){
                            if ($result->num_rows>0){
                                while($row = mysqli_fetch_array($result))
                                {
                                    $pesoMaximo=$row["PesoMaximo"];

                                }
                            }
                        }
                }


                $fecha = new DateTime();
                $hoy = $fecha->getTimestamp();

                $sql2="INSERT INTO pesoavances (`Peso`, `TipoPeso`, `id_Serie`,`Fecha`,`PesoMaximo`) VALUES ($NuevoPeso, $TipoDePeso, $idSerie, $hoy, $pesoMaximo)";

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
                                $response["message"]='Series actualizadas correctamente';

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
                    WHERE id_SubrutinaEjercicio =$idEjercicio order by NumeroSerie;";

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


     function configurarSeriesMasivas($arregloEjercicios,$arregloSeries){
         // Esta función permite configurar todas las series de un conjunto de ejercicios


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

          mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            $ejerciciosDepurados = array(); // Creamos un arreglo para almacenar los ejercicios que fueron depurados
            $ejerciciosErrorAlDepurarse = array(); // Creamos un arreglo para almacenar los ejercicios cuyas series no pudieron ser depuradas
            $serieAlmacenadaCorrectamente = array(); // Creamos un arreglo con las nuevas series almacenadas
            $b=1; //Creamos una bandera para checar, cuando haya un error en las consultas

            mysqli_autocommit($conexion, FALSE); //Desactivamos la opción de autocomit, para verificar que se actualicen todos los registros

            mysqli_begin_transaction($conexion); // Iniciamos con la transacción

          foreach ($arregloEjercicios as $datosEjercicio) {
              // Recorreremos cada uno de los ejercicios del arreglo
              $idEjercicio=$datosEjercicio["IdEjercicio"]; // Obtenemos el Id del ejercicio

                //Lo primero que haremos será depurar las series, para que no exista inconsistencia en los datos
                $sql= "DELETE FROM `serie` WHERE id_SubrutinaEjercicio='$idEjercicio';";

                mysqli_query($conexion, $sql); //Ejecutamos la consulta

              // Una vez que se ha depurados las series anteriores, procederemos a guardar mediante un ciclo, las nuevas series.

              //**********************************************************************************************************************
                $NumeroSerie=0;
                foreach ($arregloSeries as $datosSerie) {
                    // Recorremos cada una de las series

                    $NumeroSerie=$NumeroSerie+1; // Incrementamos el número de serie
                    $Observaciones=''; // Las observaciones siempre van en vacio


                    switch ($datosSerie[TipoSerie]) {
                        case "Peso fijo":
                            $TipoSerieId=1;
                        break;
                        case "Ascendente":
                            $TipoSerieId=2;
                        break;
                        case "Descendente":
                            $TipoSerieId=3;
                        break;
                        case "Ascendente-descendente":
                            $TipoSerieId=4;
                        break;
                        case "Descendente-ascendente":
                            $TipoSerieId=5;
                        break;
                        default:
                        {
                            $TipoSerieId=1;
                        }

                    }



                    $sql="INSERT INTO `serie` (`NumeroSerie`, `Repeticiones`, `id_TipoSerie`, `PesoPropuesto`, `id_SubrutinaEjercicio`, `Observaciones`, `TipoPeso`)
                        VALUES ($NumeroSerie, $datosSerie[Repeticiones], $TipoSerieId , $datosSerie[Peso], $idEjercicio,'$Observaciones', $datosSerie[TipoPeso]);";

                        if($result = mysqli_query($conexion, $sql)){
                            //Ejecutamos la consulta para insertar en la tabla de series

                            $fecha = new DateTime();
                            $hoy = $fecha->getTimestamp(); //Obtenemos la fecha exacta del sistema, para el historico de pesos

                            $idSerie=mysqli_insert_id($conexion); // Obtenemos el id de la serie, para registrarlo en el histórico de pesos
                            $sql2="INSERT INTO pesoavances (`Peso`, `TipoPeso`, `id_Serie`,`Fecha`)
                                    VALUES ($datosSerie[Peso], $datosSerie[TipoPeso], $idSerie, $hoy)";

                            if($result = mysqli_query($conexion, $sql2)){
                                // Ejecutamos la consulta para insertar en la tabla del histórico de pesos

                            }
                            else{
                                $b=0;

                               // En caso de que no se pueda almacenar correctamente algun dato en la tabla de histórico, haremos un rollback
                                $response["success"]=5;
                                $response["message"]='El peso no pudo ser almacenado correctamente en el histórico';
                                break 2; // En caso de presentarse un error, salimos de ambos ciclos
                            }


                        }
                        else{
                            $b=0;
                            echo $sql;
                            // Si no se puede almacenar correctamente la serie, procederemos a revertir los cambios
                            $response["success"]=4;
                            $response["message"]='La serie no se pudo registrar correctamente';
                            break 2; // En caso de presentarse un error, salimos de ambos ciclos

                        }


                }
              //**********************************************************************************************************************


          }

            if ($b==0){
                // Si se encontró algún error hacemos un rollback
                mysqli_rollback($conexion);
            }
            else{
                // Si existe algún error, se procede a hacer el commit
                $response["success"]=0;
				$response["message"]='Series almacenadas correctamente';
                mysqli_commit($conexion);
            }
            desconectar($conexion); //desconectamos la base de datos

        }
         else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }

         return $response;

     }


}



//
//  $S = new Serie();
//  $Ss=$S->deleteSerie (49996,13111,3);
//  echo json_encode ($Ss);


?>
