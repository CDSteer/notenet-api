<?php
function __autoload($class) {
	$path = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.$class.".php";

	if(!class_exists($class) && file_exists($path))
		require_once($path);
}

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