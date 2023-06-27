<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: actions_ajax.inc.php,v 1.6 2014-04-02 08:50:38 mhoestlandt Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/demandes_actions.class.php");
require_once("$include_path/templates/demandes_actions.tpl.php");

switch($quoifaire){
	
	case 'change_read_action':
		
		$action=new demandes_actions($id_action,false);		
		demandes_actions::change_read($action,"_gestion");		
		ajax_http_send_response(demandes_actions::action_majParentEnfant($id_action, $action->num_demande,"_gestion"));
	break;

}

?>