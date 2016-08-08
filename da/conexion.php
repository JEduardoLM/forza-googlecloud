<?php

//require_once('config.php');



/*
	define('SERVIDOR', 'mysql.hostinger.mx');
	define('USUARIO', 'u304663758_admin');
	define('CONTRASENA','enforma123');
	define('BASEDEDATOS','u304663758_enfo');



	define('SERVIDOR', 'localhost');
	define('USUARIO', 'admin');
	define('CONTRASENA','enforma123');
	define('BASEDEDATOS','enforma');

function obtenerConexion(){

    error_reporting(0);
    $conexion = mysqli_connect(SERVIDOR,USUARIO,CONTRASENA,BASEDEDATOS);
    return $conexion;
}
*/

//Aqui se describe la conexión con google cloud
function obtenerConexion(){

    error_reporting(0);

    $username='root';
    $password='enforma123';
    $database='forza';
    $instance_name="/cloudsql/forza-1355:db-forza";


    $conexion = mysqli_connect(null, $username, $password, $database, 0, $instance_name);

    return $conexion;
}



function desconectar($conexion){

    $close = mysqli_close($conexion);

    if($close){
       // echo 'La desconexión de la base de datos se ha hecho satisfactoriamente';
    }
	else{
        echo 'Ha sucedido un error inesperado en la desconexión de la base de datos';
    }

    return $close;
}


?>
