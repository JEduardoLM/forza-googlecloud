<?php

	// JELM
	// 24/02/2016
	// Creación de archivo PHP, el cual permite gestionar los gimnasios

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

	require('../da/Gimnasio.php'); //Se requiere el archivo de acceso a la base de datos, de los gimnasios

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idGimnasioBl = $data["id_Gym"];
    $idUsuarioBl = $data["id_Usuario"];


	//$metodoBl="getSucursalesByGym";
	//$idGimnasioBl=1;
    //$idUsuarioBl=2;



	//***************************************************************************************************************************************
	//***************************************************************************************************************************************
	//**********               AQUI INICIA LA DEFINICIÓN DE FUNCIONES QUE A SU VEZ ACCEDERAN A LOS MÉTODOS DEL OBJETO GIMNASIO  ***********
	//***************************************************************************************************************************************
	//***************************************************************************************************************************************

	function getSucursalesByGym ($idGimnasio, $idUsuario)
	{

		if ($idGimnasio!=NULL or $idGimnasio!=0){

            $gimansio = new Gimnasio();  //Instanciamos un objeto de la clase TipoFuente (esta clase cuenta con los métodos para obtener el listado de TipoFuentes, agregar uno nuevo y eliminar uno existente)

            if (is_int($idGimnasio)){
                if ($idGimnasio>=0){
                $response = $gimansio->getSucursalesByGym($idGimnasio, $idUsuario);
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


	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBl) {
		case "getSucursalesByGym": // Mandar cero, para obtener todos los TipoFuentes, o el id del aparatado especifico.
			$response=getSucursalesByGym($idGimnasioBl,$idUsuarioBl);
		break;
		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}

	}

	echo json_encode ($response)


?>
