<?php

require_once('conexion.php');

class Socio{

function getSocioByIdUsuarioIdGym($idUsuario,$idGym){ // Esta función nos regresa los socios que esten asociados a un usuario/gymnasio (en teoría sólo debe haber un registro así)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idUsuario!=0)
		{
			$sql= "SELECT UG_Id, IdUsuario, IdGym, So_Id, socio.Estatus, id_Sucursal as sucursal  FROM usuariogimnasio join socio on UG_Id=Id_UsuarioGym
            where IdUsuario='$idUsuario' and IdGym='$idGym'";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["socios"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["UG_Id"];
                            $item["IdUsuario"]=$row["IdUsuario"];
                            $item["IdGym"]=$row["IdGym"];
                            $item["IdSocio"]=$row["So_Id"];
                            $item["Estatus"]=$row["Estatus"];
                            $item["IdSucursal"]=$row["sucursal"];

                            array_push($response["socios"], $item);
                        }
                        $response["success"]=1;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=0;
                        $response["message"]='No existe un socio registrado del usuario en el gimnasio indicado';
                    }

                }
                else
                    {
                        $response["success"]=0;
                        $response["message"]='No existe un socio registrado del usuario en el gimnasio indicado';
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
                $response["message"]='El id del usuario debe ser diferente de cero';
		}
		desconectar($conexion); //desconectamos la base de datos
		return ($response); //devolvemos el array
	}

//********************************************************************************************************************
//********************************************************************************************************************
//********************************************************************************************************************


function getSociosBySucursalId($idSucursal){ // Esta función nos regresa los socios que esten asociados a un usuario/gymnasio (en teoría sólo debe haber un registro así)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
		$sql= "SELECT ue.Id as UsuarioEnformaId, ug.UG_ID as UsuarioGymId, s.So_Id as SocioId, CodigoEnforma, ue.Nombre as NombreUsuario, Apellidos, Correo, IdFacebook, s.Estatus, ug.IdRol, r.Nombre as NombreRol, Id_Sucursal
	               FROM usuarioenforma ue join usuariogimnasio ug on ue.id=ug.idUsuario join rol r on ug.IdRol=r.R_id join socio s on ug.UG_Id=s.Id_UsuarioGym where s.Id_Sucursal=$idSucursal";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["socios"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["UsuarioEnformaId"]=$row["UsuarioEnformaId"];
                            $item["UsuarioGymId"]=$row["UsuarioGymId"];
                            $item["SocioId"]=$row["SocioId"];

                            $item["CodigoEnforma"]=$row["CodigoEnforma"];

                            $item["NombreUsuario"]=$row["NombreUsuario"];
                            if ($item["NombreUsuario"]==NULL){$item["NombreUsuario"]='';}

                            $item["Apellidos"]=$row["Apellidos"];
                            if ($item["Apellidos"]==NULL){$item["Apellidos"]='';}

                            $item["Correo"]=$row["Correo"];
                            if ($item["Correo"]==NULL){$item["Correo"]='';}

                            $item["IdFacebook"]=$row["IdFacebook"];
                            if ($item["IdFacebook"]==NULL){$item["IdFacebook"]='';}

                            $item["Estatus"]=$row["Estatus"];

                            $item["NombreRol"]=$row["NombreRol"];
                            if ($item["NombreRol"]==NULL){$item["NombreRol"]='';}

                            $item["Id_Sucursal"]=$row["Id_Sucursal"];

                            array_push($response["socios"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No existen socios registrados en la sucursal';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No existen socios registrados en la sucursa';
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
        $response["message"]='Se presento un error al realizar la conexión';

    }
		return ($response); //devolvemos el array

    }

//********************************************************************************************************************
//********************************************************************************************************************
//********************************************************************************************************************

function asociarSocioGimnasio($idUsuario, $idGimnasio, $idSucursal){

    //Creamos la conexión con la función anterior
	$conexion = obtenerConexion();

    //generamos la consulta
    mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    /* deshabilitar autocommit */
    if ($conexion){


    mysqli_autocommit($conexion, FALSE);


			$sql="INSERT INTO usuariogimnasio (`IdGym`, `IdUsuario`, `Estatus`, `IdRol`) VALUES ($idGimnasio, $idUsuario , '1', '1');";

			if($result = mysqli_query($conexion, $sql)){

                $idUsuarioGym=mysqli_insert_id($conexion); //Obtenemos el id del registro insertado

                $hoy = round(microtime(true) * 1000);

                $sql2="INSERT INTO socio (`Id_Sucursal`, `Id_UsuarioGym`, `FechaIngreso`, `Estatus`) VALUES ($idSucursal, $idUsuarioGym , $hoy , '1')";

                if($result = mysqli_query($conexion, $sql2)){

                    mysqli_commit($conexion);

                    $response["socios"]=$this->getSociosBySucursalId($idSucursal);
                    $response["success"]=0;
				    $response["message"]='Socio registrado correctamente';
                }
                else{

                    $response["success"]=5;
					$response["message"]='No se logró registrar correctamente el socio';
                    /* Revertir */
                    mysqli_rollback($conexion);
                }
            }
			else {
				//return 'El aparato no pudo ser almacenado correctamente';
					$response["success"]=4;
					$response["message"]='No se logró registrar correctamente el UsuarioGym';
                    /* Revertir */
                    mysqli_rollback($conexion);

				}
		  desconectar($conexion); //desconectamos la base de datos
        }
    else
    {
       	$response["success"]=3;
		$response["message"]='Se presentó un error con la conexión a la BD';
    }
		return  ($response); //devolvemos el array

    }

//********************************************************************************************************************
//********************************************************************************************************************
//********************************************************************************************************************

function modificarEstatusSocio($idUsuarioGym, $estatus,$sucursal){
    //Creamos la conexión con la función anterior
	$conexion = obtenerConexion();
    mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    if ($conexion){

    /* deshabilitar autocommit para poder hacer un rollback*/
    mysqli_autocommit($conexion, FALSE);

    //Lo primero que haremos será obtener el id del socio, de acuerdo con el idUsuarioGym que se haya enviado

	$sql="SELECT So_Id, Id_UsuarioGym FROM socio where Id_UsuarioGym=$idUsuarioGym;";
     if($result = mysqli_query($conexion, $sql)){ // Ejecutamos la consulta para obtener el id del socio
          while($row = mysqli_fetch_array($result)){ //Obtenemos los resultados
            $idSocio= $row["So_Id"];
          }
         $sql2="UPDATE usuariogimnasio SET `Estatus`=$estatus WHERE `UG_Id`=$idUsuarioGym;";
         if($result2 = mysqli_query($conexion, $sql2)){ //Ejecutamos la sentencia para actualizar el estatus del gimnasio

             //Si se actualizó correctamente el estatus del UsuarioGimnasio, procederemos a actualizar el estatus del Socio
            $sql3="UPDATE `socio` SET `Estatus`=$estatus WHERE `So_Id`=$idSocio;";
            if($result3 = mysqli_query($conexion, $sql3)){ //Ejecutamos la sentencia para actualizar el estatus del socio
                    mysqli_commit($conexion);
                    $response["Socios"]=$this->getSociosBySucursalId($sucursal);
                    $response["success"]=0;
                    $response["message"]='Se ha modificado correctamente el estatus del socio';

                }
                else{
                    mysqli_rollback($conexion);
                    $response["success"]=5;
                    $response["message"]='Se presentó un error al actualizar el estatus del Socio';
                }
         }
         else //Si falla la consulta, hacemos un rollBack;
         {

             mysqli_rollback($conexion);
             $response["success"]=4;
             $response["message"]='Se presentó un error al actualizar el estatus del UsuarioGimnasio';
         }

     }
     else{ // Se presentó un error al generar la consulta del id del socio
        $response["success"]=1;
        $response["message"]='Se presentó un error al consultar el id del socio';
     }


    desconectar($conexion);
    }
    else
    {
        $response["success"]=3;
        $response["message"]='Se presentó un error en la conexión con la base de datos';
    }
	return  ($response); //devolvemos el array

}

//********************************************************************************************************************
//********************************************************************************************************************
//********************************************************************************************************************

function actualizarSucursalSocio($idSocio, $idSucursal){

    //Creamos la conexión
	$conexion = obtenerConexion();
    mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    if ($conexion){ //Verificamos la conexión, en caso de fallar regresamos el error de conexión NO EXITOSA

        /* deshabilitar autocommit para poder hacer un rollback*/
        mysqli_autocommit($conexion, FALSE);

        //Una vez que se ha deshabilitado el autocomit, procedemos con las consultas para actualizar la sucursal del socio

        $sql="UPDATE `socio` SET `Id_Sucursal`=$idSucursal WHERE `So_Id`=$idSocio;";
        if($result = mysqli_query($conexion, $sql)){
            mysqli_commit($conexion);
            $response["Socios"]=$this->getSociosBySucursalId($idSucursal);
            $response["success"]=0;
            $response["message"]='Se ha actualizado correctamente la sucursal del socio';

        }
            else
        {
                $response["success"]=4;
                $response["message"]='Se presentó un error al actualizar la sucursal del socio';
        }

        desconectar($conexion);
    }
    else
    {
        $response["success"]=3;
        $response["message"]='Se presentó un error en la conexión con la base de datos';
    }
	return  ($response); //devolvemos el array

}

function getSociosDisponibles($idSucursal){
    //Este método es utilizado para poder determinar cuantos socios se pueden agregar a una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
		$sql= "Select  (SELECT NumeroSocios FROM sucursal where S_Id=$idSucursal) as LimiteDeSocios,
                        ((SELECT NumeroSocios FROM sucursal where S_Id=$idSucursal) -
                (SELECT COUNT(Id_Sucursal) as SociosActivos FROM socio where Id_Sucursal=$idSucursal and Estatus=1)) as SociosDisponibles;";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["SociosDisponibles"] = 0;
                        while($row = mysqli_fetch_array($result))
                        {

                            $response["SociosDisponibles"]=$row["SociosDisponibles"];
                            $response["LimiteDeSocios"]=$row["LimiteDeSocios"];

                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=5;
                        $response["message"]='Se presentó un error al calcular el número de socios disponibles';
                    }

                }
                else
                    {
                        $response["success"]=5;
                        $response["message"]='Se presentó un error al calcular el número de socios disponibles';
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
        $response["message"]='Se presentó un error al realizar la conexión';

    }
		return ($response); //devolvemos el array




}

//******************************************************************************************************************
//******************************************************************************************************************


function getNotificaciones($idUsuario){
    //Este método es utilizado para poder determinar cuantos socios se pueden agregar a una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
		$sql= "SELECT idnotificacion,titulo, Descripcion,fecha,
                (SELECT concat(Nombre,' ', Apellidos) as Autor FROM usuarioenforma where Id=n.idUsuario) as Autor,
                idSucursal, URL, idGimnasio AS IdGym, (SELECT Id_Gimnasio FROM sucursal where S_Id=idSucursal) AS IdGym2
		          FROM notificacion n where idSucursal in
                        (SELECT S_ID FROM sucursal su join socio so on su.S_Id=so.Id_Sucursal
                                    where Id_UsuarioGym in (SELECT UG_Id FROM usuariogimnasio where IdUsuario=$idUsuario and Estatus=1 ))
                                    or idGimnasio in (SELECT IdGym FROM usuariogimnasio where IdUsuario=$idUsuario and Estatus=1)
                                    order by fecha desc limit 10";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Notificaciones"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["IdNotificacion"]=$row["idnotificacion"];
                            $item["Titulo"]=$row["titulo"];
                            $item["Descripcion"]=$row["Descripcion"];
                            $item["Fecha"]=$row["fecha"];
                            $item["Autor"]=$row["Autor"];
                            $item["IdSucursal"]=$row["idSucursal"];

                            $item["IdGym"]=$row["IdGym"];
                            if ($item["IdGym"]==NULL or $item["IdGym"]==0 or $item["IdGym"]=='')
                            {
                                $item["IdGym"]=$row["IdGym2"];
                            }

                            if ( $item["IdSucursal"]==NULL){ $item["IdSucursal"]=0;}
                            if ( $item["IdGym"]==NULL){ $item["IdGym"]=0;}

                            $item["URL"]=$row["URL"];

                            array_push($response["Notificaciones"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';

                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron notificaciones';
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
        $response["message"]='Se presentó un error al realizar la conexión';

    }
        $response["getCodigos"]=$this->getCodigosActivos($idUsuario);
		return ($response); //devolvemos el array




}

//******************************************************************************************************************
//******************************************************************************************************************

function getCodigosActivos($idUsuario){
    //Este método es utilizado para poder determinar cuantos socios se pueden agregar a una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){
		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        // Armamos la consulta, para determinar cuantos socios quedan disponibles para ser asignados a la sucursal.
            $sql= "Select CodigoSucursal, (SELECT CodigoGym FROM gimnasio where G_Id=su.Id_Gimnasio) as CodigoGym from socio so join sucursal su on so.Id_Sucursal=su.S_Id where Id_UsuarioGym in
                    (Select UG_Id from usuarioenforma ue join usuariogimnasio ug on ue.Id=ug.IdUsuario where ue.Id=$idUsuario and ug.Estatus=1) and so.Estatus=1;";


            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Codigos"] = array();
                        while($row = mysqli_fetch_array($result))
                        {

                            $item1=$row["CodigoSucursal"];
                            $item2=$row["CodigoGym"];

                            array_push($response["Codigos"], $item1);
                            array_push($response["Codigos"], $item2);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron codigos activos';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron codigos activos';
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
        $response["message"]='Se presentó un error al realizar la conexión';

    }
		return ($response); //devolvemos el array




}

}





//$UG = new Socio();
//$UGs=$UG->getSociosDisponibles(1);
//echo json_encode ($UGs);


?>
