<?php

	// JELM
	// 08/04/2016
	// Creación de archivo PHP, el cual permite ingresar a las funcionalidades de la aplicación FORZA Instructor

    header("Access-Control-Allow-Origin: *");

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos


    // Se indican los archivos PHP que se utilizarán
	require('../da/Rutina.php');
    require('../da/Subrutina.php');
    require('../da/Asesor.php');
    require('../da/Ejercicio.php');

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idGimnasioBl = $data["IdGym"];
    $idUsuarioBl = $data["IdUsuario"];
    $idSucursalBl = $data["IdSucursal"];

    $idRutinaBl = $data["IdRutina"];
	$idSocioBl = $data["IdSocio"];
    $fechaBl = $data["Fecha"];
    $numeroSemanasBl = $data["NumeroSemanas"];
	$objetivoBl = $data["Objetivo"];
	$idInstructorBl = $data["IdInstructor"];

    $nombreBl=  $data["Nombre"];
    $estatus=   $data["Estatus"];

    $idSubrutinaBl= $data["IdSubrutina"];
    $subrutinasBl= $data["Subrutinas"];

    $ordenBl=$data["Orden"];





      // $metodoBl="deleteSubrutina";
      // $idGimnasioBl=2;
      // $idUsuarioBl=5;
      // $idSucursalBl=2;
      // $nombreBl='TEST XXXXXXXXXXXXXXXXXXXXXXXXXXX';
      // $idRutinaBl =2;
      // $idSocioBl = 2;
      // $fechaBl = 1461202946527;
      // $numeroSemanasBl = 2;
	  // $objetivoBl = 'Ponerse mamer';
	  // $idInstructorBl = 2;
      // $estatus= 1;
      // $idSubrutinaBl=2;
      // $ordenBl=5;

    //    $subrutinasBl=array(array("Id"=>"1", "Orden"=>"11");("Id"=>"1", "Orden"=>"11"));



	//***************************************************************************************************************************************
	//***************************************************************************************************************************************
	//**********                          AQUI INICIA LA DEFINICIÓN DE FUNCIONES DE LA APLICACIÓN DEL INSTRUCTOR                  ***********
	//***************************************************************************************************************************************
	//***************************************************************************************************************************************


    function validarTextoNulo($Texto,$Valor,$numeroError){
		if ($Texto!==NULL){
			if (trim($Texto)!=''){
				$Rvalidacion["success"]=0;
			}
			else{
				$Rvalidacion["success"]=$numeroError+1;
				$Rvalidacion["message"]=$Valor.' debe ser diferente de cadena vacia';
			}
		}
		else{
			$Rvalidacion["success"]=$numeroError;
			$Rvalidacion["message"]=$Valor.' debe ser diferente de NULO';
		}
		return $Rvalidacion;
	}


    //***************************************************************************************************************************************
    //***************************************************************************************************************************************    //***************************************************************************************************************************************
    //***************************************************************************************************************************************


	function getAsesorByIdUsuarioIdGym ($idGimnasio, $idUsuario)
	{

		if ($idGimnasio!=NULL or $idGimnasio!=0){

            if (is_int($idGimnasio)){
                if ($idGimnasio>=0){

                    //Si el dato de gimnasio se encuentra correctamente, procedemos a validar el id del usuario
                    if ($idUsuario!=NULL or $idUsuario!=0){
                            if (is_int($idUsuario)){
                                    if ($idUsuario>=0){
                                        $asesor = new Asesor();
                                        $response = $asesor->getAsesorByIdUsuarioIdGym($idUsuario,$idGimnasio);
                                    }
                                    else{
                                        $response["success"]=10;
                                        $response["message"]='El id del usuario no puede ser un valor negativo';
                                    }
                                }
                                else {
                                    $response["success"]=9;
                                    $response["message"]='El id del usuario debe ser un valor numérico';
                                }
                            }
                    else {
                                    $response["success"]=8;
                                    $response["message"]='El id del Usuario debe ser diferente de NULO o cero';
                    }


                }
                else{
                    $response["success"]=7;
                    $response["message"]='El id del gimnasio no puede ser un valor negativo';
                }
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del gimnasio debe ser un valor numérico';
            }
        }
        else {
                $response["success"]=5;
                $response["message"]='El id del Gimnasio debe ser diferente de NULO o cero';
        }
		return $response;
	}


	//***************************************************************************************************************************************

	function getRutinasByIdSucursal ($idSucursal)
	{

		if ($idSucursal!=NULL or $idSucursal!=0){

            if (is_int($idSucursal)){
                if ($idSucursal>=0){

                    $rutina = new Rutina();
                    $response = $rutina->getRutinasGenericasBySucursal($idSucursal);


                }
                else{
                    $response["success"]=7;
                    $response["message"]='El id de la sucursal no puede ser un valor negativo';
                }
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la sucursal debe ser un valor numérico';
            }
        }
        else {
                $response["success"]=5;
                $response["message"]='El id de la sucursal debe ser diferente de NULO o cero';
        }
		return $response;
	}


    //***************************************************************************************************************************************

	function duplicarRutina($idRutina, $idSocio, $fecha, $numeroSemanas, $objetivo, $idInstructor)
	{

		if ($idRutina!=NULL or $idRutina!=0){
            if (is_int($idRutina)){
                if ($idRutina>=0){ //Verificamos que el id de la rutina sea un valor valido, diferente de nulo y mayor a cero, y un dato númerico

                    if ($idSocio!=NULL and $idSocio!=0){
                        if (is_int($idSocio)){
                            if ($idSocio>=0){ //Verificamos que el id del socio sea un valor valido, diferente de nulo y mayor a cero, y un dato númerico

                                if ($fecha!=NULL and $fecha!=0){  //Verificamos el valor de la fecha, sea un valor diferente de nulo y cero

                                    $rutina = new Rutina();
                                    $ultimaRutina=$rutina->getLastRutinaSocio($idSocio); //Obtenemos la última rutina que haya tenido asignada el socio

                                    if (!($ultimaRutina["success"]>1)){ //Si el resultado arroja un success mayor a uno, significa que se presentó un error en alguna de las consultas

                                        if ($ultimaRutina["success"]==1){ //Si el resultado es uno, significa que no encontró una rutina previa del socio, por lo que no es necesario validar la fecha
                                            $response = $rutina->duplicarRutina($idRutina, $idSocio, $fecha, $numeroSemanas, $objetivo, $idInstructor);
                                        }
                                        else{ //Si el resultado e suno, se debe verificar que la fecha de la última rutina asignada sea menor a la nueva fecha
                                            if ($ultimaRutina["Rutina"]["FechaInicio"]<$fecha){
                                                $response = $rutina->duplicarRutina($idRutina, $idSocio, $fecha, $numeroSemanas, $objetivo, $idInstructor);

                                            }
                                            else{
                                                $response["success"]=15;
                                                $response["message"]='La fecha debe ser mayor a la fecha de la última rutina, la cual es: '.$ultimaRutina["Rutina"]["FechaInicio"];
                                            }
                                        }

                                    }
                                    else{

                                        $response=$ultimaRutina; //Obtenemos el mensaje de error
                                    }

                                }
                                else{
                                    $response["success"]=14;
                                    $response["message"]='La fecha debe ser diferente de nulo';
                                }

                            }
                            else{
                                $response["success"]=13;
                                $response["message"]='El id del socio no puede ser un valor negativo';
                            }
                        }
                        else {
                            $response["success"]=12;
                            $response["message"]='El id del socio debe ser un valor numérico';
                        }
                    }
                    else {
                            $response["success"]=11;
                            $response["message"]='El id del socio debe ser diferente de NULO o cero';
                    }



                }
                else{
                    $response["success"]=13;
                    $response["message"]='El id de la rutina no puede ser un valor negativo';
                }
            }
            else {
                $response["success"]=12;
                $response["message"]='El id de la rutina debe ser un valor numérico';
            }
        }
        else {
                $response["success"]=11;
                $response["message"]='El id de la rutina debe ser diferente de NULO o cero';
        }
		return $response;
	}

    //***************************************************************************************************************************************

    function getRutinaById($idRutina){
        if ($idRutina!=NULL){
            if ($idRutina!=0){
                $rutina = new Rutina();
                $response = $rutina->getRutinaById($idRutina);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la rutina debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la rutina debe ser diferente de NULO o cero';
        }
		return $response;
    }


    //***************************************************************************************************************************************

    function getRutinaByIdSocio($idSocio){
        if ($idSocio!=NULL){
            if ($idSocio!=0){
                $rutina = new Rutina();
                $response = $rutina->getRutinaByIdSocio($idSocio);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del socio debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del socio debe ser diferente de NULO o cero';
        }
		return $response;
    }


    //***************************************************************************************************************************************

    function getTotalRutinasByIdSocio($idSocio){
        if ($idSocio!=NULL){
            if ($idSocio!=0){
                $rutina = new Rutina();
                $response = $rutina->getTotalRutinasByIdSocio($idSocio);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id del socio debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id del socio debe ser diferente de NULO o cero';
        }
		return $response;
    }

    //***************************************************************************************************************************************

    function saveRutina($R_ID, $nombre, $fechaInicio, $numeroSemanas, $estatus, $objetivo, $id_Socio, $id_Sucursal, $id_Instructor ){
        //Se crea método para la creación de una nueva rutina

        //Lo primero que haremos es validar el nombre

        $nombreValidado=validarTextoNulo($nombre, 'El nombre ', 5);
        if ($nombreValidado["success"]==0){

            $fechaValida=validarTextoNulo($fechaInicio, 'La fecha ', 7);
            if ($fechaValida["success"]==0){

                $numSemanasValidas=validarTextoNulo($numeroSemanas, 'La fecha ', 9);
                if ($numSemanasValidas["success"]==0){
                    // Verificamos que no se encuentre una rutina, con el mismo nombre
                    $rutina = new Rutina();

                    if ($id_Sucursal>0){
                        $rutinaRepetida = $rutina->buscarRutinaPorNombreYSucursal($id_Sucursal,$nombre);

                        if ($rutinaRepetida["success"]==1){ //Si el valor es igual a 1, significa que la rutina no esta dada de alta, y se puede proceder a clonar el valor
                            $response=$rutina->saveRutina($R_ID, $nombre, $fechaInicio, $numeroSemanas, $estatus, $objetivo, $id_Socio, $id_Sucursal , $id_Instructor );
                        }
                        elseif($rutinaRepetida["success"]==0){
                            if ($rutinaRepetida["Rutina"]["Id"]==$R_ID){
                                //Checamos que la rutina que encontró sea la misma que se está actualizando
                                $response=$rutina->saveRutina($R_ID, $nombre, $fechaInicio, $numeroSemanas, $estatus, $objetivo, $id_Socio, $id_Sucursal , $id_Instructor );
                            }
                            else{ //Si es otra diferente, entonces no la podremos guardar, ya que estaremos duplicando la información.
                                $response["success"]=11;
                                $response["message"]='Ya se encuentra una rutina registrada con el mismo nombre';
                            }
                        }
                        else{
                            $response=$rutinaRepetida;
                        }

                    } //Si la sucursal es igual o mayor a cero, se debe proceder a verificar el dato del id del socio
                    elseif ($id_Socio>0){
                            $response=$rutina->saveRutina($R_ID, $nombre , $fechaInicio, $numeroSemanas, $estatus, $objetivo, $id_Socio, NULL , $id_Instructor );
                    }
                    else{
                            $response["success"]=12;
                            $response["message"]='Por lo menos el id de la sucursal o el id del socio, debe ser diferente de nulo o cero';
                    }

                }
                else{
                    $response=$numSemanasValidas;
                }


            }
            else{
                $response=$fechaValida;
            }
        }
        else{
            $response=$nombreValidado;
        }
        return $response;

    }

    //***************************************************************************************************************************************

    function deleteRutina($idRutina){
        if ($idRutina!=NULL){
            if ($idRutina!=0){
                $rutina = new Rutina();
                $response = $rutina->deleteRutina($idRutina);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la rutina debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la rutina debe ser diferente de NULO o cero';
        }
		return $response;
    }

    //***************************************************************************************************************************************
    //***************************************************************************************************************************************
    //*************************************** METODOS PARA LA ADMINISTRACIÓN DE SUBRUTINAS **************************************************
    //***************************************************************************************************************************************
    //***************************************************************************************************************************************


    function getSubrutinaByID($idSubrutina){
        if ($idSubrutina!=NULL){
            if ($idSubrutina!=0){
                $Subrutina = new Subrutina();
                $response = $Subrutina->getsubrutinaByIdSubutina($idSubrutina);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la subrutina debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la subrutina debe ser diferente de NULO o cero';
        }
		return $response;
    }


    //***************************************************************************************************************************************
    //***************************************************************************************************************************************


    function getSubrutinaByRutina($idRutina){
        if ($idRutina!=NULL){
            if ($idRutina!=0){
                $Subrutina = new Subrutina();
                $response = $Subrutina->getsubrutinaByIdRutina($idRutina);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la rutina debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la rutina debe ser diferente de NULO o cero';
        }
		return $response;
    }


    //***************************************************************************************************************************************
    //***************************************************************************************************************************************

    function saveSubrutina($idSubRutina, $Orden, $idRutina, $Nombre){
        //Se crea método para la creación de una nueva rutina

        //Lo primero que haremos es validar el nombre

        $nombreValidado=validarTextoNulo($Nombre, 'El nombre ', 5);
        if ($nombreValidado["success"]==0){

            if ($Orden!=NULL and $Orden!=0){
                if ($idRutina!=NULL and $idRutina!=0){
                        $Subrutina = new Subrutina();
                        $response = $Subrutina->saveSubrutina($idSubRutina, $Orden, $idRutina, $Nombre);
                }
                else
                {
                    $response["success"]=7;
                    $response["message"]='El id de la rutina debe ser diferente de Nulo o cero';
                }

            }
            else
            {
            $response["success"]=6;
            $response["message"]='El orden de la subrutina debe ser diferente de Nulo o cero';
            }

        }
        else{
            $response=$nombreValidado;
        }
        return $response;

    }

    //***************************************************************************************************************************************

    function deleteSubrutina($idSubrutina, $idRutina, $orden){
        if ($idSubrutina!=NULL){
            if ($idSubrutina!=0){
                $Subrutina = new Subrutina();
                $response = $Subrutina->deleteSubrutina($idSubrutina, $idRutina, $orden);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la rutina debe ser diferente de cero o cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la rutina debe ser diferente de NULO o cero';
        }
		return $response;
    }

    function actualizarOrdenSubrutina($subrutinas){
            $Subrutina = new Subrutina();
            $response = $Subrutina->actualizarOrdenSubrutina($subrutinas);
            return $response;
    }

    //*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
    //*************************************************************************************************************************************************
	//*************************************************************************************************************************************************


    function getSucursalEjerciciosBySucursal($idSucursal){
        if ($idSucursal!=NULL){
            if ($idSucursal!=0){
                $ejercicio = new Ejercicio();
                $response = $ejercicio->getSucursalEjerciciosBySucursal($idSucursal);
            }
            else {
                $response["success"]=6;
                $response["message"]='El id de la rutina debe ser diferente de cero';
            }
        }
        else {
            $response["success"]=5;
            $response["message"]='El id de la sucursal debe ser diferente de Nulo';
        }
		return $response;
    }



	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBl) {
		case "getAsesorByIdUsuarioIdGym": // Este método lo utilizaremos para obtener el id del instructor
			$response=getAsesorByIdUsuarioIdGym($idGimnasioBl,$idUsuarioBl);
		break;

        //***********************************************SE INCLUYEN MÉTODOS PARA LA ADMINISTRACIÓN DE LAS RUTINAS      ******************************************

        case "getRutinasByIdSucursal": // Este método lo utilizaremos para obtener las rutinas de una sucursal
			$response=getRutinasByIdSucursal($idSucursalBl);
		break;

        case "duplicarRutina": // Este método lo utilizaremos para obtener el id del instructor
			$response=duplicarRutina($idRutinaBl, $idSocioBl, $fechaBl, $numeroSemanasBl, $objetivoBl, $idInstructorBl);
		break;

        case "getRutinaById": // Este método lo utilizaremos para obtener los datos de una rutina por su ID
			$response=getRutinaById($idRutinaBl);
		break;

        case "getRutinaByIdSocio": // Este método lo utilizaremos para obtener los datos de una rutina por su ID
			$response=getRutinaByIdSocio($idSocioBl);
		break;

        case "getTotalRutinasByIdSocio": // Este método lo utilizaremos para obtener los datos de una rutina por su ID
			$response=getTotalRutinasByIdSocio($idSocioBl);
		break;


        case "saveRutina": // Este método lo utilizaremos para guardar los datos de una rutina, ya sea nueva o actualizar una existente
			$response=saveRutina($idRutinaBl,$nombreBl,$fechaBl,$numeroSemanasBl,$estatus,$objetivoBl,$idSocioBl,$idSucursalBl,$idInstructorBl);
		break;

        case "deleteRutina": // Este método lo utilizaremos para eliminar una rutina
			$response=deleteRutina($idRutinaBl);
		break;




        //***********************************************SE INCLUYEN MÉTODOS PARA LA ADMINISTRACIÓN DE LAS SUBRUTINAS  ******************************************

        case "getSubrutinasByRutina": // Este método nos regresa las subrutinas de una rutina
			$response=getSubrutinaByRutina($idRutinaBl);
		break;

        case "getSubrutinaByIdSubrutina": // Este método lo utilizaremos para obtener el id del instructor
			$response=getSubrutinaByID($idSubrutinaBl);
		break;

        case "saveSubrutina": // Este método lo utilizaremos para guardar la información de una subrutina
			$response=saveSubrutina($idSubrutinaBl, $ordenBl, $idRutinaBl, $nombreBl);
		break;

        case "deleteSubrutina": // Este método lo utilizaremos para guardar la información de una subrutina
			$response=deleteSubrutina($idSubrutinaBl, $idRutinaBl, $ordenBl);
		break;

        case "actualizarOrdenSubrutina": // Este método lo utilizaremos para guardar la información de una subrutina
			$response=actualizarOrdenSubrutina($subrutinasBl);
		break;

        //***********************************************SE INCLUYEN MÉTODOS PARA LA ADMINISTRACIÓN DE LOS EJERCICIOS ******************************************
        case "getSucursalEjerciciosBySucursal": // Éste método recibe el id de la sucursal, y regresa todos los ejercicios configurados en dicha sucursal
			$response=getSucursalEjerciciosBySucursal($idSucursalBl);
		break;

		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}



	}

	echo json_encode ($response)


?>
