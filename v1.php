<?php
define("ROOT", "/home/oliverda/addons/notenet.io");
header("Content-Type: text/plain");
header("Access-Control-Allow-Origin: *");
require_once(ROOT."/api/v1/v1.php");

function request($key, $default = "", $array = FALSE) {
	return isset($_GET[$key]) ? ($array ? array($_GET[$key]) : $_GET[$key]) : (isset($_POST[$key]) ? ($array ? array($_POST[$key]) : $_POST[$key]) : $default);
}

$url = array_filter(explode("/", $_SERVER["REQUEST_URI"])); array_shift($url);
$out = array();
$params = request("params", array(), TRUE);
$access_token = request("access_token");

$url[count($url)-1] = preg_replace("/\?.*/", "", $url[count($url)-1]);

switch($url[0]) {
	case "devices":
		$device = new Cube($url[1]);

		if($device->exists()) {
			if($device->getPublicAccessToken() == $access_token) {
				if(in_array($url[2], Cube::$publicCalls)) {
					$out = call_user_func_array(array($device, $url[2]), $params);

					if(is_null($out)) {
						$out = array("ok" => "false", "error" => "No result");
					} else {
						if(is_object($out)) {
							$out = (array)$out;
						} else if(!is_array($out)) {
							$out = array("result" => $out);
						}
					}
				} else {
					$out = array("ok" => "false", "error" => "Invalid function call");
				}
			} else {
				$out = array("ok" => "false", "error" => "Invalid access token");
			}
		} else {
			$out = array("ok" => "false", "error" => "No such cube");
		}
	break;

	case "users":
		if($url[1] == "find") {
			$out = User::find($params);
		} else {
			$user = new User($url[1]);

			if($user->exists()) {
				if($user->getAccessToken() == $access_token) {
					if(in_array($url[2], User::$publicCalls)) {
						$out = call_user_func_array(array($user, $url[2]), $params);

						if(is_null($out)) {
							$out = array("ok" => "false", "error" => "No result");
						} else {
							if(is_object($out)) {
								$out = (array)$out;
							} else if(!is_array($out)) {
								$out = array("result" => $out);
							}
						}
					} else {
						$out = array("ok" => "false", "error" => "Invalid function call");
					}
				} else {
					$out = array("ok" => "false", "error" => "Invalid access token");
				}
			} else {
				$out = array("ok" => "false", "error" => "No such user");
			}
		}
		break;

	default: break;
}

// Because JSON
print(json_encode($out));
die;
?>