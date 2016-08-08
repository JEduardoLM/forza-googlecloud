<?php

	// JELM
	// 02/02/2016
	// Se define la clase TipoFuente, utilizada para acceder a la base de datos y realizar operaciones sobre la tabla TipoFuente

	require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos

class TipoFuente{

//***************************************************************************************************************************************
//******************************** FUNCIÓN PARA OBTENER LOS DIFERENTES TIPOS DE FUENTE **************************************************
//***************************************************************************************************************************************

	function getTipoFuente($idTipoFuente) //Función que regresa el listado de TipoFuentes (si el id es igual a cero), o un TipoFuente especifico de acuerdo al id
	{
		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

		if ($idTipoFuente!=0) //Si el id es igual a cero, obtenemos todos los tipos de fuente, en caso contrario, vamos por el tipo de fuente especifico.
		{
			$sql="select * from tipofuente where A_Id='$idTipoFuente'";
		}
		else
		{
			$sql="select *  from tipofuente";
		}

		if($result = mysqli_query($conexion, $sql))
		{
			if($result!=null){
				if ($result->num_rows>0){
					$response["TipoFuentes"] = array();
					while($row = mysqli_fetch_array($result))
					{

						$item = array();
						$item["Id"]=$row["tf_id"];
						$item["Nombre"]=$row["Nombre"];
						array_push($response["TipoFuentes"], $item);
					}
					$response["success"]=1;
					$response["message"]='Consulta exitosa';
				}
				else{
					$response["success"]=0;
					$response["message"]='No se encontró el tipo de fuente con el Id indicado';
				}

			}
			else
				{
					$response["success"]=0;
					$response["message"]='No se encontró el tipo de fuente con el Id indicado';
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
//*********************************  FUNCIÓN PARA BUSCAR UN TIPO DE FUENTE POR NOMBRE PARECIDO ******************************************
//***************************************************************************************************************************************


		function buscarTipoDeFuentePorNombreParecido($nombre){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="Select * from tipofuente where  Nombre like '%$nombre%'";
		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){

				$response["TipoFuentes"] = array();
				while($row = mysqli_fetch_array($result))
				{
						$item = array();
						$item["Id"]=$row["tf_id"];
						$item["Nombre"]=$row["Nombre"];
						array_push($response["TipoFuentes"], $item);

				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró TipoDeFuente alguno con el nombre indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró TipoDeFuente alguno con el nombre indicado';
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
//*********************************  FUNCIÓN PARA BUSCAR UN TIPO DE FUENTE POR NOMBRE IDENTICO ******************************************
//***************************************************************************************************************************************


	function buscarTipoDeFuentePorNombreExacto($nombre){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="Select * from tipofuente where Nombre='$nombre'";

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){

				$response["TipoFuentes"] = array();
				while($row = mysqli_fetch_array($result))
				{
						$item = array();
						$item["Id"]=$row["tf_id"];
						$item["Nombre"]=$row["Nombre"];
						array_push($response["TipoFuentes"], $item);

				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró TipoDeFuente alguno con el nombre indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró TipoDeFuente alguno con el nombre indicado';
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

	// $tf = new TipoFuente();
	// echo json_encode($A->updateAparatoByID(14,'Barra yX','Barra yX descripción',1));
	// echo json_encode($A->addAparato('TEST EDUARDO EDD','TEST_ 2'));
	// $TFS=$tf->getTipoFuente(0);
	// $TFS=$tf->buscarTipoDeFuentePorNombreExacto('MusculosFuente');
	// echo json_encode ($TFS);

?>
