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
			$sql="select Id, CodigoEnforma, Nombre, Apellidos, Correo, IdFacebook, Estatus, IdNotificaciones from usuarioenforma where Id='$idUsuarioEnforma'";
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
                       // $item["Password"]=$row["Password"];
                        $item["Estatus"]=$row["Estatus"];
                        $item["IdNotificaciones"]=$row["IdNotificaciones"];
                        $response["Usuario"]=$item;
                       // array_push($response["Usuarios"], $item);
                    }
                    $response["success"]=0;
                    $response["message"]='Consulta exitosa';
			}
			else{
				$response["success"]=1;
				$response["message"]='No se encontró UsuarioEnforma con el Id indicado';
			}

		}
		else
			{
				$response["success"]=4;
				$response["message"]='No se encontró UsuarioEnforma con el Id indicado';
			}
		}
		else
		{
			$response["success"]=3;
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
                                $item["Id"]=$row["Id"];
                                $idUsuario=$item["Id"];

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

        //Vamos a proceder a armar las consultas para almacenar al nuevo usuario dentro de la base de datos.
        $codigo=strtoupper(substr ($nombre,0,3)) ;

        $sqlCodigo="SELECT COUNT( codigoEnforma ) as conteo FROM  `usuarioenforma` WHERE codigoEnforma LIKE '%$codigo%';";

        if($result = mysqli_query($conexion, $sqlCodigo)){
                  if($result!=null){
                    if ($result->num_rows>0){
                        while($row = mysqli_fetch_array($result))
                        {
                             $conteo=$row["conteo"];
                        }
                        $codigoTexto=$codigo.str_pad($conteo, 4, "0", STR_PAD_LEFT);
                        $sql="INSERT INTO  `UsuarioEnforma` (
                                            `Id` ,
                                            `CodigoEnforma` ,
                                            `Nombre` ,
                                            `Apellidos` ,
                                            `Correo` ,
                                            `IdFacebook` ,
                                            `Password` ,
                                            `Estatus`
                                            )
                                            VALUES (
                                                NULL , '$codigoTexto', '$nombre' , '$apellido' , '$correo' , '$facebook', '$password', 1
                                            );";
                                if($result = mysqli_query($conexion, $sql)){
                                    $response["success"]=0;
                					$response["message"]='El Usuario fue almacenado correctamente';
                                }
                                else
                                {

                                    $response["success"]=1;
                					$response["message"]='Se presentó un error';

                                }

                    }
                  }
            }
        else{

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

        $codigo=strtoupper(substr ($nombre,0,3)) ;
		$sqlCodigo="SELECT COUNT( codigoEnforma ) as conteo FROM  `usuarioenforma` WHERE codigoEnforma LIKE '%$codigo%';";

        if($result = mysqli_query($conexion, $sqlCodigo)){
              if($result!=null){
                    if ($result->num_rows>0){
                        while($row = mysqli_fetch_array($result))
                        {
                             $conteo=$row["conteo"]+1;
                        }
                        $codigoTexto=$codigo.str_pad($conteo, 4, "0", STR_PAD_LEFT);


                        $sql="INSERT INTO  `usuarioenforma` (
                                            `Id` ,
                                            `CodigoEnforma` ,
                                            `Nombre` ,
                                            `Apellidos` ,
                                            `Correo` ,
                                            `IdFacebook` ,
                                            `Password` ,
                                            `Estatus`
                                            )
                                            VALUES (
                                                NULL , '$codigoTexto', '$nombre' , '$apellido' , '$correo' , '$facebook', '$password', 1
                                            );";



                        if ($correo==='' or $correo===0 or $correo===NULL){
                            $sql="INSERT INTO  `usuarioenforma` (
                                            `Id` ,
                                            `CodigoEnforma` ,
                                            `Nombre` ,
                                            `Apellidos` ,
                                            `IdFacebook` ,
                                            `Password` ,
                                            `Estatus`
                                            )
                                            VALUES (
                                                NULL , '$codigoTexto', '$nombre' , '$apellido' , '$facebook', '$password', 1
                                            );";
                        }

                        if ($facebook==='' or $facebook===0 or $facebook===NULL){
                                                $sql="INSERT INTO  `usuarioenforma` (
                                            `Id` ,
                                            `CodigoEnforma` ,
                                            `Nombre` ,
                                            `Apellidos` ,
                                            `Correo` ,
                                            `Password` ,
                                            `Estatus`
                                            )
                                            VALUES (
                                                NULL , '$codigoTexto', '$nombre' , '$apellido' , '$correo' , '$password', 1
                                            );";
                        }





                                if($result = mysqli_query($conexion, $sql)){

                                        $response["Usuario"]= array();
                                        $idUsuario=mysqli_insert_id($conexion);
                                        $arregloUsuarios=$this->getUsuarioEnformaByID($idUsuario);
                                        $response["Usuario"]=$arregloUsuarios["Usuario"];
                                        $response["success"]=0;
                                        $response["message"]='Usuario almacenado correctamente';
                                }
                                else
                                {

                                    $response["success"]=4;
                					$response["message"]='El Usuario no pudo ser almacenado correctamente';

                                }

                    }
            }
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


   	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************

    function aplanarPassword($correo){
        //Este método es utlizado para actualizar el pasword de un socio

        //Lo primero que vamos a realizar es buscar el usuario, por correo
        $buscarUsuarioPorCorreo=$this->buscarUsuarioEnformaCorreo($correo);

        //Validamos si se encontró el correo
        if ($buscarUsuarioPorCorreo["success"]==0){
        // Una vez que encontramos el usuario, procedemos a generar un código, para poder modificar la contraseña

            $idUsuario=$buscarUsuarioPorCorreo["Usuario"]["Id"];
            $fecha = new DateTime();
            $fechaCodigo= $fecha->getTimestamp();
            $CodigoPassword= substr($buscarUsuarioPorCorreo["Usuario"]["CodigoEnforma"],0,3).'+'.$this->generar_clave(3);



            // Creamos una conexión con la base de datos, para guardar el código generado y la fecha

            $conexion = obtenerConexion();
            //generamos la consulta
            if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $codigo=strtoupper(substr ($nombre,0,3)) ;
            $sql="UPDATE `usuarioenforma` SET `CodigoPassword`='$CodigoPassword', `FechaCodigoPassword`=$fechaCodigo WHERE `Id`=$idUsuario;";

            if($result = mysqli_query($conexion, $sql)){

                $link='http://enformadesarrollo.esy.es/DemoGym/bl/UsuarioEnformaBL.php';
                   $mensaje = '<html>
                             <head>
                                <title>Restablece tu contraseña</title>
                             </head>
                             <body>
                               <p>Hemos recibido una petición para restablecer la contraseña de tu cuenta.</p>
                               <p>Si hiciste esta petición, haz clic en el siguiente enlace, o puedes acceder a cambiar tu contraseña directamente desde tu aplicación móvil.</p>
                                <p>Si no hiciste esta petición puedes ignorar este correo.</p>

                                <p>
                                 <strong>Para reestablecer desde tu aplicación móvil, debes copiar el siguiente código:</strong><br>
                                 <p><big>'.$CodigoPassword.'</big></p>
                               </p>

                               <p>
                                 <strong>Enlace para restablecer tu contraseña</strong><br>
                                 <a href="'.$link.'?CodigoPassword='.$CodigoPassword.'"> Restablecer contraseña </a>
                               </p>


                              <p><i> Este correo es informativo, favor no responder a esta dirección de correo, ya que no se encuentra habilitada para recibir mensajes </i> </p>

                             </body>
                            </html>';


                $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

                // $cabeceras .= 'From: FORZA';

                $bool2=mail($correo,"Código de recuperación de contraseña FORZA",$mensaje,$cabeceras);

                        if($bool2){

                                $response["success"]=0;
                                $response["message"]='Se ha generado correctamente el código de recuperación de password y ha sido enviado a su correo electrónico';

                        }else{

                                $response["success"]=1;
                                $response["message"]='Se ha generado correctamente el código de recuperación de password pero no pudo ser enviado a su correo electrónico';
                        }



            }

            else {
                        //return 'El Usuario no pudo ser almacenado correctamente';
                        $response["success"]=4;
                        $response["message"]='Se ha presentado un error en la consulta';

            }

            desconectar($conexion); //desconectamos la base de datos
            }
            else
            {
               $response["success"]=3;
               $response["message"]='Se presentó un error en la conexión con la base de datos';
            }












        }
        else{
            //Sino se encontró el correo, regresamos mensaje de error.
            $response=$buscarUsuarioPorCorreo;
        }


       // UPDATE `forza`.`usuarioenforma` SET `CodigoPassword`='UN-DOS-TRES-SIN-PARAR-DE BAILAR', `FechaCodigoPassword`='FECHA-DE-CODIGO' WHERE `Id`='3';


        return  ($response); //devolvemos el array

    }


    function generar_clave($longitud){
       $cadena="[^A-Z0-9]";
       return substr(eregi_replace($cadena, "", md5(rand())) .
       eregi_replace($cadena, "", md5(rand())) .
       eregi_replace($cadena, "", md5(rand())),
       0, $longitud);
    }


   	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************
	//******************************************************************************************************************************************************


   function actualizarPassword($correo,$password, $codigoPassword){
       // Éste método nos va a permitir actualizar la contraseña de un usuario

       //Lo primero que haremos será buscar el usuario con el correo proporcionado, para obtener el código y la vigencia del código


       	//Creamos la conexión a la base de datos
		$conexion = obtenerConexion();
        if ($conexion){ //Verificamos la conexión con la base de datos

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


            $sql="select Id, CodigoPassword, FechaCodigoPassword  from usuarioenforma where Correo='$correo'";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows==1){
                        while($row = mysqli_fetch_array($result))
                        {

                            $idUsuario=$row["Id"];
                            $codigoPasswordBD=$row["CodigoPassword"];
                            $fechaCodigoPassword=$row["FechaCodigoPassword"];

                        }


                        //Verificaremos que el código ingresado por el usuario, corresponda con el código generado por el sistema y se encuentra almacenado en la base de datos

                        if ($codigoPasswordBD==$codigoPassword){

                            //Si el código es correcto, procedemos a verificar que aún se encuentra vigente.

                             $hoy = round(microtime(true) * 1000);

                           $tiempoDiferencia=$hoy-$fechaCodigoPassword;


                            if ($tiempoDiferencia<86400){
                                // Si el código es correcto y se encuentra vigente, procederemos a actualizar la contraseña


                                $sql2="UPDATE `usuarioenforma` SET `Password`='$password' WHERE `Id`='$idUsuario';";
			                     if($result = mysqli_query($conexion, $sql2)){

                                    $response["success"]=0;
                                    $response["message"]='La contraseña ha sido correctamente actualizada';
                                 }
                                else
                                {
                                    $response["success"]=8;
                                    $response["message"]='Se presentó un error al actualizar la contraseña';
                                }




                            }
                            else{
                                $response["success"]=7;
                                $response["message"]='El código ingresado no se encuentra vigente';
                            }


                        }
                        else{
                            $response["success"]=6;
                            $response["message"]='El código para modificar la contraseña es incorrecto ';

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

    function saveIdNotificaciones($idUsuario, $idNotificacion){
        //Esta función nos permitirá actualizar el id de notificaciones de un usuario

        //Realizamos la conexión con la base de datos
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            //Procedemos a armar las consultas
            //Primero insertamos el registro dentro de la tabla usuario
            $sql= "UPDATE `usuarioenforma` SET `IdNotificaciones`='$idNotificacion' WHERE `Id`='$idUsuario';";



                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {

                                desconectar($conexion);
                                $response["getUsuario"]=$this->getUsuarioEnformaByID($idUsuario);
                                $response["success"]=0;
                                $response["message"]='Id de notificaciones almacenado correctamente';

                }

                else
                {

                    $response["success"]=4;
                    $response["message"]='Se presentó un error al guardar el usuario';

                }
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }


}



?>
