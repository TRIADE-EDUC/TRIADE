<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alert.php,v 1.18 2018-09-10 13:21:36 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CIRCULATION_AUTH|CATALOGAGE_AUTH|AUTORITES_AUTH|ADMINISTRATION_AUTH|EDIT_AUTH";  
$base_title = "\$msg[5]";
require_once ("$base_path/includes/init.inc.php");  
if(!SESSrights) exit;
require_once("./alert/message.inc.php");
if ($current_alert=="circ") {
	require_once("./alert/resa.inc.php");
	require_once("./alert/expl_todo.inc.php");		
	require_once("./alert/empr.inc.php");
	
	if($pmb_scan_request_activate) {
		require_once("./alert/scan_request.inc.php");
	}
	//pour les alertes de transferts
	if ($pmb_transferts_actif && (SESSrights & TRANSFERTS_AUTH))
		require_once ("./alert/transferts.inc.php");
}
if ($current_alert=="catalog") {
	require_once("./alert/tag.inc.php");
	require_once("./alert/sugg.inc.php");
	require_once("./alert/bulletinage.inc.php");
	if($pmb_pnb_param_login) {
	   require_once("./alert/pnb.inc.php");
	}
}

if ($current_alert=="acquisition") {
	require_once("./alert/sugg.inc.php");
}
if ($current_alert=="demandes") {
	require_once("./alert/demandes.inc.php");
}

print "<div id='contenu-frame'><div class='erreur'>$aff_alerte</div></div></body></html>" ;

pmb_mysql_close($dbh);

?>