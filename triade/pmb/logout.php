<?php

// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: logout.php,v 1.5 2015/04/03 11:16:23 jpermanne Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[8]";
$base_noheader=1;
require_once ("$base_path/includes/init.inc.php");  
 
// modules propres à logout.php ou à ses sous-modules

sessionDelete('PhpMyBibli');
pmb_mysql_close($dbh);

// appel de l'index

header("Location: index.php");
exit();

?>