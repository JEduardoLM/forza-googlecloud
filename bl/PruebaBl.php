<?php

	// JELM
	// 08/04/2016
	// Creación de archivo PHP, el cual permite ingresar a las funcionalidades de la aplicación FORZA Instructor

    $response1["Mensaje1"]='AQUI AUN NO SE INVOCA EL DECODE';
	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

    // $json = '{"metodo":"actualizarOrdenSubrutina","Subrutinas":[{"Id":1,"Orden":"11111"},{"Id":2,"Orden":"22222"},{"Id":3,"Orden":"32223"}]}';
    // $data=(json_decode($json, true));

    $response1["Mensaje2"]='AQUI YA SE INVICÓ EL DECODE PERO NO SE HAN EXTRAIDO LAS VARIABLES';


	//Extraemos la información del método POST, y lo asignamos a diferentes variables

    $metodoBl = $data["metodo"];
    $response1["VariableMetodo"]='metodo';


    $subrutinasBl= (array) $data["Subrutinas"];
    $response1["VariableSubrutinas"]='Subrutinas';

    require('../da/Subrutina.php');

    $response1["Mensaje3"]='AQUI YA SE ASIGNARON LAS VARIABLES';

    $response1["MensajeRequire"]='AQUI YA SE INVOCARON TODOS LOS ARCHIVOS PHP';

 //   echo $subrutinasBl[0]["Id"];

    function actualizarOrdenSubrutina($subrutinas){
/*		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $registrosActualizados=0; // Se declara una variable para determinar cuantos registros se actualizaron
            $registrosNoActualizados=0;

            foreach ($subrutinas as $datosSubrutina) {

                $idSubrutina = $datosSubrutina["Id"];
                $orden = $datosSubrutina["Orden"];
                $sql="UPDATE `Subrutina` SET `Orden`='$orden' WHERE `SR_ID`='$idSubrutina';";

                if($result = mysqli_query($conexion, $sql))
                {
                    $registrosActualizados=$registrosActualizados+1;
                }
                else
                {
                    $registrosNoActualizados=$registrosNoActualizados+1;
                }

            }

              $response["success"]=0;
              $response["message"]=''.$registrosActualizados.' registros actualizados, y '.$registrosNoActualizados.'no actualizados';
              desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }*/

        $Subrutina = new Subrutina();
        $response = $Subrutina->actualizarOrdenSubrutina($subrutinas);


		return ($response); //devolvemos el array
    }


switch ($metodoBl) {
		case "actualizarOrdenSubrutina": // Este método lo utilizaremos para obtener el id del instructor
			$resultado=actualizarOrdenSubrutina($subrutinasBl);
		break;
		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
}


//    $Subrutina = new Subrutina();
//    $response1["MetodoDeObjeto"] = $Subrutina->actualizarOrdenSubrutina($subrutinasBl);

    $response= "Id1=".$subrutinasBl[0]["Id"]."Orden1=".$subrutinasBl[0]["Orden"]." + + Id2=".$subrutinasBl[1]["Id"]."Orden1=".$subrutinasBl[1]["Orden"];

    $response1["ARREGLO"]=$response;
    $response1["RESULTADO REAL"]=$resultado;
	echo json_encode ($response1)


?>
