<?php
require_once("Entity.php");

class Cube {
	use Entity {
		all    as trait_all;
		create as trait_create;
	}

	private $_id;
	public static $publicCalls = array(
		"getID", "getName",
		"setLocation", "getLocation", 
		"setLED",
		"setMode",
		"setFlashRate",
		"ping",
		);

	public function Cube($id) {
		$this->_id = $id;
		$this->load($id);
	}

	public function exists() {
		return !is_null($this->data);
	}

	public function ping() {
		$start = microtime(TRUE);
		$result = $this->_fetchCurlResult("ping");
		$end = microtime(TRUE);

		if(is_null($result))
			return (object)array("ok" => FALSE, "code" => Code::CANT_CONNECT_TO_DEVICE);
		else {
			if($result->code == 400)
				return (object)array("ok" => FALSE, "code" => Code::INVALID_DEVICE);
			else if($result->code == 200)
				return (object)array("ok" => FALSE, "code" => Code::CANT_CONNECT_TO_DEVICE);
			else {
				if($result->connected)
					return (object)array("ok" => TRUE, "code" => Code::SUCCESS, "latency" => intval(($end - $start) * 1000));
				else if(!$result->ok)
					return (object)array("ok" => FALSE, "code" => Code::OUTDATED_FIRMWARE);
			}
		}
	}

	public function getID() {
		return $this->_id;
	}

	public function getName() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["name"];
	}

	public function getDeviceID() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["device_id"];
	}

	public function getPublicAccessToken() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["public_access_token"];
	}

	public function getPrivateAccessToken() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["private_access_token"];
	}

	public function getLocation() {
		if(is_null($this->_id))
			return NULL;

		if(is_null($this->data["location"])) {
			return (object)array("ok" => FALSE, "code" => Code::NULL_LOCATION, "message" => "NULL location");
		} else {
			$result = DB::queryFirstRow("SELECT * FROM City WHERE id = %d", $this->data["location"]);
			$result = array_change_key_case($result, CASE_LOWER);
			$result["ok"] = TRUE;

			return (object)$result;
		}
	}

	public function setLocation() {
		if(is_null($this->_id))
			return NULL;

		$location = func_get_arg(0);
		$result = DB::queryFirstRow("SELECT * FROM City WHERE id = %d", $location);
		if(DB::count() == 0) return array("ok" => "false", "code" => Code::INVALID_LOCATION, "message" => "Invalid location ID");

		DB::update("Cube", array("location" => $location), "id = %s", $this->_id);
		return (object)array("ok" => "true", "code" => Code::SUCCESS, "location" => $location, "name" => $result["name"]);
	}

	public function setLED() {
		$color = func_get_arg(0);
		return $this->_fetchCurlResult("setLED", $color);
	}

	public function setMode() {
		$mode = func_get_arg(0);
		return $this->_fetchCurlResult("setMode", $mode);
	}

	public function setFlashRate($rate) {
		$rate = func_get_arg(0);
		return $this->_fetchCurlResult("setFlashRate", $rate);
	}

	public function getUser() {
		return new User($this->data["user"]);
	}

	private function _fetchCurlResult($function, $params = "") {
		if(is_null($this->_id))
			return NULL;

		$result = CURL::request(array("devices", $this->getDeviceID(), "func"), NULL, array("access_token" => $this->getPrivateAccessToken(), "params" => $function."|".$params));

		if($result === FALSE) return NULL;
		else return (object)json_decode($result);
	}

	public static function all() {
		return Cube::trait_all();
	}

	public static function create() {
		$params = func_get_arg(0);

		$params["id"] = substr(User_Service::generateAccessToken(), 0, 24);
		$params["public_access_token"]  = User_Service::generateAccessToken();

		Cube::trait_create($params);
		return (object)array("ok" => TRUE, "code" => Code::SUCCESS);
	}
};
?>