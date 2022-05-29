<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentails: true');
header('Content-type: json/application');
require './vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];

$q = $_GET['q'];
$params = explode('/', $q);
$type = $params[0].'/'.$params[1];
$KEY = $params[2];
Predis\Autoloader::register();

try {
	$redis = new Predis\Client();
}
catch (Exception $e) {
	die($e->getMessage());
}
if(isset($argv)){
$command = implode(' ',$argv);
}
if((strpos($command, 'redis add')) !== false)
{
    if(preg_match('/\{(.+)\}\s*\{(.+)\}/', $command, $matches)){
        $key = $matches[1];
        $value = $matches[2];
        $redis->set($key, $value);
        $value = $redis->get($key);		
		$redis->expireat($key, time() + 3600); // истечёт через 1 час
		$redis->ttl($key); 
    }
}
	elseif((strpos($command, 'redis delete')) !== false)
{
    if(preg_match('/\{(.+)\}/', $command, $matches)){
        $key = $matches[1];
        $res = $redis->del($key);
        if($res) echo 'Удалено';
    }
}elseif((strpos($command, 'redis get')) !== false){
	$keys = $redis->keys('*');
		$data = [];
		foreach($keys as $key){
			$data[$key] = $redis->get($key);
		}
	print_r($data);
}

if($method === 'GET')
{
    if($type === 'api/redis'){
        $keys = $redis->keys('*');
		$data = [];
		foreach($keys as $key){
			$data[$key] = $redis->get($key);
		}
        http_response_code(200);

		$res = [
			"status" => true,
			"code" => 200,
			"data" => $data
		];

    echo json_encode($res);
    }
}
	elseif($method === 'DELETE')
{
    if($type === 'api/redis'){
        if(isset($KEY)){
           $result = $redis->del($KEY);
			http_response_code(200);
			if($result){
				$res = [
				"status" => true,
				"code" => 200,
				"data" => ''
			];
			}else{
				$res = [
				"status" => false,
				"code" => 500,
				"data" => ['message' => 'Error info message']
			];
			}
			

		echo json_encode($res);
        }
    }
}



