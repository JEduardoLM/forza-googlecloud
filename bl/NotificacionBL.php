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
    $idUsuarioBl=$data["IdUsuarioEnforma"];


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




//******************************************************************************************************************************************
//******************************************************************************************************************************************
//******************************************************************************************************************************************



		switch ($metodoBl) {
        case "updateIdNotificaciones": // Método utilizado para actualizar el id de notificaciones
                $response=updateIdNotificaciones($idUsuarioBl, $idNotificacionesBl);
		break;

		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
	}

    echo json_encode ($response)





/* //Se comentan métodos utilizados para enviar las notificaciones PUSH
class NotificacionPush{

  function enviarMensajePuntual($mensaje, $destinatarios){
    $content = array(
      "en" => ''.$mensaje,
      "es" => ''.$mensaje
      );

    $fields = array(
      'app_id' => "287aac75-9b6e-4a87-b6d4-f461e2b8bf96",
 //     'included_segments' => array('All'),
      'include_player_ids' => $destinatarios,
      'data' => array("foo" => "bar"),
      'contents' => $content
    );

    $fields = json_encode($fields);

    print("\nJSON sent:\n");
    print($fields);

    echo 'antes del curl_init';
    $ch = curl_init();
    echo 'despues del curl_init';

    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                           'Authorization: Basic ZjlmMGFlYTYtNjA3Yy00MmExLWFiM2YtYzkzODdhNTA4OTMz'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }


}



  $np = new NotificacionPush();

  $destino=array ("a72114ef-3b2b-4257-bb58-6b79c6015fda","664122d7-2eb9-4428-a4fa-b9f236f9566a");

  $response = $np->enviarMensajePuntual('Este mensaje fue enviado desde googleCloud',$destino);

  $return["allresponses"] = $response;
  $return = json_encode( $return);


  print("\n\nJSON received:\n");
  print($return);
  print("\n");

  */

?>
