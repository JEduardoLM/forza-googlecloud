<?php
	// Get cURL resource
	$ch = curl_init();

	// Set url
	curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

	// Set method
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	// Set options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Set headers
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	  "Authorization:key=AIzaSyBBXEj5mSDFK-w1HnSfw7yRhrJrZyI7mf0",
	  "Content-Type: application/json",
	 ]
	);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );

	// Create body
	$json_array = [
			"notification" => [
				"title" => "Forza",
				"sound" => "default",
				"body" => "Bienvenido a la familia Forza"
			],
			"to" => "/topics/gym",
			"priority" => "high"
		];
	$body = json_encode($json_array);

	// Set body
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

	// Send the request & save response to $resp
	$resp = curl_exec($ch);

	if(!$resp) {
	  die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	} else {
	  echo "Response HTTP Status Code : " . curl_getinfo($ch,     CURLINFO_HTTP_CODE);
	  echo "\nResponse HTTP Body : " . $resp;
	}

	// Close request to clear up some resources
	curl_close($ch);
?>
