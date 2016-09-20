<?php
require_once('conexion.php');

class Rutina{

	function getRutinaByIdSocio_old($idSocio){ // Esta función nos regresa la rutina activa de un socio especifico
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

		if ($idSocio!=0)
		{
			$sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Sucursal, id_Instructor
                FROM rutina where Estatus=1  and id_Socio=$idSocio order  by FechaInicio desc  LIMIT 1";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Rutina"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["R_ID"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["FechaInicio"]=$row["FechaInicio"];
                            if($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                            $item["NumeroSemanas"]=$row["NumeroSemanas"];
                            if ($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                            $item["Estatus"]=$row["Estatus"];

                            $item["Objetivo"]=$row["Objetivo"];
                            if ($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                            $item["IdSocio"]=$row["id_Socio"];
                            if ($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                            $item["IdSucursal"]=$row["id_Sucursal"];
                            if($item["IdSucursal"]==NULL){$item["IdSucursal"]=0;}

                            $item["IdInstructor"]=$row["id_Instructor"];
                            if ($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                            array_push($response["Rutina"], $item);
                        }
                        $response["success"]=1;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=0;
                        $response["message"]='El socio no cuenta con una rutina activa';
                    }

                }
                else
                    {
                        $response["success"]=0;
                        $response["message"]='El socio no cuenta con una rutina activa';
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

    function duplicarRutina($idRutina, $idSocio, $fecha, $numeroSemanas, $objetivo, $idInstructor){

        //Creamos la conexión
        $conexion = obtenerConexion();

        mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

        if ($conexion){ //Verificamos la conexión, en caso de fallar regresamos el error de conexión NO EXITOSA

            /* deshabilitar autocommit para poder hacer un rollback*/
            mysqli_autocommit($conexion, FALSE);

            //Lo primero que vamos a hacer es duplicar la rutina

            if($objetivo==NULL or $objetivo==''){
                $sql="INSERT INTO rutina
                        SELECT NULL as R_ID, Nombre, '$fecha' as FechaInicio, '$numeroSemanas' NumeroSemanas, 1 as Estatus, Objetivo, '$idSocio' as id_Socio,NULL as id_Sucursal, '$idInstructor' as Id_Instructor FROM rutina
                        where R_ID=$idRutina;";
            }
            else{
                $sql="INSERT INTO rutina
                    SELECT NULL as R_ID, Nombre, '$fecha' as FechaInicio, '$numeroSemanas' NumeroSemanas, 1 as Estatus, '$objetivo' as Objetivo, '$idSocio' as id_Socio,NULL as id_Sucursal, '$idInstructor' as Id_Instructor FROM rutina
                    where R_ID=$idRutina;";
            }

                if($result = mysqli_query($conexion, $sql)){ //Ejecutamos la consulta para duplicar la rutina y verificamos si se ejecutó correctamente

                        $idRutinaNueva=mysqli_insert_id($conexion);

                        //Procedemos a hacer una consulta, para obtener los días de la rutina a clonar, y poder duplicar cada uno de los días.
                        $sql2="SELECT SR_ID, Orden, IdRutina, Nombre  FROM subrutina where IdRutina = $idRutina;";

                        if($result2 = mysqli_query($conexion, $sql2)){
                            $seDuplicoTodo=1;
                            while($row = mysqli_fetch_array($result2)) //Recorremos cada uno de los días, para proceder a dar de alta cada registro.
                            {

                                $idSubrutinaOrigen=$row["SR_ID"];
                                $orden=$row["Orden"];
                                $nombreSubrutina=$row["Nombre"];

                                $sqlSubrutina="INSERT INTO `subrutina` (`Orden`, `IdRutina`, `Nombre`) VALUES ('$orden', '$idRutinaNueva', '$nombreSubrutina');" ;

                                if($resultSubrutina = mysqli_query($conexion, $sqlSubrutina)){
                                    $idSubrutinaNueva=mysqli_insert_id($conexion); // Si la subrutina se insertó correctamente procedemos a obtener el Id de la nueva subrutina

                                    //Una vez que tenemos la subrutina, vamos a proceder a duplicar los registros de la tabla de cardio.
                                    $sqlCardio="INSERT INTO subrutinaejerciciocardio
                                        (SELECT NULL as SEC_ID, '$idSubrutinaNueva' as Id_Subrutina, Id_EjercicioCardio, Tiempototal, Velocidadpromedio, TipoDeVelocidad, DistanciaTotal, TipoDistancia, Ritmocardiaco, Nivel, Observaciones, Orden, Circuito, NotaSocio FROM subrutinaejerciciocardio where Id_Subrutina=$idSubrutinaOrigen);";

                                    if($resultCardio = mysqli_query($conexion, $sqlCardio)){

                                        //Si se ejecutó correctamente la duplicidad de los ejercicios de cárdio, procedemos con la duplicidad de los ejercicios de pesas

                                        // En este proceso, lo realizamos diferente, primero vamos a consultar todos los ejercicios de pesas, y posteriormente, vamos a proceder a insertarlos uno por uno, ya que necesitamos obtener el id de cada ejercicio de pesa, para posteriormente agregar las series.
                                        $sqlPesa="SELECT SEP_ID, Id_Subrutina, Id_EjercicioPeso, Circuito, TiempoDescansoEntreSerie, Observaciones, Orden
                                        FROM subrutinaejerciciopeso where Id_Subrutina=$idSubrutinaOrigen ;"; //Ya que necesitamos obtener el id de cada registro ingresado, vamos a proceder a recorrer cada ejercicio de pesas de la serie, para irlos registrando.



                                        if($resultPesa = mysqli_query($conexion, $sqlPesa)){

                                            while($rowPesa = mysqli_fetch_array($resultPesa)) //Recorremos cada uno de los ejercicios de pesas, para proceder a dar de alta cada registro.
                                            {
                                                $idEjercicioPesasOrigen=$rowPesa["SEP_ID"];
                                                $idSubrutinaPesas=$rowPesa["Id_Subrutina"];
                                                $ejercicioPesas=$rowPesa["Id_EjercicioPeso"];
                                                $circuitoPesas=$rowPesa["Circuito"];
                                                if ($circuitoPesas==NULL or $circuitoPesas==""){$circuitoPesas=0;}

                                                $tiempoDescansoEntreSerie=$rowPesa["TiempoDescansoEntreSerie"];
                                                if ($tiempoDescansoEntreSerie==NULL or $tiempoDescansoEntreSerie==''){$tiempoDescansoEntreSerie=0;}

                                                $observacionesPesas=$rowPesa["Observaciones"];

                                                $ordenPesas=$rowPesa["Orden"];
                                                if($ordenPesas==NULL or $ordenPesas==''){$ordenPesas=0;}

                                                $sqlPesas2="INSERT INTO `subrutinaejerciciopeso` (`Id_Subrutina`, `Id_EjercicioPeso`, `Circuito`, `TiempoDescansoEntreSerie`, `Observaciones`, `Orden`)
                                                VALUES ('$idSubrutinaNueva', '$ejercicioPesas', '$circuitoPesas', '$tiempoDescansoEntreSerie', '$observacionesPesas', '$ordenPesas');";

                                                if($resultPesas2 = mysqli_query($conexion, $sqlPesas2)){ // Ejecutamos la consulta, para insertar los ejecicios de pesas
                                                    $idEjercicioPesas=mysqli_insert_id($conexion); //Obtenemos el id del registro de pesas
                                                    //Una vez que registramos el ejercicio de pesas, procedemos a duplicar las series

                                                    $sqlSeries="INSERT INTO serie
                                                    (SELECT NULL as Sr_ID, NumeroSerie, Repeticiones, id_TipoSerie, PesoPropuesto, '$idEjercicioPesas' as id_SubrutinaEjercicio, Observaciones, TipoPeso
                                                    FROM serie where id_SubrutinaEjercicio=$idEjercicioPesasOrigen);";

                                                    if ($resultSeries=mysqli_query($conexion, $sqlSeries)){ //Ejecutamos la consulta para duplicar las diferentes series del ejercicio

                                                    }
                                                    else{
                                                        $seDuplicoTodo=0;
                                                        $response["success"]=10;
                                                        $response["message"]='Se presentó un error al duplicar las series del ejercicio: '.$idEjercicioPesasOrigen." ";
                                                        /* Revertir */
                                                        mysqli_rollback($conexion);
                                                    }

                                                }
                                                else{
                                                    $seDuplicoTodo=0;
                                                    $response["success"]=9;
                                                    $response["message"]='Se presentó un error al duplicar el ejercicio con id: '.$idEjercicioPesasOrigen." ";
                                                    /* Revertir */
                                                    mysqli_rollback($conexion);
                                                }

                                            }
                                        }
                                        else{
                                                $seDuplicoTodo=0;
                                                $response["success"]=8;
                                                $response["message"]='Se presentó un error al consultar los ejercicios de pesas de la subrutina con Id: '.$idSubrutinaOrigen." ";
                                                /* Revertir */
                                                 mysqli_rollback($conexion);
                                        }

                                    }
                                    else{
                                        $seDuplicoTodo=0;
                                        $response["success"]=7;
                                        $response["message"]='Se presentó un error al duplicar los ejercicios de cardio de la subrutina con Id: '.$idSubrutinaOrigen." ";
                                        /* Revertir */
                                        mysqli_rollback($conexion);
                                    }


                                }
                                else{
                                    $seDuplicoTodo=0;
                                    $response["success"]=6;
                                    $response["message"]='Se presentó un error al duplicar la subrutina con Id: '.$idSubrutinaOrigen." ";
                                    /* Revertir */
                                    mysqli_rollback($conexion);
                                }

                            }

                            if ($seDuplicoTodo==1){
                                mysqli_commit($conexion);
                                $response["success"]=0;
                                $response["message"]='Rutina clonada correctamente';
                            }
                        }
                        else{

                            $response["success"]=5;
                            $response["message"]='Se presentó un error al consultar los días de la rutina (subrutinas)';
                            /* Revertir */
                            mysqli_rollback($conexion);
                        }
                        }
                        else{

                            $response["success"]=4;
                            $response["message"]='Se presentó un error al duplicar la rutina';
                            /* Revertir */
                            mysqli_rollback($conexion);
                        }
        }
        else
        {
            $response["success"]=3;
            $response["message"]='Se presentó un error al realizar la conexión';

        }
        return $response;
    }

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function getRutinasGenericasBySucursal($idSucursal){ //Este método nos va a permitir obtener las rutinas genericas de una sucursal

        //Creamos la conexión
		$conexion = obtenerConexion();

        if ($conexion){ //Verificamos si la conexión se realizó correctamente

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
        //Procedemos a armar la consulta, para obtener las rutinas genericas de una sucursal

		$sql= "SELECT R_ID, Nombre, Estatus, Objetivo, id_Sucursal, id_Instructor FROM rutina where id_Sucursal=$idSucursal and (id_Socio is NULL or id_Socio=0)";

            if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        $response["Rutinas"] = array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["R_ID"]=$row["R_ID"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["Estatus"]=$row["Estatus"];

                            $item["Objetivo"]=$row["Objetivo"];
                            if ($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                            $item["IdSucursal"]=$row["id_Sucursal"];

                            $item["IdInstructor"]=$row["id_Instructor"];
                            if ($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                            array_push($response["Rutinas"], $item);
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No existen rutinas registradas para la sucursa indicada';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No existen rutinas registradas para la sucursa indicada';
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

    function getRutinaById($idRutina){


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            //Procedemos a armar la consulta, para obtener la rutina de acuerdo a su id
            $sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Sucursal, id_Instructor FROM rutina where R_ID=$idRutina";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                    if($result!=null){ //Verificamos que no haya regresado Nulo la consulta
                        if ($result->num_rows>0){

                            while($row = mysqli_fetch_array($result))  //Extraemos los datos del registro (debe ser sólo uno)
                            {
                                $item = array();
                                $item["Id"]=$row["R_ID"];
                                $item["Nombre"]=$row["Nombre"];

                                $item["FechaInicio"]=$row["FechaInicio"];
                                if ($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                                $item["NumeroSemanas"]=$row["NumeroSemanas"];
                                if($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                                $item["Estatus"]=$row["Estatus"];

                                $item["Objetivo"]=$row["Objetivo"];
                                if($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                                $item["IdSocio"]=$row["id_Socio"];
                                if($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                                $item["IdSucursal"]=$row["id_Sucursal"];
                                if($item["IdSucursal"]==NULL){$item["IdSucursal"]=0;}

                                $item["IdInstructor"]=$row["id_Instructor"];
                                if($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                                $response["Rutina"]= $item;
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
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
		return ($response); //devolvemos el array
    }


    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function getRutinaByIdSocio($idSocio){


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            //Procedemos a armar la consulta, para obtener la rutina de acuerdo a su id
            $sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Sucursal, id_Instructor FROM rutina where id_Socio=$idSocio order by FechaInicio desc limit 1";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                    if($result!=null){ //Verificamos que no haya regresado Nulo la consulta
                        if ($result->num_rows>0){

                            while($row = mysqli_fetch_array($result))  //Extraemos los datos del registro (debe ser sólo uno)
                            {
                                $item = array();
                                $item["Id"]=$row["R_ID"];
                                $item["Nombre"]=$row["Nombre"];

                                $item["FechaInicio"]=$row["FechaInicio"];
                                if ($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                                $item["NumeroSemanas"]=$row["NumeroSemanas"];
                                if($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                                $item["Estatus"]=$row["Estatus"];

                                $item["Objetivo"]=$row["Objetivo"];
                                if($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                                $item["IdSocio"]=$row["id_Socio"];
                                if($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                                $item["IdSucursal"]=$row["id_Sucursal"];
                                if($item["IdSucursal"]==NULL){$item["IdSucursal"]=0;}

                                $item["IdInstructor"]=$row["id_Instructor"];
                                if($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                                $response["Rutina"]= $item;
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
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
		return ($response); //devolvemos el array
    }
    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function getTotalRutinasByIdSocio($idSocio){


        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();

        if ($conexion){
            mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

            //Procedemos a armar la consulta, para obtener la rutina de acuerdo a su id
            $sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Sucursal, id_Instructor FROM rutina where id_Socio=$idSocio order by FechaInicio desc limit 10;";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                    if($result!=null){ //Verificamos que no haya regresado Nulo la consulta
                        if ($result->num_rows>0){

                            $response["RutinaSocio"] = array();
                            while($row = mysqli_fetch_array($result))  //Extraemos los datos del registro (debe ser sólo uno)
                            {
                                $item = array();
                                $item["Id"]=$row["R_ID"];
                                $item["Nombre"]=$row["Nombre"];

                                $item["FechaInicio"]=$row["FechaInicio"];
                                if ($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                                $item["NumeroSemanas"]=$row["NumeroSemanas"];
                                if($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                                $item["Estatus"]=$row["Estatus"];

                                $item["Objetivo"]=$row["Objetivo"];
                                if($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                                $item["IdSocio"]=$row["id_Socio"];
                                if($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                                $item["IdSucursal"]=$row["id_Sucursal"];
                                if($item["IdSucursal"]==NULL){$item["IdSucursal"]=0;}

                                $item["IdInstructor"]=$row["id_Instructor"];
                                if($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                                 array_push($response["RutinaSocio"], $item);
                            }
                            $response["success"]=0;
                            $response["message"]='Consulta exitosa';
                        }
                        else{
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
                        }

                    }
                    else
                        {
                            $response["success"]=1;
                            $response["message"]='No se encontró la rutina con el id indicado';
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
		return ($response); //devolvemos el array
    }

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************


    function saveRutina($R_ID, $nombre, $fechaInicio, $numeroSemanas, $estatus, $objetivo, $id_Socio, $id_Sucursal, $id_Instructor ){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();



        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

            if ($id_Sucursal==''){
                $id_Sucursal='NULL';
            }
            if ($id_Socio==''){
                $id_Socio='NULL';
            }
            if ($id_Instructor==''){
                $id_Instructor='NULL';
            }

            //Procedemos a armar las consultas
            if($R_ID==NULL or $R_ID==0 or $R_ID==''){
                $sql= "INSERT INTO `rutina` (`Nombre`, `FechaInicio`, `NumeroSemanas`, `Estatus`, `Objetivo`, `id_Socio`, `id_Sucursal`, `id_Instructor`)
                        VALUES ('$nombre', '$fechaInicio' , $numeroSemanas, $estatus, '$objetivo', $id_Socio, $id_Sucursal, $id_Instructor);";
            }
            else{
                $sql="UPDATE `rutina` SET `Nombre`='$nombre', `FechaInicio`='$fechaInicio', `NumeroSemanas`='$numeroSemanas',
                `Estatus`='$estatus', `Objetivo`='$objetivo', `id_Socio`=$id_Socio, `id_Sucursal`=$id_Sucursal, `id_Instructor`=$id_Instructor  WHERE `R_ID`='$R_ID';";
            }

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                            if ($R_ID==NULL or $R_ID==0 or $R_ID==''){
                                $R_ID=mysqli_insert_id($conexion);
                            }

                            $response["Rutina"]=$this->getRutinaById($R_ID);
                            $response["success"]=0;
                            $response["message"]='Rutina guardada correctamente';



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

		return ($response); //devolvemos el array
    }

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function deleteRutina($R_ID ){

        //Creamos la conexión a la base de datos
		$conexion = obtenerConexion();



        if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

            mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8


            //Procedemos a armar las consultas

                $sql= "DELETE FROM `rutina` WHERE `R_ID`=$R_ID";

                if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                {
                            $response["success"]=0;
                            $response["message"]='La rutina se eliminó correctamente';
                }
                else
                {
                    $response["success"]=4;
                    $response["message"]='Se presento un error al eliminar la rutina';
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

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

    function buscarRutinaPorNombreYSucursal($idSucursal,$nombreRutina){ // Esta función nos permite verificar si existe una rutina activa, para la misma sucursal con el mismo nombre
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
		$sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Instructor
                FROM rutina where Nombre='$nombreRutina' and id_Sucursal=$idSucursal and Estatus=1;";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["R_ID"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["FechaInicio"]=$row["FechaInicio"];
                            if($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                            $item["NumeroSemanas"]=$row["NumeroSemanas"];
                            if ($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                            $item["Estatus"]=$row["Estatus"];

                            $item["Objetivo"]=$row["Objetivo"];
                            if ($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                            $item["IdSocio"]=$row["id_Socio"];
                            if ($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                            $item["IdInstructor"]=$row["id_Instructor"];
                            if ($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                            $response["Rutina"]=$item;
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontró rutina, con el nombre indicado para la sucursal seleccionada';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontró rutina, con el nombre indicado para la sucursal seleccionada';
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
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array
	}

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

      function getLastRutinaSocio($idSocio){ // Esta función nos permite obtener la última rutina que se haya configurado a un socio
		//Creamos la conexión con la función anterior
		$conexion = obtenerConexion();

        if ($conexion){

		mysqli_set_charset($conexion, "utf8"); //formato de datos utf8
		$sql= "SELECT  R_ID, Nombre, FechaInicio, NumeroSemanas, Estatus, Objetivo, id_Socio, id_Instructor
                FROM rutina where Estatus=1 and id_Socio=$idSocio order by FechaInicio desc Limit 1;";

            if($result = mysqli_query($conexion, $sql))
            {
                if($result!=null){
                    if ($result->num_rows>0){

                        while($row = mysqli_fetch_array($result))
                        {
                            $item = array();
                            $item["Id"]=$row["R_ID"];
                            $item["Nombre"]=$row["Nombre"];
                            $item["FechaInicio"]=$row["FechaInicio"];
                            if($item["FechaInicio"]==NULL){$item["FechaInicio"]=0;}

                            $item["NumeroSemanas"]=$row["NumeroSemanas"];
                            if ($item["NumeroSemanas"]==NULL){$item["NumeroSemanas"]=0;}

                            $item["Estatus"]=$row["Estatus"];

                            $item["Objetivo"]=$row["Objetivo"];
                            if ($item["Objetivo"]==NULL){$item["Objetivo"]='';}

                            $item["IdSocio"]=$row["id_Socio"];
                            if ($item["IdSocio"]==NULL){$item["IdSocio"]=0;}

                            $item["IdInstructor"]=$row["id_Instructor"];
                            if ($item["IdInstructor"]==NULL){$item["IdInstructor"]=0;}

                            $response["Rutina"]= $item;
                        }
                        $response["success"]=0;
                        $response["message"]='Consulta exitosa';
                    }
                    else{
                        $response["success"]=1;
                        $response["message"]='No se encontraron rutinas para el socio indicado';
                    }

                }
                else
                    {
                        $response["success"]=1;
                        $response["message"]='No se encontraron rutinas para el socio indicado';
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
            $response["message"]='Se presentó un error en la conexión con la base de datos';
        }
		return ($response); //devolvemos el array
	}

    //********************************************************************************************************************
    //********************************************************************************************************************
    //********************************************************************************************************************

}

    // $Rutina = new Rutina();
    // $RutinaR=$Rutina->saveRutina(60, 'TEST2', '2015-12-14', 4, 1, 'Objetivos', 1, NULL , 1 );
    // $RutinaR=$Rutina->getRutinasGenericasBySucursal(2);
    // $RutinaR=$Rutina->deleteRutina(NULL);
    //  $RutinaR=$Rutina->getRutinaByIdSocio(2);
    // $RutinaR=$Rutina->duplicarRutina(2, 1, '2015-12-14', 2, 1, 1);
    // $RutinaR=$Rutina->buscarRutinaPorNombreYSucursal(2, 'principiante hombre');
    // echo json_encode ($RutinaR);

    // echo date_timestamp_get(1461202946506);


?>
