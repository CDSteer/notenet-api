<?php
session_start();

/*function __autoload($class) {
	print(__FILE__);
	$path = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.$class.".php";

	if(!class_exists($class) && file_exists($path)) {
		printf(" REQUIRING: %s\n", $path);
		require_once($path);
	}
}*/

require_once("Code.php");
require_once("Color.php");
require_once("Cube.php");
require_once("CURL.php");
require_once("DB.php");
require_once("Entity.php");
require_once("Light_Mode.php");
require_once("PushWeather.php");
require_once("User.php");
require_once("User_Service.php");

// Mysql Defines
define("MYSQL_HOSTNAME", "localhost");
define("MYSQL_USERNAME", "nothingp");
define("MYSQL_PASSWORD", "360p0nt4life56123");
define("MYSQL_DATABASE", "nothingp_notenet");

// Set DB values
DB::$host     = MYSQL_HOSTNAME;
DB::$user     = MYSQL_USERNAME;
DB::$password = MYSQL_PASSWORD;
DB::$dbName   = MYSQL_DATABASE;
DB::$encoding = "utf8-bin";

$service = new User_Service();

if($service->isLoggedIn()) {
	$user = $service->getUser();
}
?>