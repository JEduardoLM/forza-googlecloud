<?PHP


	// JELM
	// 01/12/2016
	// Creación de archivo PHP, el cual permite editar los catálogos


    //Se agrega cabezara, para permitir el acceso CORS
    header("Access-Control-Allow-Origin: *");

    // Las variables se reciben por el método POST desde un objeto JSON
     $data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos


    require('../da/Sucursal.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["Metodo"];

    $idSucursalBl=$data["IdSucursal"];

    $idEjercicioSucursalBl=$data["Id_SucursalEjercicio"];
    $aliasBl=$data["Alias"];
    $imagenURLBl=$data["ImagenUrl"];
    $videoURLBl=$data["VideoUrl"];
    $numeroAparatoBl=$data["NumAparato"];
    $tipoEjercicioBl=$data["TipoEjercicio"];



    // ****************************************************************************************************************************************
    // ****************************************************************************************************************************************

    function getEjerciciosByIdSucursal($idSucursal){

        if ($idSucursal==NULL or $idSucursal==0){
                $respuesta["success"]=5;
                $respuesta["message"]='El Id de la sucursal debe ser diferente de nulo o cero';
        }
        else{
                    $sucursal = new Sucursal();
                    $respuesta= $sucursal->getEjerciciosByIdSucursal($idSucursal);
        }
        return $respuesta;
    }


    // ****************************************************************************************************************************************
    // ****************************************************************************************************************************************

    function updateEjercicioSucursal($idEjercicioSucursal, $alias, $imagenURL, $videoURL, $numeroAparato, $tipoEjercicio){

        if ($idEjercicioSucursal==NULL or $idEjercicioSucursal==0){
                $respuesta["success"]=5;
                $respuesta["message"]='El Id del ejercicio sucursal debe ser diferente de nulo o cero';
        }
        else{
            $alias=trim($alias);
            $imagenURL=trim($imagenURL);
            $videoURL=trim($videoURL);
            $numeroAparato=trim($numeroAparato);

            $alias=addslashes($alias);
            $imagenURL=addslashes($imagenURL);
            $videoURL=addslashes($videoURL);
            $numeroAparato=addslashes($numeroAparato);

                    $sucursal = new Sucursal();
                    $respuesta= $sucursal->updateEjercicioSucursal($idEjercicioSucursal, $alias, $imagenURL, $videoURL, $numeroAparato, $tipoEjercicio);
        }
        return $respuesta;
    }


//******************************************************************************************************************************************
//******************************************************************************************************************************************
//******************************************************************************************************************************************



		switch ($metodoBl) {
        case "getEjerciciosByIdSucursal": // Método utilizado para actualizar el id de notificaciones
                $response=getEjerciciosByIdSucursal($idSucursalBl);
		break;

        case "updateEjercicioSucursal": // Método utilizado para actualizar el id de notificaciones
                $response=updateEjercicioSucursal($idEjercicioSucursalBl, $aliasBl, $imagenURLBl, $videoURLBl, $numeroAparatoBl, $tipoEjercicioBl);
		break;

		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
	}

    echo json_encode ($response)

?>
