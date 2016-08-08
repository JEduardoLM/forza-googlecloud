<?php


	// JELM
	// 08/02/2016
	// Creación de archivo PHP, el cual permite obtener la información de un socio especifico:
    // Id del socio
    // Rutina
    // Subrutina

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

	require('../da/UsuarioGym.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idUsuarioBl = $data["idUsuario"];
    $idGimnasioBl = $data["idGimnasio"];



	//$metodoBl="obtenerGimnasiosDeUsuario";
    //$idUsuarioBl=-92;

	function getUsuarioGymByIDU($idUsuario){

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO

            if (is_numeric($idUsuario)){
                $gymsocio = new UsuarioGym();
                $response= $gymsocio->getUsuarioGymByIDU($idUsuario);

            }
            else
            {
            $response["success"]=0;
			$response["message"]='El id del usuario debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=0;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;

    }



	switch ($metodoBl) {
		case "obtenerGimnasiosDeUsuario": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=getUsuarioGymByIDU($idUsuarioBl);
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
