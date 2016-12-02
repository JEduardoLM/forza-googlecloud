<?PHP


	// JELM
	// 21/09/2016
	// Creación de archivo PHP, el cual permite actualizar el id de notificaciones push


    //Se agrega cabezara, para permitir el acceso CORS
    header("Access-Control-Allow-Origin: *");

    // Las variables se reciben por el método POST desde un objeto JSON
     $data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

    require('../da/UsuarioEnforma.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Notificacion.php');



	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["Metodo"];
    $idNotificacionesBl=$data["IdNotificaciones"];
    $idUsuarioBl=$data["IdUsuario"];

    $tituloBl=$data["Titulo"];
    $descripcionBl=$data["Descripcion"];
    $idSucursalBl=$data["IdSucursal"];
    $URLBl=$data["URL"];
    $codigoSucursalBl=$data["CodigoSucursal"];
    $codigoGymBl=$data["CodigoGym"];
    $colorBl=$data["ColorFondo"];
    $soundBl=$data["Sound"];


    // ****************************************************************************************************************************************
    // ****************************************************************************************************************************************

    function updateIdNotificaciones($idUsuario, $idNotificacion){

        if ($idUsuario===NULL or $idUsuario==='' or $idUsuario===0){
                $respuesta["success"]=5;
                $respuesta["message"]='El Id del usuario debe ser diferente de nulo o cero';
        }
        else{
                    $user = new UsuarioEnforma();
                    $respuesta= $user->saveIdNotificaciones($idUsuario, $idNotificacion);
        }
        return $respuesta;
    }


    function saveNotificacion ($titulo, $descripcion, $idUsuario, $idSucursal, $URL, $codigoSucursal, $codigoGym, $color, $sound)
    {
        $noti= new Notificacion();
        if ($titulo==NULL or $titulo==''){
                $respuesta["success"]=5;
                $respuesta["message"]='El título debe ser diferente de nulo o cadena vacia';
        }
        else{
            if ($descripcion==NULL or $descripcion==''){
                $respuesta["success"]=6;
                $respuesta["message"]='La descripción debe ser diferente de nulo o cadena vacia';
            }
            else{
                if ($idUsuario==NULL or $idUsuario==0){
                    $respuesta["success"]=7;
                    $respuesta["message"]='El usuario  debe ser diferente de nulo o cero';
                }
                else{
                    if (($codigoSucursal==NULL or $codigoSucursal=='') and ($codigoGym==NULL or $codigoGym=='')){
                        $respuesta["success"]=8;
                        $respuesta["message"]='El código de la sucursal o gimnasio debe ser diferente de nulo o cadena vacia';
                    }
                    else{

                        if ($codigoSucursal==NULL){$topic=$codigoGym;}else {$topic=$codigoSucursal;}
                        $sound="default";
                        $respuesta = $noti->saveNotificacion ($titulo, $descripcion, $idUsuario, $idSucursal, $URL, $topic, $color, $sound);

                    }


                }

            }


        }



        return $respuesta;
    }


//******************************************************************************************************************************************
//******************************************************************************************************************************************
//******************************************************************************************************************************************



		switch ($metodoBl) {
        case "updateIdNotificaciones": // Método utilizado para actualizar el id de notificaciones
                $response=updateIdNotificaciones($idUsuarioBl, $idNotificacionesBl);
		break;
        case "saveNotificacion": // Método utilizado para actualizar el id de notificaciones
                $response=saveNotificacion($tituloBl, $descripcionBl, $idUsuarioBl, $idSucursalBl, $URLBl, $codigoSucursalBl, $codigoGymBl, $colorBl, $soundBl);
		break;

		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
	}

    echo json_encode ($response)

?>
