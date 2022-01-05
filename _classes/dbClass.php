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

/* ©2013 Michael Corrigan - autorizado para uso de HOSTALALHAJAPLAYA.COM   */
/* 			webmaster@CDWweb.com    Tel: +34 615 18 73 74   ESPAÑA       */


/* Database Connect & Select */

require_once("config.php");


class DBConnect extends mysqli {
	
	public $dbHandle;
	public $queryString;
	public $queryRes;	
	public $connected = false;	
	public $textError;
	
	public $clientList, $provList;
	
	public $afRows 	= 0;
	protected $Rows = 0;
	
	private $payType 	= PAYTYPE;
	private $action 	= "bookcheck.php";	
	private $actionfin  = "bookconf.php";	// not using at this time.
	
	// reg-ex for checking dates and times:
	public $dtp 	= '/^(\d{2})\/(\d{2})\/(\d{4})$/';
	public $hrp 	= '/^(\d{2})\:(\d{2})	$/';
	public $onoff	= array(1=>"Si",0=>"No");


   public $formapago	= array("Asignar"=>"Sin asignar", "Xfer"=>"Transferencia", "Cash"=>"Efectivo", "Card"=>"Credit Card", "PayPal"=>"PayPal");
	
	public $provincias = array("A CORUÑA", "ALAVA", "ALBACETE", "ALICANTE", "ALMERIA", "ASTURIAS", "AVILA", "BADAJOZ", "BARCELONA", "BURGOS", "CACERES", "CADIZ", "CANTABRIA", "CASTELLON", "CEUTA", "CIUDAD REAL", "CORDOBA", "CUENCA", "GIRONA", "GRANADA", "GUADALAJARA", "GUIPUZCOA", "HUELVA", "HUESCA", "ILLES BALEARS", "JAEN", "LA RIOJA", "LAS PALMAS", "LEON", "LLEIDA", "LUGO", "MADRID", "MALAGA", "MELILLA", "MURCIA", "NAVARRA", "OURENSE", "PALENCIA", "PONTEVEDRA", "SALAMANCA", "SANTA CRUZ DE TENERIFE", "SEGOVIA", "SEVILLA", "SORIA", "TARRAGONA", "TERUEL", "TOLEDO", "VALENCIA", "VALLADOLID", "VIZCAYA", "ZAMORA", "ZARAGOZA");
	
	
	
	
	public function __construct() {
		
		$this->textError = "";
		
		parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		if ($this->connect_errno != 0) {
			$this->connected = false;
			die($this->connect_errno . " " . $this->connect_error );
		} else {
			$this->connected = true;
		}
		
		$this->set_charset("utf8");
		
	}
	
	public function DBOff() {
			
		if ($this->connected) {
			$this->close();
		}
		
	}
	
	public function setQuery() {		// Makes changes to database (use for Updates / Inserts)
		if ( $this->connected && is_string($this->queryString) ) {
			
			if (DEBUG && VERBAL) { echo "<br>SET QUERY:<br/>" . $this->queryString; }
			
			if ( !$this->query( $this->queryString ) ) {
				$this->showError( $this->error );
			}
		
			if (strstr(	$this->queryString,"INSERT") ) {
				$this->afRows = $this->insert_id;
			} elseif (strstr($this->queryString, "UPDATE") or strstr($this->queryString, "DELETE") ) {
				$this->afRows =$this->affected_rows;
			}
			
			return(true);
			
		} else {
			
			return(false);
		}
	}
	
	public function getAsocSglQuery() {		// Pull one row from database
		
		$errorAry = array("ERROR");
		
		if ( !is_string( $rw = $this->getQuery() ) && $this->Rows==1) {
			
			if (DEBUG && VERBAL) { echo "(ASOC)"; }
			
			$asoc = $rw->fetch_array();
			return($asoc);
			
		} else {
			
			return($errorAry);
		}
			
		
	}
	
	
	
	public function getQuery() {		// Pulls a recordset from the database
	
		if ($this->connected && is_string($this->queryString) ) {
			
			if (DEBUG && VERBAL) { echo "<br>GET QUERY:<br/>" . $this->queryString; }
			
			$this->queryRes = $this->query( $this->queryString );
				
			if ( $this->errno > 0 ) {
				$this->showError( $this->error );
			} else {
				$this->Rows = $this->affected_rows;
				$this->afRows = $this->Rows;
			}
			
			
			if ( $this->Rows > 0 ) {
				return ($this->queryRes);
			} else {
				return("NO ENTRIES");
			}
			
		} else {
			
			return("DB NOT CONNECTED");
			
		}
	}
	
	
	private function showError($error) {
		
		$this->textError = $error;
		if (strstr($error,"DELETE") ) {
			$error = ("No se puede borrar. Lo mas probable es que existen datos dentro de este apartado" . $error );
		}
		
		if ( strstr($error,"Duplicate entry") ) {
			$error = $error = ("Ha intentado actualizar una entrada con un dato que ya existe (usuario, correo, Id, etc...)" );
		}
		
		if ( strstr($error,"error in your SQL syntax") ) {
			$error = ("Error de sintaxis." . $error);
		}
		
		if ( strstr($error,"a foreign key constraint") ) {
			$error = ("No se puede eliminar porque tiene datos en otras zonas (ej. Examenes, Mensajes)." );
		}
		
		if (DEBUG && VERBAL) {
			echo ("<br>" . $error);
		}
		//header("location:index.php?erc=99&ert=" . $error);
		
	}
	
	public function logger($action, $table, $user, $id) {
		/*	
		$LOGGING = "INSERT INTO registroacciones 
					(fecha, usuario, accion, tabla, id_accion)
					VALUES
					(CURRENT_TIMESTAMP, " . $user . ", '" . $action . "', '" . $table . "', " . $id . ")";
	
		$this->queryString=$LOGGING;
		$this->setQuery();
		*/	
	}
	

}


class FormWrite extends DBConnect {
	
	public $dbVals = array();
	
	function __construct() {		
		parent::__construct();				
	}
	
	function rFormWrite($field, $type, $count=0, $ary=array() ) {		
		if (!isset($field) || !isset($type) || !is_numeric($count) ) {
			return ("ERROR");
		}
	
		$value = ( !isset($this->dbVals[$field]) ) ? "" : $this->dbVals[$field];
		
		switch ($type) {
			
			case("text") :
				$return = "<input name='{$field}' id ='{$field}' value='{$value}' type='text' size='{$count}' maxlength='200'  autocomplete='off' />";
				break;
				
			case("file") : 	
			
				$lnk = (strlen($value)>3) ? "<br/><a href='" . TEMARIOBASE . $value . "' target='_blank'>Archivo actual: " . $value . "</a>" : "";
				
				$return = "<input name='{$field}' id ='{$field}' type='file' size='{$count}' />" . $lnk;
				break;
				
			case("numbertext") :
				$return = "<input name='{$field}' id ='{$field}' value='" . number_format( (float)$value ,2,".","") . "' type='text' size='{$count}' maxlength='10' />";
				break;
				
			case("password") :
				$return = "<input name='{$field}' id ='{$field}' value='{$value}' type='password' size='{$count}' maxlength='30'  autocomplete='off'/>";
				break;
				
			case("hidden") :
				$return = "<input name='{$field}' id ='{$field}' value='{$value}' type='hidden' />{$value}";
				break;
				
			case("textarea") :
				$return = "<textarea name='{$field}' id ='{$field}' cols='{$count}' rows='4'>{$value}</textarea>";
				break;
				
			case("button"):
				$return = "<input type='submit' name='{$field}' id ='{$field}' value='{$value}' />";
				break;
				
			case("checkbox") :
				$check = ($value==1) ? "checked" : "";
				$return = "<input name='{$field}' name='{$field}' type='checkbox' value='SI' " . $check . " />";
				break;
				
			case("select"):
				$return = "<select name='{$field}' id ='{$field}'> \n";
				
				foreach ($ary as $key => $val ) {
					
					$selected = ( $value == $key ) ? " selected" : "";
					$labl = trim( str_replace("_","", $val ) );
					
					$return .= "<option value='{$key}' label='{$labl}' {$selected}>{$val}</option> \n";
				}
				
				$return .= "</select> \n";
				break;
				
			case("selectm"):
			
				$valueAry = explode(",",$value);
				$return = "<select size='10' multiple='multiple' name='{$field}[]' id ='{$field}'> \n";
				
				foreach ($ary as $key => $val ) {
					
					$selected = "";
					foreach ($valueAry as $v)	{
						if ( $v == $key ) {
							$selected =  "selected";
						}
					}
					$labl = trim( str_replace("_","", $val ) );
					
					$return .= "<option value='{$key}' label='{$labl}' {$selected}>{$val}</option> \n";
				}
				
				$return .= "</select> \n";
				break;
				
				
			default :
				$return = "ERROR EN CAMPO	";
				break;
		}
		
		return($return);			
	}	
	
	
	public function popAgencias(){
	
		$this->queryString = "SELECT agid, empresa FROM agencias ORDER BY empresa";
		$res = $this->getQuery();
		if ($this->affected_rows>0) {
			while ($rw = $res->fetch_array()){
				$this->agentetList[ $rw["agid"] ] = $rw["empresa"];	
			}
		}
	}
	
	
	
	public function getClientList() {
   	
   	$this->queryString = "SELECT cif, cliente FROM clientes WHERE activo = 1 ORDER BY cliente ASC";
   	
   	$res= $this->getQuery();
   	$this->clientList["-"] = "Sin asignar";
   	
   	while ($rw = $res->fetch_assoc() ) {
      	$this->clientList[$rw["cif"]] = $rw["cliente"];
   	}
   	
   	
	}
	
	public function getProvList() {
   	
   	$this->queryString = "SELECT cif, cliente FROM proveedores WHERE activo = 1 ORDER BY cliente ASC";
   	
   	$res= $this->getQuery();
   	$this->provList["-"] = "Sin asignar";
   	
   	while ($rw = $res->fetch_assoc() ) {
      	$this->provList[$rw["cif"]] = $rw["cliente"];
   	}
   	
   	
	}



	protected function formatfacid($id) {
   	
   	$id_fac = substr($id, 0,4) . "-" . substr($id, 4,3);
   	$id_fac = "<a href='gest_ingresos.php?pg=0&f=0&rID=" . $id . "' target='_blank'>" . $id_fac . "</a>";
   	return($id_fac);
	}



	
	
}

class funcLib {
	
	public function convTimeStamp($dt) {
		
		$dtAry = explode( "-", substr($dt,0,10) );
		$niceDate = $dtAry[2] . "/" . $dtAry[1] . "/" . $dtAry[0];
		return($niceDate);
	}
	
	
	public function drawDate($dt, $style="long") {								// Converts MySQL Date into Spanish Version	
		if (LANG=="" || LANG=="ESP" ) {
			$mthAry = array ("none","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
		} elseif (LANG=="" || LANG=="DEU" ) {
			$mthAry = array ('none', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'); 
		} elseif (LANG=="FRE" ) {
			$mthAry = array ('rien','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'); 
		} else {
			$mthAry = array ("none","January","Febrary","March","April","May","June","July","August","September","October","November","December"); 
		}	
		$mth = substr($dt,5,2);
		if ( substr($mth,0,1)==0 )  { $mth = substr($mth,1,1); }  // remove leading zero
		$mthText = substr($dt,8,2) . " " . $mthAry[ $mth ] . " " . substr($dt,0,4);
		
		if ($style == "short") {
			$mthText = substr($dt,8,2) . " " . substr($mthAry[ $mth ], 0, 3);
		} else {
			$mthText = substr($dt,8,2) . " " . $mthAry[ $mth ] . " " . substr($dt,0,4);
		}
			
		return ($mthText);
	}
	
		
	public function genClave() {
		$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			"k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v",
			"V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
	
		$str = '';
	
		for($i = 1; $i <= 10; ++$i)
		{
			$ch = rand(0, count($set)-1);
			$str .= $set[$ch];
		}
	
		return($str);
	}
	
	
		
}




?>