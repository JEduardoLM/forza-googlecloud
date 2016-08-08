<?php

	// JELM
	// 26/01/2016
	// Creación de archivo PHP, el cual permite obtener el listado de aparatos, agregar un nuevo aparato o modificar uno existente

    $data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

//    $json = '{"metodo":"getAparato","id":1}';
//    $data=(json_decode($json, true));


	require('../da/Aparato.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idAparatoBl = $data["id"];
	$nombreBl = $data['nombre'];
	$descripcionBl = $data['descripcion'];
	$estatusBl = $data['estatus'];



	/*
	$metodoBl="saveAparato";
	$idAparatoBl=14;
	$nombreBl='BARRA Z';
	$descripcionBl='APARATO DE PRUEBA - NO EXISTE 14';
	$estatusBl=1;
	*/

	//***************************************************************************************************************************************
	//***************************************************************************************************************************************
	//**********               AQUI INICIA LA DEFINICIÓN DE FUNCIONES QUE A SU VEZ ACCEDERAN A LOS MÉTODOS DEL OBJETO $aparato    ***********
	//***************************************************************************************************************************************
	//***************************************************************************************************************************************

	function getAparatoBL($idAparato)
	{
		$aparato = new Aparato();  //Instanciamos un objeto de la clase Aparato (esta clase cuenta con los métodos para obtener el listado de aparatos, agregar uno nuevo y eliminar uno existente)
		if ($idAparato==NULL){
			$idAparato=0;
		}
		if (is_int($idAparato)){
			$aparatos = $aparato->getAparato($idAparato);
		}
		else {
			$aparatos["success"]=0;
			$aparatos["message"]='El id del aparato debe ser un valor numerico';
		}
		return $aparatos;
	}

	//*******************************************************************************************************************************************
	function validarNombre($nombreAValidar){
		if ($nombreAValidar!==NULL){
			if (trim($nombreAValidar)!=''){
				$Rvalidacion["success"]=1;
			}
			else{
				$Rvalidacion["success"]=0;
				$Rvalidacion["message"]='El nombre del aparato debe ser diferente de cadena vacia';
			}
		}
		else{
			$Rvalidacion["success"]=0;
			$Rvalidacion["message"]='El nombre del aparato debe ser diferente de NULO';
		}
		return $Rvalidacion;
	}

	//********************************************************************************************************************************************
	function addAparatoBl($nombre,$descripcion){
		$NombreValidado=validarNombre($nombre);
		if ($NombreValidado["success"]==1){
			$aparato = new Aparato();  //Instanciamos un objeto de la clase Aparato (esta clase cuenta con los métodos para obtener el listado de aparatos, agregar uno nuevo y eliminar uno existente)
			$nombreRepetido= $aparato->buscarAparatoPorNombre(trim($nombre));
			if ($nombreRepetido["success"]==1){
				$aparatos["success"]=0;
				$aparatos["message"]='El nombre del aparato ya se encuentra registrado';
			}
			else{
				$aparatos=$aparato->addAparato(trim($nombre),trim($descripcion));
			}

		}
		else{
			$aparatos=$NombreValidado;
		}
		return $aparatos;
	}

	//*******************************************************************************************************************************************

	function updateAparatoByIDBl($idAparato,$nombre,$descripcion,$estatus)
	{
		if ($idAparato!==NULL){

			$aparato = new Aparato();
			$RegistroAparato = $aparato->getAparato($idAparato);
			if (count ($RegistroAparato)<3){
				$response["success"]=$RegistroAparato["success"];
				$response["message"]="Se presentó un error al consultar el aparato por id msj: ".$RegistroAparato["message"];
			}
			else{
				if  (count($RegistroAparato["aparatos"])==1){ // Si encuentra el registró, el sístema va a regresar valor de 1
					$NombreValidado=validarNombre($nombre);
					if ($NombreValidado["success"]==1){
						$nombreRepetido= $aparato->buscarAparatoPorNombre(trim($nombre));

						if ($nombreRepetido["success"]==1){
							$idNombreRepetido= $nombreRepetido["aparatos"][0]["Id"];
							if ($idNombreRepetido==$idAparato){
								$response=$aparato->updateAparatoByID($idAparato,trim($nombre),trim($descripcion),$estatus);
							}
							else{
								$response["success"]=0;
								$response["message"]='El nombre del aparato ya se encuentra registrado';
							}
						}
						else{
							$response=$aparato->updateAparatoByID($idAparato,trim($nombre),trim($descripcion),$estatus);
						}
					}
					else{
						$response = $NombreValidado;
					}
				}
				else{
					$response["success"]=0;
					$response["message"]='El id del aparato no se encuentra en la base de datos';
				}
			}
		}
		else{
			$response["success"]=0;
			$response["message"]='El id del aparato debe ser diferente de NULO';
		}

		return $response;
	}

	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBl) {
		case "getAparato": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=getAparatoBL($idAparatoBl);
		break;
		case "saveAparato":
			if ($idAparatoBl==NULL or $idAparatoBl==0){
				$response=addAparatoBl($nombreBl,$descripcionBl);// Método para agregar un nuevo aparato, aquí el ID no es necesario
			}
			else{
				$response=updateAparatoByIDBl($idAparatoBl,$nombreBl,$descripcionBl,$estatusBl);// Método para agregar un nuevo aparato, aquí el ID no es necesario
			}
		break;
		default:
		{
			$response["success"]=0;
			$response["message"]='El método indicado no se encuentra registrado';
		}

	}

	echo json_encode ($response)



?>
