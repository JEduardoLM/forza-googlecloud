<?php

	// JELM
	// 26/01/2016
	// Creación de archivo PHP, el cual permite obtener el listado de Musculos, agregar un nuevo Musculo o modificar uno existente

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

	require('../da/Musculo.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idMusculoBl = $data["id"];
	$nombreBl = $data['nombre'];
	$descripcionBl = $data['descripcion'];
	$estatusBl = $data['estatus'];

	$imagenBl = $data['Imagen'];
	$tipoFuenteImagenBl = $data['ID_TipoFuente'];
	// $nombreFuenteBl = $data['NombreFuente'];
	$tamañoFuenteImagenBl = $data['TamañoFuenteImagen'];
	$colorFuenteImagenBl = $data['ColorFuenteImagen'];


	$metodoBl="saveMusculo";
	$idMusculoBl=9;
	$nombreBl='PANTORRILLA 2';
	$descripcionBl='MUSCULO EN LA PARTE INFERIOR DE LA PIERNA 2';
	$estatusBl=1;
	$imagenBl='O';
	$tipoFuenteImagenBl=1;
	$tamañoFuenteImagenBl=22;
	$colorFuenteImagenBl='RED';


	//***************************************************************************************************************************************
	//***************************************************************************************************************************************
	//**********               AQUI INICIA LA DEFINICIÓN DE FUNCIONES QUE A SU VEZ ACCEDERAN A LOS MÉTODOS DEL OBJETO $Musculo    ***********
	//***************************************************************************************************************************************
	//***************************************************************************************************************************************

	function getMusculoBL($idMusculo)
	{
		$Musculo = new Musculo();  //Instanciamos un objeto de la clase Musculo (esta clase cuenta con los métodos para obtener el listado de Musculos, agregar uno nuevo y eliminar uno existente)
		if ($idMusculo==NULL){
			$idMusculo=0;
		}

		if (is_int($idMusculo)){
			if ($idMusculo>=0){
			$Musculos = $Musculo->getMusculo($idMusculo);
			}
			else{
				$Musculos["success"]=0;
				$Musculos["message"]='El id del Musculo no puede ser un valor negativo';
			}
		}
		else {
			$Musculos["success"]=0;
			$Musculos["message"]='El id del Musculo debe ser un valor numerico';
		}
		return $Musculos;
	}

	//*******************************************************************************************************************************************
	function validarNombre($nombreAValidar){
		if ($nombreAValidar!==NULL){
			if (trim($nombreAValidar)!=''){
				$Rvalidacion["success"]=1;
			}
			else{
				$Rvalidacion["success"]=0;
				$Rvalidacion["message"]='El nombre del Musculo debe ser diferente de cadena vacia';
			}
		}
		else{
			$Rvalidacion["success"]=0;
			$Rvalidacion["message"]='El nombre del Musculo debe ser diferente de NULO';
		}
		return $Rvalidacion;
	}

	//********************************************************************************************************************************************
	function addMusculoBl($nombre, $descripcion, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen){
		$NombreValidado=validarNombre($nombre);
		if ($NombreValidado["success"]==1){
			$Musculo = new Musculo();  //Instanciamos un objeto de la clase Musculo (esta clase cuenta con los métodos para obtener el listado de Musculos, agregar uno nuevo y eliminar uno existente)
			$nombreRepetido= $Musculo->buscarMusculoPorNombreExacto(trim($nombre)); //Verificamos que el nombre no se encuentre repetido
			if ($nombreRepetido["success"]==1){
				$Musculos["success"]=0;
				$Musculos["message"]='El nombre del Musculo ya se encuentra registrado';
			}
			else{
				$Musculos=$Musculo->addMusculo(trim($nombre),trim($descripcion),$imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen);
			}

		}
		else{
			$Musculos=$NombreValidado;
		}
		return $Musculos;
	}

	//*******************************************************************************************************************************************

	function updateMusculoByIDBl($idMusculo,$nombre, $descripcion, $estatus, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen)
	{
		if ($idMusculo!==NULL){

			$Musculo = new Musculo();
			$RegistroMusculo = $Musculo->getMusculo($idMusculo);
			if (count ($RegistroMusculo)<3){
				$response["success"]=$RegistroMusculo["success"];
				$response["message"]="Se presentó un error al consultar el Musculo por id msj: ".$RegistroMusculo["message"];
			}
			else{
				if  (count($RegistroMusculo["Musculos"])==1){ // Si encuentra el registró, el sístema va a regresar valor de 1
					$NombreValidado=validarNombre($nombre); // Se valida que el nombre no sea NULO ni cadena vacia
					if ($NombreValidado["success"]==1){
						$nombreRepetido= $Musculo->buscarMusculoPorNombreExacto(trim($nombre)); //Revisamos que el nombre no se encuentre repetido

						if ($nombreRepetido["success"]==1){
							$idNombreRepetido= $nombreRepetido["Musculos"][0]["Id"];
							if ($idNombreRepetido==$idMusculo){
								$response=$Musculo->updateMusculoByID($idMusculo,trim($nombre),trim($descripcion),$estatus);
							}
							else{
								$response["success"]=0;
								$response["message"]='El nombre del Musculo ya se encuentra registrado';
							}
						}
						else{
							$response=$Musculo->updateMusculoByID($idMusculo,trim($nombre),trim($descripcion), $estatus, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen);
						}
					}
					else{
						$response = $NombreValidado;
					}
				}
				else{
					$response["success"]=0;
					$response["message"]='El id del Musculo no se encuentra en la base de datos';
				}
			}
		}
		else{
			$response["success"]=0;
			$response["message"]='El id del Musculo debe ser diferente de NULO';
		}

		return $response;
	}

	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************
	//** AQUI INICIA EL SWICH UTILIZADO PARA MANDAR A LLAMAR A LAS FUNCIONES DEFINIDAS PREVIAMENTE DE ACUERDO A LO INDICADO EN LA VARIABLE $METODO  ***
	//*************************************************************************************************************************************************
	//*************************************************************************************************************************************************

	switch ($metodoBl) {
		case "getMusculo": // Mandar cero, para obtener todos los Musculos, o el id del aparatado especifico.
			$response=getMusculoBL($idMusculoBl);
		break;
		case "saveMusculo":
			if ($idMusculoBl==NULL or $idMusculoBl==0){
				$response=addMusculoBl($nombreBl, $descripcionBl, $imagenBl, $tipoFuenteImagenBl, $tamañoFuenteImagenBl, $colorFuenteImagenBl);// Método para agregar un nuevo Musculo, aquí el ID no es necesario
			}
			else{
				$response=updateMusculoByIDBl($idMusculoBl,$nombreBl, $descripcionBl, $estatusBl, $imagenBl, $tipoFuenteImagenBl, $tamañoFuenteImagenBl, $colorFuenteImagenBl);// Método para agregar un nuevo Musculo, aquí el ID no es necesario
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
