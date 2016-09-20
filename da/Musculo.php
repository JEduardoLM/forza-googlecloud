<?php

	// JELM
	// 01/02/2016
	// Se define la clase Musculo, utilizada para acceder a la base de datos y realizar operaciones sobre la tabla Musculo

	require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos

class Musculo{

//***************************************************************************************************************************************
//********************************      FUNCIÓN PARA OBTENER MUSCULOS          **********************************************************
//***************************************************************************************************************************************

	function getMusculo($idMusculo) //Función que regresa el listado de Musculos (si el id es igual a cero), o un Musculo especifico de acuerdo al id
	{
		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

		if ($idMusculo!=0) //Si el id es igual a cero, obtenemos todos Musculos, en caso contrario, vamos por el Musculo especifico.
		{
			$sql="SELECT M_ID,musculo.Nombre as Nombre,Descripcion, Estatus, Imagen, TipoFuenteImagen as ID_TipoFuente, tipoFuente.Nombre as NombreFuente , TamañoFuenteImagen, ColorFuenteImagen FROM musculo join tipofuente on TipoFuenteImagen=tf_id where M_ID='$idMusculo'";
		}
		else
		{
			$sql="SELECT M_ID,musculo.Nombre as Nombre,Descripcion, Estatus, Imagen, TipoFuenteImagen as ID_TipoFuente, tipoFuente.Nombre as NombreFuente , TamañoFuenteImagen, ColorFuenteImagen FROM musculo join tipofuente on TipoFuenteImagen=tf_id";
		}

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){
				$response["Musculos"] = array();
				while($row = mysqli_fetch_array($result))
				{

					$item = array();
					$item["Id"]=$row["M_ID"];
					$item["Nombre"]=$row["Nombre"];
					$item["Descripcion"]=$row["Descripcion"];
					$item["estatus"]=$row["Estatus"];

					$item["CodigoImagen"]=$row["Imagen"];
					$item["ID_TipoFuente"]=$row["ID_TipoFuente"];
					$item["NombreFuente"]=$row["NombreFuente"];
					$item["TamañoFuenteImagen"]=$row["TamañoFuenteImagen"];
					$item["ColorFuenteImagen"]=$row["ColorFuenteImagen"];

					array_push($response["Musculos"], $item);
				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo con el Id indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo con el Id indicado';
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
//******************************** FUNCIÓN PARA OBTENER AGREGAR UN NUEVO MUSCULO      ***************************************************
//***************************************************************************************************************************************

	function addMusculo($nombre, $descripcion, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

			//INSERT INTO `enforma`.`musculo` (`Nombre`, `Descripcion`, `Estatus`, `Imagen`, `TipoFuenteImagen`, `TamañoFuenteImagen`, `ColorFuenteImagen`) VALUES ('ESPALDA', 'COMPRENDE LOS MUSCULOS PRINCIPALES DE LA ESPALDA', '1', 'E', '1', '20', '#FFFFFF');

			$sql="INSERT INTO musculo (`Nombre`, `Descripcion`, `Estatus`, `Imagen`, `TipoFuenteImagen`, `TamañoFuenteImagen`, `ColorFuenteImagen`)
							   VALUES ('$nombre', '$descripcion', 1, '$imagen', '$tipoFuenteImagen', '$tamañoFuenteImagen', '$colorFuenteImagen');";


			if($result = mysqli_query($conexion, $sql)){

				// Volvemos a consultar el listado de Musculos
				$response["Musculos"]= array();
				$arregloMusculos=$this->getMusculo(0);
				$response["Musculos"]=$arregloMusculos["Musculos"];

				$response["success"]=1;
				$response["message"]='Musculo almacenado correctamente';

				}
			else {
				//return 'El Musculo no pudo ser almacenado correctamente';
					$response["success"]=0;
					$response["message"]='El Musculo no pudo ser almacenado correctamente';

				}
		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array

	}

//***************************************************************************************************************************************
//********************************************* FUNCIÓN PARA ELIMINAR UN Musculo      ***************************************************
//***************************************************************************************************************************************

	function deleteMusculoByID($idMusculo){}//Pendiente implementación}

//***************************************************************************************************************************************
//******************************** FUNCIÓN PARA ACTUALIZAR UN            Musculo      ***************************************************
//***************************************************************************************************************************************

	function updateMusculoByID ($idMusculo,$nombre, $descripcion, $estatus, $imagen, $tipoFuenteImagen, $tamañoFuenteImagen, $colorFuenteImagen)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idMusculo!=0 and $nombre!=NULL and strlen($nombre)>0)
		{

			$sql="UPDATE musculo SET `Nombre`='$nombre', `Descripcion`='$descripcion', `Estatus`='$estatus', `Imagen`='$imagen', `TipoFuenteImagen`='$tipoFuenteImagen', `TamañoFuenteImagen`='$tamañoFuenteImagen', `ColorFuenteImagen`='$colorFuenteImagen' WHERE `M_ID`='$idMusculo';";
			if($result = mysqli_query($conexion, $sql)){

				// Volvemos a consultar el listado de Musculos
				$response["Musculos"]= array();
				$arregloMusculos=$this->getMusculo(0);
				$response["Musculos"]=$arregloMusculos["Musculos"];

			//return 'Musculo actualizado correctamente';
				$response["success"]=1;
				$response["message"]='Musculo actualizado correctamente';

			}
			else{
				$response["success"]=0;
				$response["message"]='El Musculo no pudo ser actualizado correctamente';

				}
		}
		else{
		//	return 'El Musculo no pudo ser actualizado correctamente';
				$response["success"]=0;
				$response["message"]='El Musculo no pudo ser actualizado correctamente, el id y/o nombre debe ser diferente de nulo';

			}

		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array
	}

//***************************************************************************************************************************************
//***********************************  FUNCIÓN PARA BUSCAR UN MUSCULO POR NOMBRE      ***************************************************
//***************************************************************************************************************************************

	function buscarMusculoPorNombreParecido($nombre){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="SELECT M_ID,musculo.Nombre as Nombre,Descripcion, Estatus, Imagen, TipoFuenteImagen as ID_TipoFuente, tipoFuente.Nombre as NombreFuente , TamañoFuenteImagen, ColorFuenteImagen FROM musculo join tipofuente on TipoFuenteImagen=tf_id where musculo.Nombre like '%$nombre%'";

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){

				$response["Musculos"] = array();
				while($row = mysqli_fetch_array($result))
				{
					$item = array();
					$item["Id"]=$row["M_ID"];
					$item["Nombre"]=$row["Nombre"];
					$item["Descripcion"]=$row["Descripcion"];
					$item["estatus"]=$row["Estatus"];

					$item["CodigoImagen"]=$row["Imagen"];
					$item["ID_TipoFuente"]=$row["ID_TipoFuente"];
					$item["NombreFuente"]=$row["NombreFuente"];
					$item["TamañoFuenteImagen"]=$row["TamañoFuenteImagen"];
					$item["ColorFuenteImagen"]=$row["ColorFuenteImagen"];

					array_push($response["Musculos"], $item);
				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo alguno con el nombre indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo alguno con el nombre indicado';
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

	function buscarMusculoPorNombreExacto($nombre){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="SELECT M_ID,musculo.Nombre as Nombre,Descripcion, Estatus, Imagen, TipoFuenteImagen as ID_TipoFuente, tipoFuente.Nombre as NombreFuente , TamañoFuenteImagen, ColorFuenteImagen FROM musculo join tipofuente on TipoFuenteImagen=tf_id where musculo.Nombre='$nombre'";

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){

				$response["Musculos"] = array();
				while($row = mysqli_fetch_array($result))
				{
					$item = array();
					$item["Id"]=$row["M_ID"];
					$item["Nombre"]=$row["Nombre"];
					$item["Descripcion"]=$row["Descripcion"];
					$item["estatus"]=$row["Estatus"];

					$item["CodigoImagen"]=$row["Imagen"];
					$item["ID_TipoFuente"]=$row["ID_TipoFuente"];
					$item["NombreFuente"]=$row["NombreFuente"];
					$item["TamañoFuenteImagen"]=$row["TamañoFuenteImagen"];
					$item["ColorFuenteImagen"]=$row["ColorFuenteImagen"];

					array_push($response["Musculos"], $item);
				}
				$response["success"]=1;
				$response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo alguno con el nombre indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró Musculo alguno con el nombre indicado';
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


// $A = new Musculo();
//echo json_encode($A->updateMusculoByID(8,'PECHO PHP','MUSCULO DE PECHO', 1, 'P', 1, 20, '#AADDSS'));
// echo json_encode($A->addMusculo('PECHO 22222', 'COMPRENDE LOS MUSCULOS PRINCIPALES DEL PECHO 2222', 'P', '1', '20', '#FFFFFF'));
// $Musculos=$A->getMusculo(0);
//$Musculos=$A->buscarMusculoPorNombreParecido('PECHO');
//$Musculos=$A->buscarMusculoPorNombreExacto('PECHO');
//echo json_encode ($Musculos);

?>
