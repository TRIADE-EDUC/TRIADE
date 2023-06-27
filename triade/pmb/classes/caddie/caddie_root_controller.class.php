<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_root_controller.class.php,v 1.41 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/classementGen.class.php");
require_once($class_path.'/event/events/event_caddie.class.php');
require_once($class_path.'/event/events/event_users_group.class.php');
require_once($class_path."/spreadsheetPMB.class.php");

abstract class caddie_root_controller {
	
	protected static $title = '';
	
	protected static $lien_origine = '';
	
	protected static $action_click = '';
	
	protected static $object_type = '';
	
// 	protected static $item = 0;
	
	public static function get_model_class_name() {
		return static::$model_class_name;
	}
	
	public static function get_procs_class_name() {
		return static::$procs_class_name;
	}
	
	public static function proceed_module_gestion($quoi, $idcaddie) {
		global $msg;
		global $item;
		
		$layout = static::get_template_layout();
		switch ($quoi) {
			case 'procs':
				$layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_gestion_procs"], $layout);
				print $layout ;
				$procs_class_name = static::get_procs_class_name();
				$procs_class_name::proceed();
				break;
			case 'remote_procs':
				$layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["remote_procedures_catalog_title"], $layout);
				print $layout ;
				$procs_class_name = static::get_procs_class_name();
				$procs_class_name::proceed_remote();
				break;
			case "classementGen" :
				$layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["classementGen_list_libelle"], $layout);
				print $layout ;
				static::proceed_classement();
				break;
			case 'panier':
			default:
				$layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_gestion_panier"], $layout);
				print $layout ;
				static::proceed($idcaddie, $item);
				break;
		}
	}
	
	public static function proceed_module_collecte($moyen, $idcaddie) {
		global $msg;
		
		$layout = static::get_template_layout();
		$layout = str_replace('<!--!!sous_menu_choisi!! -->', ($moyen ? $msg["caddie_menu_collecte_".$moyen] : ''), $layout);
		print $layout ;
		
		switch ($moyen) {
			case 'import':
				static::proceed_import($idcaddie, 'EXPL');
				break;
			case 'selection':
				static::proceed_selection($idcaddie, 'collecte', '', 'selection');
				break;
			case 'douchette':
				static::proceed_barcode($idcaddie, 'collecte', 'add');
				break;
			default:
				print "<br /><br /><b>".$msg["caddie_select_collecte"]."</b>" ;
				break;
		}
	}
	
	public static function proceed_module_pointage($moyen, $idcaddie) {
		global $msg;
		
		$layout = static::get_template_layout();
		$layout = str_replace('<!--!!sous_menu_choisi!! -->', ($moyen ? $msg["caddie_menu_pointage_".$moyen] : ''), $layout);
		print $layout ;
		
		switch ($moyen) {
			case 'raz':
				static::proceed_raz($idcaddie);
				break;
			case 'selection':
				static::proceed_selection($idcaddie, 'pointage', '', 'selection');
				break;
			case 'douchette':
				static::proceed_barcode($idcaddie, 'pointage', 'pointe');
				break;
			case 'panier':
				static::proceed_by_caddie($idcaddie);
				break;
			case 'search_history':
// 				include ("./".$module."/caddie/pointage/search_history.inc.php");
				break;
			default:
				print "<br /><br /><b>".$msg["caddie_select_pointage"]."</b>" ;
				break;
		}
	}
	
	public static function proceed_module_action($quelle, $idcaddie) {
		global $msg;
		
		$layout = static::get_template_layout();
		$layout = str_replace('<!--!!sous_menu_choisi!! -->', ($quelle ? $msg["caddie_menu_action_".$quelle] : ''), $layout);
		print $layout ;
		
		switch ($quelle) {
			case 'transfert':
				static::proceed_transfert($idcaddie);
				break;
			case 'export':
				static::proceed_export($idcaddie);
				break;
			case 'supprpanier':
				static::proceed_supprpanier($idcaddie);
				break;
			case 'supprbase':
				static::proceed_supprbase($idcaddie);
				break;
			case 'edition':
				global $mode;
				if(empty($mode)) $mode = 'simple';
				static::proceed_edition($idcaddie, $mode);
				break;
			case 'selection':
				static::proceed_selection($idcaddie, 'action', 'selection');
				break;
			case 'expdocnum':
				static::proceed_expdocnum($idcaddie);
				break;
			case 'reindex':
				static::proceed_reindex($idcaddie);
				break;
			case 'access_rights':
				static::proceed_access_rights($idcaddie);
				break;
			default:
				print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
				break;
		}
	}
	
	public static function proceed_module_remplir($callback, $elements) {
		global $msg, $charset;
		global $PMBuserid;
		
		$layout = static::get_template_layout();
		$layout = str_replace('<!--!!sous_menu_choisi!! -->', '', $layout);
		print $layout;
		print '<div class="row"><div class="msg-perio">'.$msg['caddie_creation_in_progress'].'</div></div>';
		
		$caddie = new authorities_caddie();
		$caddie->type = $_SESSION['session_history'][$_SESSION['CURRENT']]['AUT']['SEARCH_OBJECTS_TYPE'];
		$caddie->name = date($msg['1005']." H:i:s - ").html_entity_decode(strip_tags($_SESSION['session_history'][$_SESSION['CURRENT']]['QUERY']['HUMAN_QUERY']), ENT_COMPAT | ENT_HTML401, $charset);
		$caddie->autorisations = $PMBuserid;
		$caddie->classementGen = $msg['caddie_classement_created_from_search'];
		$id_caddie = $caddie->create_cart();
		
		$values = array();
		if (!empty($elements)) { // Vérifie si des éléments sont cochés
			$elements = explode(",", $elements);
			foreach ($elements as $element) {
				$values[] = "('$id_caddie', '$element', '')";
			}
		} else {
			if (!empty($_SESSION['session_history'][$_SESSION['CURRENT']]['AUT']["FORM_VALUES"])) {
				$sat = new searcher_authorities_tab($_SESSION['session_history'][$_SESSION['CURRENT']]['AUT']["FORM_VALUES"]);
				$notice_ids = explode(',',$sat->get_result());
				foreach ($notice_ids as $notice_id) {
					$values[] = "('$id_caddie', '$notice_id', '')";
				}
			} else {
				foreach ($_SESSION['session_history'][$_SESSION['CURRENT']]['QUERY']['POST'] as $varname => $value) {
					global ${$varname};
					${$varname} = $value;
					
				}
				$sh = new search(false, $search_xml_file);
				$table = $sh->make_search();
				$requete = "select * from $table";
				$result = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_assoc($result)) {
						$values[] = "('$id_caddie', '".$row['id_authority']."', '')";
					}
				}
			}
		}
		if (!empty($values)) {
			pmb_mysql_query("INSERT INTO authorities_caddie_content (caddie_id, object_id, flag) VALUES ".implode(",", $values));
		}
		print '<script>document.location = "'.str_replace("!!id_caddie!!", $id_caddie, $callback).'";</script>';
	}
	
	public static function get_constructed_link($sub='', $sub_categ='', $action='', $idcaddie=0, $args_others='') {
	
	}
	
	public static function display_cart_objects($idcaddie) {
		$myCart = static::get_object_instance($idcaddie);
		print pmb_bidi($myCart->aff_cart_titre());
		print pmb_bidi($myCart->aff_cart_nb_items());
		print $myCart->aff_filters_form_objects(static::get_constructed_link('gestion', 'panier', '', $idcaddie));
		$myCart->aff_cart_objects(static::get_constructed_link('gestion', 'panier', '', $idcaddie));
	}
	
	public static function proceed($idcaddie=0, $item='') {
		global $msg;
		global $action;
		global $form_actif;
		global $object_type;
	
		$idcaddie += 0;
		//$item += 0; item peux etre un cb
		switch ($action) {
			case 'new_cart':
				$myCart = static::get_object_instance();
				$form_action = static::get_constructed_link('gestion', 'panier', 'valid_new_cart')."&item=".$item;
				$form_cancel = "history.go(-1);";
				print $myCart->get_form($form_action, $form_cancel);
				break;
			case 'edit_cart':
				$myCart = static::get_object_instance($idcaddie);
				$form_action = static::get_constructed_link('gestion', 'panier', 'save_cart', $idcaddie, "&item=".$item);
				$form_cancel = "document.location='".static::get_constructed_link('gestion', 'panier')."&item=".$item."';";
				print $myCart->get_form($form_action, $form_cancel);
				break;
			case 'del_cart':
				$myCart = static::get_object_instance($idcaddie);
				$myCart->delete();
 				static::get_aff_editable_paniers($idcaddie);
				break;
			case 'save_cart':
				$myCart = static::get_object_instance($idcaddie);
				$myCart->set_properties_from_form();
				if($form_actif) $myCart->save_cart();
				static::get_aff_editable_paniers($idcaddie);
				break;
			case 'del_item':
				$myCart = static::get_object_instance($idcaddie);
				if ($object_type=="EXPL_CB") $myCart->del_item_blob($item);
				else $myCart->del_item($item);
				static::display_cart_objects($idcaddie);
				break;
			case 'valid_new_cart':
				$myCart = static::get_object_instance(0);
				$myCart->set_properties_from_form();
				if($form_actif) $myCart->create_cart();
				static::get_aff_editable_paniers($idcaddie);
				break;
			default:
				if($idcaddie) {
					static::display_cart_objects($idcaddie);
				} else {
					static::get_aff_editable_paniers($idcaddie);
				}
		}
	}
	
	public static function proceed_classement() {
		global $action;
		global $baseLink;
		
		$baseLink=static::get_constructed_link("gestion", "classementGen");
		$classementGen = new classementGen(static::$model_class_name,0);
		$classementGen->proceed($action);
	}
	
	public static function proceed_selection($idcaddie=0, $sub='', $quelle='', $moyen='') {
		//Enrichi dans les classes enfants
	}
	
	public static function proceed_by_caddie($idcaddie=0) {
		global $msg;
		global $action;
		global $idcaddie_selected;
		global $elt_flag, $elt_no_flag, $elt_flag_inconnu, $elt_no_flag_inconnu;
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			switch ($action) {
				case 'choix_quoi':
					print $myCart->aff_cart_titre();
					print $myCart->aff_cart_nb_items();
					print $myCart->get_choix_quoi_form(static::get_constructed_link('pointage', 'panier', 'pointe_item', $idcaddie, "&idcaddie_selected=".$idcaddie_selected),
							static::get_constructed_link('pointage', 'panier', '', $idcaddie, "&item=0"),
							$msg["caddie_choix_pointe_panier"],
							$msg["caddie_item_pointer"],
							"",false);
					if ($idcaddie_selected) {
						$myCart_selected = static::get_object_instance($idcaddie_selected);
						print $myCart_selected->aff_cart_titre();
						print $myCart_selected->aff_cart_nb_items();
					}
					break;
				case 'pointe_item':
					if ($idcaddie_selected) {
						$myCart_selected = static::get_object_instance($idcaddie_selected);
						print $myCart_selected->aff_cart_titre();
						print $myCart_selected->aff_cart_nb_items();
						$liste_0=$liste_1= array();
						if ($elt_flag) {
							$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
						}
						if ($elt_no_flag) {
							$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
						}
						$liste= array_merge($liste_0,$liste_1);
						if($liste) {
						    foreach ($liste as $cle => $object) {
								$myCart_selected->pointe_item($object,$myCart->type);
							}
						}
						print "<h3>".$msg["caddie_menu_pointage_apres_pointage"]."</h3>";
						print $myCart_selected->aff_cart_nb_items();
					}
					static::get_aff_paniers("pointage", "", "panier");
					break;
				default:
					print $myCart->aff_cart_titre();
					print $myCart->aff_cart_nb_items();
					static::get_aff_paniers_from_panier($idcaddie, "pointage");
					break;
			}
		} else {
			static::get_aff_paniers("pointage", "", "panier");
		}
	}
	
	public static function proceed_edition($idcaddie=0, $mode="simple") {
		global $action;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print pmb_bidi($myCart->aff_cart_nb_items()) ;
					print $myCart->get_edition_switch_form($mode, static::get_constructed_link('action', 'edition', 'choix_quoi', $idcaddie, '&object_type='.static::$object_type.'&item=0'));
					switch ($mode) {
						case 'advanced':
							print $myCart->get_list_caddie_ui()->get_display_list();
							break;
						case 'simple':
						default:
							print $myCart->get_edition_form();
							break;
					}
					break;
				case 'suite':
					print pmb_bidi($myCart->aff_cart_nb_items()) ;
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers("action", "edition");
		}
	}
	
	public static function proceed_edition_tableau($idcaddie=0, $mode="simple") {
		global $msg;
		global $worksheet ; //Pour les fonctions dans edition_func.inc.php
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			switch ($mode) {
				case 'advanced':
					$myCart->get_list_caddie_ui()->get_display_spreadsheet_list();
					break;
				case 'simple':
				default:
				    $worksheet = new spreadsheetPMB();
					$worksheet->write_string(0,0,$msg["caddie_numero"].$idcaddie);
					$worksheet->write_string(0,1,$myCart->type);
					$worksheet->write_string(0,2,$myCart->name);
					$worksheet->write_string(0,3,$myCart->comment);
					
					$myCart->write_tableau($worksheet);
					
					$worksheet->download('Caddie_'.$myCart->type.'_'.$idcaddie.'.xls');
					break;
			}
		}
	}
	
	public static function proceed_edition_tableauhtml($idcaddie=0, $mode='simple') {
		global $charset;
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			header("Content-Type: application/download\n");
			header("Content-Disposition: atachement; filename=\"tableau.xls\"");
			print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>";
			switch ($mode) {
				case 'advanced':
					print $myCart->get_list_caddie_ui()->get_display_html_list();
					break;
				case 'simple':
				default:
					print $myCart->get_display_tableauhtml();
					break;
			}
		}
	}
	
	public static function proceed_edition_html($idcaddie=0, $mode="simple") {
		global $charset;
		global $std_header;
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			header ("Content-Type: text/html; charset=$charset");
			print $std_header;
			switch ($mode) {
				case 'advanced':
					print $myCart->get_list_caddie_ui()->get_display_html_list();
					break;
				case 'simple':
				default:
					print $myCart->get_display_tableauhtml();
					break;
			}
		}
	}
	
	public static function proceed_export($idcaddie=0) {
		global $action;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print pmb_bidi($myCart->aff_cart_nb_items()) ;
					print $myCart->get_export_form(static::get_constructed_link('action', 'export', 'exporter', $idcaddie), static::get_constructed_link('action', 'export', '', 0));
					break;
				case 'exporter':
					print $myCart->get_export_iframe();
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'export');
		}
	}
	
	public static function proceed_transfert($idcaddie=0) {
		//Enrichi dans les classes enfants
	}
	
	public static function proceed_supprbase($idcaddie=0) {
		global $msg;
		global $action;
		global $begin_result_liste;
		global $end_result_liste;
		
		global $elt_flag, $elt_no_flag;
		global $elt_flag_inconnu;
		global $elt_no_flag_inconnu;
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print $myCart->aff_cart_nb_items();
					print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'supprbase', 'del_base', $idcaddie),
							static::get_constructed_link('action', 'supprbase', '', 0),
							$msg["caddie_choix_supprbase"],
							$msg["supprimer"],
							"return confirm('$msg[caddie_confirm_supprbase]')");
					break;
				case 'del_base':
					// On déclenche un événement sur la supression
					$evt_handler = events_handler::get_instance();
					$event = new event_users_group("users_group", "get_autorisation_del_base");
					$event->set_id_caddie($idcaddie);
					$evt_handler->send($event);
					if($event->get_error_message()){
						echo $event->get_error_message();
						break;
					}
					print "<br /><h3>".$msg['caddie_situation_before_suppr']."</h3>";
					print $myCart->aff_cart_nb_items();
					$liste_0=$liste_1= array();
					if ($elt_flag) {
						$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
					}
					if ($elt_no_flag) {
						$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
					}
					$liste= array_merge($liste_0,$liste_1);
					$res_aff_suppr_base = $myCart->del_items_base_from_list($liste);
					if ($res_aff_suppr_base) {
						print "<br /><h3>".$msg['caddie_supprbase_elt_used']."</h3>";
						// inclusion du javascript de gestion des listes dépliables
						// début de liste
						print $begin_result_liste;
						print $res_aff_suppr_base ;
						print $end_result_liste;
					}
					print "<br /><h3>".$msg['caddie_situation_after_suppr']."</h3>";
					$myCart->compte_items();
					print $myCart->aff_cart_nb_items();
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'supprbase');
		}
	}
	
	public static function proceed_supprpanier($idcaddie=0) {
		global $msg;
		global $action;
		global $elt_flag;
		global $elt_flag_inconnu;
		global $elt_no_flag;
		global $elt_no_flag_inconnu;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print $myCart->aff_cart_titre();
			switch ($action) {
				case 'choix_quoi':
					print $myCart->aff_cart_nb_items() ;
					$action_link = static::get_constructed_link('action', 'supprpanier', 'del_cart', $idcaddie);
					$action_cancel_link = static::get_constructed_link('action', 'supprpanier', '', 0);
					print $myCart->get_choix_quoi_form($action_link, $action_cancel_link, $msg["caddie_choix_supprpanier"], $msg["caddie_act_vider_le_panier"],"return confirm('$msg[caddie_confirm_supprpanier]')");
					break;
				case 'del_cart':
					print "<br /><h3>".$msg['caddie_situation_before_suppr']."</h3>";
					print $myCart->aff_cart_nb_items() ;
					if ($elt_flag) $myCart->del_item_flag($elt_flag_inconnu);
					if ($elt_no_flag) $myCart->del_item_no_flag($elt_no_flag_inconnu);
					print "<br /><h3>".$msg['caddie_situation_after_suppr']."</h3>";
					print $myCart->aff_cart_nb_items() ;
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'supprpanier');
		}
	}
	
	public static function proceed_raz($idcaddie=0) {
		global $msg;
	
		$idcaddie += 0;
		if ($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			$model_class_name = static::get_model_class_name();
			if ($model_class_name::check_rights($idcaddie)) $myCart->depointe_items();
			print pmb_bidi($myCart->aff_cart_nb_items());
		} else {
			static::get_aff_paniers('pointage', '', 'raz');
		}
	}
	
	public static function proceed_import($idcaddie=0, $object_type='') {
		global $msg;
		global $action;
		global $item;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'add_item':
					$myCart->add_item($item,$object_type);
					$myCart->compte_items();
					print $myCart->aff_cart_nb_items();
					break;
				default:
					print $myCart->aff_cart_nb_items();
					break;
			}
		} else {
			static::get_aff_paniers('collecte', '', 'import');
		}
	}
	
	public static function proceed_expdocnum($idcaddie=0) {
		global $msg;
		global $action;
		global $base_path;
		global $elt_flag, $elt_no_flag;
		global $elt_flag_inconnu, $elt_no_flag_inconnu;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print $myCart->aff_cart_nb_items();
					print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'expdocnum', 'export')."&idcaddie=$idcaddie", static::get_constructed_link('action', 'expdocnum')."&idcaddie=0", $msg["caddie_choix_expdocnum"], $msg["caddie_expdocnum_export"], "return confirm('$msg[caddie_confirm_export]')");
					break;
				case 'export':
					print "<br /><h3>".$msg['caddie_situation_exportdocnum']."</h3>";
					print $myCart->aff_cart_nb_items();
	
					// vérifier et/ou créer le répertoire $chemin
					$chemin_export_doc_num=$base_path."/temp/cart".$idcaddie."/";
					$handledir = @opendir($chemin_export_doc_num);
					if (!$handledir) {
						if (!mkdir($chemin_export_doc_num)) die ("Unsufficient privileges on temp directory");
					} else closedir($handledir);
	
					$res_aff_exp_doc_num="";
					if ($elt_flag) {
						$liste = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
						foreach ($liste as $cle => $object) {
							$res_aff_exp_doc_num.=$myCart->export_doc_num ($object,$chemin_export_doc_num) ;
						}
					}
					if ($elt_no_flag) {
						$liste = $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
						foreach ($liste as $cle => $object) {
							$res_aff_exp_doc_num.=$myCart->export_doc_num ($object,$chemin_export_doc_num) ;
						}
					}
					if ($res_aff_exp_doc_num) {
						print "<br /><h3>".$msg['caddie_res_expdocnum']."</h3>";
						print $res_aff_exp_doc_num;
					} else print "<br /><h3>".$msg['caddie_res_expdocnum_nodocnum']."</h3>";
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'expdocnum');
		}
	}
	
	public static function proceed_reindex($idcaddie=0) {
		global $msg;
		global $action;
		global $elt_flag, $elt_no_flag;
		global $elt_flag_inconnu, $elt_no_flag_inconnu;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print pmb_bidi($myCart->aff_cart_nb_items());
					print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'reindex', 'suite', $idcaddie), static::get_constructed_link('action', 'reindex', '', 0), $msg["caddie_choix_reindex"], $msg["caddie_bouton_reindex"],"");
					break;
				case 'suite':
					@set_time_limit(0);
					$nb_elements_flag=$nb_elements_no_flag=0;
					$liste_0=$liste_1= array();
					if ($elt_flag) {
						$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
						$nb_elements_flag=count($liste_0);
					}
					if ($elt_no_flag) {
						$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
						$nb_elements_no_flag=count($liste_1);
					}
					$liste= array_merge($liste_0,$liste_1);
					$nb_elements_total=count($liste);
						
					if($nb_elements_total){
						$myCart->reindex_from_list($liste);						
					}
						
					print "<br /><h3>".$msg['caddie_situation_reindex']."</h3>";
					print sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag)."<br />";
					print sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag)."<br />";
					print "<b>".sprintf($msg["caddie_action_total_processed"],$nb_elements_total)."</b><br /><br />";
					print $myCart->aff_cart_nb_items();
					echo "<input type='button' class='bouton' value='".$msg["caddie_menu_action_suppr_panier"]."' onclick=\"document.location='".static::get_constructed_link('action', 'supprpanier', 'choix_quoi', $idcaddie, '&object_type='.$myCart->type.'&item=0&elt_flag='.$elt_flag.'&elt_no_flag='.$elt_no_flag)."'\" />";
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'reindex');
		}
	}
	
	public static function proceed_access_rights($idcaddie=0) {
		global $msg;
		global $action;
		global $elt_flag, $elt_no_flag;
		global $elt_flag_inconnu, $elt_no_flag_inconnu;
		global $gestion_acces_user_notice, $gestion_acces_empr_notice;
	
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print pmb_bidi($myCart->aff_cart_nb_items());
					print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'access_rights', 'suite', $idcaddie), static::get_constructed_link('action', 'access_rights', '', 0), $msg["caddie_choix_access_rights"], $msg["caddie_bouton_access_rights"],"");
					break;
				case 'suite':
					$ac= new acces();
					if ($gestion_acces_user_notice==1) {
						$dom_1= $ac->setDomain(1);
					}
					if ($gestion_acces_empr_notice==1) {
						$dom_2= $ac->setDomain(2);
					}

					@set_time_limit(0);
					$nb_elements_flag=$nb_elements_no_flag=0;
					$liste_0=$liste_1= array();
					if ($elt_flag) {
						$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
						$nb_elements_flag=count($liste_0);
					}
					if ($elt_no_flag) {
						$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
						$nb_elements_no_flag=count($liste_1);
					}
					$liste= array_merge($liste_0,$liste_1);
					$nb_elements_total=count($liste);

					if($nb_elements_total){
						$pb=new progress_bar($msg['caddie_situation_access_rights_encours'],$nb_elements_total,5);
						if ($myCart->type=='NOTI'){
						    foreach ($liste as $cle => $object) {
								if ($gestion_acces_user_notice==1) {
									$dom_1->delRessource($object);
									$dom_1->applyRessourceRights($object);
								}
								if ($gestion_acces_empr_notice==1) {
									$dom_2->delRessource($object);
									$dom_2->applyRessourceRights($object);
								}
								$pb->progress();
							}
						}elseif($myCart->type=='BULL'){
						    foreach ($liste as $cle => $object) {
								$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$object."'";
								$res=pmb_mysql_query($requete);
								if(pmb_mysql_num_rows($res)){
									$element=pmb_mysql_fetch_object($res);
									if(trim($element->bulletin_titre)){
										$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$object."'";
										pmb_mysql_query($requete);
									}
									if($element->num_notice){
										if ($gestion_acces_user_notice==1) {
											$dom_1->delRessource($element->num_notice);
											$dom_1->applyRessourceRights($element->num_notice);
										}
										if ($gestion_acces_empr_notice==1) {
											$dom_2->delRessource($element->num_notice);
											$dom_2->applyRessourceRights($element->num_notice);
										}
									}
				
								}
								$pb->progress();
							}
						}elseif($myCart->type=='EXPL'){
						    foreach ($liste as $cle => $object) {
								$requete="SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id='".$object."' ";
								$res=pmb_mysql_query($requete);
								if(pmb_mysql_num_rows($res)){
									$row=pmb_mysql_fetch_object($res);
									if($row->expl_notice){
										if ($gestion_acces_user_notice==1) {
											$dom_1->delRessource($row->expl_notice);
											$dom_1->applyRessourceRights($row->expl_notice);
										}
										if ($gestion_acces_empr_notice==1) {
											$dom_2->delRessource($row->expl_notice);
											$dom_2->applyRessourceRights($row->expl_notice);
										}
									}else{
										$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$row->expl_bulletin."'";
										$res2=pmb_mysql_query($requete);
										if(pmb_mysql_num_rows($res2)){
											$element=pmb_mysql_fetch_object($res2);
											if(trim($element->bulletin_titre)){
												$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$row->expl_bulletin."'";
												pmb_mysql_query($requete);
											}
											if($element->num_notice){
												if ($gestion_acces_user_notice==1) {
													$dom_1->delRessource($element->num_notice);
													$dom_1->applyRessourceRights($element->num_notice);
												}
												if ($gestion_acces_empr_notice==1) {
													$dom_2->delRessource($element->num_notice);
													$dom_2->applyRessourceRights($element->num_notice);
												}
											}
										}
									}
								}
								$pb->progress();
							}
						}
						$pb->hide();
					}
				
					print "<br /><h3>".$msg['caddie_situation_access_rights']."</h3>";
					print sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag)."<br />";
					print sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag)."<br />";
					print "<b>".sprintf($msg["caddie_action_total_processed"],$nb_elements_total)."</b><br /><br />";
					print $myCart->aff_cart_nb_items();
					echo "<input type='button' class='bouton' value='".$msg["caddie_menu_action_suppr_panier"]."' onclick=\"document.location='".static::get_constructed_link('action', 'supprpanier', 'choix_quoi', $idcaddie, '&object_type='.$myCart->type.'&item=0&elt_flag='.$elt_flag.'&elt_no_flag='.$elt_no_flag)."'\" />";
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'access_rights');
		}
	}
	
	public static function proceed_ajax($idcaddie=0, $id_item=0) {
		global $charset;
		global $sub;
		global $moyen;
		global $action;
		
		$idcaddie += 0;
		$id_item += 0;
		$res_pointage = 0;
		
		$model_class_name = static::get_model_class_name();
		$idcaddie = $model_class_name::check_rights($idcaddie) ;
		switch($sub) {
			case "pointage" :
				switch ($moyen) {
					case 'douchette':
						break;
					case 'manu':
						if($idcaddie) {
							$myCart = static::get_object_instance($idcaddie);
							switch ($action) {
								case 'add_item':
									if($id_item) {
										$res_pointage = $myCart->pointe_item($id_item,$myCart->type);
									}			
									break;
								case 'del_item':
									$res_pointage = $myCart->depointe_item($id_item);
									break;
								default:
									break;
							}
							$aff_cart_nb_items = $myCart->aff_cart_nb_items();
						} 
						
						
						$result = array(
							'id'=>$id_item,
							'idcaddie'=>$idcaddie,
							'res_pointage'=>$res_pointage,
							'aff_cart_nb_items'=>($charset != "utf-8" ? utf8_encode($aff_cart_nb_items) : $aff_cart_nb_items)
						);
						ajax_http_send_response($result);
						break;
					default:
						break;
				}
				break;
			case "collecte" :
				break;
			case "list_from_item":
				if ($idcaddie) {
					$myCart = static::get_object_instance($idcaddie);
					switch($action) {
						case 'delete':
							$myCart->del_item($id_item);
							print static::get_display_list_from_item('display', $myCart->type, $id_item);
							break;
						default:
							$myCart->add_item($id_item,$myCart->type);
							print static::get_display_list_from_item('display', $myCart->type, $id_item);
							break;
					}
				}
				break;
			default:
				if ($idcaddie) {
					$myCart = static::get_object_instance($idcaddie);
					switch($action) {
						case 'delete':
							$myCart->del_item($id_item);
							break;
						default:
							$myCart->add_item($id_item,$myCart->type);
							break;
					}
					$myCart->compte_items();
					print $myCart->nb_item;
				} else die("Failed: "."obj=".$id_item." caddie=".$idcaddie);
				break;
		}
	}
	
	public static function proceed_quick_access($id_object=0, $type_object='') {
		global $msg, $charset;
		
		$id_object += 0;
		$list = array();
		
		// Publication d'un évenement pour la récupération du panier préféré
		$evt_handler = events_handler::get_instance();
		$event = new event_caddie("caddie", "preferred_caddie_".(isset($type_object) ? strtolower($type_object) : 'noti'));
		$evt_handler->send($event);
		if($event->get_id_caddie()) {
			$preferred_caddie[] = array(
					'idcaddie' => $event->get_id_caddie(),
					'name' => $event->get_name(),
					'nb_item' => $event->get_nb_items()
			);
		} else {
			$preferred_caddie = array();
		}
		$model_class_name = static::get_model_class_name();
		$list = $model_class_name::get_cart_list($type_object, 1);
		if(count($preferred_caddie)) {
			$list = array_merge($preferred_caddie, $list);
		}
		print "<div>
			<table style='width:100%'><tbody>
				<tr>
					<td class='align_left' style='width:90%'></td>
					<td class='align_right'><a href='#' id='close_cart_div' ><img style='border:0px' class='align_middle' src='".get_url_icon('close.gif')."'/></a></td>
				</tr>
			</tbody></table></div>";
		if(count($list)) {
			print "<h3>".$model_class_name::get_type_label($type_object)."</h3><br />";
			for ($i=0; $i<count($list); $i++) {
				if($model_class_name::has_item($list[$i]["idcaddie"], $id_object)) {
					$pannel_cart_delete_link = "javascript:object_delete_caddie(".$id_object.", '".$type_object."', ".$list[$i]["idcaddie"].")";
					$img_caddie = "<img src='".get_url_icon('basket_empty_20x20.gif')."' title='".htmlentities($msg['caddie_icone_suppr_elt'], ENT_QUOTES, $charset)."' onclick=\"".$pannel_cart_delete_link."\" />";
				} else {
					$img_caddie = "<img src='".get_url_icon('basket_20x20.gif')."'/>";
				}
				$cart_link= static::get_constructed_link('gestion', 'panier')."&action=&object_type=".$type_object."&idcaddie=".$list[$i]["idcaddie"]."&item=0";
				$pannel_cart_see = "&nbsp;<a href=\"".$cart_link."\"><i class='fa fa-eye'></i></a>";
				$pannel_cart_link = "javascript:object_div_caddie(".$id_object.", '".$type_object."', ".$list[$i]["idcaddie"].")";
				print "
					<div id=\"".$type_object."_".$list[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\">
						".$img_caddie."
						&nbsp;<a href=\"".$pannel_cart_link."\">".htmlentities($list[$i]["name"],ENT_QUOTES,$charset)."<span id=\"".$type_object."_nbitem_".$list[$i]["idcaddie"]."\"> (".$list[$i]["nb_item"].")</span></a>".$pannel_cart_see."
					</div>";
			}
		} else {
			switch ($type_object) {
				case 'EXPL' :
					print "<h3>".$msg["caddie_fast_access_expl_no_selected"]."</h3>";
					break;
				case 'BULL' :
					print "<h3>".$msg["caddie_fast_access_bull_no_selected"]."</h3>";
					break;
				case 'EMPR' :
					print "<h3>".$msg["caddie_fast_access_empr_no_selected"]."</h3>";
					break;
				case 'NOTI' :
					print "<h3>".$msg["caddie_fast_access_no_selected"]."</h3>";
					break;
				default :
					print "<h3>".$msg["caddie_fast_access_authorities_no_selected"]."</h3>";
					break;
			}
		}
	}
			
	public static function get_create_button($item=0) {
		global $msg;
	
		return "<input class='bouton' type='button' value=' ".$msg['new_cart']." ' onClick=\"document.location='".static::$lien_origine."&action=new_cart&object_type=".static::$object_type."&item=$item'\" />";
	}
	
	public static function get_display_row($caddie_instance, $type='', $valeur=array(), $id_object=0) {
		global $msg;
		global $PMBuserid;
		global $charset;
		global $action;
		global $baselink;
		global $base_path, $current_module;
		global $item;
		
		$id_object += 0;
		switch ($type) {
			case 'editable':
				$item = 0;
				$lien_edition = 1;
				$lien_creation = 1;
				$nocheck = false;
				$lien_pointage = 0;
				break;
			case 'in_cart':
				$lien_edition = 0;
				$lien_creation = 0;
				$nocheck = false;
				$lien_pointage = 0;
				break;
			case 'display':
			default:
				$item = 0;
				$lien_edition = 0;
				$lien_creation = 1;
				$nocheck = false;
				$lien_pointage = 0;
				break;
		}
		
		if ($lien_edition) $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='".static::$lien_origine."&action=edit_cart&idcaddie=!!idcaddie!!';\" />";
		else $lien_edition_panier_cst = "";
		$aff_lien=str_replace('!!idcaddie!!', $valeur['idcaddie'], $lien_edition_panier_cst);
		$display= "
				<td class='classement60'>";
		if($item && $action!="save_cart" && $action!="del_cart") {
			$display .= (!$nocheck?"<input type='checkbox' id='id_".$valeur['idcaddie']."' name='caddie[".$valeur['idcaddie']."]' value='".$valeur['idcaddie']."'>":"")."&nbsp;";
			if(!$nocheck){
				$display.=  "<a href='#' onclick='javascript:document.getElementById(\"id_".$valeur['idcaddie']."\").checked=true;document.forms[\"print_options\"].submit();' />";
			} else {
				if ($lien_pointage) {
					$display.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$item.";document.getElementById(\"idcaddie_selected\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' />";
				} else {
					$display.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' />";
				}
			}
		} else {
			if($id_object) {
				$display .= "
				<script type='text/javascript'>
					function ".$caddie_instance->type."_delete_item(idcaddie,id_item) {
						var url = '".$base_path."/ajax.php?module=".$current_module."&categ=caddie&sub=list_from_item&action=delete&idcaddie='+idcaddie+'&object_type=".$caddie_instance->type."&id_item='+id_item;
				 		var ajax_gestion=new http_request();
						ajax_gestion.request(url,0,'',1,".$caddie_instance->type."_delete_item_callback,0,0);
					}
					function ".$caddie_instance->type."_delete_item_callback(response) {
						var data = response;
						if(document.getElementById('".strtolower($caddie_instance->type)."_caddie_".$id_object."_content')) {
							dojo.forEach(dijit.findWidgets(dojo.byId('".strtolower($caddie_instance->type)."_caddie_".$id_object."_content')), function(w) {
								w.destroyRecursive();
							});
							if(typeof(data) != 'undefined') {
								document.getElementById('".strtolower($caddie_instance->type)."_caddie_".$id_object."_content').innerHTML = data;
							} else {
								document.getElementById('".strtolower($caddie_instance->type)."_caddie_".$id_object."_content').innerHTML = '';
							}
							dojo.parser.parse('".strtolower($caddie_instance->type)."_caddie_".$id_object."_content');
						}
					}
				</script>
				<a onclick='".$caddie_instance->type."_delete_item(".$valeur['idcaddie'].",".$id_object.");' style='cursor:pointer;'>
					<img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" />
				</a>";
			}
			if(empty(static::$lien_origine)) {
				$link = static::get_constructed_link('gestion', 'panier', '', $valeur['idcaddie'], "&object_type=".$caddie_instance->type."&item=".$item);
			} else {
				$link = static::$lien_origine."&action=".static::$action_click."&object_type=".$caddie_instance->type."&idcaddie=".$valeur['idcaddie']."&item=$item";
			}
			$display.= "<a href='$link' />";
		}
		$display .= "<span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">";
		$display .= "<strong>".$valeur['name']."</strong>";
		if ($valeur['comment']){
			$display.=  "<br /><small>(".$valeur['comment'].")</small>";
		}
		$display .= "</span>";
		if($item && $action!="save_cart" && $action!="del_cart") {
			$display.= "
					</td>
	            		".$caddie_instance->aff_nb_items_reduit()."
		            		<td class='classement20'>$aff_lien</td>";
		} else {
			$display.= "</a></td>";
			$display.= $caddie_instance->aff_nb_items_reduit();
			if ($lien_creation) {
				$classementGen = new classementGen(static::$model_class_name, $valeur['idcaddie']);
				$model_class_name = static::get_model_class_name();
				$display.= "<td class='classement15'>".$aff_lien."&nbsp;".$model_class_name::show_actions($valeur['idcaddie'],$valeur['type'])."</td>";
				$display.= "<td class='classement5'>".$classementGen->show_selector($baselink,$PMBuserid)."</td>";
			} else {
				$display.= "<td class='classement20'>$aff_lien</td>";
			}
		}
		return $display;
	}
	
	public static function get_display_list($type='display', $object_type='') {
		global $msg;
		global $PMBuserid;
		global $charset;
		global $deflt_catalog_expanded_caddies;
		global $idcaddie_new;
		
		$display = '';
		$model_class_name = static::get_model_class_name();
		$liste = $model_class_name::get_cart_list($object_type);
		$script_submit = '';
		if(sizeof($liste)) {
			$display .= "<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' style='border:0px'></a>
			<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' style='border:0px'></a>".static::$title."</div>";
			$parity=array();
			foreach ($liste as $cle => $valeur) {
			    if (!empty($idcaddie_new) && ($idcaddie_new != $valeur['idcaddie'])) continue;
			    if (!empty($idcaddie_new) && ($idcaddie_new == $valeur['idcaddie'])) {
    			    $script_submit = "<script>document.getElementById('id_" . $valeur['idcaddie'] . "').checked=true;document.forms['print_options'].submit()</script>";
    			}
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
					$myCart = new $model_class_name();
					$myCart->nb_item=$valeur['nb_item'];
					$myCart->nb_item_pointe=$valeur['nb_item_pointe'];
					$myCart->type=$valeur['type'];
					$print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
					if(!trim($valeur["caddie_classement"])){
						$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
					}
					if(!isset($parity[$myCart->type])) $parity[$myCart->type] = 0;
					$parity[$myCart->type]=1-$parity[$myCart->type];
					if ($parity[$myCart->type]) $pair_impair = "even";
					else $pair_impair = "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
					if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
						$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
					}
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= "<tr class='$pair_impair' $tr_javascript >".static::get_display_row($myCart, $type, $valeur)."</tr>";
				}
			}
			//Tri des classements
			foreach($print_cart as $key => $cart_type) {
				ksort($print_cart[$key]["classement_list"]);
			}
			// affichage des paniers par type
			foreach($print_cart as $key => $cart_type) {
				//on remplace les clés à cause des accents
				$cart_type["classement_list"]=array_values($cart_type["classement_list"]);
				$contenu="";
				foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
					$contenu.=gen_plus($key.$keyBis,$cart_typeBis["titre"],"<table border='0' cellspacing='0' style='width:100%' class='classementGen_tableau'>".$cart_typeBis["cart_list"]."</table>",$deflt_catalog_expanded_caddies);
				}
				$display .= gen_plus($key,$cart_type["titre"],$contenu,$deflt_catalog_expanded_caddies);
			}
		} else {
			$display .= $msg[398];
		}
		return $display.$script_submit;
	}
	
	public static function get_display_list_from_item($type='display', $object_type='', $item=0) {
		global $msg;
		global $PMBuserid;
		
		$display = '';
		$model_class_name = static::get_model_class_name();
		$liste = $model_class_name::get_cart_list_from_item($object_type, 0, $item);
		if(sizeof($liste)) {
			$print_cart = array();
			foreach ($liste as $cle => $valeur) {
				$rqt_autorisation=explode(" ",$valeur['autorisations']);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
					$myCart = new $model_class_name($valeur["idcaddie"]);
					if(!trim($valeur["caddie_classement"])){
						$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
					}
					$print_cart["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
					if(!isset($print_cart["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
						$print_cart["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
					}
					$pair_impair = "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					$print_cart["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= "<tr class='$pair_impair' $tr_javascript >".static::get_display_row($myCart, $type, $valeur, $item)."</tr>";
				}
			}
			//Tri des classements
			ksort($print_cart["classement_list"]);
			foreach($print_cart["classement_list"] as $key => $cart_type) {
				$display.=gen_plus($key,$cart_type["titre"],"<table border='0' cellspacing='0' style='width:100%' class='classementGen_tableau'>".$cart_type["cart_list"]."</table>",0);
			}
			$display = gen_plus(strtolower($object_type).'_caddie_'.$item, $model_class_name::get_type_label($object_type).' ('.sizeof($liste).')',$display,0,'','','notice-parent caddie_list_header','notice-child caddie_list_content');
			$display = "<div id='".strtolower($object_type)."_caddie_".$item."_content'>".$display."</div>";
		}
		return $display;
	}
	
	public static function process_print($idcaddie_new=0) {
		global $action;
	
		switch ($action) {
			case "print_prepare" :
			    static::print_prepare($idcaddie_new);
				break;
			case "print" :
				static::set_session();
				break;
			case "add_item":
			default :
				if ($_SESSION["PRINT_CART"]) {
					static::print_cart();
				}
				break;
		}
	}
} // fin de déclaration de la classe caddie_root_controller
