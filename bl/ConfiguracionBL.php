<?php

	// JELM
	// 22/09/2016
	// Creación de archivo PHP, utilizado para configurar y depurar información de FORZA
   header("Access-Control-Allow-Origin: *");


	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

	require('../da/config.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBL = $data["Metodo"];
    $claveBL = $data["ClaveConfig"];



	//********************************************************************************************************************************************
	function depurarRutinasSocio($clave){

			$config = new Configuracion();
			$response= $config->depurarRutinasSocio($clave);
            return $response;
	}

	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBL) {
		case "depurarRutinasSocio":
			$response=depurarRutinasSocio($claveBL);
		break;
		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}

	}

	echo json_encode ($response)


?>
