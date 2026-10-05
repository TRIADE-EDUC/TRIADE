<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.15 2021/12/08 15:59:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $sub, $partial_submit, $elem, $id;

require_once($class_path."/editions_datasource.class.php");
require_once($class_path."/editions_state.class.php");

$id = intval($id);

$data = new editions_datasource();
$data->get_datasources_list();
switch($action){
	case "add" :
	case "edit" :
		$editions_state = new editions_state($id);
		if($partial_submit){//Modification par l'ajax ou par le javascript
			$editions_state->get_from_form();	
		}		
		print $editions_state->get_form();
		break;
	case "save" :
		$editions_state = new editions_state($id);
		$editions_state->get_from_form();
		$editions_state->save();
		show_state_list();
		break;
	case "show" :
		$editions_state = new editions_state($id);
		print $editions_state->show($sub,$elem);
		break;
	case "delete" :
		editions_state::delete($id);
	default:
		show_state_list();
		break; 
}

function show_state_list(){
	print list_editions_states_ui::get_instance()->get_display_list();
}