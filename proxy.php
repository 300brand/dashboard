<?php
$routes = array();

if (substr($_SERVER['SERVER_NAME'], -8) == 'squeaker') {
	$routes = array(
		'campbeltown' => array(
			'addr' => '192.168.1.4:51700',
			'type' => 'docker',
		),
		'highland'    => array(
			'addr' => '192.168.1.4:51800',
			'type' => 'docker',
		),
		'island'      => array(
			'addr' => '192.168.1.4:51900',
			'type' => 'docker',
		),
	);
}

header("Content-Type: application/json");

$route_name = substr($_SERVER['PATH_INFO'], 1);
$url_path = '';
if (strpos($route_name, '/') > -1) {
	$url_path = strstr($route_name, '/');
	$route_name = strstr($route_name, '/', true);
}

if (!array_key_exists($route_name, $routes)) {
	http_response_code(404);
	print(json_encode(array(
		'ok'      => false,
		'message' => "No route found for '{$route_name}'",
	)));
	return;
}

$route = $routes[$route_name];
switch ($route['type']) {
case 'docker':
	$url = sprintf("http://%s%s?%s", $route['addr'], $url_path, http_build_query($_GET));
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
	));
	if (curl_exec($ch) == false) {
		print(json_encode(array(
			'ok'      => false,
			'message' => sprintf("cURL failure: [%d] %s", curl_errno($ch), curl_error($ch)),
		)));
	}
	curl_close($ch);
	break;
default:
	print(json_encode(array(
		'ok'      => false,
		'message' => "Not sure how to handle '{$route['type']}'",
	)));
}
