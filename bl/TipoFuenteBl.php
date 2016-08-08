<?php

	// JELM
	// 26/01/2016
	// Creación de archivo PHP, el cual permite obtener el listado de TipoFuentes, agregar un nuevo TipoFuente o modificar uno existente

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

	require('../da/TipoFuente.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idTipoFuenteBl = $data["id"];
	$nombreBl = $data['nombre'];


	/* $metodoBl="getTipoFuente";
	$idTipoFuenteBl=0;
	$nombreBl='PANTORRILLA 2';*/



	//***************************************************************************************************************************************
	//***************************************************************************************************************************************
	//**********               AQUI INICIA LA DEFINICIÓN DE FUNCIONES QUE A SU VEZ ACCEDERAN A LOS MÉTODOS DEL OBJETO $TipoFuente    ***********
	//***************************************************************************************************************************************
	//***************************************************************************************************************************************

	function getTipoFuenteBL($idTipoFuente)
	{
		$TipoFuente = new TipoFuente();  //Instanciamos un objeto de la clase TipoFuente (esta clase cuenta con los métodos para obtener el listado de TipoFuentes, agregar uno nuevo y eliminar uno existente)
		if ($idTipoFuente==NULL){
			$idTipoFuente=0;
		}

		if (is_int($idTipoFuente)){
			if ($idTipoFuente>=0){
			$TipoFuentes = $TipoFuente->getTipoFuente($idTipoFuente);
			}
			else{
				$TipoFuentes["success"]=0;
				$TipoFuentes["message"]='El id del TipoFuente no puede ser un valor negativo';
			}
		}
		else {
			$TipoFuentes["success"]=0;
			$TipoFuentes["message"]='El id del TipoFuente debe ser un valor numerico';
		}
		return $TipoFuentes;
	}

	//*******************************************************************************************************************************************
	function validarNombre($nombreAValidar){
		if ($nombreAValidar!==NULL){
			if (trim($nombreAValidar)!=''){
				$Rvalidacion["success"]=1;
			}
			else{
				$Rvalidacion["success"]=0;
				$Rvalidacion["message"]='El nombre del TipoFuente debe ser diferente de cadena vacia';
			}
		}
		else{
			$Rvalidacion["success"]=0;
			$Rvalidacion["message"]='El nombre del TipoFuente debe ser diferente de NULO';
		}
		return $Rvalidacion;
	}

	//********************************************************************************************************************************************
	function addTipoFuenteBl($nombre, $descripcion, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen){
		$NombreValidado=validarNombre($nombre);
		if ($NombreValidado["success"]==1){
			$TipoFuente = new TipoFuente();  //Instanciamos un objeto de la clase TipoFuente (esta clase cuenta con los métodos para obtener el listado de TipoFuentes, agregar uno nuevo y eliminar uno existente)
			$nombreRepetido= $TipoFuente->buscarTipoFuentePorNombreExacto(trim($nombre)); //Verificamos que el nombre no se encuentre repetido
			if ($nombreRepetido["success"]==1){
				$TipoFuentes["success"]=0;
				$TipoFuentes["message"]='El nombre del TipoFuente ya se encuentra registrado';
			}
			else{
				$TipoFuentes=$TipoFuente->addTipoFuente(trim($nombre),trim($descripcion),$imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen);
			}

		}
		else{
			$TipoFuentes=$NombreValidado;
		}
		return $TipoFuentes;
	}

	//*******************************************************************************************************************************************

	function updateTipoFuenteByIDBl($idTipoFuente,$nombre, $descripcion, $estatus, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen)
	{
		if ($idTipoFuente!==NULL){

			$TipoFuente = new TipoFuente();
			$RegistroTipoFuente = $TipoFuente->getTipoFuente($idTipoFuente);
			if (count ($RegistroTipoFuente)<3){
				$response["success"]=$RegistroTipoFuente["success"];
				$response["message"]="Se presentó un error al consultar el TipoFuente por id msj: ".$RegistroTipoFuente["message"];
			}
			else{
				if  (count($RegistroTipoFuente["TipoFuentes"])==1){ // Si encuentra el registró, el sístema va a regresar valor de 1
					$NombreValidado=validarNombre($nombre); // Se valida que el nombre no sea NULO ni cadena vacia
					if ($NombreValidado["success"]==1){
						$nombreRepetido= $TipoFuente->buscarTipoFuentePorNombreExacto(trim($nombre)); //Revisamos que el nombre no se encuentre repetido

						if ($nombreRepetido["success"]==1){
							$idNombreRepetido= $nombreRepetido["TipoFuentes"][0]["Id"];
							if ($idNombreRepetido==$idTipoFuente){
								$response=$TipoFuente->updateTipoFuenteByID($idTipoFuente,trim($nombre),trim($descripcion),$estatus);
							}
							else{
								$response["success"]=0;
								$response["message"]='El nombre del TipoFuente ya se encuentra registrado';
							}
						}
						else{
							$response=$TipoFuente->updateTipoFuenteByID($idTipoFuente,trim($nombre),trim($descripcion), $estatus, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen);
						}
					}
					else{
						$response = $NombreValidado;
					}
				}
				else{
					$response["success"]=0;
					$response["message"]='El id del TipoFuente no se encuentra en la base de datos';
				}
			}
		}
		else{
			$response["success"]=0;
			$response["message"]='El id del TipoFuente debe ser diferente de NULO';
		}

		return $response;
	}

	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBl) {
		case "getTipoFuente": // Mandar cero, para obtener todos los TipoFuentes, o el id del aparatado especifico.
			$response=getTipoFuenteBL($idTipoFuenteBl);
		break;
		default:
		{
			$response["success"]=0;
			$response["message"]='El método indicado no se encuentra registrado';
		}

	}

	echo json_encode ($response)





?>
