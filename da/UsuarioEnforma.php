<?php

// JELM
// 27/01/2016
// Se define la clase UsuarioEnforma, utilizada para acceder a la base de datos y realizar operaciones sobre la tabla Usuario Enforma

require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos

class UsuarioEnforma{

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

	function getUsuarioEnformaByID($idUsuarioEnforma) //Esta función permite consultar la información de un Usuario_Enforma por Id
	{												  // en caso, de que el id sea cero, el sistema regresará todos los Usuarios ENFORMA

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

		if ($idUsuarioEnforma!=0) //Si el id es igual a cero, obtenemos todos Usuarios, en caso contrario, vamos por el UsuarioEnforma especifico.
		{
			$sql="select * from usuarioenforma where Id='$idUsuarioEnforma'";
		}
		else
		{
			$sql="select *  from usuarioenforma";
		}

		if($result = mysqli_query($conexion, $sql))
		{
		if($result!=null){
			if ($result->num_rows>0){
                    $response["Usuario"] = array();
                    while($row = mysqli_fetch_array($result))
                    {
                        $item = array();
                        $item["Id"]=$row["Id"];
                        $item["CodigoEnforma"]=$row["CodigoEnforma"];
                        $item["Nombre"]=$row["Nombre"];
                        $item["Apellidos"]=$row["Apellidos"];
                        $item["Correo"]=$row["Correo"];
                        $item["IdFacebook"]=$row["IdFacebook"];
                        $item["Password"]=$row["Password"];
                        $item["Estatus"]=$row["Estatus"];
                        $response["Usuario"]=$item;
                       // array_push($response["Usuarios"], $item);
                    }
                    $response["success"]=0;
                    $response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=0;
				$response["message"]='No se encontró UsuarioEnforma con el Id indicado';
			}

		}
		else
			{
				$response["success"]=0;
				$response["message"]='No se encontró UsuarioEnforma con el Id indicado';
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

    //******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

	function getUsuarioEnformaCodigo($codigo, $gimnasio, $sucursal) //Esta función permite consultar la información de un Usuario_Enforma por codigo
	{												  // en caso, de que el id sea cero, el sistema regresará todos los Usuarios ENFORMA

		//Creamos la conexión a la base de datos
        $conexion = obtenerConexion();

        if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            $sql="select * from usuarioenforma where CodigoEnforma='$codigo'";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){
                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["UsuarioEnformaId"]=$row["Id"];
                                $idUsuario=$item["UsuarioEnformaId"];

                                $item["CodigoEnforma"]=$row["CodigoEnforma"];
                                $item["Nombre"]=$row["Nombre"];

                                $item["Apellidos"]=$row["Apellidos"];
                                if ($item["Apellidos"]==NULL){$item["Apellidos"]='';}

                                $item["Correo"]=$row["Correo"];
                                if ($item["Correo"]==NULL){$item["Correo"]='';}

                                $item["IdFacebook"]=$row["IdFacebook"];
                                if ($item["IdFacebook"]==NULL){$item["IdFacebook"]='';}

                                $item["Estatus"]=$row["Estatus"];

                                $item["estatusDisposicion"]=0; // Se encontró el usuario y no está asociado
                                $item["UsuarioGymId"]=0;
                                $item["SocioId"]=0;
                                $item["Sucursal"]="";

                                $success=0;
                                $message="El usuario no se encuentra registrado en el gimnasio";


                               // array_push($response["Usuarios"], $item);
                                if ($gimnasio!=NULL)
                                {
                                    $sql2="SELECT UG_Id, idGym, IdUsuario, UG.Estatus, So_Id, Id_Sucursal
                                    FROM usuariogimnasio UG left Join  socio S on UG_Id=Id_UsuarioGym where IdRol=1 and idGym=$gimnasio and IdUsuario=$idUsuario;";

                                    if($result2 = mysqli_query($conexion, $sql2)){

                                        if($result2!=null){

                                            if ($result2->num_rows>0){

                                                while($row2 = mysqli_fetch_array($result2)){ //Si ingresa aquí, significa, que el usuario ya se encuentra registrado en el gimnasio indicado
                                                    $item["UsuarioGymId"]=$row2["UG_Id"];
                                                    $item["SocioId"]=$row2["So_Id"];
                                                    if ($row2["Id_Sucursal"]==$sucursal){ //Si ingresa aquí, significa, que el usuario, ya se encuentra asociado a la sucursal en la que estamos trabajando
                                                        $item["Sucursal"]=$row2["Id_Sucursal"];
                                                        if ($row2["Estatus"]==1){ //El usuario se encuentra activo dentro de la misma sucursal
                                                            $item["estatusDisposicion"]=5; // El usuario ya se encuentra asociado en la socursal indicada
                                                            $success=5;
                                                            $message='El usuario ya se encuentra registrado en la sucursal';
                                                        }
                                                        else{
                                                            $item["estatusDisposicion"]=6; // El usuario ya se encuentra asociado en la socursal indicada, pero se encuentra dado de baja
                                                            $success=6;
                                                            $message='El usuario ya se encuentra registrado en la sucursal, pero se encuentra dado de baja';
                                                        }
                                                    }
                                                    else{ //Si no es la misma sucursal, hay que regresar la sucursal, e indicar cual es su actual sucursal
                                                        $item["Sucursal"]=$row2["Id_Sucursal"];
                                                        $item["estatusDisposicion"]=9; // El usuario ya se encuentra asociado al gimnasio, pero a otra sucursal
                                                        $success=9;
                                                        $message='El usuario se encuentra registrado en una sucursal diferente';

                                                    }

                                                }
                                            }
                                        }
                                    }
                                }

                                $response["Usuario"]=$item;
                                $response["success"]=$success;
                                $response["message"]=$message;

                            }

                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontró UsuarioEnforma con el código indicado';
                    }

                }
            else
                {
                    $response["success"]=1;
                    $response["message"]='No se encontró UsuarioEnforma con el código indicado';
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
			$response["message"]='Se presentó un error al realizar la conexión con la base de datos';

        }
		return  ($response); //devolvemos el array
	}

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

	function buscarUsuarioEnformaCorreoPassword($correo,$password){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8
            $sql="select * from usuarioenforma where Correo='$correo'";

            if($result = mysqli_query($conexion, $sql))
            {
            if($result!=null){
                if ($result->num_rows==1){
                    $response["Usuario"] = array();
                    $bandera=0;
                    while($row = mysqli_fetch_array($result))
                    {
                        $item = array();
                        $item["Id"]=$row["Id"];
                        $item["CodigoEnforma"]=$row["CodigoEnforma"];
                        $item["Nombre"]=$row["Nombre"];
                        $item["Apellidos"]=$row["Apellidos"];
                        $item["Correo"]=$row["Correo"];
                        $item["IdFacebook"]=$row["IdFacebook"];
                        $contrasena=$row["Password"];
                        $item["Estatus"]=$row["Estatus"];
                        if ($item["Estatus"]==0){
                            $bandera=1;
                        } elseif ($contrasena!=$password){
                            $bandera=2;
                        }

                    }

                    if ($bandera==0){
                        //array_push($response["Usuarios"], $item);
                        $response["Usuario"]=$item;
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';

                    }
                    if ($bandera==1){
                        $response["success"]=9;
                        $response["message"]='El Usuario ENFORMA con el correo '.$correo.' no se encuentra activo';
                    }
                    if ($bandera==2){
                        $response["success"]=6;
                        $response["message"]='La contraseña no es correcta';
                    }
                }
                else{
                    $response["success"]=5;
                    $response["message"]='El correo indicado no se encuentra registrado';
                }

            }
            else
                {
                    $response["success"]=5;
                    $response["message"]='El correo indicado no se encuentra registrado';
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

		return  ($response); //devolvemos el array
	}

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

    function buscarUsuarioEnformaCorreo($correo){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
        if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


            $sql="select * from usuarioenforma where Correo='$correo'";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows==1){
                        $response["Usuario"] = array();
                        $bandera=0;
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["Id"];
                            $item["CodigoEnforma"]=$row["CodigoEnforma"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["Apellidos"]=$row["Apellidos"];
                            $item["Correo"]=$row["Correo"];
                            $item["IdFacebook"]=$row["IdFacebook"];
                            $contrasena=$row["Password"];
                            $item["Estatus"]=$row["Estatus"];
                            if ($item["Estatus"]==0){
                                $bandera=1;
                            }

                        }

                        if ($bandera==0){
                            //array_push($response["Usuarios"], $item);
                            $response["Usuario"]=$item;
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';

                        }
                        if ($bandera==1){
                            $response["success"]=6;
                            $response["message"]='El Usuario ENFORMA con el correo '.$correo.' no se encuentra activo';
                        }

                    }
                    else{
                        $response["success"]=5;
                        $response["message"]='El correo indicado no se encuentra registrado';
                    }

                }
            else
                {
                    $response["success"]=5;
                    $response["message"]='El correo indicado no se encuentra registrado';
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
		return  ($response); //devolvemos el array
	}

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

    function buscarUsuarioEnformaFacebook($facebook){

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


            $sql="select * from usuarioenforma where IdFacebook='$facebook'";

            if($result = mysqli_query($conexion, $sql))
            {
            if($result!=null){
                if ($result->num_rows==1){
                    $response["Usuario"] = array();
                    $bandera=0;
                    while($row = mysqli_fetch_array($result))
                    {
                        $item = array();
                        $item["Id"]=$row["Id"];
                        $item["CodigoEnforma"]=$row["CodigoEnforma"];
                        $item["Nombre"]=$row["Nombre"];
                        $item["Apellidos"]=$row["Apellidos"];
                        $item["Correo"]=$row["Correo"];
                        $item["IdFacebook"]=$row["IdFacebook"];
                        $contrasena=$row["Password"];
                        $item["Estatus"]=$row["Estatus"];
                        if ($item["Estatus"]==0){
                            $bandera=1;
                        }

                    }

                    if ($bandera==0){
                        $response["Usuario"]=$item;
                        //array_push($response["Usuarios"], $item);
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';

                    }
                    if ($bandera==1){
                        $response["success"]=6;
                        $response["message"]='El Usuario ENFORMA con el id de facebook: '.$facebook.' no se encuentra activo';
                    }

                }
                else{
                    $response["success"]=5;
                    $response["message"]='El id de facebook indicado no se encuentra registrado';
                }

            }
            else
                {
                    $response["success"]=5;
                    $response["message"]='El id de facebook indicado no se encuentra registrado';
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
		return  ($response); //devolvemos el array
	}

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

    function validarFacebookRepetido($facebook)
    {

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

        $sql="select * from usuarioenforma where IdFacebook='$facebook'";

		if($result = mysqli_query($conexion, $sql))
		{
            if($result!=null){
                if ($result->num_rows!=0){
                        $response["success"]=10;
                        $response["message"]='El facebook '.$facebook.' ya se encuentra registrado';
                }
                else{
                    $response["success"]=0;
                    $response["message"]='El facebook se encuentra disponible';
                }

            }
            else
                {
                    $response["success"]=0;
                    $response["message"]='El facebook se encuentra disponible';
                }
		}
		else
		{
			$response["success"]=4;
			$response["message"]='Se presentó un error al ejecutar la consulta';
		}

		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array
    }

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

    function validarCorreoRepetido($correo)
    {

		//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
		mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


		$sql="select * from usuarioenforma where Correo='$correo'";

		if($result = mysqli_query($conexion, $sql))
		{
            if($result!=null){
                if ($result->num_rows!=0){
                        $response["success"]=9;
                        $response["message"]='El correo '.$correo.' ya se encuentra registrado';


                }
                else{
                    $response["success"]=0;
                    $response["message"]='El correo se encuentra disponible';
                }

            }
            else
                {
                    $response["success"]=0;
                    $response["message"]='El correo se encuentra disponible';
                }
		}
		else
		{
			$response["success"]=4;
			$response["message"]='Se presento un error al ejecutar la consulta';
		}

		desconectar($conexion); //desconectamos la base de datos

		return  ($response); //devolvemos el array
    }

	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
    function addUsuarioEnforma($nombre,$apellido,$correo, $facebook, $password)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

			$sql=$conexion->prepare("CALL nuevoUsuario(?,?,?,?,?);");
			$sql->bind_param("sssss",$nombre,$apellido,$correo, $facebook, $password);

            if ($sql->execute()){
                $response["Usuario"]= array();
                $arregloUsuarios=$this->getUsuarioEnformaByID(0);
                $response["Usuario"]=$arregloUsuarios["Usuario"];
                $response["success"]=1;
                $response["message"]='Usuario almacenado correctamente';

            }
			else {
				    //return 'El Usuario no pudo ser almacenado correctamente';
					$response["success"]=0;
					$response["message"]='El Usuario no pudo ser almacenado correctamente';

				}
		desconectar($conexion); //desconectamos la base de datos
		return  ($response); //devolvemos el array

	}

   	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
    function RegistroUsuarioEnforma($nombre,$apellido,$correo, $facebook, $password)
	{
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();
 		//generamos la consulta
        if ($conexion){


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

			$sql=$conexion->prepare("CALL nuevoUsuario(?,?,?,?,?);");
			$sql->bind_param("sssss",$nombre,$apellido,$correo, $facebook, $password);

            if ($sql->execute()){

                $sql->close();

                $response["Usuario"]= array();
                $arregloUsuarios=$this->buscarUsuarioEnformaCorreo($correo);
                $response["Usuario"]=$arregloUsuarios["Usuario"];
                $response["success"]=0;
                $response["message"]='Usuario almacenado correctamente';

            }
			else {
				    //return 'El Usuario no pudo ser almacenado correctamente';
					$response["success"]=4;
					$response["message"]='El Usuario no pudo ser almacenado correctamente';

				}
		desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
           $response["success"]=3;
           $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return  ($response); //devolvemos el array

	}


}

   // $UE=new UsuarioEnforma();
   // $usuario = $UE->getUsuarioEnformaCodigo('kjkjhkhj',6,3);
   // echo json_encode($usuario);



?>
