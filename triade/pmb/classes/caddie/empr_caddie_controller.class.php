<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie_controller.class.php,v 1.24.6.1.2.1 2025/03/05 07:14:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/caddie/caddie_root_controller.class.php");
require_once($class_path."/caddie/empr_caddie_lists_controller.class.php");

class empr_caddie_controller extends caddie_root_controller {
	
	protected static $model_class_name = 'empr_caddie';
	
	protected static $procs_class_name = 'empr_caddie_procs';
	
	protected static $list_ui_class_name = 'list_empr_caddies_ui';
	
	protected static $list_content_ui_class_name = 'list_empr_caddie_content_ui';
	
	public static function proceed_module_gestion($quoi, $idcaddie) {
		switch ($quoi) {
			case 'razpointage':
				static::proceed_raz($idcaddie);
				break;
			case 'pointage':
				static::proceed_selection($idcaddie, 'gestion', 'pointage', 'selection');
				break;
			case 'pointagebarcode':
				static::proceed_barcode($idcaddie, 'gestion', 'pointe');
				break;
			case 'selection':
				static::proceed_selection($idcaddie, 'gestion', 'selection', 'selection');
				break;
			case 'barcode':
				static::proceed_barcode($idcaddie, 'gestion', 'add');
				break;
			case 'pointagepanier':
				static::proceed_by_caddie($idcaddie);
				break;
			default:
				parent::proceed_module_gestion($quoi, $idcaddie);
				break;
		}
	}
	
	public static function get_aff_paniers_from_panier($idcaddie = 0, $sub = '') {
		global $msg;
		 
		$idcaddie = intval($idcaddie);
		static::$title = $msg['caddie_select_pointe_panier'];
		static::$action_click = "choix_quoi";
		static::$lien_origine = static::get_constructed_link($sub) . "&quoi=pointagepanier&idcaddie_selected=".$idcaddie;
		return aff_paniers_empr(0, static::$lien_origine, static::$action_click, static::$title, "", 0, 0, 0);
	}
	
	public static function get_aff_paniers($sub = '', $sub_action = '', $moyen = '') {
		global $msg;
	
		switch ($sub) {
			case 'action':
				switch ($sub_action) {
					case 'edition':
						static::$title = $msg["caddie_select_edition"];
						static::$action_click = "choix_quoi";
						break;
					case 'export':
						static::$title = $msg["caddie_select_export"];
						static::$action_click = "choix_quoi";
						break;
					case 'selection':
						static::$title = $msg["caddie_select_for_action"];
						static::$action_click = "";
						break;
					case 'supprpanier':
						static::$title = $msg["caddie_select_supprpanier"];
						static::$action_click = "choix_quoi";
						break;
					case 'transfert':
						static::$title = $msg['caddie_select_transfert'];
						static::$action_click = "transfert";
						break;
					case 'supprbase':
						static::$title = $msg['caddie_select_supprbase'];
						static::$action_click = "choix_quoi";
						break;
					case 'carte':
						static::$title = $msg['caddie_select_carte'];
						static::$action_click = "choix_quoi";
						break;
				}
				static::$lien_origine = static::get_constructed_link($sub, $sub_action);
				break;
			case 'pointage':
				switch ($sub_action) {
					case 'razpointage':
						static::$title = $msg['caddie_pointage_raz'];
						break;
					default:
						static::$title = $msg['caddie_select_pointe'];
						break;
				}
				static::$lien_origine = static::get_constructed_link($sub).($sub_action ? "&quoi=".$sub_action : "").($moyen ? "&moyen=".$moyen : "");
				static::$action_click = "";
				break;
			case 'collecte':
				switch ($sub_action) {
					default:
						static::$title = $msg["caddie_select_ajouter"];
						break;
				}
				static::$lien_origine = static::get_constructed_link($sub).($sub_action ? "&quoi=".$sub_action : "").($moyen ? "&moyen=".$moyen : "");
				static::$action_click = "";
				break;
			case 'gestion':
				switch ($sub_action) {
					case 'pointage':
					case 'pointagebarcode':
					case 'pointagepanier':
						static::$title = $msg['caddie_select_pointe'];
						break;
					case 'selection':
					case 'barcode':
						static::$title = $msg['caddie_select_ajouter'];
						break;
				}
				static::$lien_origine = "./circ.php?categ=caddie&sub=".$sub.($sub_action ? "&quoi=".$sub_action : "").($moyen ? "&moyen=".$moyen : "");
				static::$action_click = "";
				break;
		}
	
		return aff_paniers_empr(0, static::$lien_origine, static::$action_click, static::$title, "", 0, 0, 0);
	}
	
	public static function get_aff_editable_paniers($idcaddie) {
		global $msg;
	
		return aff_paniers_empr($idcaddie, static::get_constructed_link('gestion', 'panier'), "", $msg["caddie_select_afficher"], "", 1, 0, 1);
	}
	
	public static function aff_ajax_editable_paniers($idcaddie) {
	    global $msg;
	    
	    static::$object_type = 'EMPR';
	    static::$title = $msg["caddie_select_afficher"];
	    parent::aff_ajax_editable_paniers($idcaddie);
	}
	
	public static function get_object_instance($empr_caddie_id=0) {
		return new empr_caddie($empr_caddie_id);
	}
	
	public static function get_constructed_link($sub='', $sub_categ='', $action='', $idcaddie=0, $args_others='') {
		global $base_path;
		global $quoi;
		
		$link = $base_path."/circ.php?categ=caddie&sub=".$sub;
		if($sub_categ) {
			switch ($sub) {
				case 'gestion':
					switch ($quoi) {
						case 'selection':
							$link .= "&quoi=selection&moyen=".$sub_categ;
							break;
						case 'pointage':
							$link .= "&quoi=pointage&moyen=".$sub_categ;
							break;
						default :
							$link .= "&quoi=".$sub_categ;
							break;
					}
					break;
				case 'action':
					$link .= "&quelle=".$sub_categ;
					break;
			}
		}
		if($action) $link .= "&action=".$action;
		if($args_others) $link .= $args_others;
		if($idcaddie) $link .= "&idemprcaddie=".$idcaddie;
		return $link;
	}
	
	public static function proceed_selection($idcaddie=0, $sub='', $quelle='', $moyen = '') {
		global $msg, $charset;
		global $action;
		global $id;
		global $elt_flag, $elt_no_flag;
		global $cart_choix_quoi_action;
	
		$idcaddie=intval($idcaddie);
		$id=intval($id);
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			if($sub == 'action') {
				if ((($action=="form_proc")||($action=="add_item"))&&((!$elt_flag)&&(!$elt_no_flag))) {
					error_message_history($msg["caddie_no_elements"], $msg["caddie_no_elements_for_cart"], 1);
					exit();
				}
			}
			switch ($action) {
				case 'form_proc' :
					$hp = new parameters ($id,"empr_caddie_procs") ;
					if($sub == 'action') {
						$hp->gen_form(static::get_constructed_link('action', $quelle, 'add_item', $idcaddie, "&id=$id&elt_flag=$elt_flag&elt_no_flag=$elt_no_flag")) ;
					} else {
						if($quelle == 'pointage') {
							$action_in_form = 'pointe_item';
						} else {
							$action_in_form = 'add_item';
						}
						$hp->gen_form(static::get_constructed_link($sub, $moyen, $action_in_form, $idcaddie, "&id=$id"));
					}
					break;
				case 'pointe_item':
					$model_class_name = static::get_model_class_name();
					print $model_class_name::show_actions($idcaddie, 'EMPR');
					if (empr_caddie_procs::check_rights($id)) {
						$hp = new parameters ($id,"empr_caddie_procs") ;
						$hp->get_final_query();
						echo "<hr />".$hp->final_query."<hr />";
						$myCart->pointe_items_from_query($hp->final_query);
					}
					print pmb_bidi($myCart->aff_cart_nb_items());
					break;
				case 'add_item':
					$model_class_name = static::get_model_class_name();
					print $model_class_name::show_actions($idcaddie, 'EMPR');
					//C'est ici qu'on fait une action
					if (empr_caddie_procs::check_rights($id)) {
						$hp = new parameters ($id,"empr_caddie_procs") ;
						$hp->get_final_query();
						print "<hr />".$hp->final_query."<hr />";
						switch ($sub) {
							case 'gestion':
								print pmb_bidi($myCart->add_items_by_collecte_selection($hp->final_query));
								break;
							case 'action':
								$myCart->update_items_by_action_selection($hp->final_query);
								break;
						}
					}
					print $myCart->aff_cart_nb_items();
					if($sub == 'action') {
					    echo "<hr />".static::get_display_button($msg["caddie_menu_action_suppr_panier"], ['location' => "./circ.php?categ=caddie&sub=action&quelle=supprpanier&action=choix_quoi&idemprcaddie=".$idcaddie."&item=&elt_flag=".$elt_flag."&elt_no_flag=".$elt_no_flag]),
						"&nbsp;".static::get_display_button($msg["caddie_menu_action_edit_panier"], ['location' => static::get_constructed_link('gestion', 'panier', 'edit_cart', $idcaddie, '&item=0')]),
						"&nbsp;<input type='button' class='bouton' value='".$msg["caddie_supprimer"]."' onclick=\"confirmation_delete(".$myCart->get_idcaddie().",'".htmlentities(addslashes($myCart->name),ENT_QUOTES, $charset)."')\" />",
						confirmation_delete(static::get_constructed_link('', '', 'del_cart')."&idemprcaddie=");
					}
					break;
				default:
					print $myCart->aff_cart_nb_items();
					switch ($sub) {
						case 'gestion':
							if($quelle == 'pointage') {
								$action_in_list = 'pointe_item';
							} else {
								$action_in_list = 'add_item';
							}
							$type = 'SELECT';
							break;
						default:
							print $cart_choix_quoi_action;
							$action_in_list = 'add_item';
							$type = 'ACTION';
							break;
					}
					if($sub == 'action') {
						print empr_caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub='.$sub.'&quelle='.$quelle, $type, $action_in_list);
					} else {
						print empr_caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub='.$sub.'&quoi='.$quelle.'&moyen='.$moyen, $type, $action_in_list);
					}
					break;
			}
		} else {
			static::get_aff_paniers($sub, $quelle, $moyen);
		}
	}
	
	public static function proceed_by_caddie($idcaddie=0) {
		global $msg;
		global $action;
		global $idcaddie_selected;
		global $elt_flag, $elt_no_flag;
		
		$idcaddie = intval($idcaddie);
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			switch ($action) {
				case 'choix_quoi':
					print $myCart->aff_cart_titre();
					print $myCart->aff_cart_nb_items();
					print $myCart->get_choix_quoi_form(static::get_constructed_link('gestion', 'pointagepanier', 'pointe_item', $idcaddie, "&idcaddie_selected=".$idcaddie_selected),
							static::get_constructed_link('gestion', 'pointagepanier', '', $idcaddie, "&item=0"),
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
							$liste_0 = $myCart->get_cart("FLAG") ;
						}
						if ($elt_no_flag) {
							$liste_1= $myCart->get_cart("NOFLAG") ;
						}
						$liste= array_merge($liste_0,$liste_1);
						if($liste) {
						    foreach ($liste as $object) {
								$myCart_selected->pointe_item($object);
							}
						}
						print "<h3>".$msg["caddie_menu_pointage_apres_pointage"]."</h3>";
						print $myCart_selected->aff_cart_nb_items();
					}
					static::get_aff_paniers("gestion", "pointagepanier", "");
					break;
				default:
					print $myCart->aff_cart_titre();
					print $myCart->aff_cart_nb_items();
					static::get_aff_paniers_from_panier($idcaddie, "pointage");
					break;
			}
		} else {
			static::get_aff_paniers("gestion", "pointagepanier", "");
		}
	}
	
	public static function proceed_transfert($idcaddie=0, $idcaddie_origine=0) {
		global $msg;
		global $action;
		global $elt_flag, $elt_no_flag;
		
		$idcaddie = intval($idcaddie);
		$idcaddie_origine = intval($idcaddie_origine);
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			switch ($action) {
				case 'transfert':
					print pmb_bidi($myCart->aff_cart_titre());
					print $myCart->aff_cart_nb_items();
					aff_paniers_empr($idcaddie, static::get_constructed_link('action')."&quelle=transfert&idemprcaddie_origine=$idcaddie", "transfert_suite", $msg["caddie_select_transfert_dest"], "", 0, 0, 0);
					break;
				case 'transfert_suite':
					$idcaddie_origine = empr_caddie::check_rights($idcaddie_origine) ;
					if ($idcaddie_origine) {
						$myCartOrigine = static::get_object_instance($idcaddie_origine);
						// procédure d'ajout
						print pmb_bidi($myCartOrigine->aff_cart_titre());
						print $myCartOrigine->aff_cart_nb_items();
						print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'transfert', 'transfert_final', $idcaddie)."&idemprcaddie_origine=$idcaddie_origine", static::get_constructed_link('action', 'transfert'), $msg["caddie_choix_transfert"], $msg["caddie_bouton_transferer"]);
						print pmb_bidi($myCart->aff_cart_titre());
						print $myCart->aff_cart_nb_items();
					}
					break;
				case 'transfert_final':
					$idcaddie_origine = empr_caddie::check_rights($idcaddie_origine) ;
					if ($idcaddie_origine) {
						$myCartOrigine = static::get_object_instance($idcaddie_origine);
						print pmb_bidi($myCart->aff_cart_titre());
						print $myCart->aff_cart_nb_items();
						if ($elt_flag) {
							$liste = $myCartOrigine->get_cart("FLAG") ;
							foreach ($liste as $object) {
								$myCart->add_item($object) ;
							}
						}
						if ($elt_no_flag) {
							$liste = $myCartOrigine->get_cart("NOFLAG") ;
							foreach ($liste as $object) {
								$myCart->add_item($object) ;
							}
						}
						$myCart->compte_items();
						// procédure d'ajout
						echo "<h3>".$msg['empr_caddie_menu_action_apres_transfert']."</h3>";
						print $myCart->aff_cart_nb_items();
					}
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'transfert');
		}
	}
	
	public static function proceed_barcode($idcaddie=0, $sub='', $action_prefix='') {
		global $msg;
		global $action;
		global $form_cb;
		global $empr_location_id;
	
		if ($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print $myCart->aff_cart_titre();
			switch ($action) {
				case 'add_item':
				case 'pointe_item':
					$message_empr =  "";
					if($form_cb) {
						if ($empr_location_id>0) $where = " and empr_location=$empr_location_id ";
						else $where = "";
						$query = "select id_empr, empr_nom, empr_prenom from empr where (empr_cb='$form_cb' or empr_nom like '$form_cb%') $where ";
						$result = pmb_mysql_query($query);
						if (!pmb_mysql_num_rows($result)) {
							// emprunteur inconnu
							$message_empr =  "<strong>$form_cb&nbsp;: ".$msg['empr_caddie_unknown_barcode']."</strong>";
						} elseif (pmb_mysql_num_rows($result)==1) {
							$empr_trouve = pmb_mysql_fetch_object($result);
							if($action == 'add_item') {
								$myCart->add_item($empr_trouve->id_empr);
								$message_empr =  "<strong>".$empr_trouve->empr_nom."&nbsp;".$empr_trouve->empr_prenom."&nbsp;: ".$msg['empr_caddie_collect_added']."</strong>";
							} else {
								$myCart->pointe_item($empr_trouve->id_empr);
								$message_empr =  "<strong>".$empr_trouve->empr_nom."&nbsp;".$empr_trouve->empr_prenom."&nbsp;: ".$msg['empr_caddie_pointage_pointe']."</strong>";
							}
						} else {
							$message_empr =  "<strong>$form_cb&nbsp;: ".$msg['empr_caddie_toomany_barcode']."</strong>";
						}
					}
					print $message_empr;
					$myCart->compte_items();
					print $myCart->aff_cart_nb_items();
					if($action_prefix == 'add') {
						print get_cb("", $msg['empr_caddie_collect_form_message'], $msg['empr_caddie_collect_form_title'], static::get_constructed_link('gestion', 'barcode', 'add_item', $idcaddie), 0, "", 0);
					} else {
						print get_cb("", $msg['empr_caddie_pointage_form_message'], $msg['empr_caddie_pointage_form_title'], static::get_constructed_link('gestion', 'pointagebarcode', 'pointe_item', $idcaddie), 0, "", 0) ;
					}
					break;
				default:
					print $myCart->aff_cart_nb_items();
					if($action_prefix == 'add') {
						print get_cb("", $msg['empr_caddie_collect_form_message'], $msg['empr_caddie_collect_form_title'], static::get_constructed_link('gestion', 'barcode', 'add_item', $idcaddie), 0, "", 0) ;
					} else {
						print get_cb("", $msg['empr_caddie_pointage_form_message'], $msg['empr_caddie_pointage_form_title'], static::get_constructed_link('gestion', 'pointagebarcode', 'pointe_item', $idcaddie), 0, "", 0) ;
					}
					break;
			}
		} else {
			if($action_prefix == 'add') {
				static::get_aff_paniers($sub, 'barcode');
			} else {
				static::get_aff_paniers($sub, 'pointagebarcode', 'barcode');
			}
		}
	}
	
	public static function proceed_raz($idcaddie=0) {
		$idcaddie = intval($idcaddie);
		if ($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			$model_class_name = static::get_model_class_name();
			if ($model_class_name::check_rights($idcaddie)) $myCart->depointe_items();
			print pmb_bidi($myCart->aff_cart_nb_items());
		} else {
			static::get_aff_paniers('gestion', 'razpointage', 'raz');
		}
	}
	
	public static function proceed_carte($idcaddie=0) {
		global $action, $msg;
		
		$idcaddie = intval($idcaddie);
		if ($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'choix_quoi':
					print "<script type='text/javascript'>
							function generate_cards() {
								var link = './pdf.php?pdfdoc=carte-lecteur&idemprcaddie=".$idcaddie."';
								if(document.getElementById('elt_flag').checked) {
									link += '&elt_flag='+document.getElementById('elt_flag').value;
								}
								if(document.getElementById('elt_no_flag').checked) {
									link += '&elt_no_flag='+document.getElementById('elt_no_flag').value;
								}
								openPopUp(link, 'print_PDF');
							}
						</script>
					";
					print pmb_bidi($myCart->aff_cart_nb_items()) ;
					print $myCart->aff_cart_nb_items();
					print $myCart->get_choix_quoi_form("",
							static::get_constructed_link('action', 'carte', '', 0),
							$msg["caddie_choix_carte"],
							$msg["804"],
							"generate_cards();return false;",false);
					break;
				default:
					break;
			}
		} else {
			static::get_aff_paniers('action', 'carte');
		}
	}
	
	public static function proceed_pdf_carte($idcaddie=0) {
		global $elt_flag, $elt_no_flag;
		
		$idcaddie = intval($idcaddie);
		if ($idcaddie) {
			$lettre_reader_card_PDF = lettre_reader_card_PDF::get_instance('reader');
			$myCart = static::get_object_instance($idcaddie);
			$liste_0=$liste_1= array();
			if ($elt_flag) {
				$liste_0 = $myCart->get_cart("FLAG") ;
			}
			if ($elt_no_flag) {
				$liste_1= $myCart->get_cart("NOFLAG") ;
			}
			$liste= array_merge($liste_0,$liste_1);
			if($liste) {
				foreach ($liste as $object) {
					$lettre_reader_card_PDF->doLettre($object);
				}
			}
			$ourPDF = $lettre_reader_card_PDF->PDF;
			$ourPDF->OutPut();
		}
	}
		
	public static function proceed_edition_advanced($idcaddie=0, $object_type='') {
		empr_caddie_lists_controller::set_id_caddie($idcaddie);
		empr_caddie_lists_controller::set_object_type($object_type);
		empr_caddie_lists_controller::proceed($idcaddie);
	}
	
} // fin de déclaration de la classe empr_caddie_controller
