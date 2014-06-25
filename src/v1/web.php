<?php
die;
header("Content-Type: text/plain");
require_once("v1.php");

$url = explode("/", substr($_SERVER["REDIRECT_URL"], strlen("/NoteCube/api/v1/")));
$out = array();
$params = isset($_GET["params"]) ? array($_GET["params"]) : (isset($_POST["params"]) ? array($_POST["params"]) : array());
$access_token = isset($_GET["access_token"]) ? $_GET["access_token"] : (isset($_POST["access_token"]) ? $_POST["access_token"] : "");

switch($url[0]) {
	case "devices":
		$device = new Cube($url[1]);

		// Verify the cube exists
		if($device->exists()) {
			// Validate access token
			if($device->getPublicAccessToken() == $access_token) {
				// Is the function publicly allowed to be accessed?
				if(in_array($url[2], Cube::$publicCalls)) {
					// Return the requested data
					$out = call_user_func_array(array($device, $url[2]), $params);

					if(is_null($out)) {
						// Nothing returned
						$out = array("ok" => "false", "error" => "No result");
					} else {
						// Success!
						if(is_object($out)) {
							// Cast the output into an array
							$out = (array)$out;
						} else if(!is_array($out)) {
							// Put the output into an array
							$out = array("result" => $out);
						}
					}
				} else {
					// Inaccessible function
					$out = array("ok" => "false", "error" => "Invalid function call");
				}
			} else {
				// Invalid access token
				$out = array("ok" => "false", "error" => "Invalid access token");
			}
		} else {
			// No such cube
			$out = array("ok" => "false", "error" => "No such cube");
		}
	break;

	case "users":
		if($url[1] == "find") {
			// Find user based on username
			$out = User::find($params);
		} else {
			$user = new User($url[1]);

			// Verify the user exists
			if($user->exists()) {
				// Validate access token
				if($user->getAccessToken() == $access_token) {
					// Is the function publicly allowed to be accessed?
					if(in_array($url[2], User::$publicCalls)) {
						// Return the requested data
						$out = call_user_func_array(array($user, $url[2]), $params);

						if(is_null($out)) {
							// Nothing returned
							$out = array("ok" => "false", "error" => "No result");
						} else {
							// Success!

							if(!is_array($out)) {
								// Convert the output into an array
								$out = array("result" => $out);
							}
						}
					} else {
						// Inaccessible function
						$out = array("ok" => "false", "error" => "Invalid function call");
					}
				} else {
					// Invalid access token
					$out = array("ok" => "false", "error" => "Invalid access token");
				}
			} else {
				// No such user
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