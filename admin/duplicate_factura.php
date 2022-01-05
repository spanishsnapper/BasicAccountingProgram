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


header("location:gest_ingresos.php?rID=" . $id);

   		
?>