<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: actions_ajax.inc.php,v 1.7 2021/08/06 11:50:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $quoifaire;
global $id_action;

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