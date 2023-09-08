<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: help.php,v 1.7 2015-04-03 11:16:23 jpermanne Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[1900]";
require_once ("$base_path/includes/init.inc.php");  
require_once ("$base_path/includes/error_report.inc.php");  

// modules propres à help.php ou à ses sous-modules

switch($whatis) {
	case 'regex':
		include("$include_path/messages/help/$helpdir/regex.txt");
		break;
	case 'import_empr':
		include("$include_path/messages/help/$helpdir/import_empr.txt");
		break;
	case 'search_empr':
		include("$include_path/messages/help/$helpdir/search_empr.txt");
		break;
	default:
		break;
	}
print "<script>self.focus();</script>";
print $footer;
pmb_mysql_close($dbh);
?>