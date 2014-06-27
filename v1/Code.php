<?php
abstract class Code {
	const SUCCESS = 0;

	// User codes
	const NO_SUCH_USER         = 101;
	const INVALID_PASSWORD     = 102;
	const INVALID_EMAIL        = 103;
	const INVALID_ACCESS_TOKEN = 104;
	const USER_EXISTS          = 105;
	const BRUTE_ATTACK         = 106;
	const PASSWORD_MISMATCH    = 107;
	const EMAIL_IN_USE         = 108;
	const USERNAME_IN_USE      = 109;

	// Device codes
	const DEVICE_REGISTER_SUCCESS   = 200;
	const NO_SUCH_DEVICE            = 201;
	const INVALID_DEVICE            = 202;
	const CANT_CONNECT_TO_DEVICE    = 203;
	const DEVICE_ALREADY_REGISTERED = 204;
	const DEVICE_REMOVE_SUCCESS     = 205;
	const INVALID_DEVICE_ID         = 206;

	// Location
	const NULL_LOCATION    = 301;
	const INVALID_LOCATION = 302;

	// Code codes
	const NO_CODE = 900;
};
?>