<?php

$headers = "From: José Eduardo <li.eduardo.lm@gmail.com>\r\n";
$bool2=mail("li.eduardo.lm@gmail.com","asuntillo","Este es el cuerpo del mensaje",$headers);

if($bool2){
    echo "Mensaje 2 enviado";
}else{
    echo "Mensaje 2 no enviado";
}

?>
