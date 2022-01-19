<?php

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");

$DB = new DBConnect();

// VARIABLES FIELDS
$QType = "";
$table 	= "facturas";

if ( !isset($_POST["id"]) ) {
	header("location:gest_ingresos.php");
} else {
	if (!is_numeric($_POST["id"]) ) {
		$chkey = 0;
	} else {
		$chkey	= trim($_POST["id"]);
	}
}

$fields = array("nif","importe","retencion","iva","emitido","cobrado","formapago",
               "desc_1","desc_2","desc_3","desc_4",
               "importe_1","importe_2","importe_3","importe_4");	

// keep the same  easier:
if( $_POST["cobrado"] == "") {
   $_POST["cobrado"] = "1975-02-26";
}





if ($chkey == 0) {	
	
	$QUERY = "INSERT INTO " . $table . " (";
	$QUERY .= implode(",",$fields);
	$QUERY .= ") VALUES (";
	
	
	for ($i=0; $i<count($fields); $i++) {		
		$QUERY .= "'" . $DB->real_escape_string($_POST[ $fields[$i] ]) . "', ";
	}
	
	$QUERY = substr($QUERY,0,-2);
	
	$QUERY .=  ")";
	
	$QType = "INSERT";

} else {																						// UPDATE SCHOOL

	
	// if updating, check whether we are resetting the password:
	$QUERY = "UPDATE " . $table . " SET ";
	
	for ($i=0; $i<count($fields); $i++) {		
		$QUERY .= $fields[$i] . "='" .$DB->real_escape_string($_POST[ $fields[$i] ]) . "', ";
	}
	
	$QUERY = substr($QUERY,0,-2);
	
	$QUERY .= " WHERE id=" . $chkey ;
	
	$QType = "UPDATE";
	
}

$DB->queryString=$QUERY;
$DB->setQuery();

// Logging Script:
if ($QType == "INSERT") {
	$ID = $DB->insert_id;


	// Check if this is the first factura of a new year and update the ID to YYYY-001 if so: 
	$DB->queryString = "SELECT id, YEAR(emitido) AS yr FROM facturas WHERE id=" . $ID;
	$row = $DB->getAsocSglQuery();
	
	if ($row["yr"] != substr($row["id"],0,4)) {
		$newyear_id = $row["yr"] . "001";
		$DB->queryString = "UPDATE facturas SET id=" . $newyear_id . " WHERE id=" . $ID;
		$DB->setQuery();

		$ID = $newyear_id;
	}


} elseif ($QType == "UPDATE") {
	$ID = $chkey;
} elseif ($QType == "DELETE") {
	$ID = $chkey;
}

$DB->DBOff();

header("location:gest_ingresos.php");


?>