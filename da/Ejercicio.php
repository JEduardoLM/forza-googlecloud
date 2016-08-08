<?php
require_once('conexion.php');

class Ejercicio{

    function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    function getSucursalEjerciciosBySucursal ($idSucursal){// Esta función nos regresa todos los ejercicios que se encuentran dados de alta en una sucursal

		$conexion = obtenerConexion(); //Creamos la conexión a la base de datos, para poder acceder a la información

        if ($conexion){ //Verificamos que la conexión se haya realizado correctamente

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $sql= "( SELECT SEC_ID as ID,1 as TipoEjercicio, Alias as NombreEnSucursal, Explicacion as NombreGenerico, CodigoImagen2, ImagenGenerica2,
		                    (Select Nombre from aparato where A_ID=ec.Id_Aparato) as Aparato,
                            '' as Musculos
                     FROM sucursalejerciciocardio sec join ejerciciocardio ec on sec.Id_EjercicioCardio=ec.EC_ID where Id_Sucursal=1)
                 UNION
                 ( SELECT SEP_ID as ID, 2 as TipoEjercicio, Alias as NombreEnSucursal, Explicacion as NombreGenerico, CodigoImagen2, ImagenGenerica2,
		                  (Select Nombre from aparato where A_ID=ep.Id_Aparato) as Aparato,
		                  (Select  group_concat(distinct Nombre) as Musculos from musculo M join musculoejerciciopesa MEP on M.M_ID=MEP.idMusculo  where idEjercicioPesa=EP_ID and EsGrupoMuscular=1) as Musculos
                    FROM sucursalejerciciopesa sep join ejerciciopesa ep on sep.Id_EjercicioPesa=ep.EP_ID where Id_Sucursal=1);";

                    if($result = mysqli_query($conexion, $sql)) //Verificamos que la conexión se haya realizado correctamente
                    {
                        if($result!=null){
                            if ($result->num_rows>0){

                                $response["SucursalEjercicio"]=array();
                                while($row = mysqli_fetch_array($result))
                                {
                                    $item = array();
                                    $item["Id"]=$row["ID"];

                                    $item["TipoEjercicio"]=$row["TipoEjercicio"];

                                    //*******************************************************************************************
                                    // Verificamos si cuenta con un Alias el ejercicio, si cuenta con Alias, regresamos el Alias asignado en la sucursal, sino se regresa el nombre genérico
                                    if ($row["NombreEnSucursal"]==NULL or $row["NombreEnSucursal"]==''){
                                        $item["NombreEjercicio"]=$row["NombreGenerico"];
                                        if ($item["NombreEjercicio"]==NULL){$item["NombreEjercicio"]='';}
                                    }
                                    else{
                                        $item["NombreEjercicio"]=$row["NombreEnSucursal"];
                                    }

                                    //*******************************************************************************************

                                    $item["CodigoImagen"]=$row["CodigoImagen2"];
                                    if ($item["CodigoImagen"]==NULL){$item["CodigoImagen"]=0;}

                                    $item["UrlImagen"]=$row["ImagenGenerica2"];
                                    if ($item["UrlImagen"]==NULL){$item["UrlImagen"]='';}

                                    $item["Aparato"]=$row["Aparato"];
                                    if ($item["Aparato"]==NULL){$item["Aparato"]='';}

                                    $item["Musculos"]=$row["Musculos"];
                                    if ($item["Musculos"]==NULL){$item["Musculos"]='';}



                                    array_push($response["SucursalEjercicio"], $item);

                                }
                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontraron ejercicios para la sucursal indicada';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontraron ejercicios para la sucursal indicada';
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
		return ($response); //devolvemos el array

    }

    function insertEjerciciosSubrutina($arregloEjercicios, $idSubrutina){ // Esta función recibe un arreglo de ejercicios y los almacena en la base dedatos
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $registrosActualizados=0; // Se declara una variable para determinar cuantos registros se actualizaron
            $registrosNoActualizados=0;
            // $idSubrutina=0;

            foreach ($arregloEjercicios as $datosEjercicio) {

                $tipoEjercicio=$datosEjercicio["TipoEjercicio"];
                // $idSubrutina = $datosEjercicio["IdSubrutina"];
                $idEjercicio = $datosEjercicio["IdEjercicio"];




                $orden = $datosEjercicio["Orden"];

                if ($tipoEjercicio==1){  // Si el tipo de ejercicio es 1, significa que es Cardio
                    $sql="INSERT INTO `subrutinaejerciciocardio` (`Id_Subrutina`, `Id_EjercicioCardio`, `Orden`) VALUES ($idSubrutina, $idEjercicio, $orden);";

                }
                else{ //Si no es ejercicio de cardio, es ejercicio de Pesas
                        $sql=" INSERT INTO `subrutinaejerciciopeso` (`Id_Subrutina`, `Id_EjercicioPeso`, `Circuito`, `TiempoDescansoEntreSerie`, `Orden`)
                        VALUES ($idSubrutina, $idEjercicio, 0 , 0, $orden);";
                }



                if($result = mysqli_query($conexion, $sql))
                {
                    $registrosActualizados=$registrosActualizados+1;
                }
                else
                {
                    $registrosNoActualizados=$registrosNoActualizados+1;
                }

            }

            $response["getEjercicios"]=$this->getEjerciciosBySubrutina($idSubrutina);

              $response["success"]=0;
              $response["message"]=''.$registrosActualizados.' registros actualizados, y '.$registrosNoActualizados.' no actualizados';
            if ($registrosNoActualizados>0){$response["success"]=1;}
              desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }


		return ($response); //devolvemos el array
	}

    function actualizarOrdenCircuito($arregloEjercicios,$idSubrutina){ // Esta función recibe un arreglo de ejercicios y los almacena en la base dedatos
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();


        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $registrosActualizados=0; // Se declara una variable para determinar cuantos registros se actualizaron
            $registrosNoActualizados=0;
            // $idSubrutina=0;

            foreach ($arregloEjercicios as $datosEjercicio) {

                $tipoEjercicio=$datosEjercicio["TipoEjercicio"];
                $idEjercicioCardio=$datosEjercicio["IdEjercicioSubrutina"];
                $circuito = $datosEjercicio["Circuito"];
                $orden = $datosEjercicio["Orden"];
                // $idSubrutina = $datosEjercicio["IdSubrutina"];

                if ($tipoEjercicio==1){  // Si el tipo de ejercicio es 1, significa que es Cardio
                    $sql="UPDATE `subrutinaejerciciocardio` SET `Circuito`=$circuito, `Orden`=$orden WHERE `SEC_ID`=$idEjercicioCardio;";
                }
                else{ //Si no es ejercicio de cardio, es ejercicio de Pesas
                    $sql="UPDATE `subrutinaejerciciopeso` SET `Circuito`=$circuito, `Orden`=$orden WHERE `SEP_ID`=$idEjercicioCardio;";
                }



                if($result = mysqli_query($conexion, $sql))
                {
                    $registrosActualizados=$registrosActualizados+1;
                }
                else
                {
                    $registrosNoActualizados=$registrosNoActualizados+1;
                }

            }

             $response["getEjercicios"]=$this->getEjerciciosBySubrutina($idSubrutina);
              $response["success"]=0;
              $response["message"]=''.$registrosActualizados.' registros actualizados, y '.$registrosNoActualizados.' no actualizados';
              desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }


		return ($response); //devolvemos el array
	}

    function getEjercicioById  ($idEjercicio, $tipo){// Esta función nos regresa el detalle de ejercicios contenidos en una subrutina (getDetalleSubrutina en Subrutina.php)
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos si la conexión se realizó correctamente

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            if ($idEjercicio!=0)
            { //Verificamos que el id del ejercicio sea diferente de cero

                if ($tipo===1){
                      $sql= "(SELECT sec.SEC_ID as ID, sec.Orden, sec.Id_EjercicioCardio as IdEjercicio,
                            e.Explicacion as NombreEjercicio,
                            sc.Alias as AliasEjercicio,
                            e.CodigoImagen1,
                            e.CodigoImagen2,
                            e.ImagenGenerica1,
                            e.ImagenGenerica2,
                            e.TipoFuenteImagen,
                            Circuito,
                            0 as TiempoDescansoEntreSerie,
                            0 as NumeroSeries,
                            0 as Repeticiones,
                            0 as PesoPropuesto,
                            0 AS UnidadPeso,
                            sec.TiempoTotal,
                            sec.VelocidadPromedio,
                            (select abreviatura from unidadesvelocidad where UV_ID= sec.TipoDeVelocidad) as UnidadVelocidad,
                            sec.DistanciaTotal,
                            (select Abreviatura from unidadesdistancia where UD_ID= sec.TipoDistancia) as UnidadDistancia,
                            sec.RitmoCardiaco, sec.Nivel, sec.Observaciones, 0 as TiempoDescansoEntreSerie,
                            e.ImagenUrl as ImagenUrl1, sc.ImagenUrl as ImagenUrl2, e.VideoUrl as VideoUrl1, sc.VideoUrl as VideoUrl2,
                            1 as TipoDeEjercicio
                        FROM subrutinaejerciciocardio sec JOIN sucursalejerciciocardio sc on sec.Id_EjercicioCardio=sc.SEC_ID
                        join ejerciciocardio e on sc.Id_EjercicioCardio=e.EC_ID
                        where sec.SEC_ID=$idEjercicio);";
                    }
                    else{


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
                                (SELECT COUNT(Sr_ID) FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as NumeroSeries,
                                (Select group_concat(Repeticiones) as Repeticiones FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as Repeticiones,
                                (Select group_concat(DISTINCT PesoPropuesto) as PesoPropuesto FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as PesoPropuesto,
                                (SELECT u.Abreviatura FROM serie s join unidadespeso u ON s.TipoPeso=u.UP_ID where id_SubrutinaEjercicio=sep.SEP_ID LIMIT 1) AS UnidadPeso,
                                0 as TiempoTotal, 0 as VelocidadPromedio, 0 as UnidadVelocidad, 0 as DistanciaTotal, 0 as UnidadDistancia , 0 as RitmoCardiaco, 0 as Nivel, Observaciones, TiempoDescansoEntreSerie,
                                p.ImagenUrl as ImagenUrl1, sp.ImagenUrl as ImagenUrl2, p.VideoUrl as VideoUrl1, sp.VideoUrl as VideoUrl2,
                                2 as TipoDeEjercicio

                        from subrutinaejerciciopeso sep JOIN sucursalejerciciopesa sp on sep.id_EjercicioPeso=sp.SEP_ID
                            join ejerciciopesa p on sp.id_EjercicioPesa=p.EP_ID
                        where sep.SEP_ID=$idEjercicio)";
                    }

                    if($result = mysqli_query($conexion, $sql))
                    {
                        if($result!=null){
                            if ($result->num_rows>0){

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




                                    if ($item["TipoDeEjercicio"]==2){ //Si es un ejercicio de pesas, hay que agregar las series
                                        $Series=array();

                                        $Series=$this->getSerieByEjercicioSubrutina( $item["ID"]);
                                        $item["Series"]=$Series;
                                    }


                                   $response["ejercicio"]=$item;
                                }
                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontró ejercicio con el id indicado';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontró ejercicio con el id indicado';
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
                $response["message"]='El id del ejercicio debe ser diferente de cero';

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

    function getSerieByEjercicioSubrutina($idEjercicio){
        //Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){


		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idEjercicio!=0)
		{
			$sql= "SELECT Sr_ID, NumeroSerie, ( SELECT ts.Nombre FROM tiposerie ts WHERE ts.TSr_ID = s.id_TipoSerie ) AS TipoSerie,
                            Repeticiones, PesoPropuesto,
                            (SELECT Abreviatura FROM unidadespeso up WHERE up.UP_ID = s.TipoPeso ) AS TipoPeso, Observaciones FROM serie s
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

    function getEjerciciosBySubrutina  ($idSubrutina){// Esta función nos regresa el detalle de ejercicios contenidos en una subrutina (getDetalleSubrutina en Subrutina.php)
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
                            Circuito,
                            0 as TiempoDescansoEntreSerie,
                            0 as NumeroSeries,
                            0 as Repeticiones,
                            0 as PesoPropuesto,
                            0 AS UnidadPeso,
                            sec.TiempoTotal,
                            sec.VelocidadPromedio,
                            (select abreviatura from unidadesvelocidad where UV_ID= sec.TipoDeVelocidad) as UnidadVelocidad,
                            sec.DistanciaTotal,
                            (select Abreviatura from unidadesdistancia where UD_ID= sec.TipoDistancia) as UnidadDistancia,
                            sec.RitmoCardiaco, sec.Nivel, sec.Observaciones, 0 as TiempoDescansoEntreSerie,
                            e.ImagenUrl as ImagenUrl1, sc.ImagenUrl as ImagenUrl2, e.VideoUrl as VideoUrl1, sc.VideoUrl as VideoUrl2,
                            1 as TipoDeEjercicio
                        FROM subrutinaejerciciocardio sec JOIN sucursalejerciciocardio sc on sec.Id_EjercicioCardio=sc.SEC_ID
                        join ejerciciocardio e on sc.Id_EjercicioCardio=e.EC_ID
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
                            (SELECT COUNT(Sr_ID) FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as NumeroSeries,
                            (Select group_concat(Repeticiones) as Repeticiones FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as Repeticiones,
                            (Select group_concat(DISTINCT PesoPropuesto) as PesoPropuesto FROM serie where id_SubrutinaEjercicio=sep.SEP_ID) as PesoPropuesto,
                            (SELECT u.Abreviatura FROM serie s join unidadespeso u ON s.TipoPeso=u.UP_ID where id_SubrutinaEjercicio=sep.SEP_ID LIMIT 1) AS UnidadPeso,
                            0 as TiempoTotal, 0 as VelocidadPromedio, 0 as UnidadVelocidad, 0 as DistanciaTotal, 0 as UnidadDistancia , 0 as RitmoCardiaco, 0 as Nivel, Observaciones, TiempoDescansoEntreSerie,
                            p.ImagenUrl as ImagenUrl1, sp.ImagenUrl as ImagenUrl2, p.VideoUrl as VideoUrl1, sp.VideoUrl as VideoUrl2,
                            2 as TipoDeEjercicio

                    from subrutinaejerciciopeso sep JOIN sucursalejerciciopesa sp on sep.id_EjercicioPeso=sp.SEP_ID
                        join ejerciciopesa p on sp.id_EjercicioPesa=p.EP_ID
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




                                    if ($item["TipoDeEjercicio"]==2){ //Si es un ejercicio de pesas, hay que agregar las series
                                        $Series=array();
                                        $Series=$this->getSerieByEjercicioSubrutina( $item["ID"]);
                                        $item["Series"]=$Series;
                                    }


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

    function deleteEjercicio ($idEjercicio,$idTipo,$idSubrutina, $Orden){
        // Esta función nos permite eliminar un ejercicio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                if ($idTipo==1){
                    $sql="DELETE FROM `subrutinaejerciciocardio` WHERE `SEC_ID`=$idEjercicio;";
                } else
                {
                    $sql="DELETE FROM `subrutinaejerciciopeso` WHERE `SEP_ID`=$idEjercicio;";
                }


                if($result = mysqli_query($conexion, $sql))
                {
                            $response["ejerciciosReordenados"]=$this->reordenarEjercicios($idSubrutina, $Orden);
                            $response["success"]=0;
                            $response["message"]='Ejercicio eliminado correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al eliminar la subrutina';
                }

            desconectar($conexion); //desconectamos la base de datos
            if ($response["success"]===0){
                $response["getEjercicios"]=$this->getEjerciciosBySubrutina($idSubrutina);
            }
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    function reordenarEjercicios($idSubrutina, $Orden){
        // Esta función nos permite reordenar los ejercicios de una rutina en particular, cuando se haya eliminado un ejercicio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                $sql="UPDATE `subrutinaejerciciocardio` SET `Orden`=(Orden-1) WHERE  Id_Subrutina=$idSubrutina and Orden >$Orden;";

                if($result = mysqli_query($conexion, $sql))
                {
                    $sql="UPDATE `subrutinaejerciciopeso` SET `Orden`=(Orden-1) WHERE  Id_Subrutina=$idSubrutina and Orden >$Orden;";

                    if($result = mysqli_query($conexion, $sql))
                    {

                                $response["success"]=0;
                                $response["message"]='Ejercicios actualizados correctamente';
                    }
                    else
                    {
                        $response["success"]=4;
                        $response["message"]='Se presentó un error al actualizar los ejercicios de peso';
                    }
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al actualizar los ejercicios de cardio';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    function actualizarObservaciones($idEjercicio,$idTipo, $Observaciones){
        // Esta función nos permite actualizar las observaciones de un ejercicio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

                if ($idTipo==1){
                    $sql="UPDATE `subrutinaejerciciocardio` SET `Observaciones`='$Observaciones' WHERE `SEC_ID`=$idEjercicio;";
                } else
                {
                    $sql="UPDATE `subrutinaejerciciopeso` SET `Observaciones`='$Observaciones' WHERE `SEP_ID`=$idEjercicio;";
                }


                if($result = mysqli_query($conexion, $sql))
                {
                            $response["getEjercicio"]=$this->getEjercicioByID($idEjercicio,2);
                            $response["success"]=0;
                            $response["message"]='Ejercicio actualizado correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al actualizar las observaciones del ejercicio';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    function actualizarInformaciónCardio($idEjercicio,$tiempoTotal, $velocidadPromedio, $tipoVelocidad, $distanciaTotal, $tipoDistancia, $ritmoCardiaco, $nivel, $observaciones){
        // Esta función nos permite actualizar las observaciones de un ejercicio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8



                  $sql="UPDATE `subrutinaejerciciocardio` SET
                        `Tiempototal`='$tiempoTotal', `Velocidadpromedio`='$velocidadPromedio', `TipoDeVelocidad`='$tipoVelocidad', `DistanciaTotal`='$distanciaTotal', `TipoDistancia`='$tipoDistancia', `Ritmocardiaco`='$ritmoCardiaco', `Nivel`='$nivel',  `Observaciones`='$observaciones'
                        WHERE `SEC_ID`=$idEjercicio;";




                if($result = mysqli_query($conexion, $sql))
                {
                            $response["getEjercicio"]=$this->getEjercicioByID($idEjercicio,1);
                            $response["success"]=0;
                            $response["message"]='Ejercicio actualizado correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presentó un error al actualizar las el ejercicio';
                }

            desconectar($conexion); //desconectamos la base de datos
            }
        else{
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión con la base de datos';
        }

		return ($response); //devolvemos el array
    }

    function getAvancesPesoPorEjercicio($idEjercicio){
        //Esta función nos permite obtener los avances de peso, de cada ejercicio, que el socio ha capturado a lo largo de su rutina de entrenamiento
        //Se muestran los valores máximos capturados en cada día

		$conexion = obtenerConexion(); //Creamos la conexión a la base de datos, para poder acceder a la información

        if ($conexion){ //Verificamos que la conexión se haya realizado correctamente

            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            $sql= " Select  PA_ID, Peso, TipoPeso, id_Serie, Fecha,  from_unixtime(Fecha,'%y%m%d') as FechaCorta
		                  from pesoavances where id_Serie in (Select Sr_ID from serie where id_SubrutinaEjercicio=$idEjercicio) order By id_Serie, Fecha desc;";

                    if($result = mysqli_query($conexion, $sql)) //Verificamos que la conexión se haya realizado correctamente
                    {
                        if($result!=null){
                            if ($result->num_rows>0){

                                $response["AvanceDePeso"]=array();

                                $idSerie=0;
                                $fecha=0;

                                $i=0; //Creo un indice para recorrer el arreglo y obtener el objeto de la posición anterior
                                $arregloDeAvances=array();
                                $registro=array();
                                while($row = mysqli_fetch_array($result))
                                {
                                    $item = array();
                                    // Lo primero que haremos, será depurar los datos que se hayan ingresado por error, y que se hayan corregido en el mismo día.
                                    // Para depurar los datos erroneos, vamos a quedarnos sólo con el último registro de cada Fecha-Día

                                    $item["PA_ID"]=$row["PA_ID"];
                                    $item["id_Serie"]=$row["id_Serie"];
                                    if ($row["TipoPeso"]==1){
                                            $item["PesoKg"]=$row["Peso"];
                                            $item["PesoLbs"]=$item["PesoKg"]*2.205;
                                        }
                                        else{
                                            $item["PesoKg"]=$row["Peso"]* 0.454;
                                            $item["PesoLbs"]=$row["Peso"];
                                        }

                                        $item["Fecha"]=$row["Fecha"];
                                        $item["FechaCorta"]=$row["FechaCorta"];
                                       array_push($registro,$item);


                                    if ($i>0 and ($idSerie!=$row["id_Serie"] or $fecha!=$row["FechaCorta"])){

                                            array_push($arregloDeAvances, $registro[$i-1]);

                                    }

                                            $idSerie=$row["id_Serie"];
                                            $fecha=$row["FechaCorta"];
                                            $i=$i+1;


                                }
                                array_push($arregloDeAvances, $item);

                                // Una vez que se han depurado las correcciones de pesos (pesos registrados en la misma fecha, para el mismo día), procederemos a ordenar el arreglo por FECHA, de tal manera
                                // que podamos obtener el peso mayor  y menor de cada fecha
                                $arregloDeAvances=$this->array_sort($arregloDeAvances,"Fecha", SORT_ASC);

                                $pesoMayorKg=0;
                                $pesoMayorLb=0;
                                $idSeriePesoMayor=0;
                                $arregloDeAvances2= array();

                                // Procederemos a crear un nuevo arreglo, de tal manera que vayamos extrayendo cada fecha en que se modificó el peso, e ir calculando cuanto fue el peso máximo y minumo que se cargó en cada pesa.
                                $fecha=0;
                                $fechaLarga=0;
                                $registroFecha=array();
                                $j=0;
                                $item2 = array();
                                foreach ($arregloDeAvances as $registroDePeso) {

                                    // Vamos a armar un arreglo, con los siguientes valores: Fecha, y el valor máximo de cada Fecha

                                    if ($j>0){
                                        if ($registroDePeso["FechaCorta"]!=$fecha){
                                            $item2["Fecha"]=$fechaLarga;
                                            $item2["PesoKg"]=$pesoMayorKg;
                                            $item2["PesoLbs"]=$pesoMayorLb;
                                            array_push($arregloDeAvances2, $item2);
                                        }

                                    }


                                    //Si se tiene un peso Mayor, entonces procedemos a registrarlo
                                    if (($pesoMayorKg<$registroDePeso["PesoKg"]) or ($idSeriePesoMayor==$registroDePeso["id_Serie"])){
                                        $pesoMayorKg=$registroDePeso["PesoKg"];
                                        $pesoMayorLb=$registroDePeso["PesoLbs"];
                                        $idSeriePesoMayor=$registroDePeso["id_Serie"];
                                    }



                                    $fecha=$registroDePeso["FechaCorta"];
                                    $fechaLarga=$registroDePeso["Fecha"];
                                    $j=$j+1;

                                }

                                $item2["Fecha"]=$fechaLarga;
                                $item2["PesoKg"]=$pesoMayorKg;
                                $item2["PesoLbs"]=$pesoMayorLb;
                                array_push($arregloDeAvances2, $item2);

                                $fecha = new DateTime();
                                $hoy = $fecha->getTimestamp();

                                $item2["Fecha"]=$hoy;
                                $item2["PesoKg"]=$pesoMayorKg;
                                $item2["PesoLbs"]=$pesoMayorLb;
                                array_push($arregloDeAvances2, $item2);




                                $response["success"]=0;
                                $response["message"]='Consulta exitosa';
                                $response["AvanceDePeso"]=$arregloDeAvances2;
                            }
                            else{
                                $response["success"]=1;
                                $response["message"]='No se encontraron registros de peso, para el ejercicio seleccionado';
                            }

                        }
                        else
                            {
                                $response["success"]=1;
                                $response["message"]='No se encontraron registros de peso, para el ejercicio seleccionado';
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
		return ($response); //devolvemos el array


    }


}

//    $t1 = array("TipoEjercicio"=>"2", "IdEjercicioSubrutina"=>"118", "Circuito"=>"118", "TiempoDescanso"=>"0", "Orden"=>"118","IdSubrutina"=>"33");
//    $t2 = array("TipoEjercicio"=>"2", "IdEjercicioSubrutina"=>"119", "Circuito"=>"119", "TiempoDescanso"=>"0", "Orden"=>"119","IdSubrutina"=>"33");
//    $t3 = array("TipoEjercicio"=>"1", "IdEjercicioSubrutina"=>"163", "Circuito"=>"163", "TiempoDescanso"=>"0", "Orden"=>"163","IdSubrutina"=>"33");
//    $t4 = array("TipoEjercicio"=>"1", "IdEjercicioSubrutina"=>"164", "Circuito"=>"164", "TiempoDescanso"=>"0", "Orden"=>"164","IdSubrutina"=>"33");
//
//    $arreglo=array ($t1,$t2,$t3,$t4);
//
  //    $ejercicio = new Ejercicio();
 //
 //  $resultado=$ejercicio->actualizarOrdenCircuito($arreglo);
//     $resultado=$ejercicio->actualizarObservaciones(4,2, 'NUEVAS OBSERVACIONES');
//     echo json_encode ($resultado);


?>
