<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.28.8.1 2024/10/15 08:19:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $item, $object_type, $msg, $caddie, $empr_location_id, $empr_codestat_filter, $empr_categ_filter, $action, $idemprcaddie, $sub_action;
global $empr_sort_rows, $empr_show_rows, $empr_filter_rows, $clause, $filtered_query, $charset, $selected_objects, $pmb_relance_adhesion, $empr_statut_edit;
global $list_ui_objects_type;

include_once("$include_path/empr_cart.inc.php");
include_once("$class_path/empr_caddie.class.php");
require_once ($class_path."/list/readers/list_readers_edition_ui.class.php");
require_once ($class_path."/list/readers/list_readers_circ_ui.class.php");

if ($item) {
	switch ($object_type) {
		case "GROUP":
			print "<h3>".$msg["print_cart_title"]."</h3>\n";
			$groupe = new group($item);
			print '<strong>'.$groupe->libelle.' ('.$groupe->nb_members.' '.$msg["group_nb_empr"].')</strong><br />';
			$items = array();
			foreach($groupe->members as $member) {
				$items[] = $member['id'];
				print '<strong>'.$member['prenom'].' '.$member['nom'].'</strong><br />';
			}
			break;
		case "EMPR":
		default:
			print "<h3>".$msg["print_cart_title"]."</h3>\n";
			$emprunteur = new emprunteur($item,'', FALSE, 0);
			print '<strong>'.$emprunteur->prenom." ".$emprunteur->nom.'</strong><br />';
			break;
	}	
}

if (!$object_type) $object_type="EMPR";
if(!isset($caddie)) $caddie = array();
if(!isset($empr_location_id)) $empr_location_id = '';
if(!isset($empr_codestat_filter)) $empr_codestat_filter = '';
if(!isset($empr_categ_filter)) $empr_categ_filter = '';
switch ($action) {
	case 'add_item':
		if($idemprcaddie)$caddie[0]=$idemprcaddie;
		foreach($caddie  as $idemprcaddie) {
			$myCart = new empr_caddie($idemprcaddie);
			if (is_array($items)) {
				foreach ($items as $item) {
					$myCart->add_item($item);
				}
			} else {
				$myCart->add_item($item);
			}
			$myCart->compte_items();
		}	
		print "<script type='text/javascript'>window.close();</script>"; 
		break;
	case 'new_cart':
		break;
	case 'del_cart':
	case 'valid_new_cart':		
	case 'add_result':
		if ($sub_action=='update') {
			if($item) {
				switch($object_type) {
					case "GROUP" :
						foreach($groupe->members as $member) {
							foreach($caddie  as $idemprcaddie) {
								$myCart = new empr_caddie($idemprcaddie);
								$myCart->add_item($member['id']);
								$myCart->compte_items();
							}
						}
						break;
					case "EMPR" :
					default:
						if($idemprcaddie)$caddie[0]=$idemprcaddie;
						foreach($caddie  as $idemprcaddie) {
							$myCart = new empr_caddie($idemprcaddie);
							$myCart->add_item($item);
							$myCart->compte_items();
						}	
						break;
				}
			} elseif (!empty($list_ui_objects_type)) {
				$message='';
				switch ($list_ui_objects_type) {
					case 'readers_circ_ui':
						$list_ui_objects_type_json_filters = $list_ui_objects_type."_json_filters";
						global ${$list_ui_objects_type_json_filters};
						if (!empty(${$list_ui_objects_type_json_filters})) {
                            $filters = encoding_normalize::json_decode(stripslashes(${$list_ui_objects_type_json_filters}), 1);
						} else {
						    $filters = array();
						}
					    $list_readers_circ_ui = new list_readers_circ_ui($filters);
						$message .= $list_readers_circ_ui->run_global_action_add_caddie();
						break;
				}
				if($message) {
					print "<script>alert(\"$message\"); self.close();</script>";
				}
			} elseif(!empty($selected_objects)) {
				$readers_circ_ui_selected_objects = explode(',', $selected_objects);
				$list_readers_circ_ui = new list_readers_circ_ui();
				$message = $list_readers_circ_ui->run_action_add_caddie();
				if($message) {
					print "<script type='text/javascript'>alert(\"$message\");window.close();</script>";
				}
			} elseif (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
				require_once("$class_path/filter_list.class.php");
				$filter=new filter_list("empr","empr_list",$empr_show_rows,$empr_filter_rows,$empr_sort_rows);
				
				$requete = "SELECT id_empr FROM empr ".stripslashes($clause);
				$filter->original_query=$requete;
				if($idemprcaddie)$caddie[0]=$idemprcaddie;
				if (is_array($caddie) && count($caddie)) {
					//on stocke les quantités avant
					foreach($caddie  as $idemprcaddie) {
						$myCart = new empr_caddie($idemprcaddie);
						$myCart->compte_items();
						$nb_items_before[$idemprcaddie]=$myCart->nb_item;
					}
					foreach($caddie  as $idemprcaddie) {							
						$filter->filtered_query = stripslashes($filtered_query);
						$filter->activate_filters();
						if (!$filter->error) {	
							if ($filter->t_query) {
								$myCart = new empr_caddie($idemprcaddie);
								while ($r=pmb_mysql_fetch_object($filter->t_query)) {
									$myCart->add_item($r->id_empr);
								} // fin while
								$myCart->compte_items();
							}							
						} // fin if filter->t_query	
					} // fin if idemprcaddie
					//on compte après
					$message="";
					foreach($caddie  as $idemprcaddie) {
						$myCart = new empr_caddie($idemprcaddie);
						$myCart->compte_items();
						$message.=sprintf($msg["print_cart_n_added"]."\\n",($myCart->nb_item-$nb_items_before[$idemprcaddie]),$myCart->name);
					}
					print "<script>alert(\"$message\"); self.close();</script>";
				} // fin if !$filster->error
			}else{
				$requete = "SELECT id_empr FROM empr ".stripslashes($clause);
				$res = pmb_mysql_query($requete);
				if ($res) {
					if (pmb_mysql_num_rows($res) > 0) {
						if($idemprcaddie)$caddie[0]=$idemprcaddie;
						if (is_array($caddie) && count($caddie)) {
							foreach($caddie  as $idemprcaddie) {
								$myCart = new empr_caddie($idemprcaddie);
								while ($r=pmb_mysql_fetch_object($res)) {
									$myCart->add_item($r->id_empr);
								} // fin while
								$myCart->compte_items();
							}							
						} // fin if idemprcaddie
					} // fin if mysql_num_rows
				}
			}
			print "<script type='text/javascript'>window.close();</script>"; 
		} else {
			print "<h3>".$msg["print_cart_title"]."</h3>\n";
			$post_param="<input type='hidden' name='clause' value=\"".htmlentities(stripslashes($clause), ENT_QUOTES, $charset)."\">";
	 		$post_param.="<input type='hidden' name='filtered_query' value=\"".htmlentities(stripslashes($filtered_query), ENT_QUOTES, $charset)."\">";
	 		if(!empty($list_ui_objects_type)) {
	 			$list_ui_objects_type_json_filters = $list_ui_objects_type."_json_filters";
	 			global ${$list_ui_objects_type_json_filters};
	 			$post_param.="<input type='hidden' name='list_ui_objects_type' value=\"".htmlentities(stripslashes($list_ui_objects_type), ENT_QUOTES, $charset)."\">";
	 			$post_param.="<input type='hidden' name='".$list_ui_objects_type."_json_filters' value=\"".htmlentities(stripslashes(${$list_ui_objects_type_json_filters}), ENT_QUOTES, $charset)."\">";
	 		}
			if ($object_type == "GROUP")
				aff_paniers_empr($item, "./cart.php?object_type=GROUP", "add_result&sub_action=update&selected_objects=$selected_objects",$msg["caddie_add_GROUP"], "", 0, 0, 1,serialize($post_param));
			else
				aff_paniers_empr($item, "./cart.php?object_type=EMPR", "add_result&sub_action=update&selected_objects=$selected_objects",$msg["caddie_add_EMPR"], "", 0, 0, 1,serialize($post_param));	
			}
		break;
	case 'add_empr_limite':
		if ($sub_action=='add' && count($caddie)) {
			if(!empty($selected_objects)) {
				$readers_edition_ui_selected_objects = explode(',', $selected_objects);
			}
			$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => date('Y-m-d'), 'date_expiration_end' =>'', 'date_expiration_limit' => '((to_days(empr_date_expiration) - to_days(now()) ) <=  '.$pmb_relance_adhesion.' )', 'change_categ' => ''));
			$list_readers_edition_ui->run_action_add_caddie();
			print "<script type='text/javascript'>window.close();</script>"; 
		} else {
			// function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1) {
			aff_paniers_empr(0, "./cart.php?object_type=$object_type&sub_action=add&empr_location_id=$empr_location_id&empr_statut_edit=$empr_statut_edit&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&selected_objects=$selected_objects", "add_empr_limite",$msg["caddie_add_emprs"] , "", 0, 0, 0);
		}
		break;
	case 'add_empr_depasse':
		if ($sub_action=='add' && count($caddie)) {
			if(!empty($selected_objects)) {
				$readers_edition_ui_selected_objects = explode(',', $selected_objects);
			}
			$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => '', 'date_expiration_end' => date('Y-m-d'), 'date_expiration_limit' => '', 'change_categ' => ''));
			$list_readers_edition_ui->run_action_add_caddie();
			print "<script type='text/javascript'>window.close();</script>"; 
		} else {
			// function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1) {
			aff_paniers_empr(0, "./cart.php?object_type=$object_type&sub_action=add&empr_location_id=$empr_location_id&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&selected_objects=$selected_objects", "add_empr_depasse",$msg["caddie_add_emprs"] , "", 0, 0, 0);
		}
		break;
	case 'add_empr_encours':
		if ($sub_action=='add' && count($caddie)) {
			if(!empty($selected_objects)) {
				$readers_edition_ui_selected_objects = explode(',', $selected_objects);
			}
			$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => date('Y-m-d'), 'date_expiration_end' => '', 'date_expiration_limit' => '', 'change_categ' => ''));
			$list_readers_edition_ui->run_action_add_caddie();
			print "<script type='text/javascript'>window.close();</script>"; 
		} else {
			// function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1) {
			aff_paniers_empr(0, "./cart.php?object_type=$object_type&sub_action=add&empr_location_id=$empr_location_id&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&selected_objects=$selected_objects", "add_empr_encours",$msg["caddie_add_emprs"] , "", 0, 0, 0);
		}
		break;
	case 'add_empr_categ_change':
		if ($sub_action=='add' && count($caddie)) {
			if(!empty($selected_objects)) {
				$readers_edition_ui_selected_objects = explode(',', $selected_objects);
			}
			$list_readers_edition_ui = new list_readers_edition_ui(array('date_expiration_start' => '', 'date_expiration_end' => '', 'date_expiration_limit' => '', 'change_categ' => '((((age_min<> 0) || (age_max <> 0)) && (age_max >= age_min)) && (((DATE_FORMAT( curdate() , "%Y" )-empr_year) < age_min) || ((DATE_FORMAT( curdate() , "%Y" )-empr_year) > age_max)))'));
			$list_readers_edition_ui->run_action_add_caddie();
			print "<script type='text/javascript'>window.close();</script>"; 
		} else {
			// function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1) {
			aff_paniers_empr(0, "./cart.php?object_type=$object_type&sub_action=add&empr_location_id=$empr_location_id&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&selected_objects=$selected_objects", "add_empr_categ_change",$msg["caddie_add_emprs"] , "", 0, 0, 0);
		}
		break;
	case 'add_empr':
	case 'add_empr_launch':
	    if ($sub_action=='add' && count($caddie)) {
	        if(!empty($selected_objects)) {
	            $readers_circ_ui_selected_objects = explode(',', $selected_objects);
	        }
	        $list_readers_circ_ui = new list_readers_circ_ui();
	        $message = $list_readers_circ_ui->run_action_add_caddie();
	        print "<script type='text/javascript'>alert(\"$message\");window.close();</script>";
	    } else {
	        // function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1) {
	        aff_paniers_empr(0, "./cart.php?object_type=$object_type&sub_action=add&empr_location_id=$empr_location_id&empr_codestat_filter=$empr_codestat_filter&empr_categ_filter=$empr_categ_filter&selected_objects=$selected_objects", "add_empr",$msg["caddie_add_emprs"] , "", 0, 0, 1);
	    }
	    break;
	default:
//		print "<h1>".$msg["fonct_no_accessible"]."</h1>";
		if ($object_type == "GROUP")
			aff_paniers_empr($item, "./cart.php?object_type=GROUP", "add_item",$msg["caddie_add_GROUP"], "", 0, 0, 1);
		else
			aff_paniers_empr($item, "./cart.php?object_type=EMPR", "add_item",$msg["caddie_add_EMPR"], "", 0, 0, 1);
		break;
		
	} 


