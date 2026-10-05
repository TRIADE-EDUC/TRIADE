<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_acquisition.php,v 1.2 2015/08/13 08:06:36 jpermanne Exp $


// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "ACQUISITION_AUTH";  
$base_title = "\$msg[acquisition_menu_title]"; 
$base_noheader=1;

require_once ("$base_path/includes/init.inc.php");  

$acquisition_no_html = 1;

//pour éviter une mauvaise entrée
if ($action != "print_budget") {
	die();
}

require_once("./acquisition/acquisition.inc.php");

// deconnection MYSql
pmb_mysql_close($dbh);
?>