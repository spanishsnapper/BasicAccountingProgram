<?php
session_start();
date_default_timezone_set('Europe/Madrid');
$alowlang = array("ESP","FRE","DEU","ENG");

if ( !isset($Lang) || !in_array($Lang, $alowlang) ) { $Lang = "ESP"; }


/* EDIT THESE DETAILS ---------------------------------- */
define('DB_HOST',	 	"YOUR SERVER");
define('DB_NAME',		"YOUR DATABASE");
define('DB_USER',		"SECRET");
define('DB_PASS',		"SECRET");


define("START_YEAR", 	"2021-01-01");
define("RETENCION", 	0.15);
define("IVA",     	   	0.21);
define("PREFIX_YEAR", 	"2021");


/* EDIT THESE DETAILS ---------------------------------- */


define("DEBUG", 		true); 
define("QUERY", 		true); 
define("VERBAL", 		false); 

define("LOCAL", 		true); 

define('LANG',		 	$Lang);

if (DEBUG) {
	
	ini_set('display_errors', 1); 
	error_reporting(E_ALL);

} else {
	
	ini_set('display_errors', 0); 
	
}

if (LOCAL) {
		
	define("URL_BASE", 		"http://localhost/accounts/");
	
} else {
	
	define("URL_BASE", 		"/");
	
}



?>