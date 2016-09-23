<?php

	require('conexion.php'); //Se requiere el archivo conexión.php, para conectarse a la base de datos
	define('CONTRASENA','GOT.&.COC');

    // ********************************************************************************************************************************************
    // Este archivo servirá para crear métodos que permitan configurar de una manera más rapida un gimnasio, así como depurar información basura. *
    // ********************************************************************************************************************************************


   class Configuracion{

        function depurarRutinasSocio($idSucursal)
        {
            // Ésta función es utilizada para depurar las rutinas de una sucursal
            // Los criterios para depurar una rutina son:
            // Si el socio se encuentra dado de baja, sus rutinas deben ser depuradas por completo
            // Si el socio se encuentra activo, se deberá verificar cual es el número máximo de rutinas permitidas por socio, y se deberá proceder a borrar las excedenetes.

            //Creamos la conexión a la base de datos
            $conexion = obtenerConexion();

            if ($conexion){ //Verificamos que la conexión se haya realizado de manera correcta

                mysqli_set_charset($conexion, "utf8"); //Formato de datos utf8

                // Procedemos a armar las consultas
                // Lo primero que haremos será obtener el número máximo de rutinas que puede tener un socio

                    $sql= "SELECT NumeroRutinasSocio FROM sucursal WHERE S_Id=$idSucursal";

                    $numeroRutinasPermitidas=0;

                    if($result = mysqli_query($conexion, $sql)) //Ejecutamos la consulta
                    {
                          if($result!=null){
                                if ($result->num_rows>0){
                                    while($row = mysqli_fetch_array($result))
                                    {
                                        $numeroRutinasPermitidas=$row["NumeroRutinasSocio"];
                                        echo $numeroRutinasPermitidas;
                                    }
                                }
                                  else
                                  {
                                        $response["success"]=1;
                                        $response["message"]='No existe sucursal registrada con el id proporcionado';
                                  }
                          }
                          else
                          {
                                $response["success"]=1;
                                $response["message"]='No existe sucursal registrada con el id proporcionado';
                          }

                    }
                    else
                    {
                        $response["success"]=4;
                        $response["message"]='Se presentó un error al consultar el número de rutinas por socio';
                    }
                    desconectar($conexion); //desconectamos la base de datos
                if ($response["success"]===0){
                    $response["getSeries"]=$this->getSerieByEjercicioSubrutina($idEjercicio);
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
