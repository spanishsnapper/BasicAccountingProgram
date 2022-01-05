<?php
// Check Session

$menuItem 	= array();

$menuItem[] = array("gest_clientes.php", 		   "Clientes"									);
$menuItem[] = array("gest_proveedores.php", 		"Proveedores"								);

$menuItem[] = array("#", 							   "<span style='color:#5A9F97'>|</span>"		);

$menuItem[] = array("gest_ingresos.php",		   "Facturas"								    );
$menuItem[] = array("gest_gastos.php", 		   "Gastos"						             );

$menuItem[] = array("#", 							   "<span style='color:#5A9F97'>|</span>"		);

$menuItem[] = array("informe_trimestre.php",		"Informe Trimestre"					    );

$menuItem[] = array("informe_retenciones.php", 	"Retenciones"						       );

$menuItem[] = array("informe_iva_europe.php", 	"IVA Europa"		             );
$menuItem[] = array("informe_3000.php", 			"347 (Over 3000€)"		             );



$topMenu = ' <div id="navmenu">
        	 <ul>';
			 
foreach ($menuItem as $m) {
	$topMenu .= '<li><a href="' . $m[0] . '">' . $m[1] . '</a></li>' . "\n";
}

$topMenu .= "</ul>
			</div><br><br>";

echo $topMenu;

// ERROR CODES
if ( isset($_GET["erc"]) && is_numeric($_GET["erc"]) ) {
	
	echo ('<div id="topErr">ERROR: ' . $erAry[$_GET["erc"] ] . '</div>');
}
?>