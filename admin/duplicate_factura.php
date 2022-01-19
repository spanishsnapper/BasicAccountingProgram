<?php

if (!isset($_GET["rID"]) ) {
   header("location:gest_facturas.php");
} else {
   $id = $_GET["rID"];
  
}

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");

$DB = new DBConnect();

$fields = array("nif","importe","retencion","iva","emitido","cobrado","formapago",
               "desc_1","desc_2","desc_3","desc_4",
               "importe_1","importe_2","importe_3","importe_4");	


$DB->queryString = "INSERT INTO facturas(" . implode(",", $fields) . ")
							 SELECT " . implode(",", $fields) . " 
                     FROM facturas f
                     WHERE f.id = " . $_GET["rID"];
          
$res = $DB->setQuery();

$id= $DB->afRows;


$DB->queryString = "UPDATE facturas 
							SET emitido = CURRENT_TIMESTAMP, cobrado='1975-02-26'
							WHERE id = " . $id;
          
$res = $DB->setQuery();

// Check if this is the first factura of a new year and update the ID to YYYY-001 if so: 
$DB->queryString = "SELECT id, YEAR(emitido) AS yr FROM facturas WHERE id=" . $id;
$row = $DB->getAsocSglQuery();

if ($row["yr"] != substr($row["id"],0,4)) {
   $newyear_id = $row["yr"] . "001";
   $DB->queryString = "UPDATE facturas SET id=" . $newyear_id . " WHERE id=" . $id;
   $DB->setQuery();

   $ID = $newyear_id;
}


header("location:gest_ingresos.php?rID=" . $id);

   		
?>