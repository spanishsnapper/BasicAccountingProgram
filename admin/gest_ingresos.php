<?php


require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/adminClass.php");


$admin         = new adminClass();
$admin->table  = "facturas";
$admin->getClientList();
$admin->getProvList();

$res			   = $admin->listTable();

$action        = "";
$admin->rID 	= 0 ;

$baseURL = "gest_ingresos.php?pg=" . $admin->pg . "&f=";

if ( isset($_REQUEST["rID"]) && $_REQUEST["rID"]>0) {

	$admin->rID = $_REQUEST["rID"] ;
	$admin->getFullData();
	$action = "Modificar";
	
} elseif ( isset($_REQUEST["rID"]) ) {
	
	$admin->getFullData();
	$action = "Insertar";
	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Panel de Control - Administración - Gestión de Ingresos</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">


<script src="js/valforms.js" language="javascript"></script>

<script language="javascript">
	
	function getMenu($id) {
		window.open("<?php echo ($baseURL) . $admin->f; ?>&rID=" + $id, "_viewfac");
	}
	
	function getFactura($id) {
		window.open("print_factura.php?rID=" + $id, "_blank");
	}
	
	function dupFactura($id) {
      if (confirm("duplicate factura?")){
		   window.location.href = "duplicate_factura.php?rID=" + $id;
      }
	}

   function togglePaid(){

      let paidRows = document.querySelectorAll(".paid_row");
      for (row of paidRows){
         row.classList.toggle("hide_paid_row");
      };

   }
	
</script>

<style>
   .hide_paid_row{
      display: none;
   }
   </style>
</head>

<body>
<!-- header bar -->
<?php require_once("menu.php"); ?>
<!-- end header bar -->
<div id="mainContainer">

    <h1>Gestión de Facturas</h1>
    
    <?php
    if ($action != "") {
        
        echo ( "<h2>" . $action . "</h2>");
    ?>
    	 <p style="text-align:right"><a href="<?php echo $baseURL; ?>" class='btnLink'>Volver a lista completa</a></p>
         
        <form action="gest_ingresos_do.php" method="post" onSubmit="return checkForm();">
        	<h3 style="text-align:center">Datos del Usuario</h3>
                <table class="listTable" style="width:60%">
                  
                    
                    <tr>
                        <th style="width:20%">Id</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("id","hidden",10) ) ?></td>
                        <th style="width:20%">Fecha Emisión</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("emitido","text",10) ) ?></td>
                    </tr>
                    
                    <tr>
                        <th>NIF / Cliente</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("nif","select",1,$admin->clientList) ) ?></td>
                    </tr>


                     <tr>
                        <th rowspan=4>Description</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("desc_1","text",40) ) ?> <?php echo ($admin->rFormWrite("importe_1","text",6) ) ?></td>
                     </tr>
                     
                     <tr>
                        <td colspan=3><?php echo ($admin->rFormWrite("desc_2","text",40) ) ?> <?php echo ($admin->rFormWrite("importe_2","text",6) ) ?></td>
                     </tr>
                     
                     <tr>
                        <td colspan=3><?php echo ($admin->rFormWrite("desc_3","text",40) ) ?> <?php echo ($admin->rFormWrite("importe_3","text",6) ) ?></td>
                     </tr>
                     
                     <tr>
                        <td colspan=3><?php echo ($admin->rFormWrite("desc_4","text",40) ) ?> <?php echo ($admin->rFormWrite("importe_4","text",6) ) ?></td>
                     </tr>
                     
                     
                   
                    
                    <tr>
                        <th>Base</th>
                        <td><?php echo ($admin->rFormWrite("importe","text",10) ) ?></td>
                        <th>Retenciones / IVA</th>
                        <td colspan="3">Retención: <?php echo ($admin->rFormWrite("retencion","select",1,$admin->onoff) ) ?> 
                                       IVA: <?php echo ($admin->rFormWrite("iva","select",1,$admin->onoff) ) ?>
                        </td>
	                  </tr>
	                  
	                  <tr>
                        
	                     <th style="width:20%">Forma Pago</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("formapago","select",1,$admin->formapago) ) ?></td>
                        <th style="width:20%">Fecha Cobrado</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("cobrado","text",10) ) ?></td>
	                  </tr>
	                  
	                                      					
                    <tr>
                        <th style="width:20%">Enviar</th>
                        <td colspan="3"><?php echo ($admin->rFormWrite("buttonText","button",0) ) ?> 
                        </td>
                    </tr>
                  
          </table>
                
      </form>

      <script>
      <?php if ($action=="Insertar"){ ?>
      document.getElementById('emitido').value="<?php echo date("Y-m-d", time() ) ?>";
      <?php } ?>
      document.getElementById('importe_4').addEventListener('change', updateValue);
      document.getElementById('importe_3').addEventListener('change', updateValue);
      document.getElementById('importe_2').addEventListener('change', updateValue);
      document.getElementById('importe_1').addEventListener('change', updateValue);
      

      function updateValue() {
         
         var total = 0;
         for (var i=1;i<=4;i++) {
            if (document.getElementById('importe_' + i).value!="") {
               total +=  parseFloat(document.getElementById('importe_' + i).value);
            } else {
               document.getElementById('importe_' + i).value = "0"
            }
         }
        
        document.getElementById('importe').value = total;
      }
      
        
    </script>
            
    <?php 
    } else { // if an agent has been selected to be modified
    ?>
        
  <h2>Lista de Facturas</h2>
     
        <p style="text-align:right"><a href="<?php echo $baseURL; ?>&rID=0" class='btnLink'>Nueva Factura</a></p>
        <p><a href="gest_ingresos.php?f=cif">Order by client</a> | <a href="gest_ingresos.php?f=old">Show paid and old</a> | <span onclick="togglePaid()">Show/Hide Paid</span>
        </p>		
        <table class="listTable" style="width:100%">
        	<tr>
      	    <th style="width:8%">Id</th>
      	    <th style="width:8%">NIF</th>
      	    <th style="width:18%">Cliente</th>
      	    
      	    <th style="width:6%">Importe</th>
      	    <th style="width:6%">Retenciones</th>
      	    <th style="width:6%">IVA</th>
      	    <th style="width:6%">A Cobrar</th>
      	    
      	    <th style="width:10%">Fecha Emitido</th>
      	    <th style="width:10%">Fecha Cobrado</th>
      	    <th style="width:7%">Forma Pago</th>
      	    <th style="width:5%">T</th>
      	    <th style="width:5%">T</th>
      	    <th style="width:5%">Print</th>
      	    <th style="width:5%">Editar</th>
      	    <th style="width:5%">Duplicar</th>
      	</tr>
                    <?php echo $res; ?>
        </table>
               
          
  <?php  
    } // end if show long list
    ?>
      
        
  



</div>


</body>
</html>