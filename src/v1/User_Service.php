<?php
session_start();

class User_Service {
	private $_user, $_error;

	public function User_Service() {
		if(isset($_SESSION["user_id"])) {
			// Fetch user data
			$user = new User($_SESSION["user_id"]);

			if($user->getAccessToken() == $_SESSION["access_token"]) {
				// Access tokens match
				$this->_user = $user;
			}
		}
	}

	public function getCode() {
		if(isset($_SESSION["code"])) return intval($_SESSION["code"]);
		else Code::NO_CODE;
	}

	/**
	* Gets the user associated with this service session.
	* @return Returns a User object representing the user for this service session.
	*/
	public function getUser() {
		return $this->_user;
	}

	/**
	* Checks if the user is logged in.
	* @return Returns TRUE if a user is associated with this service session, or FALSE otherwise.
	*/
	public function isLoggedIn() {
		return isset($this->_user) && !is_null($this->_user) && $this->getCode() == Code::SUCCESS;
	}

	/**
	* Logs the out the user
	* @return Returns TRUE if the logout was successful, or FALSE otherwise
	*/
	public function logout() {
		// Clear session data
		unset($_SESSION["user_id"]);
		unset($_SESSION["access_token"]);
		unset($_SESSION["code"]);
		unset($this->_user);

		// Return the result
		return !(isset($_SESSION["user_id"]) || isset($_SESSION["access_token"]));
	}

	/**
	* Run a login verification.
	* @param username The username to login with.
	* @param password The password to login with.
	* @return Returns TRUE if the login was successful, or FALSE otherwise.
	*/
	public function login($username, $password) {
		// Try to find a user
		$find = User::find($username);

		if($find->ok) {
			// Fetch user data
			$user = new User($find->id);

			// Check login
			if($user->exists()) {
				if($user->checkPassword($password)) {
					// Set session data
					$_SESSION["user_id"]      = $user->getID();
					$_SESSION["access_token"] = $user->getAccessToken();
					$_SESSION["code"] = Code::SUCCESS;

					$this->_user = $user;
				} else {
					$_SESSION["code"] = Code::INVALID_PASSWORD;
				}
			} else {
				// Invalid user (somehow)
				$_SESSION["code"] = Code::NO_SUCH_USER;
			}
		} else {
			// Invalid user
			$_SESSION["code"] = Code::NO_SUCH_USER;
		}

		// Return the result
		return $find->ok;
	}

	public static function generateSalt($len = 128) {
		$set = array_merge(range("A", "Z"), range("a","z"), range(0, 9));
		$str = "";
		for($i = 0; $i < $len; $i++) {
			$str .= $set[mt_rand(0, count($set))];
		}
		return $str;
	}

	public static function generateAccessToken($len = 40) {
		$set = array_merge(range("a","f"), range(0, 9));
		$str = "";
		for($i = 0; $i < $len; $i++) {
			$str .= $set[mt_rand(0, count($set))];
		}
		return $str;
	}

	public static function generateHash($password, $salt) {
		return hash("sha512", $password.$salt);
	}
};
?>