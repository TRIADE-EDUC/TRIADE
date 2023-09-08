<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pricing_systems.inc.php,v 1.4 2016-02-22 16:45:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des systèmes de tarification
require_once($class_path.'/rent/rent_pricing_systems.class.php');
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
		$rent_pricing_systems = new rent_pricing_systems($id_entity);
		print $rent_pricing_systems->get_list();
		break;
	case 'delete':
		$rent_pricing_system = new rent_pricing_system($id);
		$deleted = $rent_pricing_system->delete();
		$rent_pricing_systems = new rent_pricing_systems($id_entity);
		$rent_pricing_systems->set_messages($deleted['msg_to_display']);
		print $rent_pricing_systems->get_list();
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
		$saved = $rent_pricing_system_grid->save();
		$rent_pricing_systems = new rent_pricing_systems($id_entity);
		print $rent_pricing_systems->get_list();
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
			$rent_pricing_systems = new rent_pricing_systems($id_entity);
			print $rent_pricing_systems->get_list();
		} else {
			$entities = entites::get_entities();
			$nb_entities = count($entities);
			if($nb_entities > 1) {
				print entites::get_display_list_entities($entities, 'pricing_systems');
			} elseif($nb_entities == 1) {
				$id_entity = $entities[0]['id'];
				$rent_pricing_systems = new rent_pricing_systems($id_entity);
				print $rent_pricing_systems->get_list();
			} else {
				//Pas d'etablissements définis pour l'utilisateur
				$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
				error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
			}
		}
		break;
}
