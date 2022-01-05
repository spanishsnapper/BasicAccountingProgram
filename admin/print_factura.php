<?php

if (!isset($_GET["rID"]) ) {
   header("location:gest_facturas.php");
} else {
   $id = $_GET["rID"];
  
}

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");

$DB = new DBConnect();


$DB->queryString = "SELECT f.*, c.cliente, c.direccion, c.ciudad, c.provincia, c.codigopostal 
                     FROM facturas f
                     LEFT JOIN clientes c ON c.cif = f.nif 
                     WHERE f.id=" . $_GET["rID"];
          
$res = $DB->getAsocSglQuery();

$fl = new funcLib;

$retencion  = $res["importe"]*RETENCION*$res["retencion"]*(-1);
$iva        = $res["importe"]*IVA*$res["iva"];
$abonar     = $res["importe"]+$iva + $retencion;


$emitida       = $fl->drawDate($res["emitido"], "long");

$id            = substr($res["id"], 0,4) . "-" . substr($res["id"], 4,3);

$datos_cliente = $res["cliente"] . "<br>" .
                 $res["direccion"] . "<br>" .
                 $res["ciudad"] . "<br>" .
                 $res["codigopostal"] . " " . $res["provincia"] . "<br><br>
                 <strong>" . $res["nif"] . "</strong>";

$desc_1        = $res["desc_1"];

$base_1        = number_format($res["importe_1"],2,",","") . "€";
$iva_1         = number_format($res["importe_1"]*IVA,2,",","") . "€";
$iva_pct_1     = IVA*100 . "%";
$desc_2        = $res["desc_2"];

if ($res["importe_2"]==0) {
   
   $base_2 = $iva_2 = $iva_pct_2 = " &nbsp;";
   
} else {
   
   $base_2        = number_format($res["importe_2"],2,",","") . "€";
   $iva_2         = number_format($res["importe_2"]*IVA,2,",","") . "€" ;  
   $iva_pct_2     = IVA*100 . "%";

}


$desc_3        = $res["desc_3"];

if ($res["importe_3"]==0) {
   
   $base_3 = $iva_3 = $iva_pct_3 = " &nbsp;";
   
} else {

   $base_3        = number_format($res["importe_3"],2,",","") . "€";
   $iva_3         = number_format($res["importe_3"]*IVA,2,",","") . "€";
   $iva_pct_3     = IVA*100 . "%";

}


$desc_4        = $res["desc_4"];

if ($res["importe_4"]==0) {
   
   $base_4 = $iva_4 = $iva_pct_4 = " &nbsp;";
   
} else {

   $base_4        = number_format($res["importe_4"],2,",","") . "€";
   $iva_4         = number_format($res["importe_4"]*IVA,2,",","") . "€";
   $iva_pct_4     = IVA*100 . "%";


}


$desc_show_1   = $res["desc_1"] != "" ? "" : "showrow";
$desc_show_2   = $res["desc_2"] != "" ? "" : "showrow";
$desc_show_3   = $res["desc_3"] != "" ? "" : "showrow";
$desc_show_4   = $res["desc_4"] != "" ? "" : "showrow";



$ret_show      = $res["retencion"]>0 ? "" : "showret";

$base_tot      = number_format($res["importe"],2,",","") . "€";
$iva_tot       = number_format($iva,2,",","") . "€";
$ret_tot       = number_format($retencion,2,",","") . "€";
$iva_pct       = IVA*100 . "%";
$ret_pct       = RETENCION*100 . "%" ;
$grand_total   = number_format($abonar,2,",","") . "€";
$file_name     = substr($res["id"], 4,3) . " (" . $res["emitido"] . ") " . $res["cliente"]; 






$find = array( "{emitida}","{id}","{datos_cliente}",
               
               "{desc_1}","{base_1}","{iva_1}","{iva_pct_1}",
               "{desc_2}","{base_2}","{iva_2}","{iva_pct_2}",
               "{desc_3}","{base_3}","{iva_3}","{iva_pct_3}",
               "{desc_4}","{base_4}","{iva_4}","{iva_pct_4}",
               
               "{desc_show_1}","{desc_show_2}","{desc_show_3}","{desc_show_4}",
               "{ret_show}",
               
               "{base_tot}","{iva_tot}","{ret_tot}",
               "{iva_pct}","{ret_pct}","{grand_total}","{title}"
               );
               
               
$replace = array( $emitida,$id,$datos_cliente,
               
               $desc_1,$base_1,$iva_1,$iva_pct_1,
               $desc_2,$base_2,$iva_2,$iva_pct_2,
               $desc_3,$base_3,$iva_3,$iva_pct_3,
               $desc_4,$base_4,$iva_4,$iva_pct_4,
               
               $desc_show_1,$desc_show_2,$desc_show_3,$desc_show_4,
               $ret_show,
               
               $base_tot,$iva_tot,$ret_tot,
               $iva_pct,$ret_pct,$grand_total,$file_name
               );
               

$template = file_get_contents("factura_template.html");   

$output = str_replace($find, $replace, $template);
echo $output;         
           		
?>