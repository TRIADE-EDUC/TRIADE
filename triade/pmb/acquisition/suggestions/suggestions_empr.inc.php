<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_empr.inc.php,v 1.7 2021/05/18 06:20:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $statut, $class_path;

require_once($class_path."/suggestions_map.class.php");
require_once("./acquisition/suggestions/func_suggestions.inc.php");

print list_suggestions_empr_ui::get_instance()->get_display_list();

if (!$statut) {
	$statut = getSessionSugState(); //Recuperation du statut courant
} else {
	setSessionSugState($statut);	
}

print  "<script type='text/javascript' >this.document.forms['suggestions_empr_ui_search_form'].elements['statut'].value = '".$statut."' </script>";

?>