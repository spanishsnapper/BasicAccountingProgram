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

class adminClass extends FormWrite {
	
	public 	$adminID;
	
	public 	$rID, $table;
	public 	$pg = 0;
	public 	$f = 0;
	
	public $fl;
	
	
	
	public function __construct() {
		
		parent::__construct();
		
		$this->rID 			= 0;
		$this->adminID 		= 0;
		$this->visError		= "";
		$this->paging		= "";
		
		$this->fl = new funcLib;
		
	}
	
	
	// TABLE LISTS:
	public function listTable($limit=200) {
		
		$this->limit = $limit;
		$res = "";
		
		switch ($this->table) {
			
			case "facturas"	:
			
				$res = $this->list_facturas();
				break;
			
			case "clientes"	:
			
				$res = $this->list_clientes();
				break;

			case "proveedores" :
			
				$res = $this->list_proveedores();
				break;

			case "gastos" :
			
				$res = $this->list_gastos();
				break;

			default:
			
				die("ERROR - Hook missing");
				break;
			
		}
		
		return $res;
		
	}
	
	
	private function list_clientes() {
				
		$recperpage = 120;
		$navFront 	= "";
		$navBack 	= "";
		$where		= " WHERE 1 ";
		$order		= " ORDER BY cliente ASC";
		$limit		= "";
		$op 		= "";
		$filter 	= "";
		
		$searchField = array("","nombre", "provincia", "correo", "telefono", "activo");
		
		if ($this->f <= 6 && $this->f > 0 ) {
			$order = " ORDER BY " . $searchField[$this->f] . " ASC ";
		}
	
		$CTQY = 'SELECT COUNT(*) AS ttl
				  FROM clientes c ' . $where;
		
		$this->queryString 	= $CTQY;
		$res 			= $this->getAsocSglQuery();
		$count			= $res["ttl"];
		
		if ( $count==0 ) {
			
			// No results:
			$op = '<tr><td colspan = 6>no hay clientes</td></tr>';
			
		} elseif ($count > $recperpage) {
			
			// use pagination
			$limit = " LIMIT " . $this->pg*$recperpage . ", " . $recperpage;
		
			if ( !isset($_REQUEST["srchfield"]) ) {
				// Show back if offset > 1 and next if offset < max:
				if ($this->pg > 0) {
					$navBack = '<a href="gest_clientes.php?pg=' . ($this->pg-1) . '&f=' . $this->f . '"> <<< </a>  ';
				}
				
				if ( ($this->pg+1)*$recperpage < $count ) {
					$navFront = '<a href="gest_clientes.php?pg=' . ($this->pg+1) . '&f=' . $this->f . '"> >>> </a>';
				}
			
			} else {
				
				$limit = " LIMIT 200";
			}
		}
		
		
		if ($op == "") {
			
			$FULLQ = "SELECT * FROM clientes c" . 
								 $where . $order . $limit;
					  
			$this->queryString 	= $FULLQ;
			$res = $this->getQuery();
			$op = '';
		
			if ($res !="NO ENTRIES" ) {
				
				$fl = new funcLib;
				
				while ($rw = $res->fetch_array() ) {
					
					$baseURL = "gest_clientes.php?pg=" . $this->pg . "&f=" . $this->f . "&rID=" . $rw["cif"];
										
					$op .= '<tr>
   							<td>' . $rw["cif"] . '</td>
   							<td>' . $rw["cliente"] . '</td>
   							<td>' . $rw["direccion"] . '</td>
   							<td>' . $rw["ciudad"] . '</td>
   							<td>' . $rw["provincia"] . '</td>
   							<td>' . $rw["codigopostal"] . '</td>
   							<td>' . $rw["contacto1"] . '</td>
   							<td style="text-align:center">
   							   <a href="informe_cliente.php?cif=' . $rw["cif"] . '"><img src="images/calendar.png" style="height:15px"></a>
                        </td>
   							<td style="text-align:center">
   							   <img src="images/editar.png" style="height:15px" onClick="getMenu(\'' . $rw["cif"] . '\');" >
                        </td>
						</tr>';

						  
						               	
						  
				}
				
				$op .= '<tr><td colspan=7 style="text-align:center">' . $navBack . '&nbsp;&nbsp;&nbsp;' . $navFront . '</td></tr>';
				
			} else {
				$op = '<tr><td colspan=7>no hay clientes</td></tr>';
			}
			
		} // only run the second query if there are results - saves resource squandering.
		
		return ($op);

	}
	
	private function list_proveedores() {
				
		$recperpage = 120;
		$navFront 	= "";
		$navBack 	= "";
		$where		= " WHERE 1 ";
		$order		= " ORDER BY cliente ASC";
		$limit		= "";
		$op 		= "";
		$filter 	= "";
		
		$searchField = array("","nombre", "provincia", "correo", "telefono", "activo");
		
		if ($this->f <= 6 && $this->f > 0 ) {
			$order = " ORDER BY " . $searchField[$this->f] . " ASC ";
		}
	
		$CTQY = 'SELECT COUNT(*) AS ttl
				  FROM proveedores c ' . $where;
		
		$this->queryString 	= $CTQY;
		$res 			= $this->getAsocSglQuery();
		$count			= $res["ttl"];
		
		if ( $count==0 ) {
			
			// No results:
			$op = '<tr><td colspan = 6>no hay clientes</td></tr>';
			
		} elseif ($count > $recperpage) {
			
			// use pagination
			$limit = " LIMIT " . $this->pg*$recperpage . ", " . $recperpage;
		
			if ( !isset($_REQUEST["srchfield"]) ) {
				// Show back if offset > 1 and next if offset < max:
				if ($this->pg > 0) {
					$navBack = '<a href="gest_clientes.php?pg=' . ($this->pg-1) . '&f=' . $this->f . '"> <<< </a>  ';
				}
				
				if ( ($this->pg+1)*$recperpage < $count ) {
					$navFront = '<a href="gest_clientes.php?pg=' . ($this->pg+1) . '&f=' . $this->f . '"> >>> </a>';
				}
			
			} else {
				
				$limit = " LIMIT 100";
			}
		}
		
		
		if ($op == "") {
			
			$FULLQ = "SELECT * FROM proveedores c" . 
								 $where . $order . $limit;
					  
			$this->queryString 	= $FULLQ;
			$res = $this->getQuery();
			$op = '';
		
			if ($res !="NO ENTRIES" ) {
				
				$fl = new funcLib;
				
				while ($rw = $res->fetch_array() ) {
					
					$baseURL = "gest_proveedores.php?pg=" . $this->pg . "&f=" . $this->f . "&rID=" . $rw["cif"];
										
					$op .= '<tr>
   							<td>' . $rw["cif"] . '</td>
   							<td>' . $rw["cliente"] . '</td>
   							<td>' . $rw["direccion"] . '</td>
   							<td>' . $rw["ciudad"] . '</td>
   							<td>' . $rw["provincia"] . '</td>
   							<td>' . $rw["codigopostal"] . '</td>
   							<td style="text-align:center">
   							   <img src="images/editar.png" style="height:15px" onClick="getMenu(\'' . $rw["cif"] . '\');" >
                        </td>
						</tr>';

						  
						               	
						  
				}
				
				$op .= '<tr><td colspan=7 style="text-align:center">' . $navBack . '&nbsp;&nbsp;&nbsp;' . $navFront . '</td></tr>';
				
			} else {
				$op = '<tr><td colspan=7>no hay clientes</td></tr>';
			}
			
		} // only run the second query if there are results - saves resource squandering.
		
		return ($op);

	}
	
	private function list_facturas() {
				
		$recperpage = "";
		$navFront 	= "";
		$navBack 	= "";
		$where		= " WHERE id > 0 ";
		$order		= " ORDER BY id ASC ";
		$limit		= "";
		$op 		= "";
		$filter 	= "";
	
      if ( isset($_GET["f"]) && $_GET["f"]=="cif" ) {
         $order = " ORDER BY cif, id ASC ";
      }
      
      $skipold = (isset($_GET["f"]) && $_GET["f"]=="old" ) ? false : true;
	      
		
		$this->queryString =  "SELECT f.* , c.cliente
      								FROM facturas f
      								LEFT JOIN clientes c ON c.cif = f.nif "
      								. $where . $order;
					
		$res = $this->getQuery();
		
		if ( $this->afRows <= 0 ) {
			
			// No results:
			$table = '<tr><td colspan=8>No facturas</td></tr>';
			
		} else {

			// SHIPPING GRID TABLE:
			$table = "";
			
			while ( $rw = $res->fetch_assoc() ) {
				
				// skip old rows that are already paid
				if ( $skipold && strtotime($rw["cobrado"]) < strtotime(START_YEAR) && strtotime($rw["cobrado"]) > strtotime("2010-01-01") ) {
				
					// Skip this row
				
				
				} else {
					
					
	   			
	   			$retencion  = $rw["importe"]*RETENCION*$rw["retencion"]*(-1);
	   			$iva        = $rw["importe"]*IVA*$rw["iva"];
	   			$abonar     = $rw["importe"] + $retencion + $iva;
	   			$trimestreE = "Q" . ( ceil(substr($rw["emitido"],5,2)/3) );
	   			
	   			if ( strtotime($rw["cobrado"]) > strtotime("2019-01-01 00:00:00") ) {
	      	   	$trimestreC = "Q" . ( ceil(substr($rw["cobrado"],5,2)/3) );
	      	   	$cobrado = $rw["cobrado"];
				$cobrado_show = "paid_row";


	      	   } else {

	   			   $trimestreC = "-";
	   			   $cobrado = "-";
				   $cobrado_show = "";
				}
	            
					$table .= '<tr class="' . $cobrado_show . '">
							<td style="text-align:left">' . $this->formatfacid($rw["id"]) . '</td>
							<td style="text-align:left">' . $rw["nif"] . '</td>
							<td style="text-align:left">
							   <a href="gest_clientes.php?rID=' .$rw["nif"] . '">' . $this->clientList[ $rw["nif"] ] . '</a>
							</td>
							<td style="text-align:right">' . number_format($rw["importe"],2). '</td>
							<td style="text-align:right">' . number_format($retencion,2). '</td>
							<td style="text-align:right">' . number_format($iva,2) . '</td>
							<td style="text-align:right">' . number_format($abonar,2) . '</td>
							
							<td style="text-align:center">' . $rw["emitido"] . '</td>
							<td style="text-align:center">' . $cobrado . '</td>
							<td style="text-align:left">' . $rw["formapago"] . '</td>
							
							<td style="text-align:center" class="'.$trimestreE.'">' . $trimestreE . '</td>
							<td style="text-align:center" class="'.$trimestreC.'">' . $trimestreC . '</td>
							<td style="text-align:center"><img src="images/avisar.png" style="height:15px" onClick="getFactura(' . $rw["id"] . ');" ></td>
							
							<td style="text-align:center"><img src="images/editar.png" style="height:15px" onClick="getMenu(' . $rw["id"] . ');" ></td>
							<td style="text-align:center"><img src="images/1.png" style="height:15px" onClick="dupFactura(' . $rw["id"] . ');" ></td>
							</tr>';
				
				}
			}
			
		}
			
		
		return ($table);
	}
	
	private function list_gastos() {
				
		$recperpage = "";
		$navFront 	= "";
		$navBack 	= "";
		$where		= " WHERE f.id > 0 AND contable >= '" . START_YEAR . "'";
		$order		= " ORDER BY f.id ASC ";
		$limit		= "";
		$op 		= "";
		$filter 	= "";
		
		if ( isset($_GET["f"]) ) {
			$order = " ORDER BY cif ASC ";
		}
		
		
		$this->queryString =  "SELECT f.* , c.cliente
      								FROM gastos f
      								LEFT JOIN proveedores c ON c.cif = f.cif "
      								. $where . $order;
					
		$res = $this->getQuery();
		
		if ( $this->afRows <= 0 ) {
			
			// No results:
			$table = '<tr><td colspan=8>No facturas</td></tr>';
			
		} else {

			// SHIPPING GRID TABLE:
			$table = "";
			
			while ( $rw = $res->fetch_assoc() ) {
   			
          	$bgIVA =  ($rw["iva"]<=0) ? "warn" : "";
          	$bg390 =  ($rw["FORM_390"]>0) ? "warn" : "";
          	
          	$trimestreE = "Q" . ( ceil(substr($rw["contable"],5,2)/3) );
   			
				$table .= '<tr>
      						<td style="text-align:left">' . $rw["id"] . '</td>
      						<td style="text-align:left">' . $rw["cif"] . '</td>
      						<td style="text-align:left">' . $this->provList[ $rw["cif"] ] . '</td>
      						
      						<td style="text-align:right">' . number_format($rw["importe"],2). '</td>
      						<td style="text-align:right">' . number_format($rw["retenciones"],2). '</td>
      						<td style="text-align:right" class="'.$bgIVA.'">' . number_format($rw["iva"],2) . '</td>
      												
      						<td style="text-align:center">' . $rw["emitido"] . '</td>
      						<td style="text-align:left">' . $rw["formapago"] . '</td>
      						
      						<td style="text-align:center" class="'.$bg390.'">' . $rw["FORM_390"] . '</td>
      						<td style="text-align:center" class="'.$trimestreE.'">' . $trimestreE . '</td>
						
      						<td style="text-align:center"><img src="images/editar.png" style="height:15px" onClick="getMenu(' . $rw["id"] . ');" ></td>
   						</tr>';
			}
			
		}
			
		
		return ($table);
	}
	
		
	public function getFullData() {
		
		if ( (!is_numeric( $this->rID) || $this->rID<=0) && ($this->table!="clientes" && $this->table!="proveedores") ) {
			$this->dbVals["buttonText"] = "Nuevo";
			return(false);
			
		} else {
			
			$id = ($this->table=="clientes" || $this->table=="proveedores" ) ? "cif" : "id";
			
			$QUERY = 'SELECT *
					  FROM ' . $this->table . '
					  WHERE ' . $id . ' = "' . $this->real_escape_string($this->rID) . '"';
								  
			$this->queryString 	= $QUERY;
					
			$this->dbVals = $this->getAsocSglQuery();
			$this->dbVals["buttonText"] = "Modificar";
			
		}
		
	}
	
		
}


?>