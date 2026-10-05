<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: groups_controller.class.php,v 1.3 2023/05/03 14:39:56 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/group.class.php");

class groups_controller extends lists_controller {
	
	protected static $model_class_name = 'group';
	
	protected static $list_ui_class_name = 'list_groups_ui';
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		global $group_query;
		
		return new static::$list_ui_class_name(array('name' => $group_query));
	}
	
	public static function proceed($id=0) {
		global $msg, $charset;
		global $action;
		global $group_search, $group_query, $empr_groupes_localises;
		global $memberID, $libelle_resp, $respID, $group_add_resp;
		global $groupID; //Utilisé dans les includes
		
		$group_search = str_replace("!!group_query!!", htmlentities(stripslashes($group_query ?? ""), ENT_QUOTES, $charset), $group_search );
		if ($empr_groupes_localises) {
			$group_search = str_replace("!!group_combo!!", group::gen_combo_box_grp(), $group_search );
		} else {
			$group_search = str_replace("!!group_combo!!", '', $group_search );
		}
		
		switch($action) {
			case 'create':
				// création d'un groupe
				$group = new group(0);
				print $group->get_form();
				break;
			case 'modify':
				// modification d'un groupe
				if($id) {
					$group = new group($id);
					print $group->get_form();
				}
				break;
			case 'update':
				if(!$libelle_resp) $respID = 0;
				$group = new group($id);
				$group->set_properties_form_form();
				$group->update();
				$group_add_resp = intval($group_add_resp);
				if ($respID && $group_add_resp) {
					$group->add_member($respID);
				}
				
				if ($group->id && $group->libelle) {
					$groupID = $group->id;
					include('./circ/groups/show_group.inc.php');
				} else {
					error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
				}
				break;
			case 'addmember':
				// ajout d'un membre
				if($id && $memberID) {
					$group = new group($id);
					$res = $group->add_member($memberID);
					if($res) {
						include('./circ/groups/show_group.inc.php');
					} else {
						error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
					}
				}
				break;
			case 'delmember':
				// suppression d'un membre
				if($id && $memberID) {
					$group = new group($id);
					$res = $group->del_member($memberID);
					if($res) {
						include('./circ/groups/show_group.inc.php');
					} else {
						error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
					}
				}
				break;
			case 'delgroup':
				// suppression d'un group
				group::delete($id);
				print pmb_bidi($group_search);
				break;
			case 'listgroups':
				// affichage résultat recherche
				$list_ui_instance = static::get_list_ui_instance();
				if(count($list_ui_instance->get_objects()) == 1) {
					$objects = $list_ui_instance->get_objects();
					$groupID = $objects[0]->id;
					include('./circ/groups/show_group.inc.php');
				} else {
					print $list_ui_instance->get_display_list();
				}
				break;
			case 'showgroup':
				// affichage des membres d'un groupe
				if ($id) require_once('./circ/groups/show_group.inc.php');
				break;
			case 'prolonggroup':
				// prolonger l'abonnement des membres d'un groupe
				if ($id) {
					$group = new group($id);
					$group->update_members();
					include('./circ/groups/show_group.inc.php');
				}
				break;
			case 'group_prolonge_pret':
				// prolonger l'abonnement des membres d'un groupe
				if ($id) {
					$group = new group($id);
					$group->pret_prolonge_members();
					require_once('./circ/groups/show_group.inc.php');
				}
				break;
			case 'showcompte':
				// Transactions d'un groupe
				if ($id) {
					$group = new group($id);
					print $group->transactions_proceed();
				}
				break;
			default:
				// action par défaut : affichage form de recherche
				print pmb_bidi($group_search);
				break;
		}
	}
	
}