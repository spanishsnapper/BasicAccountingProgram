<?php
require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/informeClass.php");

$informe = new informeClass();
$informe->getClientList();
$informe->getProvList();

$desglose = $informe->drawInformeTrimestreIRPF();
$resumen = $informe->resumenBoxTrimestre();

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

<!-- 2019:::
	
	<div>
		
		<h3>NOTES: </h3>
		<p>The following facturas have been accounted for but not paid (so that retenciones match):</p>
		<ul>
			<li>52,57,67,75 (CCA). </li>
			<li>118 (Suites Sherry)</li>
			<li>116 (Harley Davidson)</li>
		</ul>
		
	</div>

-->
	
	<div>
		
		<h1>Informe Trimestre (for 110 / 130 forms)</h1>
		
	  <?php echo $resumen; ?>
	  
	  <h2>Desglose</h2>
		
	  <?php echo $desglose; ?>


	</div>
	  	
</div>
   
</body>
</html>