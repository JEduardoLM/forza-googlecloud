<?php

	// JELM
	// 02/12/2016
	// Se define la clase sucursal
    //

require_once('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos


class Sucursal{

     //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function getEjerciciosByIdSucursal($idSucursal){


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            //Procedemos a armar la consulta, para obtener la rutina de acuerdo a su id
            $sql= "SELECT SEC_ID as Id_SucursalEjercicio, Id_Sucursal, Id_EjercicioCardio as IdEjercicio, ec.Explicacion as NombreEjercicio,
                    Alias, NumAparato,ec.ImagenUrl as ImagenURLGenerica, sec.ImagenUrl, ec.VideoUrl as VideoUrlGenerico,  sec.VideoUrl, 1 as TipoEjercicio
                    FROM sucursalejerciciocardio sec join ejerciciocardio ec on sec.Id_EjercicioCardio=ec.EC_ID where Id_Sucursal=$idSucursal
                    UNION
                SELECT SEP_ID as Id_SucursalEjercicio, Id_Sucursal, Id_EjercicioPesa as IdEjercicio, ep.Explicacion as NombreEjercicio,
                    Alias, NumAparato, ep.ImagenUrl as ImagenURLGenerica, sep.ImagenUrl, ep.VideoUrl as VideoUrlGenerico,  sep.VideoUrl, 2 as TipoEjercicio
                    FROM sucursalejerciciopesa sep join ejerciciopesa ep on sep.Id_EjercicioPesa=ep.EP_ID where Id_Sucursal=$idSucursal";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                    if($result!=null){ //Verificamos que no haya regresado Nulo la consulta
                        if ($result->num_rows>0){

                            $response["Sucursal"]= array();
                            while($row = mysqli_fetch_array($result))  //Extraemos los datos del registro (debe ser sólo uno)
                            {
                                $item = array();
                                $item["Id_SucursalEjercicio"]=$row["Id_SucursalEjercicio"];
                                $item["Id_Sucursal"]=$row["Id_Sucursal"];
                                $item["IdEjercicio"]=$row["IdEjercicio"];
                                $item["NombreEjercicio"]=$row["NombreEjercicio"];

                                $item["Alias"]=$row["Alias"];
                                $item["NumAparato"]=$row["NumAparato"];


                                $item["ImagenURLGenerica"]=$row["ImagenURLGenerica"];
                                $item["ImagenUrl"]=$row["ImagenUrl"];

                                $item["VideoUrlGenerico"]=$row["VideoUrlGenerico"];
                                $item["VideoUrl"]=$row["VideoUrl"];


                                $item["TipoEjercicio"]=$row["TipoEjercicio"];

                                array_push($response["Sucursal"], $item);

                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontraron ejercicios para la sucursal indicada';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontraron ejercicios para la sucursal indicada';
                        }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }
                desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array
    }


    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function updateEjercicioSucursal($idEjercicioSucursal, $alias, $imagenURL, $videoURL, $numeroAparato, $tipoEjercicio){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            //Procedemos a armar las consultas
            if($tipoEjercicio==1){
                $sql= "UPDATE `sucursalejerciciocardio`
                SET `Alias`='$alias', `NumAparato`='$numeroAparato', `ImagenUrl`='$imagenURL', `VideoUrl`='$videoURL' WHERE `SEC_ID`='$idEjercicioSucursal';";
            }
            else{
                $sql="UPDATE `sucursalejerciciopesa`
                SET `Alias`='$alias', `NumAparato`='$numeroAparato', `ImagenUrl`='$imagenURL', `VideoUrl`='$videoURL' WHERE `SEP_ID`='$idEjercicioSucursal';";
            }

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                            if ($R_ID==NULL or $R_ID==0 or $R_ID==''){
                                $R_ID=mysqli_insert_id($conexion);
                            }

                            $response["getEjercicioSucursal"]=$this->getEjercicioSucursalById($idEjercicioSucursal,$tipoEjercicio);
                            $response["success"]=0;
                            $response["message"]='Ejercicio actualizado correctamente';



                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }
                desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function getEjercicioSucursalById($idEjercicioSucursal, $tipoEjercicio){


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            if ($tipoEjercicio==1){
            //Procedemos a armar la consulta, para obtener la rutina de acuerdo a su id
            $sql= "SELECT SEC_ID as Id_SucursalEjercicio, Id_Sucursal, Id_EjercicioCardio as IdEjercicio, ec.Explicacion as NombreEjercicio,
                    Alias, NumAparato,ec.ImagenUrl as ImagenURLGenerica, sec.ImagenUrl, ec.VideoUrl as VideoUrlGenerico,  sec.VideoUrl, 1 as TipoEjercicio
                    FROM sucursalejerciciocardio sec join ejerciciocardio ec on sec.Id_EjercicioCardio=ec.EC_ID where SEC_ID=$idEjercicioSucursal";
            }
            else{
            $sql="SELECT SEP_ID as Id_SucursalEjercicio, Id_Sucursal, Id_EjercicioPesa as IdEjercicio, ep.Explicacion as NombreEjercicio,
                    Alias, NumAparato, ep.ImagenUrl as ImagenURLGenerica, sep.ImagenUrl, ep.VideoUrl as VideoUrlGenerico,  sep.VideoUrl, 2 as TipoEjercicio
                    FROM sucursalejerciciopesa sep join ejerciciopesa ep on sep.Id_EjercicioPesa=ep.EP_ID where SEP_ID=$idEjercicioSucursal";
            }

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                    if($result!=null){ //Verificamos que no haya regresado Nulo la consulta
                        if ($result->num_rows>0){

                            while($row = mysqli_fetch_array($result))  //Extraemos los datos del registro (debe ser sólo uno)
                            {
                                $item = array();
                                $item["Id_SucursalEjercicio"]=$row["Id_SucursalEjercicio"];
                                $item["Id_Sucursal"]=$row["Id_Sucursal"];
                                $item["IdEjercicio"]=$row["IdEjercicio"];
                                $item["NombreEjercicio"]=$row["NombreEjercicio"];

                                $item["Alias"]=$row["Alias"];
                                $item["NumAparato"]=$row["NumAparato"];


                                $item["ImagenURLGenerica"]=$row["ImagenURLGenerica"];
                                $item["ImagenUrl"]=$row["ImagenUrl"];

                                $item["VideoUrlGenerico"]=$row["VideoUrlGenerico"];
                                $item["VideoUrl"]=$row["VideoUrl"];


                                $item["TipoEjercicio"]=$row["TipoEjercicio"];

                                $response["EjercicioSucursal"]=$item;

                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontraron ejercicios para la sucursal indicada';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontraron ejercicios para la sucursal indicada';
                        }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }
                desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array
    }


    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************


}

?>
