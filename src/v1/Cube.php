<?php
class Cube {
	use Entity {
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

		if($result->code == 400) return (object)array("ok" => FALSE, "code" => Code::INVALID_DEVICE);

		if($result === FALSE) return (object)array("ok" => FALSE, "code" => Code::CANT_CONNECT_TO_DEVICE);
		else return (object)array("ok" => TRUE, "code" => Code::SUCCESS, "latency" => intval(($end - $start) * 1000));
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

		$result = DB::queryFirstRow("SELECT * FROM City WHERE id = %d", $this->data["location"]);
		$result = array_change_key_case($result, CASE_LOWER);

		return (object)$result;
	}

	public function setLocation() {
		if(is_null($this->_id))
			return NULL;

		$location = func_get_arg(0);
		$result = DB::queryFirstRow("SELECT * FROM City WHERE id = %d", $location);
		if(DB::count() == 0) return array("ok" => "false", "error" => "Invalid location ID");

		DB::update("Cube", (object)array("location" => $location), "id = %s", $this->_id);
		return (object)array("ok" => "true", "location" => $location, "name" => $result["name"]);
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

	private function _fetchCurlResult($function, $params = NULL) {
		if(is_null($this->_id))
			return NULL;

		$result = CURL::request(array("devices", $this->getDeviceID(), $function), NULL, array("access_token" => $this->getPrivateAccessToken(), "params" => $params));

		if($result === FALSE) return NULL;
		else return (object)json_decode($result);
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