<?php
require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/informeClass.php");

$informe = new informeClass();
$informe->getClientList();
$informe->getProvList();

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Accounting System</title>

<link href="css/styles.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
</script>

</head>
<body>
<!-- header bar -->
<?php require_once("menu.php"); ?>
<!-- end header bar -->
<div id="mainContainer">

	<div style="text-align:left">
		
		<h1>Informe IVA Europa</h1>
		
	  <?php echo $informe->drawInformeIVAEuropa(); ?>
	  
	  <?php echo $informe->drawInformeIVAEuropaCIF(); ?>
	  

	</div>
	  	
</div>
   
</body>
</html>