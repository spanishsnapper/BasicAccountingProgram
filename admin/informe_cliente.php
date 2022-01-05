<?php

if (!isset($_GET["cif"]) ) {
   header("location:gest_clientes.php");
} else {
   $cif = $_GET["cif"];
  
}


require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/adminClass.php");

$admin         = new adminClass();
$admin->getClientList();
$admin->queryString = "SELECT * FROM facturas f
                      WHERE nif='" . $cif . "'
                      ORDER BY retencion DESC, emitido ASC";
          
$res = $admin->getQuery();
$table = '';

$subtotal_bas =
$subtotal_iva = 
$subtotal_ret = 
$totalRetencion   =
$totalImporte     =
$totalImporteRet  =
$totalIVA         =
$totalIVARet      =
$totalCount       =
$totalCountRet    = 0;




$retJump = 1;
$trimJump = "X";

if ($res !="NO ENTRIES" ) {
	
	$fl = new funcLib;
	
	while ($rw = $res->fetch_array() ) {
   	
      $emp = $admin->clientList[ $rw["nif"] ];
		 
		$retencion  = $rw["importe"]*RETENCION*$rw["retencion"]*(-1);
		$iva        = $rw["importe"]*IVA*$rw["iva"];
		$abonar     = $rw["importe"] + $retencion + $iva;
		
		$trimestreE = "Q" . ( ceil(substr($rw["emitido"],5,2)/3) );
		
		// Initialize
		if ($trimJump=="X") {
   		$trimJump = $trimestreE;
		}
		
		
		if ( strtotime($rw["cobrado"]) > strtotime("2019-01-01 00:00:00") ) {
	   	$trimestreC = "Q" . ( ceil(substr($rw["cobrado"],5,2)/3) );
	   	$cobrado = $rw["cobrado"];
	   } else {
		   $trimestreC = "-";
		   $cobrado = "-";
		}
		
		
		if ($rw["retencion"]==1) {
   		
         $totalImporteRet  += $rw["importe"];
         $totalIVARet      += $iva;
         $totalCountRet    += 1;
         $totalRetencion   += $retencion;

		} else {
   		
   		$totalImporte     += $rw["importe"];
         $totalIVA         += $iva;
         $totalCount       += 1;
         
		}
		
		if ($trimJump != $trimestreE) {
				
				$table .= '
            <tr class="subtotal">
   				<td style="text-align:left" colspan=3>Subtotal Trimestre</td>
   				<td style="text-align:right">' . number_format($subtotal_bas,2). '</td>
   				<td style="text-align:right">' . number_format($subtotal_ret,2). '</td>
   				<td style="text-align:right">' . number_format($subtotal_iva,2) . '</td>
   				<td style="text-align:right">' . number_format( ($subtotal_bas+$subtotal_ret+$subtotal_iva) ,2) . '</td>
   				
   				<td style="text-align:center" colspan=5> </td>
				  </tr>
				  
				  <tr>
   				<td colspan=12 style="background:#FFF;border-top:1px solid #000;"></td>
     				</tr>';
            				  
            $subtotal_bas = $rw["importe"];
            $subtotal_iva = $iva;
            $subtotal_ret = $retencion;  
		
		
		   } else {

   		   $subtotal_bas += $rw["importe"];
            $subtotal_iva += $iva;
            $subtotal_ret += $retencion;  
		   }
		   
		
		if ($retJump != $rw["retencion"]) {
				
				$table .= '<tr>
            				<td colspan=12 style="background:#FFF;border-top:1px solid #000;"></td>
            				</tr>';
		   }		
		   
		   
      
		$table .= '<tr>
				<td style="text-align:left">' . substr($rw["id"],0,4) . "-" . substr($rw["id"],4) . '</td>
				<td style="text-align:left">' . $rw["nif"] . '</td>
				<td style="text-align:left">' . $admin->clientList[ $rw["nif"] ] . '</td>
				<td style="text-align:right">' . number_format($rw["importe"],2). '</td>
				<td style="text-align:right">' . number_format($retencion,2). '</td>
				<td style="text-align:right">' . number_format($iva,2) . '</td>
				<td style="text-align:right">' . number_format($abonar,2) . '</td>
				
				<td style="text-align:center">' . $rw["emitido"] . '</td>
				<td style="text-align:center">' . $cobrado . '</td>
				
				<td style="text-align:center" class="'.$trimestreE.'">' . $trimestreE . '</td>
				<td style="text-align:center" class="'.$trimestreC.'">' . $trimestreC . '</td>
				
				</tr>';
				
						
				
				$retJump = $rw["retencion"];
				$trimJump = $trimestreE;
				
	}

   /* LAST ROW */
		
		$table .= '
      <tr class="subtotal">
			<td style="text-align:left" colspan=3>Subtotal Trimestre</td>
			<td style="text-align:right">' . number_format($subtotal_bas,2). '</td>
			<td style="text-align:right">' . number_format($subtotal_ret,2). '</td>
			<td style="text-align:right">' . number_format($subtotal_iva,2) . '</td>
			<td style="text-align:right">' . number_format( ($subtotal_bas+$subtotal_ret+$subtotal_iva) ,2) . '</td>
			
			<td style="text-align:center" colspan=5> </td>
		  </tr>
		  
		  <tr>
			<td colspan=12 style="background:#FFF;border-top:1px solid #000;"></td>
				</tr>';
      				            				
     
		   
		   
   
   
   $summaryTable = '<table class="summaryTable">
   <tr><th colspan=3></th><th>Base</th><th>Retención</th><th>IVA</th><th>Abonar</th><th>Nº Fac</th><th colspan=4></th></tr>';
    		
   $totalAbonarRet = $totalImporteRet + $totalIVARet + $totalRetencion;
   $totalAbonarNor = $totalImporte + $totalIVA;
   
   $summaryTable .= '<tr>
   				<td style="text-align:left" colspan=3>TOTAL RETENCIONES</td>
   				<td style="text-align:right">' . number_format($totalImporteRet,2). '</td>
   				<td style="text-align:right">' . number_format($totalRetencion,2). '</td>
   				<td style="text-align:right">' . number_format($totalIVARet,2) . '</td>
   				<td style="text-align:right">' . number_format($totalAbonarRet,2) . '</td>
   				
   				<td style="text-align:right">' . $totalCount . ' facturas</td>
   				<td style="text-align:center" colspan=4> </td>
				  </tr>';	
	
   							
   $summaryTable .= '<tr>
   				<td style="text-align:left" colspan=3>TOTAL SIN RETENCIONES</td>
   				<td style="text-align:right">' . number_format($totalImporte,2). '</td>
   				<td style="text-align:right">' . number_format(0,2). '</td>
   				<td style="text-align:right">' . number_format($totalIVA,2) . '</td>
   				<td style="text-align:right">' . number_format($totalAbonarNor,2) . '</td>
   				
   				<td style="text-align:right">' . $totalCountRet . ' facturas</td>
   				<td style="text-align:center" colspan=4> </td>
				  </tr>';	
				  
   							  
   $summaryTable .= '<tr>
   				<td style="text-align:left" colspan=3>TOTALES</td>
   				<td style="text-align:right">' . number_format($totalImporteRet+$totalImporte,2). '</td>
   				<td style="text-align:right">' . number_format($totalRetencion,2). '</td>
   				<td style="text-align:right">' . number_format($totalIVARet+$totalIVA,2) . '</td>
   				<td style="text-align:right">' . number_format($totalAbonarRet+$totalAbonarNor,2) . '</td>
   				
   				<td style="text-align:right">' . ($totalCountRet+$totalCount) . ' facturas</td>
   				<td style="text-align:center" colspan=4> </td>
				  </tr>
				  </table>';	
				     				

} else {
   $table .= "<tr><td>Sin facturas</td></tr>";	
}	  

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Panel de Control - Administración - Gestión de Ingresos</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">

</head>

<body>
<!-- header bar -->
<?php require_once("menu.php"); ?>
<!-- end header bar -->
<div id="mainContainer">

   <h1>Informe Cliente <?php echo $emp; ?></h1>
    
    
  <h2>Lista de Facturas</h2>
     
       <table class="listTable" style="width:90%">
        	<tr>
      	    <th style="width:10%">Id</th>
      	    <th style="width:8%">NIF</th>
      	    <th style="width:18%">Cliente</th>
      	    
      	    <th style="width:6%;text-align: right">Importe</th>
      	    <th style="width:6%;text-align: right">Retenciones</th>
      	    <th style="width:6%;text-align: right">IVA</th>
      	    <th style="width:6%;text-align: right">Abonar</th>
      	    
      	    <th style="width:10%;text-align: center">Fecha Emitido</th>
      	    <th style="width:10%;text-align: center">Fecha Cobrado</th>
      	    <th style="width:5%;text-align: center">T</th>
      	    <th style="width:5%;text-align: center">T</th>
      	</tr>
                    <?php echo $table; ?>
        </table>
           
           
           <?php echo $summaryTable; ?>    
            
</div>


</body>
</html>