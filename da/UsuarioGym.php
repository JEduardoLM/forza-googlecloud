<?php

require_once('conexion.php');
require_once('Gimnasio.php');
require_once('Asesor.php');

class UsuarioGym{

	function getUsuarioGymByIDU($idUsuario){ // Esta función nos regresa todos los registros de usuarioGym, que correspondan a un usuario
		//Creamos la conexión

		$conexion = obtenerConexion();

        if ($conexion)
        {

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            if ($idUsuario!=0)
            {
                $sql="SELECT UG_Id, IdGym, g.Nombre as NombreGimnasio, g.CodigoGym, IdUsuario, ug.Estatus, IdRol, rol.Nombre as NombreRol
				,(SELECT CodigoSucursal FROM sucursal where S_id= (select id_sucursal from socio so where id_usuarioGym=ug.UG_Id and so.Estatus=1 limit 1)) as CodigoSucursal
                FROM usuariogimnasio ug join gimnasio g on ug.IdGym=g.G_Id  join  rol on ug.idRol=rol.R_Id
                where IdUsuario=$idUsuario and ug.Estatus>0";
            }
            else
            {
                $sql="SELECT UG_Id, IdGym, gimnasio.Nombre as NombreGimnasio, gimnasio.CodigoGym, IdUsuario, usuariogimnasio.Estatus, IdRol, rol.Nombre as NombreRol
                FROM usuariogimnasio join gimnasio on usuariogimnasio.IdGym=gimnasio.G_Id join  rol on usuariogimnasio.idRol=rol.R_Id;";
            }

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["usuarioGyms"] = array();
                        $G = new Gimnasio();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["UG_Id"]=$row["UG_Id"];
                            $item["IdGym"]=$row["IdGym"];
                            $item["NombreGimnasio"]=$row["NombreGimnasio"];
                            $item["CodigoGym"]=$row["CodigoGym"];
                            $item["IdUsuario"]=$row["IdUsuario"];
                            $item["Estatus"]=$row["Estatus"];
                            $item["IdRol"]=$row["IdRol"];
                            $item["NombreRol"]=$row["NombreRol"];
                            $item["CodigoSucursal"]=$row["CodigoSucursal"];
                            $item["Configuracion"]= $G->getConfiguracionByGymId($item["IdGym"]);

                            if($item["IdRol"]>1){
                                $A = new Asesor();
                                $item ["Asesor"]= $A->getAsesorByIdUsuarioIdGym($idUsuario,$item["IdGym"]);
                            }
                            array_push($response["usuarioGyms"], $item);
                        }


                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontró el usuario asociado con algún Gimnasio';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontró el usuario asociado con algún Gimnasio';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presentó un error al ejecutar la consulta';
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

    //**********************************************************************

    function getUsuarioGymByIDU_IDGym($idUsuario,$idGym){ // Esta función nos regresa todos los registros de usuarioGym, que correspondan a un usuario y Gimnasio especifico
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idUsuario!=0)
		{
            if ($idGym!=0){
                $sql="SELECT UG_Id, IdGym, gimnasio.Nombre as Gimnasio, gimnasio.CodigoGym, IdUsuario, usuariogimnasio.Estatus, IdRol, rol.Nombre as Rol
                FROM usuariogimnasio join gimnasio on usuariogimnasio.IdGym=gimnasio.G_Id  join  rol on usuariogimnasio.idRol=rol.R_Id
                where IdUsuario='$idUsuario' and usuariogimnasio.idGym='$idGym'";

                if($result = mysqli_query($conexion, $sql))
                {
                    if($result!=null){
                        if ($result->num_rows>0){

                            $response["usuarioGyms"] = array();
                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["Id"]=$row["UG_Id"];
                                $item["IdGym"]=$row["IdGym"];
                                $item["Gimnasio"]=$row["Gimnasio"];
                                $item["CodigoGym"]=$row["CodigoGym"];
                                $item["IdUsuario"]=$row["IdUsuario"];
                                $item["Estatus"]=$row["Estatus"];
                                $item["IdRol"]=$row["IdRol"];
                                $item["Rol"]=$row["Rol"];
                                array_push($response["usuarioGyms"], $item);
                            }
                            $response["success"]=1;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=0;
                            $response["message"]='No se encontró el usuario asociado con el Gimnasio indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=0;
                            $response["message"]='No se encontró el usuario asociado con el Gimnasio indicado';
                        }
                }
                else
                {
                    $response["success"]=0;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }
            }
            else
		      {
                $response["success"]=0;
                $response["message"]='El id del Gimnasio debe ser diferente de cero';
		      }
        }
		else
		{
                $response["success"]=0;
                $response["message"]='El id del usuario debe ser diferente de cero';
		}
		desconectar($conexion); //desconectamos la base de datos
		return ($response); //devolvemos el array
	}


}

  //$UG = new UsuarioGym();
  //$UGs=$UG->getUsuarioGymByIDU(2);
  //echo json_encode ($UGs);


?>
