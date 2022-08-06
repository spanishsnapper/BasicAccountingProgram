<?php


/*
  		

            .:;siS5225s:                                                                                    
          .;;;:::;;rS3H#M2,                                                                                 
        rXi;:.   ..,,:iM@@@@s                                                                               
      ;9i,,,.  ...... ;B&M@@@@,                                                                             
     iG:.,:.  XGsi52X3&3XhH#@@@;                       ,;sS2X932i;.                                         
    sG..,:.  2&..,;;rsiS23&H#@@@:                   :i5SiiS5XhH#@@@@2.                                      
   ,B,.,:.  2&..,:;;rsi523GAB@@@@                 r2S;...,:;rri59G9@@@H,                                    
   hi ,,.  5&..,:;;rriS2X9GAB#@@@s              ,9S:.  :;.,:;;rs2  ,@@@@2                                   
   #;,,,  ;A..,:;;rrsi523h&HB#@@@A .,,,.       ;A;.,,  rS..::;rsS  ,##@@@M                                  
  :@r;;;  :S,:;;rrsiiS2X9&AM@@B35i2hAB#@@#&s. .A:.,,,  ri.,:;;rs2  ,#BM@@@H                                 
   @Srrs;  ;irrrssiS5239GH#MS,   ,;ri23AB#@@@MB: .,::  ri.,:;;riX. :#HB#@@@;                                
   &Biii5;  ;2iiSS52X3h&B#2.           .;9BM@@@#r .,,  ri.:  :5i3. :#HH#@@@M                                
   ,@92223; .rG939h&AHHMBr   .,  .        iBAB@@@G..,  ri,;  ;ASh. :#BBM@@@@                                
    2@&99GAr  ;22XX399M#;  .,,,:rS, .Ahi   2BAM@@@H,,  r2;r  ;A2G. :#MM#@@@@                                
     X@MAAHMi,        ;S  ,,,,,,,,  .Gs5i   9#H#@@@h:  s9ri. ;HXA. :@###@@@B                                
      r@@###@#AGhGGGhGG, ,,,,,,,,:. .9sihS   A#M#@@@s  s&i2. ;MGM. :@@#@@@@i                                
        3@@@@@@@@@@@@@h..,,,,,,,,:. .3ii2HS  .###@@@5  sMXA. r@B@, :@@@@@@#                                 
          r#@@@@@@@@@@5,:::,,,,::;, .hSi2h@;  9@#@@@2  :hH5  ,h@& .;@@@@@@;                                 
             .r2&AA&X9&;;;;;;;;;;s, .h2S2G@:  A@@@@@23.    .,     r@@@@@@;                                  
                      #2sssrrrrrs5: ,&323Mr .5@@@@@Ms@MiS52A@5iS2G@@@@@H.                                   
                      9#5SSiiiiiS9; .&&9Mr .2@@@@@@5B@@@@@@@@@@@@@@@@#;                                     
                       @BXX22222hMr ,B@@i .S@@@@@@@  2@@@@@@@@@@@@@2.                                       
                       ,@#Ghh9h3iX: ,53;  i@@@@@@@;     .;i2X2i;.                                           
                         @@BAAM;   .    :9@@@@@@#,                                                          
           ++++++++++++   A@@@@#H#@####@@@@@@@@A    ++++++++++++++++++++++++++++++++++++++++++++++++++++++
                              ,s3AM####MH9i:  

*/

class informeClass extends FormWrite {
	
	public 	$adminID;
	
	public 	$rID, $table;
	public 	$pg = 0;
	public 	$f = 0;

	public $fl;
	
	private $trimestreTotals = array();
	
	
	
	public function __construct() {
		
		parent::__construct();
		
		$this->fl = new funcLib;
		
	}
	
	
	public function drawSummary() {
   	
   	$this->queryString = "  SELECT COUNT(*) AS facturaCount,
                              SUM(importe) AS facturaImporte 
                              FROM facturas 
                              WHERE emitido >='" . START_YEAR . "'";
      
      $in = $this->getAsocSglQuery();
   
      
      $this->queryString = "  SELECT COUNT(*) AS facturaCount,
                              SUM(importe) AS facturaImporte 
                              FROM gastos 
                              WHERE emitido >='" . START_YEAR . "'";
      
      $out = $this->getAsocSglQuery();
      
      $output = "<h2>Year to date " . PREFIX_YEAR . ":</h2>
                   <table class='listTable' style='width:60%'>
                  <tr><th>Income:</th><td>" . number_format($in[1],2) . "</td><td>from " .$in[0]  . " facturas.</td></tr>
                  <tr><th>Expense:</th><td>" . number_format($out[1],2) . "</td><td>from " .$out[0]  . " facturas.</td></tr> 
                  <tr><th>Current Balance:</th><td>" . number_format($in[1]-$out[1],2) . "</td><td> </td></tr>
                  </table>";
     
     
     return($output);
   	
	}
		
	public function drawClientTopTen() {
   	
   	$this->queryString = "  SELECT nif, COUNT(*) AS cnt, SUM(importe) AS imp, c.cliente
                              FROM facturas f
                              LEFT JOIN clientes c ON c.cif = f.nif
                              WHERE f.emitido >='" . START_YEAR . "'
                              GROUP BY nif
                              ORDER BY imp DESC";
      
      $res = $this->getQuery();
      
      $output = "<h2>Totals by Client " . PREFIX_YEAR . ":</h2>
                  <table class='listTable' style='width:60%'>
                  <tr><th>Client</th><th>Nº Facturas</th><th>Importe</th></tr>";
      
      while ($rw = $res->fetch_assoc()) {
         
         $output .= "<tr><td>" . $rw["cliente"] . "</td><td>" . $rw["cnt"] . "</td><td>" . number_format($rw["imp"],2) . "</td></tr>";
      }
                  
      $output .= "</table>";
     
     
     return($output);
   	
	}
	
	public function drawInformeTrimestreIRPF() {
   	
   	$ingresos_bydate     = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$iva_bydate     		= array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	
   	$ingresos_bypaid     = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$iva_bypaid          = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$retenciones_bypaid  = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	
   	$ing_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$iva_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$ret_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-30","-12-31");
   	
   	$screen              = "";
   	
   	// 4 quarters - Ingresos By Date CREATED: 
   	for ($i=1;$i<=4;$i++) {
      	
   	   $total = 0;
   	   $output = "";
   	   
   	   if ($i==1) {
            $output = "<h1>Ingresos by Date</h1>";
         }
         
         $output .= "<h3>Ingresos By Date " . PREFIX_YEAR . " Q" . $i . ":</h2>";



   	   
      	$this->queryString = "  SELECT id, nif, importe, retencion, iva, emitido, cobrado, c.cliente
                                 FROM facturas f
                                 LEFT JOIN clientes c ON c.cif = f.nif
                                 WHERE f.emitido BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
                                 ORDER BY id ASC";
      
         $res = $this->getQuery();
        
              
         $output .= "<table class='compactTable'>
                  <tr><th>Client</th><th>Nº Factura</th><th>Emitido</th><th>Cobrado</th><th>Importe</th><th>Retencion</th><th>IVA</th></tr>";
			
			if ($this->afRows>0) {
	         while ($rw = $res->fetch_assoc()) {
	            
	            $cobrado = ($rw["cobrado"] == "1975-02-26") ? "-" : $rw["cobrado"];
	            
	            $output .= "<tr>
	                           <td>" . $rw["cliente"] . "</td>
	                           <td>" . $this->formatfacid($rw["id"]) . "</td>
	                           <td style='font-weight:700'>" . $rw["emitido"] . "</td>
	                           <td>" . $cobrado . "</td>
	
	                           <td>" . number_format($rw["importe"],2) . "</td>
	                           <td>" . number_format($rw["importe"]*$rw["retencion"]*RETENCION,2) . "</td>
	                           <td>" . number_format($rw["importe"]*$rw["iva"]*IVA,2) . "</td>
	                        </tr>";
	            
	            $ingresos_bydate["Q" . $i] += $rw["importe"];
	            $iva_bydate["Q" . $i] += $rw["importe"]*$rw["iva"]*IVA;
	            
	         }
         }
           
          $output .= "<tr><th>Total Trimestre</th><th colspan=3></th>
                        <th>" . number_format($ingresos_bydate["Q" . $i],2) . "</th>
                        <th></th><th>" . number_format($iva_bydate["Q" . $i],2) . "</th></tr>";    
                  
         $output .= "</table>";
     
         $screen .= $output;
      }





      // 4 quarters - Ingresos By Date PAID: 
   	for ($i=1;$i<=4;$i++) {
      	
   	  $total = 0;
   	   $output = "";
   	   
   	   if ($i==1) {
            $output = "<h1>Ingresos by PAID DATE</h1>";
         }
         
         $output .= "<h3>Ingresos By Paid " . PREFIX_YEAR . " Q" . $i . ":</h2>";



   	   
      	$this->queryString = "  SELECT id, nif, importe, retencion, iva, emitido, cobrado, c.cliente
                                 FROM facturas f
                                 LEFT JOIN clientes c ON c.cif = f.nif
                                 WHERE f.cobrado BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
                                 ORDER BY id ASC";
      
         $res = $this->getQuery();
      
         $output .= "<table class='compactTable'>
                  <tr><th>Client</th><th>Nº Factura</th><th>Emitido</th><th>Cobrado</th><th>Importe</th><th>Retencion</th><th>IVA</th></tr>";
      
			if ($this->afRows>0) {
	         while ($rw = $res->fetch_assoc()) {
	            
	            $output .= "<tr>
	                           <td>" . $rw["cliente"] . "</td>
	                           <td>" . $this->formatfacid($rw["id"]) . "</td>
	                           <td>" . $rw["emitido"] . "</td>
	                           <td style='font-weight:700'>" . $rw["cobrado"] . "</td>
	                           <td>" . number_format($rw["importe"],2) . "</td>
	                           <td>" . number_format($rw["importe"]*$rw["retencion"]*RETENCION,2) . "</td>
	                           <td>" . number_format($rw["importe"]*$rw["iva"]*IVA,2) . "</td>
	                        </tr>";
	            
	            $ingresos_bypaid["Q" . $i]    += $rw["importe"];
	            $iva_bypaid["Q" . $i]         += $rw["importe"]*$rw["iva"]*IVA;
	            $retenciones_bypaid["Q" . $i] += $rw["importe"]*$rw["retencion"]*RETENCION;
	            
	         }
         }
         
         // ERROR 2019
         if (PREFIX_YEAR=="2019") {
	         if ($i==2) {
	           
	            $ingresos_bypaid["Q" . $i]    += 300;
	            $iva_bypaid["Q" . $i]         += 72.45;
	            $retenciones_bypaid["Q" . $i] += -21.75;
	
	             $output .= "<tr style='color:red'>
	                           <td>ERROR Q2</td>
	                           <td>-</td>
	                           <td>Q2</td>
	                           <td style='font-weight:700'>Q2 error</td>
	                           <td>+300</td>
	                           <td>+21.75</td>
	                           <td>+72.45</td>
	                        </tr>";
	                        
	         } else if ($i==3) {
	           
	            $ingresos_bypaid["Q" . $i]    += -300;
	            $iva_bypaid["Q" . $i]         += -72.45;
	            $retenciones_bypaid["Q" . $i] += 21.75;
	
	             $output .= "<tr style='color:red'>
	                           <td>ERROR Q3</td>
	                           <td>-</td>
	                           <td>Q2</td>
	                           <td style='font-weight:700'>Q3 error</td>
	                           <td>-300</td>
	                           <td>-21.75</td>
	                           <td>-72.45</td>
	                        </tr>";
	
	
	            $ingresos_bypaid["Q" . $i]    += 0; // -260;
	            $iva_bypaid["Q" . $i]         += -54.60;
	            $retenciones_bypaid["Q" . $i] += +13.49;
	
	             $output .= "<tr style='color:red'>
	                           <td style='color:red'>ERROR Q3</td>
	                           <td>-</td>
	                           <td>Q3</td>
	                           <td style='font-weight:700'>Q3 error</td>
	                           <td>-0</td>
	                           <td>-13.49</td>
	                           <td>-54.60</td>
	                        </tr>";
	
	
	         } else if ($i==4) {
	           
	            $ingresos_bypaid["Q" . $i]    += 0;
	            $iva_bypaid["Q" . $i]         += 54.60;
	            $retenciones_bypaid["Q" . $i] += -13.49;
	
	             $output .= "<tr style='color:red'>
	                           <td>ERROR Q4</td>
	                           <td>-</td>
	                           <td>Q2</td>
	                           <td style='font-weight:700'>Q2 error</td>
	                           <td>+0</td>
	                           <td>+13.49</td>
	                           <td>+54.60</td>
	                        </tr>";
				}
			}
         
             
          $output .= "<tr><th>Total Trimestre</th><th colspan=3></th>
          <th>" . number_format($ingresos_bypaid["Q" . $i],2) . "</th>
          <th>" . number_format($retenciones_bypaid["Q" . $i],2) . "</th>
          <th>" . number_format($iva_bypaid["Q" . $i],2) . "</th>
          </tr>";    
                  
         $output .= "</table>";
     
         $screen .= $output;
      }




			
		// 4 quarters - Gastos By Date PAID: 
   	for ($i=1;$i<=4;$i++) {
      	
   	   $total = 0;
   	   $output = "";
   	   
   	   if ($i==1) {
            $output = "<h1>Gastos by Date</h1>";
         }
         
         $output .= "<h3>Gastos By Date " . PREFIX_YEAR . " Q" . $i . ":</h2>";



   	   
      	$this->queryString = "  SELECT id,id_externo, f.cif, importe, retenciones, iva, emitido, contable, trimestre, FORM_390, c.cliente
                                 FROM gastos f
                                 LEFT JOIN clientes c ON c.cif = f.cif
                                 WHERE f.contable BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
                                 ORDER BY trimestre DESC, contable ASC";
      
         $res = $this->getQuery();
        
              
         $output .= "<table class='compactTable'>
                  <tr><th>Client</th><th>CIF</th><th>Nº Factura</th><th>Cobrado</th><th>Importe</th><th>Retencion</th><th>IVA</th></tr>";
      
			if ($this->afRows>0) {
	         while ($rw = $res->fetch_assoc()) {
	            
	            if ($rw["trimestre"]==1) {
		            $trimestre = " style='font-weight:700' ";
	            } else {
		            $trimestre = "";
	            }
	            
	            
	            if ($rw["iva"]<=0) {
		            $iva_css = " style='color:red;font-weight:700' ";
	            } else {
		            $iva_css = "";
	            }
	            
	            $check390 = ($rw["FORM_390"]==1) ? " X " : "";
	            
	            $output .= "<tr>
	                           <td " . $trimestre . ">" . $this->provList[ $rw["cif"] ] . "</td>
	                           <td " . $trimestre . ">" . $rw["cif"] . "</td>
	                           <td>" . $rw["id_externo"] . "</td>
	                           <td style='font-weight:700'>" . $rw["contable"] . "</td>
	
	
	                           <td>" . number_format($rw["importe"],2) . "</td>
	                           <td>" . number_format($rw["retenciones"],2) . "</td>
	                           <td " . $iva_css . ">" . number_format($rw["iva"],2) . $check390 . "</td>
	                        </tr>";
	            
	            $ing_gastos_bypaid["Q" . $i] += $rw["importe"];
	            $iva_gastos_bypaid["Q" . $i] += $rw["iva"];
	            $ret_gastos_bypaid["Q" . $i] += $rw["retenciones"];
	            
	         }  
         }
           
          $output .= "<tr><th>Total Trimestre</th><th colspan=3></th>
                        <th>" . number_format($ing_gastos_bypaid["Q" . $i],2) . "</th>
                        <th>" . number_format($ret_gastos_bypaid["Q" . $i],2) . "</th>
                        <th>" . number_format($iva_gastos_bypaid["Q" . $i],2) . "</th></tr>";    
                  
         $output .= "</table>";
     
         $screen .= $output;
      }

		$this->trimestreTotals = array(
											"ingresos_bydate" 	=> $ingresos_bydate,
											"iva_bydate" 			=> $iva_bydate  ,
											"ingresos_bypaid" 	=> $ingresos_bypaid  ,
											"iva_bypaid"  			=> $iva_bypaid ,
											"retenciones_bypaid" => $retenciones_bypaid  ,   	
											"ing_gastos_bypaid" 	=> $ing_gastos_bypaid ,
											"iva_gastos_bypaid" 	=> $iva_gastos_bypaid  ,
											"ret_gastos_bypaid"  => $ret_gastos_bypaid
											);
     
     return($screen);
   	
	}

	public function resumenBoxTrimestre(){
		
		$resumen = "";
		
		$resumen .= "<table class='compactTable' style='font-size:1.1rem;width:60%;'>
						<tr><th colspan='5'>INGRESOS</th></tr>
						<tr><th>Trimestre</th><th>Ingresos</th><th>Retenciones</th><th>IVA</th><th>IVA Ingreso</th>";
		
		for ($i=1;$i<=4;$i++) {
			
			$resumen .= "<tr>
							<td>Q" . $i . "</td>
							<td>" . number_format($this->trimestreTotals["ingresos_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["retenciones_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["iva_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["iva_bypaid"]["Q" . $i]/0.21,2) . "</td>
							</tr>"; 
		}
		
		$resumen .= "</table>";
		
		
		$resumen .= "<br><br><table class='compactTable' style='font-size:1rem;width:60%;'>
						<tr><th colspan='5'>GASTOS</th></tr>
						<tr><th>Trimestre</th><th>Ingresos</th><th>Retenciones</th><th>IVA</th><th>IVA Ingreso</th>";
		
		for ($i=1;$i<=4;$i++) {
			
			$resumen .= "<tr>
							<td>Q" . $i . "</td>
							<td>" . number_format($this->trimestreTotals["ing_gastos_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["ret_gastos_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["iva_gastos_bypaid"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["iva_gastos_bypaid"]["Q" . $i]/0.21,2) . "</td>
							</tr>"; 
		}
		
		$resumen .= "</table>";
		
		
		$resumen .= "<br><br><table class='compactTable' style='font-size:1rem;width:60%;'>
						<tr><th colspan='5'>REGIMEN CAJA</th></tr>
						<tr><th>Trimestre</th><th>Ingresos</th><th>-</th><th>IVA</th><th>IVA Ingreso</th>";
		
		for ($i=1;$i<=4;$i++) {
			
			$resumen .= "<tr>
							<td>Q" . $i . "</td>
							<td>" . number_format($this->trimestreTotals["ingresos_bydate"]["Q" . $i],2) . "</td>
							<td>-</td>
							<td>" . number_format($this->trimestreTotals["iva_bydate"]["Q" . $i],2) . "</td>
							<td>" . number_format($this->trimestreTotals["iva_bydate"]["Q" . $i]/0.21,2) . "</td>
							</tr>"; 
		}
		
		$resumen .= "</table>";
		
		
						
			/*
			$this->trimestreTotals = array(
											"ingresos_bydate" 	=> $ingresos_bydate,
											"iva_bydate" 			=> $iva_bydate  ,
											"ingresos_bypaid" 	=> $ingresos_bypaid  ,
											"iva_bypaid"  			=> $iva_bypaid ,
											"retenciones_bypaid" => $retenciones_bypaid  ,   	
											"ing_gastos_bypaid" 	=> $ing_gastos_bypaid ,
											"iva_gastos_bypaid" 	=> $iva_gastos_bypaid  ,
											"ret_gastos_bypaid"  => $ret_gastos_bypaid
											);

			*/
			
			
			$resumen .= "<br><br>
			<table class='listTable' style='font-size:0.9rem;width:100%;text-align:right;'>
						<tr><th colspan='5'>CUENTA - Totales</th></tr>
						<tr><th>Trimestre</th>
						
						<th>Ingresos</th>
						<th>Gastos</th>
						
						<th>Balance</th>
						<th>20%</th>
						
						<th>Q. Ant.</th>
						<th>Rete.</th>
						
						<th>Ingresar</th>
						
						<th> </th>
						
						<th>From IVA</th>
						<th>IVA In</th>
						
						<th>From IVA Out</th>
						<th>IVA Out</th>
						
						<th>IVA Ingresar</th>";
		
		$tot_ing			=
		$tot_ret			=
		$tot_iva			=
		$tot_gast		=
		$tri_iva			=
		$tri_gastoiva 	= 
		$tot_gastoiva	= 0;
		$ingresarPrev = 0;
		
		$year_ing = 0;
			
		for ($i=1;$i<=4;$i++) {
			
			$tot_ing			+= $this->trimestreTotals["ingresos_bypaid"]["Q" . $i];
			$tot_ret			+= $this->trimestreTotals["retenciones_bypaid"]["Q" . $i];
			$tot_iva			+= $this->trimestreTotals["iva_bypaid"]["Q" . $i];
			$tri_iva			= $this->trimestreTotals["iva_bypaid"]["Q" . $i];
			
			$tot_gast		+=	$this->trimestreTotals["ing_gastos_bypaid"]["Q" . $i];
			$tot_gastoiva	+= $this->trimestreTotals["iva_gastos_bypaid"]["Q" . $i];
			$tri_gastoiva	= $this->trimestreTotals["iva_gastos_bypaid"]["Q" . $i];
			
			
			$ingresar = ($tot_ing - $tot_gast)*0.2 - $tot_ret - $ingresarPrev;
			$year_ing += $ingresar;
			
			$ingresarIVA = $tri_iva	- $tri_gastoiva;

			
			$resumen .= "<tr>
							<td>Q" . $i . "</td>
							<td style='text-align:right;'>[01]<br>" . number_format($tot_ing,2) . "</td>
							<td style='text-align:right;'>[02]<br>" . number_format($tot_gast,2) . "</td>
							
							<td style='text-align:right;'>[03]<br>" . number_format($tot_ing-$tot_gast,2) . "</td>
							<td style='text-align:right;'>[04]<br>" . number_format( ($tot_ing-$tot_gast)*0.2, 2) . "</td>
							
							<td style='text-align:right;'>[05]<br>" . number_format( $ingresarPrev,2) . "</td>
							<td style='text-align:right;'>[06]<br> " . number_format($tot_ret,2) . "</td>
							
							<td style='font-weight:700;text-align:right;'>[07]<br>" . number_format( $ingresar,2) . "</td>
							
							<td> </td>
							
							<td style='text-align:right;'>" . number_format($tri_iva/IVA,2) . "</td>
							<td style='text-align:right;font-weight:700;'>" . number_format($tri_iva,2) . "</td>
							
							<td style='text-align:right;'>" . number_format($tri_gastoiva/IVA,2) . "</td>
							<td style='text-align:right;font-weight:700;'>" . number_format($tri_gastoiva,2) . "</td>
							
							<td style='text-align:right;font-weight:700;'>" . number_format($ingresarIVA,2) . "</td>
							
							</tr>"; 
							
			$ingresarPrev += $ingresar;
		
		
		}
		
		$resumen .= "<tr style='border-top:3px solid #999'>
							<td>YEAR</td>
							<td style='text-align:right;'>" . number_format($tot_ing,2) . "</td>
							<td style='text-align:right;'>" . number_format($tot_gast,2) . "</td>
							
							<td style='text-align:right;'>" . number_format($tot_ing-$tot_gast,2) . "</td>
							<td style='text-align:right;'>" . number_format( ($tot_ing-$tot_gast)*0.2, 2) . "</td>
							
							<td style='text-align:right;'>" . number_format( $ingresarPrev,2) . "</td>
							<td style='text-align:right;'>" . number_format($tot_ret,2) . "</td>
							<td style='font-weight:700;text-align:right;'>" . number_format( $year_ing, 2) . "</td>
							
							<td></td>
							<td style='text-align:right;'>" . number_format($tot_iva/IVA,2) . "</td>
							<td style='font-weight:700;text-align:right;'>" . number_format($tot_iva,2) . "</td>
							
							<td style='text-align:right;'>" . number_format($tot_gastoiva/IVA,2) . "</td>
							<td style='font-weight:700;text-align:right;'>" . number_format($tot_gastoiva,2) . "</td>
							
							<td style='text-align:right;font-weight:700;'>" . number_format($tot_iva-$tot_gastoiva,2) . "</td>

							
							</tr>"; 



		
		$resumen .= "</table>";





		
		return $resumen;
		
	}


	public function drawInformeRetenciones() {
   	
   	$ingresos_bydate     =
   	$iva_bydate     		= 
   	$retencion_bydate   	= 0;
   	
		$currentCIF 			= "";
		
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-31","-12-31");
   	
   	$screen              = "";
   	$output 					= "";
   	
   	$tableHead = 			"<tr><th>Client</th><th>Nº Factura</th>
   										<th>Emitido</th><th>Cobrado</th><th>Importe</th>
   										<th>Retencion</th><th>IVA</th>
   									</tr>";
   	  
   	$output.= "<table class='compactTable'>" . $tableHead;
      
      
   	$this->queryString = "  SELECT id, nif, importe, retencion, iva, emitido, cobrado, c.cliente
                              FROM facturas f
                              LEFT JOIN clientes c ON c.cif = f.nif
                              WHERE f.emitido BETWEEN '" . PREFIX_YEAR . $qstart[1] . "' AND '" . PREFIX_YEAR . $qend[4] . "'
                              AND retencion = 1
                              ORDER BY nif, id ASC";
      
      $res = $this->getQuery();
     
           
     
      while ( $rw = $res->fetch_assoc() ) {
	      
	      if ($rw["nif"]!=$currentCIF && $currentCIF!="") {
	      
	      	// Total output row:
	      	$output .= "<tr><th>Total Cliente</th><th colspan=3></th>
					          <th>" . number_format($ingresos_bydate,2) . "</th>
					          <th>" . number_format($retencion_bydate,2) . "</th>
					          <th>" . number_format($iva_bydate,2) . "</th>
				          </tr><tr><td colspan=7>&nbsp;</td></tr>" . $tableHead;  
				          
				$ingresos_bydate =
				$iva_bydate =
				$retencion_bydate = 0;

							 	
			}
			
         
         $cobrado = ($rw["cobrado"] == "1975-02-26") ? "-" : $rw["cobrado"];
         
         $output .= "<tr>
                        <td>" . $rw["cliente"] . "</td>
                        <td>" . $this->formatfacid($rw["id"]) . "</td>
                        <td style='font-weight:700'>" . $rw["emitido"] . "</td>
                        <td>" . $cobrado . "</td>

                        <td>" . number_format($rw["importe"],2) . "</td>
                        <td>" . number_format($rw["importe"]*$rw["retencion"]*RETENCION,2) . "</td>
                        <td>" . number_format($rw["importe"]*$rw["iva"]*IVA,2) . "</td>
                     </tr>";
         
         $ingresos_bydate+= $rw["importe"];
         $iva_bydate+= $rw["importe"]*$rw["iva"]*IVA;
			$retencion_bydate+= $rw["importe"]*$rw["retencion"]*RETENCION;
	   
			$currentCIF = $rw["nif"];
			
	      
	           
      }
      
      // Final output:
      	$output .= "<tr><th>Total Cliente</th><th colspan=3></th>
				          <th>" . number_format($ingresos_bydate,2) . "</th>
				          <th>" . number_format($retencion_bydate,2) . "</th>
				          <th>" . number_format($iva_bydate,2) . "</th>
			          </tr>";  
               
		$output .= "</table>";

		$screen .= $output;
      
     
     return($screen);
   	
	}
	
	public function drawInforme347() {
   	
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-31","-12-31");
   	
   	$screen              = "";
   	
   	
   	
   	// First Check:
   	$cifList = array();
		
		$this->queryString = "SELECT nif, SUM(importe) AS imptot
									FROM `facturas` 
									WHERE retencion = 0
									AND  emitido BETWEEN '" . PREFIX_YEAR . "-01-01' AND '" . PREFIX_YEAR . "-12-31'
									GROUP BY nif
									ORDER BY imptot DESC
									LIMIT 15";
		
		$res = $this->getQuery();
		
		while ($rw = $res->fetch_assoc() ) {
			if ($rw["imptot"]>3000/(1+IVA)) {
				$cifList[] = $rw["nif"];
			}
		}
		
		
		if (count($cifList) <=0 ) {
			return("No Facturas");
		}
		
		
		// LOOP FOR EACH CLIENT:
		foreach ($cifList as $nif) {
			
			$screen .= "<h1>" . $nif . "</h1>";						
		
	   	
	   	$ingresos_bypaid     = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	$iva_bypaid          = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	$retenciones_bypaid  = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	
	   	
	   	
	   	
	      // 4 quarters - Ingresos By Date PAID: 
	   	for ($i=1;$i<=4;$i++) {
	      	
	   	  $total = 0;
	   	   $output = "";
	   	   	         
	         $output .= "<h3>Trimestre " . PREFIX_YEAR . " Q" . $i . ":</h2>";
	
	
				
	      	$this->queryString = "  SELECT id, nif, importe, retencion, iva, emitido, cobrado, c.cliente
	                                 FROM facturas f
	                                 LEFT JOIN clientes c ON c.cif = f.nif
	                                 WHERE f.emitido BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
	                                 AND nif = '". $nif. "'
	                                 AND retencion = 0
	                                 ORDER BY id ASC";
	      
	         $res = $this->getQuery();
	      
	         $output .= "<table class='compactTable'>
	                  <tr><th>Client</th><th>Nº Factura</th><th>Emitido</th><th>Cobrado</th><th>Importe</th><th>IVA</th><th>Total</th></tr>";
	      
				if ($this->afRows>0) {
						
		         while ($rw = $res->fetch_assoc()) {
			         
			         $cobrado = ($rw["cobrado"] == "1975-02-26") ? "-" : $rw["cobrado"];
           
	   	         $output .= "<tr>
		                           <td>" . $rw["cliente"] . "</td>
		                           <td>" . $this->formatfacid($rw["id"]) . "</td>
		                           <td style='font-weight:700'>" . $rw["emitido"] . "</td>
		                           <td >" . $cobrado . "</td>
		                           <td>" . number_format($rw["importe"],2) . "</td>
		                           <td>" . number_format($rw["importe"]*$rw["iva"]*IVA,2) . "</td>
		                           <td>" . number_format($rw["importe"]*$rw["iva"]*(1+IVA),2) . "</td>
		                        </tr>";
		            
		            $ingresos_bypaid["Q" . $i]    += $rw["importe"];
		            $iva_bypaid["Q" . $i]         += $rw["importe"]*$rw["iva"]*IVA;
		            
		         }
		      }
	             
	          $output .= "<tr><th  style='color:yellow'>Total Trimestre Q" . $i . "</th><th colspan=3></th>
	          <th>" . number_format($ingresos_bypaid["Q" . $i],2) . "</th>
	          <th>" . number_format($iva_bypaid["Q" . $i],2) . "</th>
	          <th style='color:yellow'>" . number_format($ingresos_bypaid["Q" . $i]+$iva_bypaid["Q" . $i],2) . "</th>
	          </tr>";    
	                  
	         $output .= "</table>";
	     
	         $screen .= $output;
	      
	      } // for each trimestre
	      

		} // for each client


     return($screen);
   	
	}
	
	
	
	
	public function drawInforme347_in() {
   	
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-31","-12-31");
   	
   	$screen              = "";
   	
   	
   	
   	// First Check:
   	$cifList = array();
		
		$this->queryString = "SELECT cif, SUM(importe) AS imptot
								FROM gastos g
								WHERE emitido BETWEEN '" . PREFIX_YEAR . "-01-01' AND '" . PREFIX_YEAR . "-12-31'
								AND FORM_347 > 0
								GROUP BY g.cif
								ORDER BY imptot DESC
								LIMIT 15";
		
		$res = $this->getQuery();
		
		if ($this->afRows >0){
			while ($rw = $res->fetch_assoc() ) {
				if ($rw["imptot"]>3000/(1+IVA)) {
					$cifList[] = $rw["cif"];
				}
			}
		}
		
		
		if (count($cifList) <=0 ) {
			return("No Facturas");
		}
		
		
		// LOOP FOR EACH CLIENT:
		foreach ($cifList as $nif) {
			
			$screen .= "<h1>" . $nif . "</h1>";						
		
	   	
	   	$ingresos_bypaid     = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	$iva_bypaid          = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	$retenciones_bypaid  = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
	   	
	   	
	   	
	   	
	      // 4 quarters - Ingresos By Date PAID: 
	   	for ($i=1;$i<=4;$i++) {
	      	
	   	  $total = 0;
	   	   $output = "";
	   	   	         
	         $output .= "<h3>Trimestre " . PREFIX_YEAR . " Q" . $i . ":</h2>";
	
	
				
	      	$this->queryString = "  SELECT f.id_externo, f.cif, f.importe, f.retenciones, f.iva, f.emitido, f.contable, c.cliente
	                                 FROM gastos f
	                                 LEFT JOIN proveedores c ON c.cif = f.cif
	                                 WHERE f.emitido BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
	                                 AND f.cif = '". $nif. "'
	                                 ORDER BY f.id ASC";
	      
	         $res = $this->getQuery();
	      
	         $output .= "<table class='compactTable'>
	                  <tr><th>Client</th><th>Nº Factura</th><th>Emitido</th><th>Cobrado</th><th>Importe</th><th>IVA</th><th>Total</th></tr>";
	      
				if ($this->afRows>0) {
						
		         while ($rw = $res->fetch_assoc()) {
			         
			         $cobrado = ($rw["contable"] == "1975-02-26") ? "-" : $rw["contable"];
           
	   	         $output .= "<tr>
		                           <td>" . $rw["cliente"] . "</td>
		                           <td>" . $rw["id_externo"] . "</td>
		                           <td style='font-weight:700'>" . $rw["emitido"] . "</td>
		                           <td >" . $cobrado . "</td>
		                           <td>" . number_format($rw["importe"],2) . "</td>
		                           <td>" . number_format($rw["iva"],2) . "</td>
		                           <td>" . number_format($rw["importe"]+$rw["iva"],2) . "</td>
		                        </tr>";
		            
		            $ingresos_bypaid["Q" . $i]    += $rw["importe"];
		            $iva_bypaid["Q" . $i]         += $rw["iva"];
		            
		         }
		      }
	             
	          $output .= "<tr><th  style='color:yellow'>Total Trimestre Q" . $i . "</th><th colspan=3></th>
	          <th>" . number_format($ingresos_bypaid["Q" . $i],2) . "</th>
	          <th>" . number_format($iva_bypaid["Q" . $i],2) . "</th>
	          <th style='color:yellow'>" . number_format($ingresos_bypaid["Q" . $i]+$iva_bypaid["Q" . $i],2) . "</th>
	          </tr>";    
	                  
	         $output .= "</table>";
	     
	         $screen .= $output;
	      
	      } // for each trimestre
	      

		} // for each client


     return($screen);
   	
	}
	
	
	
	

	public function drawInformeIVAEuropaCIF() {
   	
   	$ing_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$iva_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-31","-12-31");
   	
   	$screen              = "";
   	
			
		// 4 quarters - Gastos By Date PAID: 
   	for ($i=1;$i<=4;$i++) {
      	
   	   $total = 0;
   	   $output = "";
   	  
         $output .= "<h3>Trimestre " . PREFIX_YEAR . " Q" . $i . ":</h2>";

   	   
      	$this->queryString = "  SELECT id,id_externo, f.cif, importe, retenciones, iva, emitido, contable, trimestre, c.cliente
                                 FROM gastos f
                                 LEFT JOIN clientes c ON c.cif = f.cif
                                 WHERE f.contable BETWEEN '" . PREFIX_YEAR . $qstart[$i] . "' AND '" . PREFIX_YEAR . $qend[$i] . "'
                                 AND FORM_390 = 1
                                 ORDER BY trimestre DESC, contable ASC";
      
         $res = $this->getQuery();
        
              
         $output .= "<table class='compactTable'>
                  <tr><th>Client</th><th>CIF</th><th>Nº Factura</th><th>Cobrado</th><th>Importe</th><th>Equ. IVA</th><th>IVA</th></tr>";
			
			if ($this->afRows>0) {
	         while ($rw = $res->fetch_assoc()) {
	            
	            if ($rw["trimestre"]==1) {
		            $trimestre = " style='font-weight:700' ";
	            } else {
		            $trimestre = "";
	            }
	            
	            
	             if ($rw["iva"]<=0) {
		            $iva_css = " style='color:red;font-weight:700' ";
	            } else {
		            $iva_css = "";
	            }
	            
	            $output .= "<tr>
	                           <td " . $trimestre . ">" . $this->provList[ $rw["cif"] ] . "</td>
	                           <td " . $trimestre . ">" . $rw["cif"] . "</td>
	                           <td>" . $rw["id_externo"] . "</td>
	                           <td style='font-weight:700'>" . $rw["contable"] . "</td>
	
	
	                           <td>" . number_format($rw["importe"],2) . "</td>
	                           <td>" .number_format($rw["importe"]*.21,2) . "</td>
	                           <td " . $iva_css . ">" . number_format($rw["iva"],2) . "</td>
	                        </tr>";
	            
	            $ing_gastos_bypaid["Q" . $i] += $rw["importe"];
	            $iva_gastos_bypaid["Q" . $i] += $rw["iva"];
	            
	            
	         }
	           
	          $output .= "<tr><th>Total Trimestre</th><th colspan=3></th>
	                        <th>" . number_format($ing_gastos_bypaid["Q" . $i],2) . "</th>
	                        <th>" . number_format($ing_gastos_bypaid["Q" . $i]*IVA,2) . "</th>
	                        <th>" . number_format($iva_gastos_bypaid["Q" . $i],2) . "</th></tr>";    
	                  
	         $output .= "</table>";
	     
	         $screen .= $output;
	      }
		}








     
     return($screen);
   	
	}
	
	
	
	public function drawInformeIVAEuropa() {
   	
   	$ing_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	$iva_gastos_bypaid       = array("Q1"=>0, "Q2"=>0, "Q3"=>0, "Q4"=>0);
   	
   	$qstart              = array("","-01-01","-04-01","-07-01","-10-01");
   	$qend                = array("","-03-31","-06-30","-09-31","-12-31");
   	
   	$screen              = "";
   	
			
		// 4 quarters - Gastos By Date PAID: 
    	
   	   $total = 0;
   	   $output = "";
   	     	   
      	$this->queryString = "  SELECT f.cif, SUM(importe) as importe, SUM(iva) as iva, c.cliente
                                 FROM gastos f
                                 LEFT JOIN proveedores c ON c.cif = f.cif
                                 WHERE f.contable BETWEEN '" . PREFIX_YEAR . "-01-01' AND '" . PREFIX_YEAR . "-12-31'
                                 AND FORM_390 = 1
                                 GROUP BY cif
                                 ORDER BY cif ASC";
      
         $res = $this->getQuery();
        
              
         $output .= "<table class='compactTable'>
                  <tr><th>CIF</th><th>Cliente</th><th>Importe</th><th>Equ. IVA</th><th>IVA</th></tr>";
      
         while ($rw = $res->fetch_assoc()) {
            
               $output .= "<tr>
                            <td>" . $rw["cif"] . "</td>
                            <td>" . $rw["cliente"] . "</td>
                            <td>" . number_format($rw["importe"],2) . "</td>
                           <td>" .number_format($rw["importe"]*.21,2) . "</td>
                           <td >" . number_format($rw["iva"],2) . "</td>
                        </tr>";
                        
                        $total += $rw["importe"];
            
          }
          
          
      $output .= "<tr><th colspan=2>Total:</th><th>" . number_format($total,2) . "</th></tr></table>";
  
      $screen .= $output;
  
     return($screen);
   	
	}
	
			
}


?>