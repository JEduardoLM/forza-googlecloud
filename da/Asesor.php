<?php

require_once('conexion.php');

class Asesor{

    function getAsesorByIdUsuarioIdGym($idUsuario,$idGym){ // Esta función me permite obtener la información del instructor en base a su idUsuario y idGym
		//Creamos la conexión con la base de datos, (la información se encuentra en el archivo conexion.php)
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
        if ($conexion){
            if ($idUsuario!=0)
            {
                if ($idGym!=0){
                    $sql="SELECT A_ID, FechaIngreso, Matricula, Id_UsuarioGym, Estatus, IdRol FROM asesor I join usuariogimnasio UG on I.Id_UsuarioGym=UG.UG_Id
                    where IdUsuario='$idUsuario' and UG.idGym='$idGym' and Estatus=1 and IdRol>1";

                    if($result = mysqli_query($conexion, $sql))
                    {
                        if($result!=null){
                            if ($result->num_rows>0){


                                while($row = mysqli_fetch_array($result))
                                {
                                    $item = array();
                                    $item["A_ID"]=$row["A_ID"];
                                    $item["FechaIngreso"]=$row["FechaIngreso"];
                                    $item["Matricula"]=$row["Matricula"];
                                    $item["IdUsuarioGym"]=$row["Id_UsuarioGym"];
                                    $item["Estatus"]=$row["Estatus"];
                                    $item["IdRol"]=$row["IdRol"];
                                    $response["asesor"]= $item;
                                }
                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontró el instructor asociado con el Gimnasio indicado';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontró el instructor asociado con el Gimnasio indicado';
                            }
                    }
                    else
                    {
                        $response["success"]=4;
                        $response["message"]='Se presentó un error al ejecutar la consulta';
                    }
                }
                else
                  {
                    $response["success"]=6;
                    $response["message"]='El id del Gimnasio debe ser diferente de cero';
                  }
            }
            else
            {
                    $response["success"]=5;
                    $response["message"]='El id del usuario debe ser diferente de cero';
            }
            desconectar($conexion); //desconectamos la base de datos
            return ($response); //devolvemos el array
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presento un error al realizar la conexión';
        }
	}


}

  //$I = new Asesor();
  //$Is=$I->getInsutrctorByIdUsuarioIdGym(5,2);
  //echo json_encode ($Is);


?>
