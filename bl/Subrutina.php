<?php
require_once('conexion.php');

class Subrutina{


    function getsubrutinaByIdSubutina($idSubRutina){ // Esta función nos regresa la subrutina de una rutina especifica (dividida por días)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
                $sql= "SELECT SR_ID, Orden, IdRutina, Nombre FROM Subrutina where SR_ID=$idSubRutina;";

                if($result = mysqli_query($conexion, $sql))
                {
                    if($result!=null){
                        if ($result->num_rows>0){

                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["Id"]=$row["SR_ID"];
                                $item["Orden"]=$row["Orden"];
                                $item["IdRutina"]=$row["IdRutina"];
                                $item["Nombre"]=$row["Nombre"];


                                $response["subrutina"]=$item;
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontró la subrutina con el id indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontró la subrutina con el id indicado';
                        }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al ejecutar la consulta';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
	}


    //***********************************************************************************************************************************

    function saveSubrutina($idSubRutina, $Orden, $idRutina, $Nombre){ // Esta función nos regresa la subrutina de una rutina especifica (dividida por días)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                if ($idSubRutina==NULL or $idSubRutina==0 or $idSubRutina==''){
                    $sql="INSERT INTO `Subrutina` (`Orden`, `IdRutina`, `Nombre`) VALUES ('$Orden', '$idRutina', '$Nombre');";
                }
                else{
                   $sql="UPDATE `Subrutina` SET `Orden`='$Orden', `IdRutina`='$idRutina', `Nombre`='$Nombre' WHERE `SR_ID`='$idSubRutina';";
                }


                if($result = mysqli_query($conexion, $sql))
                {


                            if ($idSubRutina==NULL or $idSubRutina==0 or $idSubRutina==''){
                                $idSubRutina=mysqli_insert_id($conexion);
                            }

                            $response["subrutina"]=$this->getsubrutinaByIdSubutina($idSubRutina);

                            $response["success"]=0;
                            $response["message"]='Subrutina guardada correctamente';

                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al ejecutar la consulta';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
	}

    //***********************************************************************************************************************************

    function deleteSubrutina($idSubRutina)
    { // Esta función nos regresa la subrutina de una rutina especifica (dividida por días)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                $sql="DELETE FROM `Subrutina` WHERE `SR_ID`='$idSubRutina';";

                if($result = mysqli_query($conexion, $sql))
                {
                            $response["success"]=0;
                            $response["message"]='Subrutina eliminada correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al eliminar la subrutina';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
	}


    //***********************************************************************************************************************************

	function getsubrutinaByIdRutina($idRutina){ // Esta función nos regresa la subrutina de una rutina especifica (dividida por días)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
                $sql= "SELECT SR_ID, Orden, IdRutina, Nombre FROM Subrutina where idRutina=$idRutina order by Orden ";

                if($result = mysqli_query($conexion, $sql))
                {
                    if($result!=null){
                        if ($result->num_rows>0){

                            $response["Subrutinas"] = array();
                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["Id"]=$row["SR_ID"];
                                $item["Orden"]=$row["Orden"];
                                $item["IdRutina"]=$row["IdRutina"];
                                $item["Nombre"]=$row["Nombre"];


                                array_push($response["Subrutinas"], $item);
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='La rutina no cuenta con una subrutina definida';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='La rutina no cuenta con una subrutina definida';
                        }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
	}

        //***********************************************************************************************************************************

    function actualizarOrdenSubrutina($arregloSubrutinas){ // Esta función nos regresa la subrutina de una rutina especifica (dividida por días)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $registrosActualizados=0; // Se declara una variable para determinar cuantos registros se actualizaron
            $registrosNoActualizados=0;
            foreach ($arregloSubrutinas as $datosSubrutina) {
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
        }

		return ($response); //devolvemos el array
	}


    //***********************************************************************************************************************************
    //***********************************************************************************************************************************
    //***********************************************************************************************************************************
    //***********************************************************************************************************************************



    function getSubRutinaByIdIdUsuarioIdGym($idUsuario,$idGym){// Esta función nos regresa las diferentes subrutinas, de un socio especifico
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idUsuario!=0)
        {
            if ($idGym!=0){
                $sql= "SELECT SR_ID, Orden, IdRutina, Nombre
                        FROM Subrutina WHERE idRutina =
                            (SELECT R_ID FROM Rutina WHERE Estatus =1 AND id_Socio =
                                (SELECT UG_Id FROM UsuarioGimnasio JOIN Socio ON UG_Id = Id_UsuarioGym WHERE
                                UsuarioGimnasio.Estatus =1 AND Socio.Estatus =1 AND IdUsuario =$idUsuario AND IdGym =$idGym LIMIT 1 )
                            ORDER BY FechaInicio DESC  LIMIT 1 )
                        ORDER BY Orden
                        ";

                if($result = mysqli_query($conexion, $sql))
                {
                    if($result!=null){
                        if ($result->num_rows>0){

                            $response["subrutinas"] = array();
                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["Id"]=$row["SR_ID"];
                                $item["Orden"]=$row["Orden"];
                                $item["IdRutina"]=$row["IdRutina"];
                                $item["Nombre"]=$row["Nombre"];


                                array_push($response["subrutinas"], $item);
                            }
                            $response["success"]=1;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=0;
                            $response["message"]='No se encontró una rutina para el usuario y gimnasio indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=0;
                            $response["message"]='No se encontró una rutina para el usuario y gimnasio indicado';
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
                $response["message"]='El id del gimnasio debe ser diferente de cero';

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

    //***********************************************************************************************************************************

     function getSubRutinaByIdIdUIdGymCompleta($idUsuario,$idGym){// Esta función nos regresa las diferentes subrutinas, de un socio especifico
		//Creamos la conexión con la función anterior


		$conexion = obtenerConexion();

        if ($conexion)
        {


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idUsuario!=0)
        {
            if ($idGym!=0){
                $sql= "SELECT SR_ID, Orden, IdRutina, Nombre
                        FROM Subrutina WHERE idRutina =
                            (SELECT R_ID FROM Rutina WHERE Estatus =1 AND id_Socio =
                                (SELECT So_Id FROM UsuarioGimnasio JOIN Socio ON UG_Id = Id_UsuarioGym WHERE
                                UsuarioGimnasio.Estatus =1 AND Socio.Estatus =1 AND IdUsuario =$idUsuario AND IdGym =$idGym LIMIT 1 )
                            ORDER BY FechaInicio DESC  LIMIT 1 )
                        ORDER BY Orden
                        ";

                if($result = mysqli_query($conexion, $sql))
                {
                    if($result!=null){
                        if ($result->num_rows>0){

                            $response["subrutinas"] = array();
                            while($row = mysqli_fetch_array($result))
                            {
                                $item = array();
                                $item["Id"]=$row["SR_ID"];
                                $item["Orden"]=$row["Orden"];
                                $item["IdRutina"]=$row["IdRutina"];
                                $item["Nombre"]=$row["Nombre"];
                                $detalleSubrutina=$this->getDetalleSubrutina($item["Id"]);
                                $item["Ejercicios"]=$detalleSubrutina;
                                array_push($response["subrutinas"], $item);
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontró una rutina para el usuario y gimnasio indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontró una rutina para el usuario y gimnasio indicado';
                        }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al ejecutar la consulta';
                }

            }
            else
            {
                $response["success"]=5;
                $response["message"]='El id del gimnasio debe ser diferente de cero';

            }
        }
		else
		{
            $response["success"]=6;
            $response["message"]='El id del usuario debe ser diferente de cero';

		}
		desconectar($conexion); //desconectamos la base de datos
        }
         else
         {
          $response["success"]=3;
          $response["message"]='Se presentó un error en la conexión con la base de datos';
         }
		return ($response); //devolvemos el array

    }

    //***********************************************************************************************************************************

    function getSerieByEjercicioSubrutina($idEjercicio){
        //Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idEjercicio!=0)
		{
			$sql= "SELECT Sr_ID, NumeroSerie, ( SELECT ts.Nombre FROM TipoSerie ts WHERE ts.TSr_ID = s.id_TipoSerie ) AS TipoSerie,
                            Repeticiones, PesoPropuesto,
                            (SELECT Abreviatura FROM UnidadesPeso up WHERE up.UP_ID = s.TipoPeso ) AS TipoPeso, Observaciones FROM Serie s
                    WHERE id_SubrutinaEjercicio =$idEjercicio;";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["series"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Sr_ID"]=$row["Sr_ID"];

                            $item["NumeroSerie"]=$row["NumeroSerie"];
                            if ($item["NumeroSerie"]==NULL){$item["NumeroSerie"]=0;}

                            $item["TipoSerie"]=$row["TipoSerie"];
                            if ($item["TipoSerie"]==NULL){$item["TipoSerie"]='';}

                            $item["Repeticiones"]=$row["Repeticiones"];
                            if ($item["Repeticiones"]==NULL){$item["Repeticiones"]=0;}

                            $item["PesoPropuesto"]=$row["PesoPropuesto"];
                            if ($item["PesoPropuesto"]==NULL){$item["PesoPropuesto"]=0;}

                            $item["TipoPeso"]=$row["TipoPeso"];
                            if ($item["TipoPeso"]==NULL){$item["TipoPeso"]='';}

                            $item["Observaciones"]=$row["Observaciones"];
                            if ($item["Observaciones"]==NULL){$item["Observaciones"]='';}

                        array_push($response["series"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='El ejercicio no tiene series definidas';
                    }
            }
            else
            {
                $response["success"]=4;
                $response["message"]='Se presento un error al ejecutar la consulta';
            }

        }
		else
		{
                $response["success"]=5;
                $response["message"]='El id de la subrutina debe ser diferente de cero';
		}
		desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array
    }



    function getDetalleSubrutina ($idSubrutina){// Esta función nos regresa el detalle de ejercicios contenidos en una subrutina
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){



            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            if ($idSubrutina!=0)
            {

                    $sql= "(SELECT sec.SEC_ID as ID, sec.Orden, sec.Id_EjercicioCardio as IdEjercicio,
                            e.Explicacion as NombreEjercicio,
                            sc.Alias as AliasEjercicio,
                            e.CodigoImagen1,
                            e.CodigoImagen2,
                            e.ImagenGenerica1,
                            e.ImagenGenerica2,
                            e.TipoFuenteImagen,
                            0 as Circuito,
                            0 as TiempoDescansoEntreSerie,
                            0 as NumeroSeries,
                            0 as Repeticiones,
                            0 as PesoPropuesto,
                            0 AS UnidadPeso,
                            sec.TiempoTotal,
                            sec.VelocidadPromedio,
                            (select abreviatura from UnidadesVelocidad where UV_ID= sec.TipoDeVelocidad) as UnidadVelocidad,
                            sec.DistanciaTotal,
                            (select Abreviatura from UnidadesDistancia where UD_ID= sec.TipoDistancia) as UnidadDistancia,
                            sec.RitmoCardiaco, sec.Nivel, sec.Observaciones, 0 as TiempoDescansoEntreSerie,
                            e.ImagenUrl as ImagenUrl1, sc.ImagenUrl as ImagenUrl2, e.VideoUrl as VideoUrl1, sc.VideoUrl as VideoUrl2,
                            1 as TipoDeEjercicio
                        FROM SubRutinaEjercicioCardio sec JOIN SucursalEjercicioCardio sc on sec.Id_EjercicioCardio=sc.SEC_ID
                        join EjercicioCardio e on sc.Id_EjercicioCardio=e.EC_ID
                        where Id_Subrutina=$idSubrutina)
                        UNION ALL
                        (Select sep.SEP_ID as ID, sep.Orden, sep.Id_EjercicioPeso as IdEjercicio,
							p.Explicacion as NombreEjercicio,
                            sp.Alias as AliasEjercicio,
                            p.CodigoImagen1,
                            p.CodigoImagen2,
                            p.ImagenGenerica1,
                            p.ImagenGenerica2,
                            p.TipoFuenteImagen,
                            Circuito,
                            TiempoDescansoEntreSerie,
                            (SELECT COUNT(Sr_ID) FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as NumeroSeries,
                            (Select group_concat(Repeticiones) as Repeticiones FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as Repeticiones,
                            (Select group_concat(DISTINCT PesoPropuesto) as PesoPropuesto FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as PesoPropuesto,
                            (SELECT u.Abreviatura FROM Serie s join UnidadesPeso u ON s.TipoPeso=u.UP_ID where id_SubrutinaEjercicio=sep.SEP_ID LIMIT 1) AS UnidadPeso,
                            0 as TiempoTotal, 0 as VelocidadPromedio, 0 as UnidadVelocidad, 0 as DistanciaTotal, 0 as UnidadDistancia , 0 as RitmoCardiaco, 0 as Nivel, Observaciones, TiempoDescansoEntreSerie,
                            p.ImagenUrl as ImagenUrl1, sp.ImagenUrl as ImagenUrl2, p.VideoUrl as VideoUrl1, sp.VideoUrl as VideoUrl2,
                            2 as TipoDeEjercicio

                    from SubRutinaEjercicioPeso sep JOIN SucursalEjercicioPesa sp on sep.id_EjercicioPeso=sp.SEP_ID
                        join EjercicioPesa p on sp.id_EjercicioPesa=p.EP_ID
                    where Id_Subrutina=$idSubrutina
                    )
                    order by Orden";

                    if($result = mysqli_query($conexion, $sql))
                    {
                        if($result!=null){
                            if ($result->num_rows>0){

                                $response["ejercicios"] = array();
                                $bandera=TRUE;
                                while($row = mysqli_fetch_array($result))
                                {
                                    $item = array();
                                    $item["ID"]=$row["ID"];

                                    $item["Orden"]=$row["Orden"];
                                    if ($item["Orden"]==NULL){$item["Orden"]=0;}

                                    $item["IdEjercicio"]=$row["IdEjercicio"];
                                    if ($item["IdEjercicio"]==NULL){$item["IdEjercicio"]=0;}

                                    $item["NombreEjercicio"]=$row["NombreEjercicio"];
                                    if ($item["NombreEjercicio"]==NULL){$item["NombreEjercicio"]='';}

                                    $item["AliasEjercicio"]=$row["AliasEjercicio"];
                                    if ($item["AliasEjercicio"]==NULL){$item["AliasEjercicio"]='';}

                                    $item["CodigoImagen1"]=$row["CodigoImagen1"];
                                    if ($item["CodigoImagen1"]==NULL){$item["CodigoImagen1"]=0;}

                                    $item["CodigoImagen2"]=$row["CodigoImagen2"];
                                    if ($item["CodigoImagen2"]==NULL){$item["CodigoImagen2"]=0;}

                                    $item["ImagenGenerica1"]=$row["ImagenGenerica1"];
                                    if ($item["ImagenGenerica1"]==NULL){$item["ImagenGenerica1"]='';}

                                    $item["ImagenGenerica2"]=$row["ImagenGenerica2"];
                                    if ($item["ImagenGenerica2"]==NULL){$item["ImagenGenerica2"]='';}

                                    $item["TipoFuenteImagen"]=$row["TipoFuenteImagen"];
                                    if ($item["TipoFuenteImagen"]==NULL){$item["TipoFuenteImagen"]='';}

                                    $item["Circuito"]=$row["Circuito"];
                                    if ($item["Circuito"]==NULL){$item["Circuito"]=0;}

                                    //********************************************************
                                    if ($item["Circuito"]==0)
                                    {
                                        if ($bandera)
                                        {
                                            $item["CircuitoColor"]=0;
                                        }
                                        else
                                        {
                                            $item["CircuitoColor"]=1;
                                        }
                                        $bandera=!$bandera;
                                    }
                                    else
                                    {
                                        $item["CircuitoColor"]=$item["Circuito"]+1;
                                    }

                                    //********************************************************

                                    $item["TiempoDescansoEntreSerie"]=$row["TiempoDescansoEntreSerie"];
                                    if ($item["TiempoDescansoEntreSerie"]==NULL){$item["TiempoDescansoEntreSerie"]=0;}


                                    $item["NumeroSeries"]=$row["NumeroSeries"];
                                    if ($item["NumeroSeries"]==NULL){$item["NumeroSeries"]=0;}

                                    $item["Repeticiones"]=$row["Repeticiones"];
                                    if ($item["Repeticiones"]==NULL){$item["Repeticiones"]=0;}

                                    $item["PesoPropuesto"]=$row["PesoPropuesto"];
                                    if ($item["PesoPropuesto"]==NULL){$item["PesoPropuesto"]=0;}


                                    $item["UnidadPeso"]=$row["UnidadPeso"];
                                    if ($item["UnidadPeso"]==NULL){$item["UnidadPeso"]='';}


                                    $item["TiempoTotal"]=$row["TiempoTotal"];
                                    if ($item["TiempoTotal"]==NULL){$item["TiempoTotal"]=0;}

                                    $item["VelocidadPromedio"]=$row["VelocidadPromedio"];
                                    if ($item["VelocidadPromedio"]==NULL){$item["VelocidadPromedio"]=0;}

                                    $item["UnidadVelocidad"]=$row["UnidadVelocidad"];
                                    if ($item["UnidadVelocidad"]==NULL){$item["UnidadVelocidad"]='';}



                                    $item["DistanciaTotal"]=$row["DistanciaTotal"];
                                    if ($item["DistanciaTotal"]==NULL){$item["DistanciaTotal"]=0;}

                                    $item["UnidadDistancia"]=$row["UnidadDistancia"];
                                    if ($item["UnidadDistancia"]==NULL){$item["UnidadDistancia"]='';}



                                    $item["RitmoCardiaco"]=$row["RitmoCardiaco"];
                                    if ($item["RitmoCardiaco"]==NULL){$item["RitmoCardiaco"]=0;}

                                    $item["Nivel"]=$row["Nivel"];
                                    if ($item["Nivel"]==NULL){$item["Nivel"]=0;}

                                    $item["Observaciones"]=$row["Observaciones"];
                                    if ($item["Observaciones"]==NULL){$item["Observaciones"]='';}

                                    //********************************************************
                                    // 01/05/2016  Se realiza modificación, para agregar los valores de ImagenURL y videoURL

                                    if ($row["ImagenUrl2"]==NULL or $row["ImagenUrl2"]=='') //Verificamos si el valor de la URL de la imagen de la sucursal es igual a nulo o cadena vacia, regresamos la imagen genérica del ejercicio
                                        {
                                            $item["ImagenUrl"]=$row["ImagenUrl1"];
                                            if ($item["ImagenUrl"]==NULL){$item["ImagenUrl"]='';}
                                        }
                                    else{
                                            $item["ImagenUrl"]=$row["ImagenUrl2"];
                                        }


                                    if ($row["VideoUrl2"]==NULL or $row["VideoUrl2"]=='') //Verificamos si el valor de la URL de la imagen de la sucursal es igual a nulo o cadena vacia, regresamos la imagen genérica del ejercicio
                                        {
                                            $item["VideoUrl"]=$row["VideoUrl1"];
                                            if ($item["VideoUrl"]==NULL){$item["VideoUrl"]='';}
                                        }
                                        else{
                                            $item["VideoUrl"]=$row["VideoUrl2"];
                                        }

                                    // *******************************************************


                                    $item["TipoDeEjercicio"]=$row["TipoDeEjercicio"];
                                    if ($item["TipoDeEjercicio"]==NULL){$item["TipoDeEjercicio"]=0;}


                                    //****************************************************

                                    if ($item["TipoDeEjercicio"]==2){ //Si es un ejercicio de pesas, hay que agregar las series
                                        $Series=array();
                                        $Series=$this->getSerieByEjercicioSubrutina( $item["ID"]);
                                        $item["Series"]=$Series;
                                    }

                                    //****************************************************
                                    array_push($response["ejercicios"], $item);
                                }
                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontró detalle de la subrutina indicada';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontró detalle de la subrutina indicada';
                            }
                    }
                    else
                    {
                        $response["success"]=4;
                        $response["message"]='Se presento un error al ejecutar la consulta';
                    }

            }
            else
            {
                $response["success"]=5;
                $response["message"]='El id de la subrutina debe ser diferente de cero';

            }
            desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array

    }


    function getDetalleEjercicioByID ($idEjercicio,$circuitoColor){// Esta función nos regresa el detalle de ejercicios contenidos en una subrutina
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            if ($idEjercicio!=0)
            {

                    $sql= "(Select sep.SEP_ID as ID, sep.Orden, sep.Id_EjercicioPeso as IdEjercicio,
							p.Explicacion as NombreEjercicio,
                            sp.Alias as AliasEjercicio,
                            p.CodigoImagen1,
                            p.CodigoImagen2,
                            p.ImagenGenerica1,
                            p.ImagenGenerica2,
                            p.TipoFuenteImagen,
                            Circuito,
                            TiempoDescansoEntreSerie,
                            (SELECT COUNT(Sr_ID) FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as NumeroSeries,
                            (Select group_concat(Repeticiones) as Repeticiones FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as Repeticiones,
                            (Select group_concat(DISTINCT PesoPropuesto) as PesoPropuesto FROM Serie where id_SubrutinaEjercicio=sep.SEP_ID) as PesoPropuesto,
                            (SELECT u.Abreviatura FROM Serie s join UnidadesPeso u ON s.TipoPeso=u.UP_ID where id_SubrutinaEjercicio=sep.SEP_ID LIMIT 1) AS UnidadPeso,
                            0 as TiempoTotal, 0 as VelocidadPromedio, 0 as UnidadVelocidad, 0 as DistanciaTotal, 0 as UnidadDistancia , 0 as RitmoCardiaco, 0 as Nivel, Observaciones, TiempoDescansoEntreSerie,
                            p.ImagenUrl as ImagenUrl1, sp.ImagenUrl as ImagenUrl2, p.VideoUrl as VideoUrl1, sp.VideoUrl as VideoUrl2,
                            2 as TipoDeEjercicio

                    from SubRutinaEjercicioPeso sep JOIN SucursalEjercicioPesa sp on sep.id_EjercicioPeso=sp.SEP_ID
                        join EjercicioPesa p on sp.id_EjercicioPesa=p.EP_ID
                    where sep.SEP_ID=$idEjercicio
                    )";

                    if($result = mysqli_query($conexion, $sql))
                    {
                        if($result!=null){
                            if ($result->num_rows>0){

                                while($row = mysqli_fetch_array($result))
                                {
                                    $item = array();
                                    $item["ID"]=$row["ID"];

                                    $item["Orden"]=$row["Orden"];
                                    if ($item["Orden"]==NULL){$item["Orden"]=0;}

                                    $item["IdEjercicio"]=$row["IdEjercicio"];
                                    if ($item["IdEjercicio"]==NULL){$item["IdEjercicio"]=0;}

                                    $item["NombreEjercicio"]=$row["NombreEjercicio"];
                                    if ($item["NombreEjercicio"]==NULL){$item["NombreEjercicio"]='';}

                                    $item["AliasEjercicio"]=$row["AliasEjercicio"];
                                    if ($item["AliasEjercicio"]==NULL){$item["AliasEjercicio"]='';}

                                    $item["CodigoImagen1"]=$row["CodigoImagen1"];
                                    if ($item["CodigoImagen1"]==NULL){$item["CodigoImagen1"]=0;}

                                    $item["CodigoImagen2"]=$row["CodigoImagen2"];
                                    if ($item["CodigoImagen2"]==NULL){$item["CodigoImagen2"]=0;}

                                    $item["ImagenGenerica1"]=$row["ImagenGenerica1"];
                                    if ($item["ImagenGenerica1"]==NULL){$item["ImagenGenerica1"]='';}

                                    $item["ImagenGenerica2"]=$row["ImagenGenerica2"];
                                    if ($item["ImagenGenerica2"]==NULL){$item["ImagenGenerica2"]='';}

                                    $item["TipoFuenteImagen"]=$row["TipoFuenteImagen"];
                                    if ($item["TipoFuenteImagen"]==NULL){$item["TipoFuenteImagen"]='';}

                                    $item["Circuito"]=$row["Circuito"];
                                    if ($item["Circuito"]==NULL){$item["Circuito"]=0;}

                                    //********************************************************

                                    $item["CircuitoColor"]=$circuitoColor;

                                    //********************************************************

                                    $item["TiempoDescansoEntreSerie"]=$row["TiempoDescansoEntreSerie"];
                                    if ($item["TiempoDescansoEntreSerie"]==NULL){$item["TiempoDescansoEntreSerie"]=0;}


                                    $item["NumeroSeries"]=$row["NumeroSeries"];
                                    if ($item["NumeroSeries"]==NULL){$item["NumeroSeries"]=0;}

                                    $item["Repeticiones"]=$row["Repeticiones"];
                                    if ($item["Repeticiones"]==NULL){$item["Repeticiones"]=0;}

                                    $item["PesoPropuesto"]=$row["PesoPropuesto"];
                                    if ($item["PesoPropuesto"]==NULL){$item["PesoPropuesto"]=0;}


                                    $item["UnidadPeso"]=$row["UnidadPeso"];
                                    if ($item["UnidadPeso"]==NULL){$item["UnidadPeso"]='';}


                                    $item["TiempoTotal"]=$row["TiempoTotal"];


                                    $item["VelocidadPromedio"]=$row["VelocidadPromedio"];


                                    $item["UnidadVelocidad"]=$row["UnidadVelocidad"];


                                    $item["DistanciaTotal"]=$row["DistanciaTotal"];

                                    $item["UnidadDistancia"]=$row["UnidadDistancia"];

                                    $item["RitmoCardiaco"]=$row["RitmoCardiaco"];

                                    $item["Nivel"]=$row["Nivel"];

                                    $item["Observaciones"]=$row["Observaciones"];
                                    if ($item["Observaciones"]==NULL){$item["Observaciones"]='';}


                                    //********************************************************
                                    // 01/05/2016  Se realiza modificación, para agregar los valores de ImagenURL y videoURL

                                    if ($row["ImagenUrl2"]==NULL or $row["ImagenUrl2"]=='') //Verificamos si el valor de la URL de la imagen de la sucursal es igual a nulo o cadena vacia, regresamos la imagen genérica del ejercicio
                                        {
                                            $item["ImagenUrl"]=$row["ImagenUrl1"];
                                            if ($item["ImagenUrl"]==NULL){$item["ImagenUrl"]='';}
                                        }
                                    else{
                                            $item["ImagenUrl"]=$row["ImagenUrl2"];
                                        }


                                    if ($row["VideoUrl2"]==NULL or $row["VideoUrl2"]=='') //Verificamos si el valor de la URL de la imagen de la sucursal es igual a nulo o cadena vacia, regresamos la imagen genérica del ejercicio
                                        {
                                            $item["VideoUrl"]=$row["VideoUrl1"];
                                            if ($item["VideoUrl"]==NULL){$item["VideoUrl"]='';}
                                        }
                                        else{
                                            $item["VideoUrl"]=$row["VideoUrl2"];
                                        }

                                    // *******************************************************


                                    $item["TipoDeEjercicio"]=$row["TipoDeEjercicio"];
                                    //****************************************************

                                    if ($item["TipoDeEjercicio"]==2){ //Si es un ejercicio de pesas, hay que agregar las
                                        $Series=array();
                                        $Series=$this->getSerieByEjercicioSubrutina( $item["ID"]);
                                        $item["Series"]=$Series;
                                    }

                                    //****************************************************
                                    $response["ejercicio"]= $item;
                                }
                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontró detalle de la subrutina indicada';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontró detalle de la subrutina indicada';
                            }
                    }
                    else
                    {
                        $response["success"]=4;
                        $response["message"]='Se presento un error al ejecutar la consulta';
                    }

            }
            else
            {
                $response["success"]=5;
                $response["message"]='El id de la subrutina debe ser diferente de cero';

            }
            desconectar($conexion); //desconectamos la base de datos
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array

    }





}

/*     $Rutina = new Subrutina();

    $t1 = array("Id"=>"1", "Orden"=>"11");
    $t2 = array("Id"=>"2", "Orden"=>"22");
    $t3 = array("Id"=>"3", "Orden"=>"33");
    $t4 = array("Id"=>"4", "Orden"=>"44");

    $arreglo=array ($t1,$t2,$t3,$t4);
    $RutinaR=$Rutina->actualizarOrdenSubrutina($arreglo);
    echo json_encode ($RutinaR);*/


?>
