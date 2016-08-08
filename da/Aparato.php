<?php

	// JELM
	// 26/01/2016
	// Se define la clase aparato, utilizada para acceder a la base de datos y realizar operaciones sobre la tabla aparato
    //

	require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos


class Aparato{

//***************************************************************************************************************************************
//******************************** FUNCIÓN PARA OBTENER APARATOS getAparato    **********************************************************
//***************************************************************************************************************************************

	function getAparato($idAparato) //Función que regresa el listado de aparatos (si el id es igual a cero), o un aparato especifico de acuerdo al id
	{
		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

		if ($idAparato!=0) //Si el id es igual a cero, obtenemos todos aparatos, en caso contrario, vamos por el aparato especifico.
		{
			$sql="select * from aparato where A_Id='$idAparato'";
		}
		else
		{
			$sql="select *  from aparato";
		}

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){
				$response["aparatos"] = array();
				while($row = mysqli_fetch_array($result))
				{

					$item = array();
					$item["IdAparato"]=$row["A_ID"];
					$item["NombreAparato"]=$row["Nombre"];
					$item["Descripcion"]=$row["Descripcion"];
					$item["Estatus"]=$row["estatus"];
					array_push($response["aparatos"], $item);
				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró aparato con el Id indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró aparato con el Id indicado';
			}
		}
		else
		{
			$response["success"]=0;
			$response["message"]='Se presento un error al ejecutar la consulta';
		}

		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array
	}


//***************************************************************************************************************************************
//******************************** FUNCIÓN PARA OBTENER AGREGAR UN NUEVO APARATO      ***************************************************
//***************************************************************************************************************************************

	function addAparato($nombre,$descripcion)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

			$sql="INSERT INTO aparato (`Nombre`, `Descripcion`,`estatus`) VALUES ('$nombre', '$descripcion',1);";
			if($result = mysqli_query($conexion, $sql)){

				// Volvemos a consultar el listado de aparatos
				$response["aparatos"]= array();
				$arregloAparatos=$this->getAparato(0);
				$response["aparatos"]=$arregloAparatos["aparatos"];


				$response["success"]=1;
				$response["message"]='Aparato almacenado correctamente';

				}
			else {
				//return 'El aparato no pudo ser almacenado correctamente';
					$response["success"]=0;
					$response["message"]='El aparato no pudo ser almacenado correctamente';

				}
		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array

	}

//***************************************************************************************************************************************
//********************************************* FUNCIÓN PARA ELIMINAR UN APARATO      ***************************************************
//***************************************************************************************************************************************

	function deleteAparatoByID($idAparato){}//Pendiente implementación}

//***************************************************************************************************************************************
//******************************** FUNCIÓN PARA ACTUALIZAR UN            APARATO      ***************************************************
//***************************************************************************************************************************************

	function updateAparatoByID ($idAparato,$nombre,$descripcion,$estatus)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idAparato!=0 and $nombre!=NULL and strlen($nombre)>0)
		{
			$sql="UPDATE aparato SET `Nombre`='$nombre', `Descripcion`='$descripcion', `estatus`='$estatus' WHERE `A_ID`='$idAparato';";
			if($result = mysqli_query($conexion, $sql)){

				// Volvemos a consultar el listado de aparatos
				$response["aparatos"]= array();
				$arregloAparatos=$this->getAparato(0);
				$response["aparatos"]=$arregloAparatos["aparatos"];

			//return 'Aparato actualizado correctamente';
				$response["success"]=1;
				$response["message"]='Aparato actualizado correctamente';

			}
			else{
				$response["success"]=0;
				$response["message"]='El aparato no pudo ser actualizado correctamente';

				}
		}
		else{
		//	return 'El aparato no pudo ser actualizado correctamente';
				$response["success"]=0;
				$response["message"]='El aparato no pudo ser actualizado correctamente';

			}

		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array
	}

//***************************************************************************************************************************************
//***********************************  FUNCIÓN PARA BUSCAR UN APARATO POR NOMBRE      ***************************************************
//***************************************************************************************************************************************

	function buscarAparatoPorNombre($nombre){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="select * from aparato where nombre='$nombre'";

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){

				$response["aparatos"] = array();
				while($row = mysqli_fetch_array($result))
				{
					$item = array();
					$item["IdAparato"]=$row["A_ID"];
					$item["NombreAparato"]=$row["Nombre"];
					$item["Descripcion"]=$row["Descripcion"];
					$item["Estatus"]=$row["estatus"];
					array_push($response["aparatos"], $item);
				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró aparato alguno con el nombre indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró aparato alguno con el nombre indicado';
			}
		}
		else
		{
			$response["success"]=0;
			$response["message"]='Se presento un error al ejecutar la consulta';
		}

		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array
	}
}


//$A = new Aparato();
 //echo json_encode($A->updateAparatoByID(14,'Barra yX','Barra yX descripción',1));
// echo json_encode($A->addAparato('TEST EDUARDO EDD','TEST_ 2'));
// $Aparatos=$A->getAparato(0);
//$Aparatos=$A->buscarAparatoPorNombre('BANCO DECLINADO');
//echo json_encode ($Aparatos);

?>
