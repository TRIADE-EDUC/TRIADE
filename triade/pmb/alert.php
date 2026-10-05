<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alert.php,v 1.19 2020/12/24 11:01:54 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CIRCULATION_AUTH|CATALOGAGE_AUTH|AUTORITES_AUTH|ADMINISTRATION_AUTH|EDIT_AUTH";  
$base_title = "\$msg[5]";
require_once ("$base_path/includes/init.inc.php");
require_once ("$class_path/alerts/alerts.class.php");
if(!SESSrights) exit;

$list_tabs_alerts_ui = new list_tabs_alerts_ui();
$aff_alerte = $list_tabs_alerts_ui->get_display();

print "<div id='contenu-frame'><div class='erreur'>$aff_alerte</div></div></body></html>" ;

pmb_mysql_close();

?>