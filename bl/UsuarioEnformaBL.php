<?php

	// JELM
	// 27/01/2016
	// Creación de archivo PHP, el cual permite obtener acceder a la clase UsuarioEnforma

	$data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos



	require('../da/UsuarioEnforma.php'); //Se requiere el archivo de acceso a la base de datos

	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$CodigoEnformaBl= $data["CodigoEnforma"];
	$nombreBl= $data["Nombre"];
	$apellidosBl= $data["Apellidos"];
	$correoBl= $data["Correo"];
	$idFacebookBl= $data["IdFacebook"];
	$passwordBl= $data["Password"];
	$estatusBl= $data["Estatus"];
    $codigoEnformaBl= $data["codigoEnforma"];
    $gimnasioBl= $data["gimansio"];
    $sucursalBl= $data["sucursal"];

     //$metodoBl='getUsuarioEnformaByCodigo';
     //$correoBl='scorres5o@correo.com';
     //$idFacebookBl='li.eduardo.lm@gmail.com';
     //$nombreBl='Usuario de prueba BL';
     //$apellidosBl='LM TEST Bl';
	 //$passwordBl='correo';
     //$codigoEnformaBl='EDU0001';
     //$gimnasioBl=2;
     //$sucursalBl=0;

    function validarTextoNulo($Texto,$Valor){
		if ($Texto!==NULL){
			if (trim($Texto)!=''){
				$Rvalidacion["success"]=1;
			}
			else{
				$Rvalidacion["success"]=8;
				$Rvalidacion["message"]=$Valor.' debe ser diferente de cadena vacia';
			}
		}
		else{
			$Rvalidacion["success"]=7;
			$Rvalidacion["message"]=$Valor.' debe ser diferente de NULO';
		}
		return $Rvalidacion;
	}


	function logueoCorreoPassword($correo,$password){
		$correoValidado= validarTextoNulo($correo, "El correo del usuario");
		if ($correoValidado["success"]==1){
			$passwordValidado= validarTextoNulo($password, "El password del usuario");
			if ($passwordValidado["success"]==1){
                $salt = '$EnfoArt$/';
                $password = sha1(md5($salt . $password));
				$usuario = new UsuarioEnforma();
				$respuesta= $usuario->buscarUsuarioEnformaCorreoPassword($correo,$password);
			}
			else{$respuesta=$passwordValidado;}
		}
		else{$respuesta=$correoValidado;}
		return $respuesta;
	}

//***********************************************************************************

	function logueoCorreo($correo){
		$correoValidado= validarTextoNulo($correo, "El correo del usuario");
		if ($correoValidado["success"]==1){
				$usuario = new UsuarioEnforma();
				$respuesta= $usuario->buscarUsuarioEnformaCorreo($correo);
		}
		else{$respuesta=$correoValidado;}
		return $respuesta;
	}

//***********************************************************************************

	function logueoFacebook($facebook){
		$facebookValidado= validarTextoNulo($facebook, "El id facebook del usuario");
		if ($facebookValidado["success"]==1){
				$usuario = new UsuarioEnforma();
				$respuesta= $usuario->buscarUsuarioEnformaFacebook($facebook);
		}
		else{$respuesta=$facebookValidado;}
		return $respuesta;
	}


//***********************************************************************************

	function nuevoUsuarioEnforma($nombre, $apellidos,$correo,$facebook, $password){
		$facebookValidado= validarTextoNulo($facebook, "El id facebook del usuario");
        $correoValidado= validarTextoNulo($correo, "El correo del usuario");
		if ($facebookValidado["success"]==1 or $correoValidado["success"]==1){
				$usuario = new UsuarioEnforma();
                $bandera=0;

            if ($correoValidado["success"]==1)
            {

                $correoRepetido=$usuario->validarCorreoRepetido($correo);
                if ($correoRepetido["success"]!=0){
                    $respuesta=$correoRepetido;
                    $bandera+=1;
                }

            }
            if ($facebookValidado["success"]==1)
            {

                $facebookRepetido=$usuario->validarFacebookRepetido($facebook);
                if ($facebookRepetido["success"]!=0){
                    $respuesta=$facebookRepetido;
                    $bandera+=1;
                }

            }

            if ($bandera==2)
            {
                $respuesta["success"]=6;
			    $respuesta["message"]='El correo y facebook ya se encuentran registrados';
            }

            if ($bandera==0)
            {

                if ($password!=NULL and $password!='')
                {
                    $salt = '$EnfoArt$/';
                    $password = sha1(md5($salt . $password));
                }

                $respuesta= $usuario->RegistroUsuarioEnforma($nombre, $apellidos,$correo,$facebook, $password);
            }

		}
		else{
            $respuesta["success"]=5;
			$respuesta["message"]='El correo o facebook, deben ser diferente de nulo o cadena vacia';
        }
		return $respuesta;
	}

//***********************************************************************************

	function getUsuarioEnformaByCodigo($codigo,$gimnasio,$sucursal){
		$codigoValidado= validarTextoNulo($codigo, "El código del usuario");
		if ($codigoValidado["success"]==1){
				$usuario = new UsuarioEnforma();
				$respuesta= $usuario->getUsuarioEnformaCodigo($codigo,$gimnasio,$sucursal);
		}
		else{$respuesta=$correoValidado;}
		return $respuesta;
	}




//******************************************************************************************************************************************
//******************************************************************************************************************************************
//******************************************************************************************************************************************

		switch ($metodoBl) {
		case "logueoCorreoPassword": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=logueoCorreoPassword($correoBl,$passwordBl);
		break;
		case "logueoCorreo": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=logueoCorreo($correoBl);
		break;
        case "logueoFacebook": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=logueoFacebook($idFacebookBl);
		break;
        case "RegistroDeUsuario": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
            if ($nombreBl!==NULL){$nombreBl=trim($nombreBl);}
            if ($apellidosBl!==NULL){$apellidosBl=trim($apellidosBl);}
            if ($correoBl!==NULL){$correoBl=trim($correoBl);}
            if ($idFacebookBl!==NULL){$idFacebookBl=trim($idFacebookBl);}
            if ($passwordBl!==NULL){$passwordBl=trim($passwordBl);}
			$response=nuevoUsuarioEnforma($nombreBl,$apellidosBl,$correoBl,$idFacebookBl,$passwordBl);
		break;

        case "getUsuarioEnformaByCodigo":
            $response=getUsuarioEnformaByCodigo($codigoEnformaBl,$gimnasioBl, $sucursalBl);
        break;
		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}
	}

	echo json_encode ($response)


?>
