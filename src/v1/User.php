<?php
class User {
	use Entity {
		create as trait_create;
	}

	private $_id;
	public static $publicCalls = array(
		"getID", "getUsername",
		"getEmailAddress",
		"getCubes",
		"getRegisterDate"
		);

	public function User($id) {
		$this->_id = $id;
		$this->load($id);
	}

	public function exists() {
		return !is_null($this->data);
	}

	/**
	* Gets the name of this user.
	* @return Returns a string representing the name of this user.
	*/
	public function getID() {
		return $this->_id;
	}

	/**
	* Gets the name of this user.
	* @return Returns a string representing the name of this user.
	*/
	public function getUsername() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["username"];
	}

	/**
	* Gets the email address of this user.
	* @return Returns a string representing the email address of this user.
	*/
	public function getEmailAddress() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["email_address"];
	}

	/**
	* Gets the date the user registered.
	* @return Returns a DateTime instance representing the date the user registered.
	*/
	public function getRegisterDate() {
		if(is_null($this->_id))
			return NULL;

		$date = new DateTime($this->data["register_date"], new DateTimeZone(date_default_timezone_get()));
		return (object)array("ok" => TRUE, "date" => $date);
	}

	/**
	* Check if the password given matches the user's password.
	* @param password The password to check.
	* @return Returns TRUE if the password hash matches, or FALSE otherwise.
	*/
	public function checkPassword($password) {
		return $this->data["password"] == User_Service::generateHash($password, $this->data["salt"]);
	}

	/**
	* Sets a new password for this user.
	* @param password The new (raw) password.
	* @param salt The new salt.
	*/
	public function setPassword($password, $salt) {
		$this->data["password"] = User_Service::generateHash($password, $salt);
		$this->data["salt"]     = $salt;
		$this->commit();
	}

	/**
	* Sets the name of this user.
	* @param username The new username.
	*/
	public function setUsername($username) {
		$this->data["username"] = $username;
		$this->commit();
	}

	/**
	* Sets the email address of this user.
	* @param email_address The new email address.
	*/
	public function setEmailAddress($email_address) {
		$this->data["email_address"] = $email_address;
		$this->commit();
	}

	/**
	* Gets the access token of this user.
	* @return Returns a string representing the access token of this user.
	*/
	public function getAccessToken() {
		if(is_null($this->_id))
			return NULL;

		return $this->data["access_token"];
	}

	/**
	* Gets the cubes this user owns.
	* @return Returns an array of Cube objects holding the cube IDs and access tokens.
	*/
	public function getCubes() {
		if(is_null($this->_id))
			return NULL;

		$cubes = array();
		$result = DB::query("SELECT * FROM Cube WHERE user = %d", $this->_id);
		foreach($result as $row) {
			$cubes[] = (object)array("id" => $row["id"], "access_token" => $row["public_access_token"]);
		}

		return $cubes;
	}

	public static function create() {
		$params = func_get_arg(0);

		$find = User::find($params["username"]);
		if(!$find->ok) {
			$find = User::find($params["email_address"]);
			if($find->ok) {
				return (object)array("ok" => FALSE, "code" => Code::USER_EXISTS);
			}
		}

		if(!isset($params["salt"]) || empty($params["salt"])) {
			$params["salt"] = User_Service::generateSalt();
		}

		$params["password"]      = User_Service::generateHash($params["password"], $params["salt"]);
		$params["register_date"] = date("Y-m-d H:i:s");
		$params["access_token"]  = User_Service::generateAccessToken();

		User::trait_create($params);
		return (object)array("ok" => TRUE, "code" => Code::SUCCESS);
	}

	public static function find($username) {
		if(is_array($username)) $username = $username[0];

		$result = DB::queryFirstRow("SELECT id FROM User WHERE LCASE(username) = %s XOR LCASE(email_address) = %s", strtolower($username), strtolower($username));

		if(DB::count() > 0) return (object)array("ok" => TRUE, "id" => $result["id"]);
		else return (object)array("ok" => FALSE, "code" => Code::NO_SUCH_USER, "error" => "No user by that username");
	}

	private function _fetchCurlResult($function, $params = NULL) {
		if(is_null($this->_id))
			return NULL;

		$result = CURL::request(array("users", $this->_id, $function), NULL, array("access_token" => $this->getAccessToken(), "params" => $params));

		if($result === FALSE) return NULL;
		else return (object)json_decode($result);
	}
};
?>