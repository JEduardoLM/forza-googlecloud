<?php

$bool2=mail("li.eduardo.lm@gmail.com","asuntillo","Este es el cuerpo del mensaje");

if($bool2){
    echo "Mensaje 2 enviado";
}else{
    echo "Mensaje 2 no enviado";
}

?>
