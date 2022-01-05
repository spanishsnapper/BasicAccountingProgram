<?php
session_start();

require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/adminClass.php");


$admin         = new adminClass();
$admin->table  = "clientes";

$res			   = $admin->listTable();

$action        = "";
$admin->rID 	= 0 ;

$baseURL = "gest_clientes.php?pg=" . $admin->pg . "&f=";

if ( isset($_REQUEST["rID"]) && $_REQUEST["rID"]<>"") {

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
		window.location.href = "<?php echo ($baseURL) . $admin->f; ?>&rID=" + $id;
	}
	
</script>
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
         
        <form action="gest_clientes_do.php" method="post" onSubmit="return checkForm();">
        	
        	<h3 style="text-align:center">Datos del Cliente</h3>
                <table class="listTable" style="width:60%">
                  
                   
                    <tr>
                        <th>NIF / Cliente</th>
                        <td><?php echo ($admin->rFormWrite("cif","text",20) ) ?></td>
                        <th>Activo?</th>
                        <td><?php echo ($admin->rFormWrite("activo","select",1,$admin->onoff) ) ?>
                    </tr>

                     <tr>
                        <th>Nombre Cliente</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("cliente","text",40) ) ?></td>
                    </tr>
                    
                     <tr>
                        <th>Dirección</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("direccion","text",40) ) ?></td>
                    </tr>
                    
                     <tr>
                        <th>Ciudad</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("ciudad","text",40) ) ?></td>
                    </tr>
                    
                     <tr>
                        <th>Provincia</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("provincia","text",20) ) ?></td>
                    </tr>
                    
                     <tr>
                        <th>Código Postal</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("codigopostal","text",20) ) ?></td>
                    </tr>
                    
                     <tr>
                        <th>Persona Contacto</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("contacto1","text",40) ) ?></td>
                    </tr>
                    
                    <tr>
                        <th>Forma Contacto</th>
                        <td colspan=3><?php echo ($admin->rFormWrite("contacto2","text",40) ) ?></td>
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
        
  <h2>Lista de Clientes</h2>
     
        <p style="text-align:right"><a href="<?php echo $baseURL; ?>&rID=0" class='btnLink'>Nuevo Usuario</a></p>
      				
        <table class="listTable" style="width:90%">
        	<tr>
	   		<th style="width:10%">NIF</th>
      	    <th style="width:25%">Cliente</th>
      	    
      	    <th style="width:15%">Dirección</th>
      	    <th style="width:15%">Ciudad</th>
      	    <th style="width:10%">Cod Postal</th>
      	    <th style="width:10%">Provincia</th>
      	    <th style="width:10%">Contacto</th>
      	    <th style="width:5%">Informe</th>
      	    <th style="width:5%">Editar</th>
      	</tr>
        <?php echo $res; ?>
        </table>
               
          
  <?php  
    } // end if show long list
    ?>
      
        
   <script>
      
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



</div>


</body>
</html>