<?php

$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
//'to' => $token,
$fields = array(
	'to' => "/topics/FOR001",
	'notification' => array('title' => '¡Titulo fijo!', 'body' => 'Esta es una notificación hardcodeada', 'click_action' => 'OPEN_ACTIVITY_1', 'icon' => 'ic_notification_forza', 'color' => '#63a21d'),
	'data' => array('message' => array('offer' => '.5'))
);

$headers = array(
	'Authorization:key=AIzaSyBBXEj5mSDFK-w1HnSfw7yRhrJrZyI7mf0',
	'Content-Type:application/json'
);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

$result = curl_exec($ch);

curl_close($ch);

$jsonObject = json_decode($result);
$response["firebaseData"] = $jsonObject;
$response["fields"]= json_encode($fields);
$response["token"]= $token;
$response["success"]=0;
$response["message"]='Notificacion enviada correctamente.';

echo json_encode($response); //devolvemos el array
//return ($response); //devolvemos el array
?>
