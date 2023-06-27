<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_controller.class.php,v 1.34 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/caddie/caddie_root_controller.class.php");
require_once($class_path."/classementGen.class.php");
require_once($base_path."/includes/init.inc.php");
require_once($class_path."/mono_display.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($class_path."/author.class.php");
require_once($class_path."/editor.class.php");
require_once($include_path."/isbn.inc.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($include_path."/explnum.inc.php");
require_once($class_path."/category.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/search.class.php");
require_once($include_path."/cart.inc.php");
require_once($class_path."/caddie.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/notice.class.php");


class caddie_controller extends caddie_root_controller {
	
	protected static $model_class_name = 'caddie';
	
	protected static $procs_class_name = 'caddie_procs';
	
	public static function get_template_layout() {
		global $catalog_layout;
		return $catalog_layout;
	}
	
	public static function get_aff_paniers($sub = '', $sub_action = '', $moyen = '') {
		global $msg;
	
		$nocheck=false;
		$lien_pointage=0;
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
					case 'expdocnum':
						static::$title = $msg["caddie_select_expdocnum"];
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
					case 'reindex':
						static::$title = $msg['caddie_action_reindex'];
						static::$action_click = "choix_quoi";
						break;
					case 'access_rights':
						static::$title = $msg['caddie_action_access_rights'];
						static::$action_click = "choix_quoi";
						break;
				}
				static::$lien_origine = static::get_constructed_link($sub, $sub_action);
				break;
			case 'pointage':
				switch ($moyen) {
					case 'panier':
						global $idcaddie;
						if($idcaddie) {
							static::$title = $msg['caddie_select_pointe_panier'];
							static::$action_click = "choix_quoi";
							$nocheck=true;
							$lien_pointage=1;
						} else {
							static::$title = $msg['caddie_select_pointe'];
							static::$action_click = "";
						}
						break;
					case 'raz':
						static::$title = $msg['caddie_pointage_raz'];
						static::$action_click = "";
						break;
					default:
						static::$title = $msg['caddie_select_pointe'];
						static::$action_click = "";
						break;
				}
				static::$lien_origine = static::get_constructed_link($sub).($sub_action ? "&quoi=".$sub_action : "").($moyen ? "&moyen=".$moyen : "");
				break;
			case 'collecte':
				static::$title = $msg["caddie_select_ajouter"];
				static::$lien_origine = static::get_constructed_link($sub).($sub_action ? "&quoi=".$sub_action : "").($moyen ? "&moyen=".$moyen : "");
				static::$action_click = "";
				break;
		}
	
		return aff_paniers(0, "NOTI", static::$lien_origine, static::$action_click, static::$title, "", 0, 0, 0, $nocheck, $lien_pointage);
	}
	
	public static function get_aff_editable_paniers($idcaddie) {
		global $msg;
	
		return aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=gestion&quoi=panier", "", $msg["caddie_select_afficher"], "", 1, 0, 1);
	}
	
	public static function get_object_instance($caddie_id=0) {
		return new caddie($caddie_id);
	}
	
	public static function get_constructed_link($sub='', $sub_categ='', $action='', $idcaddie=0, $args_others='') {
		global $base_path;
		
		$link = $base_path."/catalog.php?categ=caddie&sub=".$sub;
		if($sub_categ) {
			switch ($sub) {
				case 'gestion':
					$link .= "&quoi=".$sub_categ;
					break;
				case 'collecte':
				case 'pointage':
					$link .= "&moyen=".$sub_categ;
					break;
				case 'action':
					$link .= "&quelle=".$sub_categ;
					break;
			}
		}
		if($action) $link .= "&action=".$action;
		if($args_others) $link .= $args_others;
		if($idcaddie) $link .= "&idcaddie=".$idcaddie;
		return $link;
	}
	
	public static function proceed_selection($idcaddie=0, $sub='', $quelle='', $moyen='') {
		global $msg, $charset;
		global $action;
		global $id;
		global $elt_flag, $elt_no_flag;
		global $cart_choix_quoi_action;
		global $gestion_acces_active;
		global $erreur_explain_rqt;
		
		$idcaddie += 0;
		$id += 0;
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
					$hp = new parameters ($id) ;
					if($sub == 'action') {
						$hp->gen_form(static::get_constructed_link($sub, $quelle, 'add_item', $idcaddie, "&id=$id&elt_flag=$elt_flag&elt_no_flag=$elt_no_flag"));
					} else {
						if($sub == 'pointage') {
							$action_in_form = 'pointe_item';
						} else {
							$action_in_form = 'add_item';
						}
						$hp->gen_form(static::get_constructed_link($sub, $moyen, $action_in_form, $idcaddie, "&id=$id"));
					}
					break;
				case 'pointe_item':
					$model_class_name = static::get_model_class_name();
					print $model_class_name::show_actions($idcaddie,static::$object_type);
					if (caddie_procs::check_rights($id)) {
						$hp = new parameters ($id) ;
						$hp->get_final_query();
						echo "<hr />".$hp->final_query."<hr />";
						$myCart->pointe_items_from_query($hp->final_query);
					}
					print pmb_bidi($myCart->aff_cart_nb_items());
					break;
				case 'add_item':
					$model_class_name = static::get_model_class_name();
					print $model_class_name::show_actions($idcaddie,static::$object_type);
					//C'est ici qu'on fait une action
					if (caddie_procs::check_rights($id)) {
						$hp = new parameters ($id) ;
						$hp->get_final_query();
						print "<hr />".$hp->final_query."<hr />";
						switch ($sub) {
							case 'collecte':
								print pmb_bidi($myCart->add_items_by_collecte_selection($hp->final_query));
								break;
							case 'action':
								if (!explain_requete($hp->final_query)) die("<br /><br />".$hp->final_query."<br /><br />".$msg["proc_param_explain_failed"]."<br /><br />".$erreur_explain_rqt);
								$myCart->update_items_by_action_selection($hp->final_query);
								break;
						}
					}
					print $myCart->aff_cart_nb_items();
					if($sub == 'action') {
						echo "<hr /><input type='button' class='bouton' value='".$msg["caddie_select_reindex"]."' onclick='document.location=&quot;./catalog.php?categ=caddie&amp;sub=action&amp;quelle=reindex&amp;action=suite&amp;idcaddie=".$idcaddie."&amp;elt_flag=".$elt_flag."&amp;elt_no_flag=".$elt_no_flag."&quot;' />";
						if ($gestion_acces_active==1) {
							echo "&nbsp;<input type='button' class='bouton' value='".htmlentities($msg["caddie_select_access_rights"],ENT_QUOTES,$charset)."' onclick='document.location=&quot;./catalog.php?categ=caddie&amp;sub=action&amp;quelle=access_rights&amp;action=suite&amp;idcaddie=".$idcaddie."&amp;elt_flag=".$elt_flag."&amp;elt_no_flag=".$elt_no_flag."&quot;' />";
						}
						echo "&nbsp;<input type='button' class='bouton' value='".$msg["caddie_menu_action_suppr_panier"]."' onclick='document.location=&quot;./catalog.php?categ=caddie&amp;sub=action&amp;quelle=supprpanier&amp;action=choix_quoi&amp;object_type=NOTI&amp;idcaddie=".$idcaddie."&amp;item=0&amp;elt_flag=".$elt_flag."&amp;elt_no_flag=".$elt_no_flag."&quot;' />",
						"&nbsp;<input type='button' class='bouton' value='".$msg["caddie_menu_action_edit_panier"]."' onclick=\"document.location='./catalog.php?categ=caddie&sub=gestion&action=edit_cart&idcaddie=".$idcaddie."'\" />",
						"&nbsp;<input type='button' class='bouton' value='".$msg["caddie_supprimer"]."' onclick=\"confirmation_delete(".$myCart->get_idcaddie().",'".htmlentities(addslashes($myCart->name),ENT_QUOTES, $charset)."')\" />",
						confirmation_delete("./catalog.php?categ=caddie&sub=gestion&action=del_cart&quoi=&idcaddie=");
					}
					break;
				default:
					print $myCart->aff_cart_nb_items();
					switch ($sub) {
						case 'pointage':
							$action_in_list = 'pointe_item';
							break;
						case 'collecte':
							$action_in_list = 'add_item';
							break;
						default:
							print $cart_choix_quoi_action;
							$action_in_list = 'add_item';
							break;
					}
					if($sub == 'action') {
						print caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub='.$sub.'&quelle='.$quelle);
					} else {
						print caddie_procs::get_display_list_from_caddie($idcaddie, 'categ=caddie&sub='.$sub.'&moyen='.$moyen, 'SELECT', $action_in_list);
					}
					break;
			}
		} else {
			static::get_aff_paniers($sub, $quelle, $moyen);
		}
	}
	
	public static function proceed_transfert($idcaddie=0, $idcaddie_origine=0) {
		global $msg;
		global $action;
		global $elt_flag, $elt_no_flag;
		global $bull_not, $bull_dep;
		
		$idcaddie += 0;
		$idcaddie_origine += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			switch ($action) {
				case 'transfert':
					print pmb_bidi($myCart->aff_cart_titre());
					print $myCart->aff_cart_nb_items();
					aff_paniers($idcaddie, "NOTI", static::get_constructed_link('action', 'transfert')."&idcaddie_origine=$idcaddie", "transfert_suite", $msg["caddie_select_transfert_dest"], "", 0, 0, 0,true);
					break;
				case 'transfert_suite':
					$idcaddie_origine = caddie::check_rights($idcaddie_origine) ;
					if ($idcaddie_origine) {
						$myCartOrigine = static::get_object_instance($idcaddie_origine);
						// procédure d'ajout
						print pmb_bidi($myCartOrigine->aff_cart_titre());
						print $myCartOrigine->aff_cart_nb_items();
						// le caddie d'origine est BULL, le caddie destination est NOTI, il fait afficher le choix de notice de bulletin ou notices de dépouillement
						if ($myCart->type=='NOTI' && $myCartOrigine->type=='BULL') $aff_choix_dep = true;
						else $aff_choix_dep = false;
						print $myCart->get_choix_quoi_form(static::get_constructed_link('action', 'transfert', 'transfert_final', $idcaddie)."&idcaddie_origine=$idcaddie_origine", static::get_constructed_link('action', 'transfert'), $msg["caddie_choix_transfert"], $msg["caddie_bouton_transferer"], "", $aff_choix_dep);
						print pmb_bidi($myCart->aff_cart_titre());
						print $myCart->aff_cart_nb_items();
					}
					break;
				case 'transfert_final':
					$idcaddie_origine = caddie::check_rights($idcaddie_origine) ;
					if ($idcaddie_origine) {
						$myCartOrigine = static::get_object_instance($idcaddie_origine);
						print pmb_bidi($myCart->aff_cart_titre());
						print $myCart->aff_cart_nb_items();
						if ($myCart->type=='NOTI' && $myCartOrigine->type=='BULL') {
							// cas du transfert depuis caddie de BULL vers caddie de notices
							if ($bull_not) {
								// transfert des notices de bulletin
								if ($elt_flag) {
									$liste = $myCartOrigine->get_cart("FLAG") ;
									foreach ($liste as $cle => $object) {
										$myCart->add_item($object, $myCartOrigine->type) ;
									}
								}
								if ($elt_no_flag) {
									$liste = $myCartOrigine->get_cart("NOFLAG") ;
									foreach ($liste as $cle => $object) {
										$myCart->add_item($object, $myCartOrigine->type) ;
									}
								}
							}
							if ($bull_dep) {
								// transfert des notices de dépouillement
								if ($elt_flag) {
									$liste = $myCartOrigine->get_cart("FLAG") ;
									foreach ($liste as $cle => $object) {
										$myCart->add_item($object, $myCartOrigine->type, "DEP") ;
									}
								}
								if ($elt_no_flag) {
									$liste = $myCartOrigine->get_cart("NOFLAG") ;
									foreach ($liste as $cle => $object) {
										$myCart->add_item($object, $myCartOrigine->type, "DEP") ;
									}
								}
							}
						} else {
							// on est dans le cas "normal"
							if ($elt_flag) {
								$liste = $myCartOrigine->get_cart("FLAG") ;
								foreach ($liste as $cle => $object) {
									$myCart->add_item($object, $myCartOrigine->type) ;
								}
							}
							if ($elt_no_flag) {
								$liste = $myCartOrigine->get_cart("NOFLAG") ;
								foreach ($liste as $cle => $object) {
									$myCart->add_item($object, $myCartOrigine->type) ;
								}
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
		global $form_cb_expl;
		global $begin_result_expl_liste_unique;
	
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			print pmb_bidi($myCart->aff_cart_titre());
			switch ($action) {
				case 'add_item':
				case 'pointe_item':
					$item_info = $myCart->get_item_info_from_expl_cb($form_cb_expl);
					if($action == 'add_item') {
						if ($item_info->expl_ajout_ok) $res_ajout = $myCart->add_item($item_info->expl_id,"EXPL");
						else $res_ajout = $myCart->add_item_blob($form_cb_expl,"EXPL_CB" );
						$myCart->compte_items();
					} else {
						$res_ajout = $myCart->pointe_item($item_info->expl_id,"EXPL", $form_cb_expl, "EXPL_CB" );
					}
					print $myCart->aff_cart_nb_items();
						
					// form de saisie cb exemplaire
					print get_cb_expl($msg["caddie_".$action_prefix."_expl"], $msg[661], "./catalog.php?categ=caddie&sub=".$sub."&moyen=douchette&action=".$action_prefix."_item&idcaddie=$idcaddie");
					if ($item_info->expl_ajout_ok) {
						if ($res_ajout==CADDIE_ITEM_OK) {
							print "<hr /><div class='row'><span class='erreur'>".$msg["caddie_".$myCart->type."_".($action_prefix == 'add' ? 'added' : $action_prefix)]."</span></div><hr />";
							print $begin_result_expl_liste_unique;
							print pmb_bidi(print_info($item_info->stuff,0,1));
						}
						if ($res_ajout==CADDIE_ITEM_NULL) {
							print "<hr /><div class='row'><span class='erreur'>".$msg['caddie_item_null']."</span></div><hr />";
							$alert_sound_list[]="critique";
						}
						if ($res_ajout==CADDIE_ITEM_IMPOSSIBLE_BULLETIN) {
							print "<hr /><div class='row'><span class='erreur'>".$msg["caddie_".($action_prefix == 'add' ? '' : $action_prefix.'_')."item_impossible_bulletin"]."</span></div><hr />";
							$alert_sound_list[]="critique";
						}
						if ($res_ajout==CADDIE_ITEM_INEXISTANT) {
							print "<hr /><div class='row'><span class='erreur'>$form_cb_expl&nbsp;: ".$msg['caddie_'.$action_prefix.'_inconnu_panier']."</span></div><hr />";
							$alert_sound_list[]="critique";
						}
					} else print "<hr /><div class='row'><span class='erreur'>".$item_info->message_ajout_expl."</span></div><hr />" ;
					break;
				default:
					print $myCart->aff_cart_nb_items();
					// form de saisie cb exemplaire
					print get_cb_expl($msg["caddie_".$action_prefix."_expl"], $msg[661], "./catalog.php?categ=caddie&sub=".$sub."&moyen=douchette&action=".$action_prefix."_item&idcaddie=$idcaddie");
					break;
			}
		} else {
			static::get_aff_paniers($sub, '', 'douchette');
		}
	}	
	
	public static function proceed_edition_export_noti($idcaddie=0, $mode="simple") {
		global $msg, $charset;
		global $elt_flag , $elt_no_flag, $notice_tpl;
		
		$idcaddie += 0;
		if($idcaddie) {
			$myCart = static::get_object_instance($idcaddie);
			$fname = "bibliographie.doc";
			header('Content-Disposition: attachment; filename="'.$fname.'"');
			header('Content-type: application/msword');
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			print '<html><head><title>'.$msg['print_title'].'</title><meta http-equiv=Content-Type content="text/html; charset='.$charset.'" /></head><body>';
			switch ($mode) {
				case 'advanced':
					print $myCart->get_list_caddie_ui()->get_display_export_noti_list();
					break;
				case 'simple':
				default:
					$contents=afftab_cart_objects ($idcaddie, $elt_flag , $elt_no_flag, $notice_tpl);
					print $contents;
					break;
			}
			print '</body></html>';
		}
	}
	
	public static function print_prepare($idcaddie_new=0) {
		global $msg, $base_path;
		global $object_type, $item, $current_print, $aff_lien, $boutons_select;
		global $bannette_id;
		global $selected_objects, $include_child, $pager;
		
		if(!$object_type) $object_type = 'NOTI';
 		print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
        print "<h3>" . $msg["print_cart_title"] . "</h3>\n";
        print "<form name='print_options' action='print_cart.php?action=print' method='post'>";
        //Affichage de la sélection des paniers
        $requete = "select caddie.*,count(object_id) as nb_objects, count(flag=1) as nb_flags from caddie left join caddie_content on caddie_id=idcaddie group by idcaddie order by type, name, comment";
        $resultat = pmb_mysql_query($requete);
        $ctype = "";
        $parity = 0;
        $script_submit = '';
        while ($ca = pmb_mysql_fetch_object($resultat)) {
            if ($idcaddie_new && ($idcaddie_new != $ca->idcaddie)) continue;
            if (!empty($idcaddie_new) && ($idcaddie_new == $ca->idcaddie)) {
                $script_submit = "<script>document.getElementById('id_" . $ca->idcaddie . "').checked=true;document.forms['print_options'].submit()</script>";
            }
            $ca_auth = explode(" ", $ca->autorisations);
            $as = in_array(SESSuserid, $ca_auth);
            if (($as !== false) && ($as !== null)) {
                if ($ca->type != $ctype) {
                    $ctype = $ca->type;
                    $print_cart[$ctype]["titre"] = "<b>" . $msg["caddie_de_" . $ca->type] . "</b><br />";
                }
                if (!trim($ca->caddie_classement)) {
                    $ca->caddie_classement = classementGen::getDefaultLibelle();
                }
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["title"] = stripslashes($ca->caddie_classement);
                if (($parity = 1 - $parity)) {
                    $pair_impair = "even";
                } else {
                    $pair_impair = "odd";
                }
                $tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
                if(!isset($print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"])) {
                	$print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"] = '';
                }
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("<tr class='$pair_impair' $tr_javascript ><td class='classement60'><input type='checkbox' id='id_" . $ca->idcaddie . "' name='caddie[" . $ca->idcaddie . "]' value='" . $ca->idcaddie . "' />&nbsp;");
                $link = "print_cart.php?action=print&object_type=" . $object_type . "&idcaddie=" . $ca->idcaddie . "&item=$item&current_print=$current_print";
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("<a href='javascript:document.getElementById(\"id_" . $ca->idcaddie . "\").checked=true;document.forms[\"print_options\"].submit();' /><strong>" . $ca->name . "</strong>");
                if ($ca->comment) {
                    $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("<br /><small>(" . $ca->comment . ")</small>");
                }
                $print_cart[$ctype]["classement_list"][$ca->caddie_classement]["cart_list"].= pmb_bidi("</td>
                                                                                        <td><b>" . $ca->nb_flags . "</b>" . $msg['caddie_contient_pointes'] . " / <b>$ca->nb_objects</b> </td>
                                                        <td>$aff_lien</td>
                                                        </tr>");
            }
        }
        if (!isset($pager) && !$selected_objects) $pager = 0;
        elseif (!isset($pager)) $pager = 2;
        if (!isset($include_child)) $include_child = 0;
        if (!isset($selected_objects)) $selected_objects = '';
        print "
			<input type='radio' id='pager_2' name='pager' value='2' " . ($pager == 2 ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_2'>" . $msg["print_size_selected_elements"] . "</label><br />
        	<input type='radio' id='pager_1' name='pager' value='1' " . ($pager == 1 ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_1'>" . $msg["print_size_current_page"] . "</label><br />
            <input type='radio' id='pager_0' name='pager' value='0' " . (!$pager ? "checked='checked'" : "") . "/>&nbsp;<label for='pager_0'>" . $msg["print_size_all"] . "</label><br />
            <input type='checkbox' id='include_child' name='include_child' value='1'/>&nbsp;<label for='include_child'>" . $msg["cart_include_child"] . "</label>";
        print "<script> 
            function get_params_url() {
                var pager = document.querySelector('input[name=\"pager\"]:checked').value;
                var include_child = '';
                if (document.querySelector('input[name=\"include_child\"]:checked')) {
                    include_child = 1;
                }
                return './cart.php?action=new_cart&object_type=" . $object_type . "&item=$item&current_print=$current_print&selected_objects=$selected_objects&pager=' +  pager + '&include_child=' + include_child;
            }
        </script>";
        
        print "<div class='row'><hr />
            	$boutons_select&nbsp;<input class='bouton' type='button' value=' " . $msg['new_cart'] . " ' onClick=\"document.location=get_params_url();\" />
            </div>";
        print "<hr />";

        print pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' style='border:0px'></a>
                                        <a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' style='border:0px'></a>" . $msg['caddie_add_search'] . "</div>");

        if (count($print_cart)) {
            foreach ($print_cart as $key => $cart_type) {
                ksort($print_cart[$key]["classement_list"]);
            }
            foreach ($print_cart as $key => $cart_type) {
                //on remplace les clés à cause des accents
                $cart_type["classement_list"] = array_values($cart_type["classement_list"]);
                $contenu = "";
                foreach ($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
                    $contenu.=gen_plus($key . $keyBis, $cart_typeBis["title"], "<table border='0' cellspacing='0' style='width:100%' class='classementGen_tableau'>" . $cart_typeBis["cart_list"] . "</table>", 1);
                }
                print gen_plus($key, $cart_type["titre"], $contenu, 1);
            }
        }
        print "<input type='hidden' name='current_print' value='$current_print'/>";
        if($bannette_id) {
        	print "<input type='hidden' name='bannette_id' value='$bannette_id'/>";
        }
        if($selected_objects) {
        	print "<input type='hidden' name='selected_objects' value='$selected_objects'/>";
        }
        $boutons_select = '';
        if (count($print_cart)) {
            $boutons_select = "<input type='submit' value='" . $msg['print_cart_add'] . "' class='bouton' />";
        }
        $boutons_select.= "&nbsp;<input type='button' value='" . $msg['print_cancel'] . "' class='bouton' onClick='self.close();' />";
        $object_type = "NOTI";
        print "<div class='row'><hr />
                        $boutons_select&nbsp;<input class='bouton' type='button' value=' " . $msg['new_cart'] . " ' onClick=\"document.location=get_params_url();\" />
                        </div>";
        print "</form>
        <script type='text/javascript' src='".$base_path."/javascript/popup.js'></script>
        ";
        print $script_submit;
	}	
	
	public static function print_cart() {
		global $msg;
		global $nb_per_page_search, $page;
		global $idcaddie;
		
		$environement = $_SESSION["PRINT_CART"];
		$object_type = "NOTI";
		if(!empty($environement['bannette_id'])){
			$requete = "SELECT notice_id FROM bannette_contenu join notices on notice_id = num_notice where num_bannette='".$environement['bannette_id']."' order by index_sew";
		} elseif ($environement["TEXT_QUERY"]) {
			if (count($environement["TEXT_LIST_QUERY"])) {
				foreach($environement["TEXT_LIST_QUERY"] as $query) {
					 @pmb_mysql_query($query);
				}
			}
			$requete = $environement["TEXT_QUERY"];
			if ($_SESSION["tri"]) {
				$sort = new sort('notices', 'base');
				//$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id");
				if ($nb_per_page_search) {
					//$requete .= " LIMIT ".$page*$nb_per_page_search.",".$nb_per_page_search;
					$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", $page * $nb_per_page_search, $nb_per_page_search);
				} else {
					$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", 0, 0);
				}
			}
			if (!$environement["pager"]) {
				$p = stripos($requete, "limit");
				if ($p) {
					$requete = substr($requete, 0, $p);
				}
			}
		} else {
			switch ($environement["SEARCH_TYPE"]) {
				case "extended":
					$sh = new search();
					$table = $sh->make_search();
					$requete = "select " . $table . ".* from $table";
					if ($_SESSION["tri"]) {
						$sort = new sort('notices', 'base');
						if ($nb_per_page_search) {
							$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", $page * $nb_per_page_search, $nb_per_page_search);
						} else {
							$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", 0, 0);
						}
						if (!$environement["pager"]) {
							$p = stripos($requete, "limit");
							if ($p) {
								$requete = substr($requete, 0, $p);
							}
						}
					} else {
						$requete .= ",notices where notices.notice_id=$table.notice_id";
						if ($environement["pager"]) {
							$requete.=" limit " . $nb_per_page_search * $page . ",$nb_per_page_search";
						}
					}
					break;
				case "cart":
					$requete = "select object_id as notice_id from caddie_content";
					if ($_SESSION["tri"]) {
						$requete.=" where caddie_id=" . $idcaddie;
						$sort = new sort('notices', 'base');
						if ($nb_per_page_search) {
							$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", $nb_per_page_search * ($page - 1), $nb_per_page_search);
						} else {
							$requete = $sort->appliquer_tri($_SESSION["tri"], $requete, "notice_id", 0, 0);
						}
						if (!$environement["pager"]) {
							$p = stripos($requete, "limit");
							if ($p) {
								$requete = substr($requete, 0, $p);
							}
						}
					} else {
						$requete.= ",notices where notices.notice_id=caddie_content.object_id and caddie_id=" . $idcaddie;
						$orderby = " order by index_sew";
						if ($environement["pager"]) {
							$requete.=$orderby . " limit " . ($nb_per_page_search * ($page - 1)) . ",$nb_per_page_search";
						}
					}
					break;
				case "expl":
					$sh = new search(true, "search_fields_expl");
					$table = $sh->make_search();
					if ($environement["pager"]) {
						$limit = "limit " . ($nb_per_page_search * $page) . ",$nb_per_page_search";
					}
					$requete = "select expl_id as notice_id from $table " . $limit;
					$object_type = "EXPL";
					break;
				default :
				    $sh = new searcher_records_tab($environement["FORM_VALUES"]);
				    $notices = $sh->get_result();
				    $requete = "select  notice_id from notices where notice_id in ($notices)";
				    break;
			}
		}
		if (!isset($environement['selected_objects'])) {
		    $environement['selected_objects'] = array();
		}
		if ($environement["caddie"]) {
			$message = '';
			foreach ($environement["caddie"] as $environement_caddie) {
				$c = new caddie($environement_caddie);
				$nb_items_before = $c->nb_item;
				$resultat = @pmb_mysql_query($requete);
				print pmb_mysql_error();
				while (($r = pmb_mysql_fetch_object($resultat))) {
					if ($environement["pager"] != 2 || in_array($r->notice_id, $environement['selected_objects'])) {
						if ($environement["include_child"]) {
							$tab_list_child = notice::get_list_child($r->notice_id);
							if (count($tab_list_child)) {
								foreach ($tab_list_child as $notice_id) {
									$c->add_item($notice_id, $object_type);
								}
							}
						} else {
							$c->add_item($r->notice_id, $object_type);
						}
					}
				}
				$c->compte_items();
				$message.=sprintf($msg["print_cart_n_added"] . "\\n", ($c->nb_item - $nb_items_before), $c->name);
			}
			print "<script>alert(\"$message\"); self.close();</script>";
		} else {
			print "<script>alert(\"" . $msg["print_cart_no_cart_selected"] . "\"); history.go(-1);</script>";
        }
        $_SESSION["PRINT_CART"] = false;
	}
	
	public static function set_session() {
		global $current_print, $caddie, $pager, $include_child, $msg;
		global $bannette_id;
		global $selected_objects;
		
		if($bannette_id) {
			$_SESSION["PRINT_CART"] = array();
			$_SESSION["PRINT_CART"]["caddie"]=$caddie;
			$_SESSION["PRINT_CART"]["pager"]=$pager;
			$_SESSION["PRINT_CART"]["include_child"]=$include_child;
			$_SESSION["PRINT_CART"]["bannette_id"]=$bannette_id;
			if($selected_objects) {
				$_SESSION["PRINT_CART"]["selected_objects"]=explode(',', $selected_objects);
			}
			echo "<script>document.location='./print_cart.php'</script>";
		} elseif ($_SESSION["session_history"][$current_print]) {
			if($_SESSION["session_history"][$current_print]["NOTI"]){
				$_SESSION["PRINT_CART"]=$_SESSION["session_history"][$current_print]["NOTI"];
			} else if ($_SESSION["session_history"][$current_print]["EXPL"]) {
				$_SESSION["PRINT_CART"]=$_SESSION["session_history"][$current_print]["EXPL"];
			}
			$_SESSION["PRINT_CART"]["caddie"]=$caddie;
			$_SESSION["PRINT_CART"]["pager"]=$pager;
			$_SESSION["PRINT_CART"]["include_child"]=$include_child;
			if($selected_objects) {
				$_SESSION["PRINT_CART"]["selected_objects"]=explode(',', $selected_objects);
			}
			echo "<script>document.location='./print_cart.php'</script>";
		} else {
			echo "<script>alert(\"".$msg["print_no_search"]."\"); self.close();</script>";
		}
	}
} // fin de déclaration de la classe caddie_controller
