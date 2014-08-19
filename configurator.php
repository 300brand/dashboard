<?php
$address = "192.168.20.15:8080";

if (substr($_SERVER['SERVER_NAME'], -8) == 'squeaker') {
	$address = "192.168.1.4:8080";
}

header("Content-Type: application/json");

if (!isset($_SERVER['PATH_INFO'])) {
	http_response_code(400);
	print(json_encode(array(
		'error'    => true,
		'response' => "No path supplied",
	)));
	return;
}

$url = sprintf("http://%s%s", $address, $_SERVER['PATH_INFO']);

$ch = curl_init();
curl_setopt_array($ch, array(
	CURLOPT_URL            => $url,
	CURLOPT_TIMEOUT        => 10,
));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$in = file_get_contents("php://input");
	$data = http_build_query(json_decode($in));
	curl_setopt_array($ch, array(
		CURLOPT_POST       => true,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			'Content-Length: ' . strlen($data),
		),
	));
}
curl_exec($ch);
curl_close($ch);
