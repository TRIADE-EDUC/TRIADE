<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.17 2021/02/25 13:27:32 dgoron Exp $


if (stristr ( $_SERVER ['REQUEST_URI'], ".inc.php" ))
	die ( "no access" );

global $class_path, $include_path, $sub, $action, $msg;
global $form_actif, $date_purge, $lang, $id, $statutDef, $sens, $idLoc;

require_once ($class_path . "/transferts.class.php");
require_once ($class_path . "/transfert.class.php");

// action en fonction du type
switch ( $sub) {
	case 'general' :
	case 'circ' :
	case 'opac' :
		//l'entete de la page
		print "<br />";
		
		$list_ui_class_name = "list_configuration_transferts_".$sub."_ui";
		$list_ui_class_instance = $list_ui_class_name::get_instance();
		switch ( $action) {
			case "modif" :
				//on est en modification
				print $list_ui_class_instance->get_display_list();
				break;
			case "save" :
			case "enregistre" :
				//on enregistre les modifications
				if ($form_actif) {
					if ($sub == 'opac') { //Cas particulier quand on bascule d'une valeur à une autre du sélecteur
						$new_choix_lieu_opac = $list_ui_class_instance->get_objects_type()."_transferts_choix_lieu_opac";
						global ${$new_choix_lieu_opac};
						transferts::check_loc_retrait_resas(${$new_choix_lieu_opac});
					}
					$list_ui_class_instance->save();
				}
				//puis on affiche le tableau
				print $list_ui_class_instance->get_display_list();
				break;
			default :
				//on affiche le tableau
				print $list_ui_class_instance->get_display_list();
				break;
		}
		break;
	case 'ordreloc' :
		//gere l'ordre des localisations pour la recherche d'un exemplaire
		print "<br />";
		switch ( $action) {
			case "enregistre" :
				//on enregistre les modifications
				transferts::save_location_order( $sens, $idLoc );
				//puis on affiche le tableau
				print transferts::get_display_location_order();
				break;
			default :
				//on affiche le tableau
				print transferts::get_display_location_order();
				break;
		}
		break;
	case 'statutsdef' :
		//gere le statut par défaut de l'exemplaire lors de la réception
		print "<br />";
		switch ($action) {
			case "modif" :
				//on est en modification
				print transferts::get_display_default_status($id);
				break;
			case "enregistre" :
				//on enregistre les modifications
				transferts::save_default_status($id, $statutDef);
				//puis on affiche le tableau
				print list_configuration_transferts_statutsdef_ui::get_instance()->get_display_list();
				break;
			default :
				//on affiche le tableau
				print list_configuration_transferts_statutsdef_ui::get_instance()->get_display_list();
				break;
		}
		break;
	case 'purge' :
		//gere le statut par défaut de l'exemplaire lors de la réception
		print "<br />";
		
		switch ( $action) {
			case "purge" :
				//on enregistre les modifications
				transferts::admin_purge_historique ( $date_purge );
				//le message de purge effectuée
				echo str_replace ( "!!date_purge!!", formatdate ( $date_purge ), $msg ["admin_transferts_message_purge"] );
				//puis on affiche l'ecran
				print transferts::get_display_purge();
				break;
			default :
				//on affiche l'ecran de purge
				print transferts::get_display_purge();
				break;
		}
		break;
	default :
		print "<br />";
		//on affiche le message de présentation
		include ("$include_path/messages/help/$lang/admin_transferts.txt");
		break;
}

?>
