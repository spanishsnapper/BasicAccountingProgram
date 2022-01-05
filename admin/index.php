<?php
require_once("../_classes/config.php");
require_once("../_classes/dbClass.php");
require_once("../_classes/informeClass.php");

$informe = new informeClass();

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
		
		
	  <?php echo $informe->drawSummary(); ?>
	  
	  <?php echo $informe->drawClientTopTen(); ?>

	</div>
	  	
</div>
   
</body>
</html>