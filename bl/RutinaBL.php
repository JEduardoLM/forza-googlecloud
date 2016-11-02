<?php

	// JELM
	// 08/04/2016
	// Creación de archivo PHP, el cual permite administrar la información de una rutina (Subrutinas, ejercicios, series, repeticiones, etc...)

    header("Access-Control-Allow-Origin: *");

	  $data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

    //  $json = '{"metodo":"actualizarOrdenCircuito","Ejercicios":[{"TipoEjercicio":1, "IdEjercicioSubrutina":2, "Circuito":1, "Orden":1},{"TipoEjercicio":1, "IdEjercicioSubrutina":3, "Circuito":1, "Orden":2},"IdSubrutina":2}';


    // $data=(json_decode($json, true));


    //Extraemos la información del método POST, y lo asignamos a diferentes variables

    $metodoBl = $data["metodo"];


    $subrutinasBl= (array) $data["Subrutinas"];
    $ejerciciosBl= (array) $data["Ejercicios"];
    $seriesBl= (array) $data["Series"];

    $idEjercicioBl= $data["IdEjercicio"];
    $tipoEjercicioBl= $data["TipoEjercicio"];
    $idSubrutinaBl= $data["IdSubrutina"];


    $idSerieBl= $data["IdSerie"];
    $NumeroSerieBl= $data["NumeroSerie"];
    $RepeticionesBl= $data["Repeticiones"];
    $TipoSerieBl= $data["TipoSerie"];
    $PesoBl= $data["Peso"];
    $ObservacionesBl= $data["Observaciones"];
    $TipoPesoBl= $data["TipoPeso"];


    $tiempoTotalBl= $data["TiempoTotal"];
    $velocidadPromedioBl= $data["VelocidadPromedio"];
    $tipoVelocidadBl= $data["TipoVelocidad"];
    $distanciaTotalBl= $data["DistanciaTotal"];
    $tipoDistanciaBl= $data["TipoDistancia"];
    $ritmoCardiacoBl= $data["RitmoCardiaco"];
    $nivelBl= $data["Nivel"];
    $numeroSerieBl= $data["NumeroSerie"];

    $ordenBl= $data["Orden"];
    $circuitoBl= $data["Circuito"];

    $notaSocioBl= $data["NotaSocio"];

    require('../da/Subrutina.php');
    require('../da/Ejercicio.php');
    require('../da/Serie.php');


    function actualizarOrdenSubrutina($subrutinas){

        $Subrutina = new Subrutina();
        $response = $Subrutina->actualizarOrdenSubrutina($subrutinas);
		return ($response); //devolvemos el array
    }

    function saveNewEjerciciosSubrutina($ejercicios,$idSubrutina){

        $ejercicio = new Ejercicio();
        $response = $ejercicio->insertEjerciciosSubrutina($ejercicios,$idSubrutina);
		return ($response); //devolvemos el array
    }

    function actualizarOrdenCircuito($ejercicios,$idSubrutina){

        $ejercicio = new Ejercicio();
        $response = $ejercicio->actualizarOrdenCircuito($ejercicios,$idSubrutina);
		return ($response); //devolvemos el array
    }


    function deleteEjercicioSubrutina($idEjercicio, $idTipo, $idSubrutina, $orden, $circuito){
        if ($idEjercicio!=NULL){
            if ($idEjercicio!=0){
                $ejercicio = new Ejercicio();
                $response = $ejercicio->deleteEjercicio($idEjercicio,$idTipo,$idSubrutina, $orden, $circuito);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del ejercicio debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del ejercicio debe ser diferente de NULO';
        }
		return $response;
    }


    function getEjerciciosBySubrutina($idSubrutina){
        if ($idSubrutina!=NULL){
            if ($idSubrutina!=0){
                $ejercicio = new Ejercicio();
                $response = $ejercicio->getEjerciciosBySubrutina($idSubrutina);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la subrutina debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la subrutina debe ser diferente de NULO';
        }
		return $response;
    }


    function getEjercicioByIdEjercicio($idEjercicio,$tipoEjercicio){
                if ($idEjercicio!=NULL){
                if ($idEjercicio!=0){
                    $ejercicio = new Ejercicio();
                    $response = $ejercicio->getEjercicioById($idEjercicio,$tipoEjercicio);
                }
                else {
                    $response["success"]=6;
                    $response["message"]='El id del ejercicio debe ser diferente de cero';
                }
            }
            else {
                $response["success"]=5;
                $response["message"]='El id del ejercicio debe ser diferente de NULO';
            }
            return $response;
    }



    function saveObservacionesDeEjercicio($idEjercicio,$idTipo, $Observaciones){
        if ($idEjercicio!=NULL){
            if ($idEjercicio!=0){
                if ($Observaciones==NULL){$Observaciones='';}
                $ejercicio = new Ejercicio();
                $response = $ejercicio->actualizarObservaciones($idEjercicio,$idTipo, $Observaciones);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del ejercicio debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del ejercicio debe ser diferente de NULO';
        }
		return $response;
    }




//******************************************************************************************************************

    function actualizarNotaSocio($idEjercicio,$idTipo, $notaSocio){
        if ($idEjercicio!=NULL){
            if ($idEjercicio!=0){
                if ($notaSocio==NULL){$notaSocio='';}
                $ejercicio = new Ejercicio();
                $response = $ejercicio->actualizarNotaSocio($idEjercicio,$idTipo, $notaSocio);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del ejercicio debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del ejercicio debe ser diferente de NULO';
        }
		return $response;
    }

//******************************************************************************************************************

    function actualizarDetalleEjercicioCardio($idEjercicio,$tiempoTotal, $velocidadPromedio, $tipoVelocidad, $distanciaTotal, $tipoDistancia, $ritmoCardiaco, $nivel, $observaciones){
        if ($idEjercicio!=NULL){
            if ($idEjercicio!=0){
                if ($Observaciones==NULL){$Observaciones='';}
                $ejercicio = new Ejercicio();
                if ($tiempoTotal==NULL){$tiempoTotal=0;}
                if ($velocidadPromedio==NULL){$velocidadPromedio=0;}

                if ($tipoVelocidad=='MPH')
                    {
                        $tipoVelocidad=2;
                    }
                    else{
                         $tipoVelocidad=1;
                    }


                if ($distanciaTotal==NULL){$distanciaTotal=0;}

                if ($tipoDistancia=='mi')
                    {
                        $tipoDistancia=2;
                    }
                    else{
                        $tipoDistancia=1;
                    }

                if ($ritmoCardiaco==NULL){$ritmoCardiaco=0;}
                if ($nivel==NULL){$nivel=0;}
                $response = $ejercicio->actualizarInformaciónCardio($idEjercicio,$tiempoTotal, $velocidadPromedio, $tipoVelocidad, $distanciaTotal, $tipoDistancia, $ritmoCardiaco, $nivel,$observaciones);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del ejercicio debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del ejercicio debe ser diferente de NULO';
        }
		return $response;
    }



// *****************************************************************************************************************************************

    function saveSerie($idSerie,$NumeroSerie, $Repeticiones, $TipoSerie, $Peso, $idSubrutinaEjercicio, $Observaciones, $TipoPeso){

        if ($NumeroSerie==NULL or $NumeroSerie==0 or $NumeroSerie==''){
            $response["success"]=6;
            $response["message"]='El número de serie, debe ser un número entero mayor a cero';
        }
        else{
            if ($idSubrutinaEjercicio==NULL){
                $response["success"]=7;
                $response["message"]='El id de la subrutina debe ser un valor numerico';
            }
            else{

                if ($Repeticiones===NULL ){$Repeticiones=0;}
                if ($Peso===NULL ){ $Peso=0;}
                if ($Observaciones==NULL){$Observaciones='';}

                switch ($TipoSerie) {
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
                if ($TipoPeso=='lb' or $TipoPeso=='2' or $TipoPeso==2)
                    {
                        $TipoPesoId=2;
                    }
                else{
                    $TipoPesoId=1;
                    }
                if  ($idSerie==NULL or $idSerie==0 or $idSerie==''){
                    $serie = new Serie();
                    $response = $serie->saveNewSerie($NumeroSerie, $Repeticiones, $TipoSerieId, $Peso, $idSubrutinaEjercicio, $Observaciones, $TipoPesoId);
                }
                else
                {
                    $serie = new Serie();
                    $response = $serie->updateSerie($idSerie,$NumeroSerie, $Repeticiones, $TipoSerieId, $Peso, $idSubrutinaEjercicio, $Observaciones, $TipoPesoId);
                }


            }

        }


		return $response;
    }


    function deleteSerie($idSerie, $idEjercicio, $numeroSerie){
        if ($idSerie!=NULL){
            if ($idSerie!=0){
                $serie = new Serie();
                $response = $serie->deleteSerie($idSerie,$idEjercicio, $numeroSerie);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la serie debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la serie debe ser diferente de NULO';
        }
		return $response;
    }

// *****************************************************************************************************************************************


    function configurarSeriesMasivas($ejercicios,$series){

        // Este método permite configurar las series de manera masiva
        // Se recibe un arreglo de ejercicios, y un arreglo de series, para configurar todos los ejercicios en las series indicadas.
        $response["getEjercicios"]='';
        $serie = new Serie();
        $response = $serie->configurarSeriesMasivas($ejercicios,$series);

         if ($response["success"]==0){
            $listadoEjercicios='0';
            foreach ($ejercicios as $datosEjercicio) {
                  // Recorreremos cada uno de los ejercicios del arreglo
                  $listadoEjercicios=$listadoEjercicios.','.$datosEjercicio["IdEjercicio"];
            }
                $ejercicio = new Ejercicio();
                $response["getEjercicios"] = $ejercicio->getEjerciciosByArregloEjercicios($listadoEjercicios);


         }

		return ($response); //devolvemos el array

    }




//******************************************************************************************************************************************************
//******************************************************************************************************************************************************
//******************************************************************************************************************************************************
//******************************************************************************************************************************************************


switch ($metodoBl) {
		case "actualizarOrdenSubrutina": //
			$response=actualizarOrdenSubrutina($subrutinasBl);
		break;
        case "saveNewEjerciciosSubrutina": //
			$response=saveNewEjerciciosSubrutina($ejerciciosBl,$idSubrutinaBl);
		break;
        case "actualizarOrdenCircuito": //
			$response=actualizarOrdenCircuito($ejerciciosBl,$idSubrutinaBl);
		break;
        case "deleteEjercicioSubrutina": //
			$response=deleteEjercicioSubrutina($idEjercicioBl, $tipoEjercicioBl,$idSubrutinaBl, $ordenBl, $circuitoBl);
		break;
        case "getEjerciciosBySubrutina": //
			$response=getEjerciciosBySubrutina($idSubrutinaBl);
		break;

        case "getEjercicioByIdEjercicio": //
			$response=getEjercicioByIdEjercicio($idEjercicioBl,$tipoEjercicioBl);
		break;

        case "saveObservacionesDeEjercicio": //
			$response=saveObservacionesDeEjercicio($idEjercicioBl,$tipoEjercicioBl, $ObservacionesBl);
		break;

        case "actualizarDetalleEjercicioCardio":
            $response=actualizarDetalleEjercicioCardio($idEjercicioBl,$tiempoTotalBl, $velocidadPromedioBl, $tipoVelocidadBl, $distanciaTotalBl, $tipoDistanciaBl, $ritmoCardiacoBl, $nivelBl, $ObservacionesBl);
        break;

        case "actualizarNotaSocio": //
			$response=actualizarNotaSocio($idEjercicioBl,$tipoEjercicioBl, $notaSocioBl);
		break;

//*************************************AQUÍ SE INCLUYEN LOS MÉTODOS PARA AGREGAR Y ELIMINAR SERIES  **************************************************************************

        case "saveSerie": // Este método lo utilizaremos para obtener el id del instructor
			$response=saveSerie($idSerieBl,$NumeroSerieBl, $RepeticionesBl, $TipoSerieBl, $PesoBl, $idEjercicioBl, $ObservacionesBl, $TipoPesoBl);
		break;
        case "deleteSerie": // Este método lo utilizaremos para obtener el id del instructor
			$response=deleteSerie($idSerieBl,$idEjercicioBl,$numeroSerieBl);
		break;

        case "configurarSeriesMasivas": // Este método lo utilizaremos para obtener el id del instructor
			$response=configurarSeriesMasivas($ejerciciosBl,$seriesBl);
		break;



		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
}



	echo json_encode ($response)


?>
