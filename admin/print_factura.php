<?php

if (!isset($_GET["rID"]) ) {
   header("location:gest_facturas.php");
} else {
   $id = $_GET["rID"];
  
}

header('Content-Type: text/html; charset=utf-8');

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
                 $res["codigopostal"] . " " . $res["provincia"] . "<br><br><strong>" . trim($res["nif"]) . "</strong>";

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


$desc_show_1   = $res["desc_1"] == "" ? "blank" : "showrow";
$desc_show_2   = $res["desc_2"] == "" ? "blank" : "showrow";
$desc_show_3   = $res["desc_3"] == "" ? "blank" : "showrow";
$desc_show_4   = $res["desc_4"] == "" ? "blank" : "showrow";



$ret_show      = $res["retencion"] <= 0 ? "blank_ret" : "showret";

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

// Now generate PDF with output (HTML)

require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Michael Corrigan');
$pdf->SetTitle($file_name);
$pdf->SetSubject('Invoice');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$tagvs = array('p' => array(
                  0 => array('h' => 0, 'n' => 0), 
                  1 => array('h' => 0, 'n'=> 0)
               ),
               'h1' => array(
                  0 => array('h' => 0, 'n' => 0), 
                  1 => array('h' => 0, 'n'=> 0)
               ),
               'h2' => array(
                  0 => array('h' => 0, 'n' => 0), 
                  1 => array('h' => 0, 'n'=> 0)
               )
            );
$pdf->setHtmlVSpace($tagvs);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(10, 5, 10);
$pdf->setImageScale(0.59);


// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

// output the HTML content
$pdf->writeHTML($output, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output( str_replace("/admin", "/facturas", __DIR__) . "/" . $file_name . '.pdf', 'FI');

//============================================================+
// END OF FILE
//============================================================+
?>