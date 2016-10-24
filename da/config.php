<?php

	require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos
	define('CONTRASENA','GOT.&.COC');

    // ********************************************************************************************************************************************
    // Este archivo servirá para crear métodos que permitan configurar de una manera más rapida un gimnasio, así como depurar información basura. *
    // ********************************************************************************************************************************************


   class Configuracion{

        function depurarRutinasSocio($clave)
        {

            // Ésta función es utilizada para depurar las rutinas que se encuentren de más
            // Los criterios para depurar una rutina son:
            // Si el socio se encuentra dado de baja, sus rutinas deben ser depuradas por completo
            // Si el socio se encuentra activo, se deberá verificar cual es el número máximo de rutinas permitidas por socio, y se deberá proceder a borrar las excedenetes.

            //Antes de realizar cualquier actividad, vamos a verificar que la contraseña se encuentre correcta
            if ($clave===CONTRASENA){


                //Creamos la conexión a la base de datos
                $conexion = obtenerConexion();

                if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

                    mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

                    // Procedemos a armar las consultas

                        // ********************************************************************************
                        // ********************************************************************************
                        //
                        //PRIMERO VAMOS A DEPURAR LAS RUTINAS DE LOS SOCIOS QUE SE ENCUENTREN DADOS DE BAJA
                        //
                        // ********************************************************************************
                        // ********************************************************************************

                        $sql="DELETE FROM `rutina` WHERE R_ID>0 and id_Socio in
                            (SELECT So_Id FROM socio s join usuariogimnasio ug on s.Id_UsuarioGym=ug.Ug_Id   where s.Estatus=0 and ug.Estatus=0);";
                        // Esta consulta lo que hace, es que busca todos los socios, cuyo estatus es igual a cero
                        // y su usuarioGym de la misma manera se encuentra en cero (ésto para corroborar que se encuentra todo de manera consistente)
                        // y posteriormente depura las rutinas de los socios encontrados (con estatus cero).

                        if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                        {
                            $response["rutinasSociosInactivos"]="Se eliminaron correctamente las rutinas de socios inactivos";

                                //
                                // UNA VEZ QUE SE HAN BORRADO LAS RUTINAS DE LOS SOCIOS QUE SE ENCUENTREN DADOS DE BAJA,
                                // PROCEDEREMOS A BORRAR LAS RUTINAS ADICIONALES DE DE LOS SOCIOS
                                //

                                $sql2="Select id_Socio, count(id_Socio) as NumeroRutinas,
	                                   (SELECT Id_Sucursal FROM socio where So_Id=id_Socio) as SucursalId,
	                                   (Select NumeroRutinasSocio from Socio So Join Sucursal Su on So.Id_Sucursal=Su.S_Id where So.So_Id=r.id_Socio)
                                            as NumeroMaximoRutinas
                                    from rutina r group by id_Socio;";

                                // Esta consulta lo que realiza es lo siguiente:
                                // Cuenta cuantas rutinas tiene asignadas cada socio
                                // Consulta cual es la sucursal de cada socio, y en base a su sucursal determina cual es el
                                // número máximo de rutinas que puede tener
                                // De esta manera tenemos el socio, su total de rutinas asignadas y el número máximo de rutinas permitidas para cada socio.

                                // Con los datos arrojados, debemos proceder a eliminar las rutinas adicionales de cada socio, por lo que debemos ingresar a un
                                // ciclo, donde para cada registro verifiquemos si es necesario o no eliminar información adicional.

                                if($result2 = mysqli_query($conexion, $sql2))
                                {
                                    if($result2!=null){
                                        if ($result2->num_rows>0){

                                            while($row = mysqli_fetch_array($result2))
                                            {

                                                $id_Socio=$row["id_Socio"];
                                                $NumeroRutinas=$row["NumeroRutinas"];
                                                $SucursalId=$row["SucursalId"];
                                                $NumeroMaximoRutinas=$row["NumeroMaximoRutinas"];

                                                $rutinasAEliminar=$NumeroRutinas-$NumeroMaximoRutinas;
                                                // Comparamos el número máximo de rutinas que puede tener el socio, de acuerdo a su sucursal
                                                // contra el número de rutinas que tiene actualmente.

                                                if ($rutinasAEliminar>0){
                                                    // Verificamos si hay por lo menos una rutina que se deba eliminar


                                                     $sqlRutinas="SELECT R_ID FROM rutina where id_Socio=$id_Socio order by R_ID asc limit $rutinasAEliminar;";
                                                     // Seleccionamos las rutinas más antiguas de cada socio, que se encuentren fuera del máximo de rutinas
                                                     //    permitidas


                                                        if($resultRutinas = mysqli_query($conexion, $sqlRutinas))
                                                        {
                                                            if($resultRutinas!=null){
                                                                if ($resultRutinas->num_rows>0){

                                                                    while($row = mysqli_fetch_array($resultRutinas))
                                                                    {

                                                                        // Extraemos el id de la rutina que debe ser eliminada.
                                                                        $idRutina=$row["R_ID"];

                                                                       $sqlEliminar="DELETE FROM `rutina` WHERE `R_ID`=$idRutina ;";

                                                                        if($resultEliminar = mysqli_query($conexion, $sqlEliminar)) //Ejecutamos la consulta
                                                                            {
                                                                                    $response["RutinasAdicionales"]="Se eliminaron correctamente las rutinas adicionales de los socios";
                                                                            }
                                                                            else
                                                                            {
                                                                                $response["success"]=11;
                                                                                $response["messageEliminarRutina"]='Se presentó un error al eliminar la rutina:'.$idRutina=$row["R_ID"].' + '.$response["messageEliminarRutina"];
                                                                            }





                                                                    } // FINALIZA CICLO MIENTRAS, QUE RECORRE LAS RUTINAS A ELIMINAR DE CADA SOCIO

                                                                } //
                                                                else
                                                                {
                                                                        $response["success"]=10;
                                                                        $response["messageSocioRutina"]='No se encontraron rutinas adicionales para eliminar del socio:'.$id_Socio.' + '. $response["messageSocioRutina"];
                                                                }


                                                            }
                                                            else
                                                            {
                                                                    $response["success"]=10;
                                                                    $response["messageSocioRutina"]='No se encontraron rutinas adicionales para eliminar del socio:'.$id_Socio.' + '. $response["messageSocioRutina"];
                                                            }



                                                        } //Error de conexión
                                                        else
                                                        {
                                                                $response["success"]=9;
                                                                $response["message"]='Se presentó un error al consultar las rutinas de cada socio';
                                                        }









                                                } // Finaliza IF ELIMINAR_RUTINAS>0

                                            } // Finaliza ciclo,

                                        } //
                                        else
                                        {
                                                $response["success"]=8;
                                                $response["message"]='No se encontraron socios';
                                        }


                                    }
                                    else
                                    {
                                            $response["success"]=8;
                                            $response["message"]='No se encontraron socios';
                                    }



                                } //Error de conexión
                                else
                                {
                                        $response["success"]=7;
                                        $response["message"]='Se presentó un error al consultar las rutinas de cada socio';
                                }





                        }
                        else
                        {
                                $response["success"]=6;
                                $response["message"]='Se presentó un error al eliminar las rutinas de los socios inactivos';
                        }



                    // Antes de finalizar, si se logró realizar la conexión, debemos proceder a desconectarla
                    desconectar($conexion); //desconectamos la base de datos



                } // FINALIZA IF, ERROR DE CONEXIÓN
                else
                {
                    $response["success"]=3;
                    $response["message"]='Se presentó un error en la conexión con la base de datos';
                }

            } // Finaliza IF, CONTRASEÑA INCORRECTA
            else
            {
                    $response["success"]=5;
                    $response["message"]='La contraseña es incorrecta';
            }

            return ($response); //devolvemos el array
        }

       function desactivarSocios($clave){

           //
           // Este método será utilizado, para verificar que cuando una sucursal tenga más de N tiempo dada de baja, se proceda a desactiviar sus socios y
           // usuario gimansio
           //
       }


    }


?>
