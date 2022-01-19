<?php

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");

$DB = new DBConnect();

// VARIABLES FIELDS
$QType = "";
$table 	= "proveedores";

if ( !isset($_POST["cif"]) ) {
  	header("location:gest_proveedores.php");
}

$chkey	= trim($_POST["cif"]);

$fields = array("cliente","direccion","ciudad","provincia","codigopostal","contacto1","contacto2","activo");	
	
$QUERY = "INSERT IGNORE INTO " . $table . " (cif, ";
$QUERY .= implode(",",$fields);
$QUERY .= ") VALUES ('" . $chkey ."', ";


for ($i=0; $i<count($fields); $i++) {		
	$QUERY .= "'" . $DB->real_escape_string($_POST[ $fields[$i] ]) . "', ";
}

$QUERY = substr($QUERY,0,-2);

$QUERY .=  ")";

$QType = "INSERT";

$DB->queryString=$QUERY;
$DB->setQuery();																				// UPDATE SCHOOL

	
// if updating, check whether we are resetting the password:
$QUERY = "UPDATE " . $table . " SET ";

for ($i=0; $i<count($fields); $i++) {		
	$QUERY .= $fields[$i] . "='" .$DB->real_escape_string($_POST[ $fields[$i] ]) . "', ";
}

$QUERY = substr($QUERY,0,-2);

$QUERY .= " WHERE cif='" . $chkey ."'";

$QType = "UPDATE";

$DB->queryString=$QUERY;
$DB->setQuery();

// Logging Script:
if ($QType == "INSERT") {
	$ID = $DB->insert_id;
} elseif ($QType == "UPDATE") {
	$ID = $chkey;
} elseif ($QType == "DELETE") {
	$ID = $chkey;
}

$DB->DBOff();

header("location:gest_proveedores.php");


?>