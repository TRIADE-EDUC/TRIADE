<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pricing_systems.inc.php,v 1.6 2021/04/08 11:41:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id, $id_entity, $msg, $charset;

// gestion des systèmes de tarification
require_once($class_path.'/rent/rent_pricing_system.class.php');
require_once($class_path.'/rent/rent_pricing_system_grid.class.php');
require_once($class_path.'/entites.class.php');

switch($action) {
	case 'edit':
		$rent_pricing_system = new rent_pricing_system($id);
		print $rent_pricing_system->get_form();
		break;
	case 'save':
		$rent_pricing_system = new rent_pricing_system($id);
		$rent_pricing_system->set_properties_from_form();
		$rent_pricing_system->save();
		$list_configuration_acquisition_pricing_systems_ui = new list_configuration_acquisition_pricing_systems_ui(array('num_entity' => $id_entity));
		print $list_configuration_acquisition_pricing_systems_ui->get_display_list();
		break;
	case 'delete':
		$rent_pricing_system = new rent_pricing_system($id);
		$deleted = $rent_pricing_system->delete();
		$list_configuration_acquisition_pricing_systems_ui = new list_configuration_acquisition_pricing_systems_ui(array('num_entity' => $id_entity));
		$list_configuration_acquisition_pricing_systems_ui->set_messages($deleted['msg_to_display']);
		print $list_configuration_acquisition_pricing_systems_ui->get_display_list();
		break;
	case 'duplicate':
		$rent_pricing_system = new rent_pricing_system($id);
		$rent_pricing_system->set_id(0);
		$rent_pricing_system->save();
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		$rent_pricing_system_grid->set_pricing_system($rent_pricing_system);
		$rent_pricing_system_grid->save();
		print $rent_pricing_system->get_form();
		break;
	case 'grid_edit':
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		print $rent_pricing_system_grid->get_form();
		break;
	case 'grid_save':
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		$rent_pricing_system_grid->set_properties_from_form();
		$rent_pricing_system_grid->save();
		$list_configuration_acquisition_pricing_systems_ui = new list_configuration_acquisition_pricing_systems_ui(array('num_entity' => $id_entity));
		print $list_configuration_acquisition_pricing_systems_ui->get_display_list();
		break;
	case 'grid_reset':
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		$rent_pricing_system_grid->init_default_grid();
		$rent_pricing_system_grid->save();
		print $rent_pricing_system_grid->get_form();
		break;
	case 'grid_display':
		$rent_pricing_system_grid = new rent_pricing_system_grid($id);
		print $rent_pricing_system_grid->get_display();
		break;
	default:
		if($id_entity) {
			print list_configuration_acquisition_pricing_systems_ui::get_instance(array('num_entity' => $id_entity))->get_display_list();
		} else {
			$entities = entites::get_entities();
			$nb_entities = count($entities);
			if($nb_entities > 1) {
				print entites::get_display_list_entities($entities, 'pricing_systems');
			} elseif($nb_entities == 1) {
				$id_entity = $entities[0]['id'];
				print list_configuration_acquisition_pricing_systems_ui::get_instance(array('num_entity' => $id_entity))->get_display_list();
			} else {
				//Pas d'etablissements définis pour l'utilisateur
				$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
			}
		}
		break;
}
