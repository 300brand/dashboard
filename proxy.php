<?php
define('USERNAME', 'supervisor');
define('PASSWORD', 'supervisor');

$machines = array(
	'campbeltown' => array('ip' => '192.168.20.17', 'port' => 4243),
	'highland'    => array('ip' => '192.168.20.18', 'port' => 4243),
	'island'      => array('ip' => '192.168.20.19', 'port' => 4243),
	// 'rpi-0000'    => array('ip' => '192.168.20.65', 'port' => 4243),
	// 'rpi-0001'    => array('ip' => '192.168.20.66', 'port' => 4243),
	// 'rpi-0002'    => array('ip' => '192.168.20.67', 'port' => 4243),
	// 'rpi-0003'    => array('ip' => '192.168.20.68', 'port' => 4243),
	// 'rpi-0004'    => array('ip' => '192.168.20.69', 'port' => 4243),
	// 'rpi-0005'    => array('ip' => '192.168.20.70', 'port' => 4243),
	// 'rpi-0006'    => array('ip' => '192.168.20.71', 'port' => 4243),
	'rpi-0007'    => array('ip' => '192.168.20.72', 'port' => 4243),
	'rpi-0008'    => array('ip' => '192.168.20.73', 'port' => 4243),
	'rpi-0009'    => array('ip' => '192.168.20.74', 'port' => 4243),
	'rpi-000a'    => array('ip' => '192.168.20.75', 'port' => 4243),
	'rpi-000b'    => array('ip' => '192.168.20.76', 'port' => 4243),
	'rpi-000c'    => array('ip' => '192.168.20.77', 'port' => 4243),
	'rpi-000d'    => array('ip' => '192.168.20.78', 'port' => 4243),
);

if (substr($_SERVER['SERVER_NAME'], -8) == 'squeaker') {
	// Use proxies to get over to the machines
	$machines = array(
		'campbeltown' => array(
			'ip'      => '192.168.1.4',
			'port'    => 51700,
			'portmap' => array( // LocalForward 0.0.0.0:51702 192.168.20.17:50001
				50001 => 51702,
				50011 => 51704,
				50021 => 51706,
				50031 => 51708,
			),
		),
		'highland'    => array(
			'ip'      => '192.168.1.4',
			'port'    => 51800,
			'portmap' => array( // LocalForward 0.0.0.0:51802 192.168.20.18:50001
				50001 => 51802,
				50011 => 51804,
				50021 => 51806,
				50031 => 51808,
			),
		),
		'island'      => array(
			'ip'      => '192.168.1.4',
			'port'    => 51900,
			'portmap' => array( // LocalForward 0.0.0.0:51902 192.168.20.19:50001
				50001 => 51902,
				50011 => 51904,
				50021 => 51906,
				50031 => 51908,
			),
		),
	);
}

header("Content-Type: application/json");

if (!isset($_SERVER['PATH_INFO'])) {
	http_response_code(400);
	print(json_encode(array(
		'error'    => true,
		'response' => "No machine name supplied; expect /proxy/machine/container_id/path",
	)));
	return;
}

list(/* leading slash */, $machine_name, $container_id, $path) = array_merge(explode('/', $_SERVER['PATH_INFO'], 4), array_fill(0, 3, ''));

switch ($machine_name) {
case '_machines':
	$response = array();
	foreach ($machines as $m => $cfg) {
		$response[] = array(
			'name'  => $m,
			'title' => ucwords($m),
			'ip'    => $cfg['ip'],
		);
	}
	print(json_encode($response));
	return;
}

if (!array_key_exists($machine_name, $machines)) {
	http_response_code(404);
	print(json_encode(array(
		'error'    => true,
		'response' => "Unknown machine: '{$machine_name}'",
	)));
	return;
}

$machine = $machines[$machine_name];

$response = null;
switch ($container_id) {
case '0':
	$url = docker_url($machine['ip'], $machine['port'], $path, $_GET);
	$response = query_docker($url);
	break;
default:
	$container_path = sprintf('containers/%s/json', $container_id);
	$container_url = docker_url($machine['ip'], $machine['port'], $container_path);
	$container = query_docker($container_url);
	if (property_exists($container, 'error')) {
		$response = $container;
		break;
	}

	$port = $container->HostConfig->PortBindings->{'9001/tcp'}[0]->HostPort;
	$url = supervisor_url($machine['ip'], port_tr($machine, $port));
	$response = query_supervisor($url, $path, $_GET);
	break;
}

print(json_encode($response));


function docker_url($ip, $port, $path = '', $params = array()) {
	return sprintf("http://%s:%d/%s?%s", $ip, $port, $path, http_build_query($params));
}

function supervisor_url($ip, $port) {
	return sprintf("http://%s:%d/RPC2", $ip, $port);
}

function query_docker($url) {
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
	));
	$str = curl_exec($ch);
	$info = curl_getinfo($ch);
	switch (true) {
	case curl_errno($ch) > 0:
		$return = (object)array(
			'error'    => true,
			'response' => sprintf("cURL failure: [%d] %s", curl_errno($ch), curl_error($ch)),
			'url'      => $url,
		);
		break;
	case $info['http_code'] != 200:
		$return = (object)array(
			'error'    => true,
			'response' => sprintf("Recieved HTTP %d: %s", $info['http_code'], $str),
			'url'      => $url,
		);
		break;
	default:
		$return = json_decode($str);
	}
	curl_close($ch);
	return $return;
}

function query_supervisor($url, $method, array $params = array()) {
	$data_in = xmlrpc_encode_request($method, array_values($params));
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_POSTFIELDS     => $data_in,
		CURLOPT_USERPWD        => sprintf("%s:%s", USERNAME, PASSWORD),
		CURLOPT_HTTPHEADER     => array(
			"Content-Type: text/xml",
			"Content-Length: " + strlen($data_in),
		),
	));
	$str = curl_exec($ch);
	$info = curl_getinfo($ch);
	switch (true) {
	case curl_errno($ch) > 0:
		$return = (object)array(
			'error'    => true,
			'response' => sprintf("cURL failure: [%d] %s", curl_errno($ch), curl_error($ch)),
			'url'      => $url,
			'data'     => $data_in,
		);
		break;
	case $info['http_code'] != 200:
		$return = (object)array(
			'error'    => true,
			'response' => sprintf("Recieved HTTP %d: %s", $info['http_code'], $str),
			'url'      => $url,
			'data'     => $data_in,
		);
		break;
	default:
		$return = xmlrpc_decode($str);
	}
	curl_close($ch);
	return $return;
}

function port_tr($machine, $in) {
	if (
		!array_key_exists('portmap', $machine)
		|| !array_key_exists($in, $machine['portmap'])
	) {
		return $in;
	}
	return $machine['portmap'][$in];
}
