<?php

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/adminClass.php");


$admin         = new adminClass();
$admin->table  = "gastos";
$admin->getProvList();

$res			   = $admin->listTable();

$action        = "";
$admin->rID 	= 0 ;

$baseURL = "gest_gastos.php?pg=" . $admin->pg . "&f=";

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
<title>Panel de Control - Administración - Gestión de Gastos</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">


<script src="js/valforms.js" language="javascript"></script>

<script language="javascript">
	
	function getMenu($id) {
		window.location.href = "<?php echo ($baseURL) . $admin->f; ?>&rID=" + $id;
	}
	
</script>
</head>

<body>
<!-- header bar -->
<?php require_once("menu.php"); ?>
<!-- end header bar -->
<div id="mainContainer">

    <h1>Gestión de Facturas Recibidas</h1>
    
    <?php
    if ($action != "") {
        
        echo ( "<h2>" . $action . "</h2>");
    ?>
    	 <p style="text-align:right"><a href="<?php echo $baseURL; ?>" class='btnLink'>Volver a lista completa</a></p>
         
        <form action="gest_gastos_do.php" method="post" onSubmit="return checkForm();">
        	<h3 style="text-align:center">Gastos (Facturas Recibidas)</h3>
                <table class="listTable" style="width:60%">
                  
                    
                    <tr>
                        <th style="width:20%">Nº Factura</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("id_externo","text",25) ) ?>
                                              <?php echo ($admin->rFormWrite("id","hidden",10) ) ?></td>
                        <th style="width:20%">Fecha Emisión<br><br>Fecha Contabilizado</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("emitido","date",10) ) ?><br>
                                                    <?php echo ($admin->rFormWrite("contable","date",10) ) ?></td>
                    </tr>
                    
                    <tr>
                        <th>NIF / Cliente</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("cif","select",1,$admin->provList) ) ?></td>
                    </tr>
                    
                    <tr>
                        <th>Base</th>
                        <td><?php echo ($admin->rFormWrite("importe","text",10) ) ?></td>
                        <th>IVA<br><br>Retención</th>
                        <td colspan="3"><?php echo ($admin->rFormWrite("iva","text",10) ) ?><br>
                                        <?php echo ($admin->rFormWrite("retenciones","text",10) ) ?> 
                        </td>
	                  </tr>
	                  
	                  
	                   <tr>
                        <th>Form 347</th>
                        <td><?php echo ($admin->rFormWrite("FORM_347","select",1,$admin->onoff) ) ?> (Over 3000€)</td>
                        <th>IVA EU</th>
                        <td><?php echo ($admin->rFormWrite("FORM_390","select",1,$admin->onoff) ) ?> (EU Tax)</td>
	                  </tr>
	                  
	                  
	                  
	                  <tr>
                        
	                     <th style="width:20%">Forma Pago</th>
                        <td style="width:30%"><?php echo ($admin->rFormWrite("formapago","select",1,$admin->formapago) ) ?></td>
                        <th style="width:20%">Trimestre?</th>
                        <td><?php echo ($admin->rFormWrite("trimestre","select",1,$admin->onoff) ) ?></td>
	                  </tr>
	                  
	                                      					
                    <tr>
                        <th style="width:20%">Enviar</th>
                        <td colspan="3"><?php echo ($admin->rFormWrite("buttonText","button",0) ) ?> 
                        </td>
                    </tr>
                  
          </table>
                
      </form>
            
    <?php 
    } else { // if an agent has been selected to be modified
    ?>
        
  <h2>Lista de Facturas Recibidas (Gastos)</h2>
     
        <p style="text-align:right"><a href="<?php echo $baseURL; ?>&rID=0" class='btnLink'>Nuevo Gasto</a></p>
        <p><a href="gest_gastos.php?f=cif">Order by CIF</a>
      
      			
        <table class="listTable" style="width:100%">
        	<tr>
      	    <th style="width:5%">Id</th>
      	    <th style="width:8%">NIF</th>
      	    <th style="width:18%">Cliente</th>
      	    
      	    <th style="width:6%">Importe</th>
      	    <th style="width:6%">Retenciones</th>
      	    <th style="width:6%">IVA</th>
      	    
      	    <th style="width:10%">Fecha Emitido</th>
      	    <th style="width:10%">Forma Pago</th>
      	    
      	    <th style="width:5%">EU IVA</th>
      	    <th style="width:5%">Form 347</th>
      	    <th style="width:5%">Editar</th>
      	</tr>
                    <?php echo $res; ?>
        </table>

        <p style="text-align:right"><a href="<?php echo $baseURL; ?>&rID=0" class='btnLink'>Nuevo Gasto</a></p>
       
                       
  <?php  
    } // end if show long list
    ?>
      
        
   <script>
      
      document.getElementById('importe').addEventListener('change', updateValue);
      document.getElementById('emitido').addEventListener('change', updateDate);

               
      function updateValue() {
        document.getElementById('iva').value = (document.getElementById('importe').value*<?php echo IVA ?>).toFixed(2);
        document.getElementById('retenciones').value = 0;
      }
      
       function updateDate() {
        document.getElementById('contable').value = document.getElementById('emitido').value;
      }
      
      
    </script>



</div>


</body>
</html>