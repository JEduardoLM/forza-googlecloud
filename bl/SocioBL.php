<?php


	// JELM
	// 08/02/2016
	// Creación de archivo PHP, el cual permite obtener la información de un socio especifico:
    // Id del socio
    // Rutina
    // Subrutina

    header("Access-Control-Allow-Origin: *");


	 $data = json_decode(file_get_contents('php://input'), true);  //Recibimos un objeto json por medio del método POST, y lo decodificamos

     //$json = '{"metodo":"ObtenerSubrutinasByIdUIdGymCompleta","idUsuario":2,"idGimnasio":1}';
     //$data=(json_decode($json, true));

	require('../da/UsuarioGym.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Socio.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Rutina.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Subrutina.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Serie.php'); //Se requiere el archivo de acceso a la base de datos
    require('../da/Ejercicio.php'); //Se requiere el archivo de acceso a la base de datos
  //  require('../da/Gimnasio.php'); //Se requiere el archivo de acceso a la base de datos


	//Extraemos la información del método POST, y lo asignamos a diferentes variables
	$metodoBl = $data["metodo"];
	$idUsuarioBl = $data["idUsuario"];
    $idGimnasioBl = $data["idGimnasio"];
    $idSocioBl = $data["idSocio"];
    $idRutinaBl = $data["idRutina"];
    $idSucursalBl= $data["idSucursal"];

	$idUsuarioGymBl = $data["idUsuarioGimnasio"];
    $estatusBl= $data["estatus"];



    $IdSerieBl=$data["IdSerie"];
    $PesoNuevoBl=$data["PesoNuevo"];
    $TipoPesoBl=$data["TipoPeso"];
    $idEjercicioBl=$data["IdEjercicio"];


    $idEjercicioBl=$data["IdEjercicio"];



	function getUsuarioGymByIDU($idUsuario){

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO

            if (is_numeric($idUsuario)){
                $gymsocio = new UsuarioGym();
                $response= $gymsocio->getUsuarioGymByIDU($idUsuario);

            }
            else
            {
            $response["success"]=5;
			$response["message"]='El id del usuario debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;

    }

    function getSocioByIdUIdG($idUsuario,$idGym){

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO
            if ($idGym!=NULL){
                if (is_numeric($idUsuario)){
                    if (is_numeric($idGym)){
                        $socio = new Socio();
                        $response= $socio->getSocioByIdUsuarioIdGym($idUsuario,$idGym);
                    }
                    else
                    {
                        $response["success"]=0;
                        $response["message"]='El id del gimnasio debe ser un dato numérico';
                    }
                }
                else
                {
                    $response["success"]=0;
                    $response["message"]='El id del usuario debe ser un dato numérico';
                }
            }
            else{
                $response["success"]=0;
			     $response["message"]='El id del gimnasio debe ser diferente de NULO';
            }

        }
        else
        {
            $response["success"]=0;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;
    }

    function getRutinaBySocio($idSocio){
        if ($idSocio!=NULL){  //Validamos que el id envíado sea diferente de NULO

            if (is_numeric($idSocio)){
                $rutina = new Rutina();
                $response= $rutina->getRutinaByIdSocio($idSocio);

            }
            else
            {
            $response["success"]=0;
			$response["message"]='El id del socio debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=0;
			$response["message"]='El id del socio debe ser diferente de NULO';
        }
        return $response;
    }

    function getSubrutinasByRutina($idRutina){
        if ($idRutina!=NULL){  //Validamos que el id envíado sea diferente de NULO
            if (is_numeric($idRutina)){
                $subrutina = new subrutina();
                $response= $subrutina->getsubrutinaByIdRutina($idRutina);

            }
            else
            {
                $response["success"]=0;
                $response["message"]='El id de la rutina debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=0;
			$response["message"]='El id de la rutina debe ser diferente de NULO';
        }
        return $response;
    }

    function getSubrutinasByIdUsuarioIdGym($idUsuario, $idGym)
    {

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO
            if ($idGym!=NULL){
                if (is_numeric($idUsuario)){
                    if (is_numeric($idGym)){
                        $subrutina = new Subrutina();
                        $response= $subrutina->getSubRutinaByIdIdUsuarioIdGym($idUsuario,$idGym);
                    }
                    else
                    {
                        $response["success"]=0;
                        $response["message"]='El id del gimnasio debe ser un dato numérico';
                    }
                }
                else
                {
                    $response["success"]=0;
                    $response["message"]='El id del usuario debe ser un dato numérico';
                }
            }
            else{
                $response["success"]=0;
			     $response["message"]='El id del gimnasio debe ser diferente de NULO';
            }

        }
        else
        {
            $response["success"]=0;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;
    }


    function getSubrutinasByIdUsuarioIdGymCompleta($idUsuario, $idGym)
    {

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO
            if ($idGym!=NULL){
                if (is_numeric($idUsuario)){
                    if (is_numeric($idGym)){
                        $subrutina = new Subrutina();
                        $response= $subrutina->getSubRutinaByIdIdUIdGymCompleta($idUsuario,$idGym);
                    }
                    else
                    {
                        $response["success"]=10;
                        $response["message"]='El id del gimnasio debe ser un dato numérico';
                    }
                }
                else
                {
                    $response["success"]=9;
                    $response["message"]='El id del usuario debe ser un dato numérico';
                }
            }
            else{
                $response["success"]=8;
			     $response["message"]='El id del gimnasio debe ser diferente de NULO';
            }

        }
        else
        {
            $response["success"]=7;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;
    }

    function AsociarUsuarioAGym($idUsuario, $idGimnasio, $idSucursal){

     if ($idUsuario!=NULL and $idUsuario>0){  //Validamos que el id envíado sea diferente de NULO
            if ($idGimnasio!=NULL and $idGimnasio>0){
                if ($idSucursal!=NULL and $idSucursal>0){
                    // ***********************   20/09/2016 ********************************************
                    // Se procede a validar que el usuario aún se pueda registrar dentro de la sucursal.
                    // *********************************************************************************

                    if (is_numeric($idUsuario)){
                        if (is_numeric($idGimnasio)){
                            if (is_numeric($idSucursal)){
                                $socio = new socio();

                                //Mandamos a llamar el método para verificar cuantos lugares estan disponibles.
                                $sociosDisponibles=$socio->getSociosDisponibles($idSucursal);

                                if ($sociosDisponibles["success"]==0){
                                    if ($sociosDisponibles["SociosDisponibles"]>0){
                                        $gym = new Gimnasio();

                                        if ($gym->validarSucursalGimnasio($idGimnasio,$idSucursal)==1){

                                        $usuarioGym = new UsuarioGym();
                                        $UGS=$usuarioGym->getUsuarioGymByIDU_IDGym($idUsuario, $idGimnasio);
                                          if ($UGS["message"]=='Consulta exitosa'){
                                            $response["success"]=13;
                                            $response["message"]='El usuario ya se encuentra asociado al gimnasio';
                                            }
                                            else{


                                                $response= $socio->asociarSocioGimnasio($idUsuario, $idGimnasio, $idSucursal);
                                            }
                                        }
                                        else
                                        {
                                            $response["success"]=12;
                                            $response["message"]='La sucursal indicada no corresponde al gimnasio';
                                        }


                                    }
                                    else{
                                            $response["success"]=13;
                                            $response["message"]='Ha excedido el límite de socios el cual es de '.$sociosDisponibles["LimiteDeSocios"].' socios';
                                    }

                                }
                                else
                                        {
                                            $response["success"]=14;
                                            $response["message"]='Error al consultar los socios disponibles: '. $sociosDisponibles["message"];
                                        }


                                //**********************************************************************

                            }
                            else
                            {
                                $response["success"]=11;
                                $response["message"]='El id de la sucursal debe ser un dato numérico';
                            }

                        }
                        else
                        {
                            $response["success"]=10;
                            $response["message"]='El id del gimnasio debe ser un dato numérico';
                        }
                    }
                    else
                    {
                        $response["success"]=9;
                        $response["message"]='El id del usuario debe ser un dato numérico';
                    }
                }
                else{
                    $response["success"]=8;
			         $response["message"]='El id de la sucursal debe ser diferente de NULO y mayor a cero';
                }
            }
            else{
                $response["success"]=7;
			     $response["message"]='El id del gimnasio debe ser diferente de NULO y mayor a cero';
            }

        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id del usuario debe ser diferente de NULO y mayor a cero';
        }
        return $response;

    }

    function actualizarPesoEnSerie($IdSerie,$PesoNuevo,$TipoPeso,$idEjercicio){

        if ($IdSerie!=NULL and $IdSerie>0 ){
            $serie= new Serie();
            $response["Serie"] = $serie->updatePesoEnSerie($IdSerie,$PesoNuevo,$TipoPeso,$idEjercicio) ;

            if ($response["Serie"]["success"]==0){
                $subrutina = new Subrutina();
                $response["Ejercicio"]=$subrutina->getDetalleEjercicioByID($idEjercicio);
                if ($response["Ejercicio"]["success"]==0){
                    $response["success"]=0;
			        $response["message"]='El peso se registró correctamente';
                }
                else
                {
                     $response["success"]=8;
			         $response["message"]='El pesos se registró correctamente, pero no se pudo obtener el ejercicio actualizado';

                }
            }
            else
            {
                $response["success"]=7;
			    $response["message"]='Se presentó un error al almacenar el peso';

            }

        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id de la serie debe ser diferente de nulo y mayor a cero';
        }
        return $response;
    }

    function ObtenerSociosBySucursal($idSucursal){
        if ($idSucursal!=NULL){  //Validamos que el id envíado sea diferente de NULO

            if (is_numeric($idSucursal)){
                $socio = new Socio();
                $response= $socio->getSociosBySucursalId($idSucursal);

            }
            else
            {
            $response["success"]=5;
			$response["message"]='El id de la sucursal debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id de la sucursal debe ser diferente de NULO';
        }
        return $response;
    }

    function actualizarEstatusSocio($idUsuarioGym, $estatus, $idSucursal){

        if (is_numeric($idUsuarioGym) and $idUsuarioGym>0){
            if (is_numeric($estatus) and $estatus<2){
                if (is_numeric($idSucursal) and $idSucursal>0){
                    $socio = new Socio();

                    if ($estatus==0){
                         $response= $socio->modificarEstatusSocio($idUsuarioGym, $estatus,$idSucursal);
                    }
                    else{
                    //************************************************************************************************
                        //Mandamos a llamar el método para verificar cuantos lugares estan disponibles.
                        $sociosDisponibles=$socio->getSociosDisponibles($idSucursal);

                        if ($sociosDisponibles["success"]==0){
                            if ($sociosDisponibles["SociosDisponibles"]>0){
                                    $response= $socio->modificarEstatusSocio($idUsuarioGym, $estatus,$idSucursal);
                            }
                            else{
                                $response["success"]=13;
                                $response["message"]='Ha excedido el límite de socios el cual es de '.$sociosDisponibles["LimiteDeSocios"].' socios';
                            }

                        }
                        else
                            {
                                $response["success"]=14;
                                $response["message"]='Error al consultar los socios disponibles: '. $sociosDisponibles["message"];
                            }
                    }
                    //************************************************************************************************
                }
                else
                {
                    $response["success"]=7;
			        $response["message"]='El nuevo estatus no es un dato valido';
                }
            }
            else
            {
                $response["success"]=7;
			    $response["message"]='El nuevo estatus no es un dato valido';
            }
        }
        else
        {
            $response["success"]=6;
			$response["message"]='El Id del usuario gimnasio debe ser un dato valido';
        }


        return $response;
    }

    function actualizarSucursalSocio($idSocio, $idSucursal){
        if (is_numeric($idSocio) and $idSocio>0){
            if (is_numeric($idSucursal) and $idSucursal>0){
                    $socio = new Socio();
                    $response= $socio->actualizarSucursalSocio($idSocio, $idSucursal);
            }
            else
            {
                    $response["success"]=7;
			        $response["message"]='El id de la sucursal debe ser un dato valido';
            }

        }
        else
        {
            $response["success"]=6;
			$response["message"]='El Id del socio debe ser un dato valido';
        }

        return $response;
    }


    function getAvancesPesoPorEjercicio($idEjercicio){

    if ($idEjercicio!=NULL){  //Validamos que el id envíado sea diferente de NULO

        if ($idEjercicio!=0){
                $ejercicio = new Ejercicio();
                $response= $ejercicio->getAvancesPesoPorEjercicio($idEjercicio);

            }
            else
            {
                 $response["success"]=5;
			     $response["message"]='El id del ejercicio debe ser mayor a cero';
            }
        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id del ejercicio debe ser diferente de NULO';
        }
        return $response;
    }

	function getNotificaciones($idUsuario){

        if ($idUsuario!=NULL){  //Validamos que el id envíado sea diferente de NULO

            if (is_numeric($idUsuario)){
                $socio = new Socio();
                $response= $socio->getNotificaciones($idUsuario);
            }
            else
            {
            $response["success"]=5;
			$response["message"]='El id del usuario debe ser un dato numérico';
            }
        }
        else
        {
            $response["success"]=6;
			$response["message"]='El id del usuario debe ser diferente de NULO';
        }
        return $response;

    }

	switch ($metodoBl) {
		case "obtenerGimnasiosDeUsuario": // Mandar cero, para obtener todos los aparatos, o el id del aparatado especifico.
			$response=getUsuarioGymByIDU($idUsuarioBl);
		break;
		case "obtenerSocioByIdUIdG":
            $response=getSocioByIdUIdG($idUsuarioBl, $idGimnasioBl);
		break;
        case "obtenerRutinaBySocio":
            $response=getRutinaBySocio($idSocioBl);
		break;
        case "ObtenerSubrutinasByRutina":
            $response=getSubrutinasByRutina($idRutinaBl);
		break;
        case "ObtenerSubrutinasByIdU_IdGym":
            $response=getSubrutinasByIdUsuarioIdGym($idUsuarioBl, $idGimnasioBl);
		break;
        case "ObtenerSubrutinasByIdUIdGymCompleta":
            $response=getSubrutinasByIdUsuarioIdGymCompleta($idUsuarioBl, $idGimnasioBl);
		break;
        case "asociarUsuarioAGimnasio":
            $response=AsociarUsuarioAGym($idUsuarioBl, $idGimnasioBl, $idSucursalBl);
		break;
        case "obtenerSociosBySucursal":
            $response=ObtenerSociosBySucursal($idSucursalBl);
		break;
        case "actulizarPesoEnSerie":
            $response=actualizarPesoEnSerie($IdSerieBl,$PesoNuevoBl,$TipoPesoBl, $idEjercicioBl);
		break;
        case "actualizarEstatusSocio":
            $response=actualizarEstatusSocio($idUsuarioGymBl, $estatusBl, $idSucursalBl);
		break;

        case "actualizarSucursalSocio":
            $response=actualizarSucursalSocio($idSocioBl, $idSucursalBl);
		break;

        case "getAvancesPesoPorEjercicio":
            $response=getAvancesPesoPorEjercicio($idEjercicioBl);
		break;

        case "getNotificaciones":
            $response=getNotificaciones($idUsuarioBl);
		break;


		default:
		{
			$response["success"]=2;
			$response["message"]='El método indicado no se encuentra registrado';
		}

	}

    echo json_encode ($response,JSON_UNESCAPED_SLASHES)




?>
