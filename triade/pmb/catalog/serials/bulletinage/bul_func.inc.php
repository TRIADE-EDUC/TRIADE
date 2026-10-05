<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_func.inc.php,v 1.84.2.1.2.1 2025/05/13 09:54:26 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;

require_once ($include_path."/avis_notice.inc.php");
require_once ($include_path.'/h2o/pmb_h2o.inc.php');
require_once ($class_path.'/notice.class.php');
require_once ($class_path.'/records_tabs.class.php');
require_once ($class_path."/map/map_locations_controler.class.php");
require_once ($class_path."/caddie/caddie_controller.class.php");

// fonctions pour le bulletinage-----------------------------------------------

$cart_click_bull = "onClick=\"openPopUp('./cart.php?object_type=BULL&item=!!item!!', 'cart')\"";
$cart_click_expl = "onClick=\"openPopUp('./cart.php?object_type=EXPL&item=!!item!!', 'cart')\"";

// affichage d'informations pour une entrée de bulletinage
function show_bulletinage_info_catalogage(	$bul_id,
											$show_in_receptions=false
											) {

	global $msg, $charset, $base_path;
	global $liste_script;
	global $liste_debut;
	global $liste_fin;
	global $bul_action_bar;
	global $bul_cb_form;
	global $cart_click_bull;
	global $pmb_droits_explr_localises;
	global $explr_visible_mod;
	global $flag_no_delete_bulletin;
	global $pmb_resa_planning;
	global $categ, $quoi, $action, $sub;
	global $pmb_map_activate;
	global $pmb_url_base;

	$form ='';

	if ($bul_id) {

		if (!$show_in_receptions) {
			$myBul = new bulletinage($bul_id, 0, "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=$bul_id&explnum_id=!!explnum_id!!");
			$myBul->notice_show_expl = 0;
			$myBul->make_display();//Refait pour avoir la notice de bulletin sans ses exemplaires et ses docnums
			$affichage_expl = get_expl($myBul->expl);
			$cpt_expl = get_expl($myBul->expl,0,true);
		} else {
			$myBul = new bulletinage($bul_id, 0, '');
			$myBul->notice_show_expl = 0;
			$myBul->make_display();//Refait pour avoir la notice de bulletin sans ses exemplaires et ses docnums
			$affichage_expl = get_expl($myBul->expl, 1);
			$cpt_expl = get_expl($myBul->expl,1,true);
		}
		$bul_titre = $myBul->bulletin_titre;
		$bul_isbd = $myBul->display;

		$aff_expl_num = $myBul->explnum ;

		$txt_drag="";
		if ($myBul->bulletin_numero) $txt_drag .= $myBul->bulletin_numero." ";
		if ($myBul->mention_date) $txt_drag .= " (".$myBul->mention_date.") ";
		$txt_drag .= "[".$myBul->aff_date_date."]";

		if (!$show_in_receptions) {
			// lien vers la notice chapeau
			$link_parent = "<a href=\"./catalog.php?categ=serials\">";
			$link_parent .= $msg[4010]."</a>";
			$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
			$link_parent .= "<a href=\"".serial::get_permalink($myBul->bulletin_notice)."\">".htmlentities($myBul->get_serial()->tit1, ENT_QUOTES, $charset).'</a>';
			$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";

			$link_parent.=$txt_drag;
			if ($bul_titre) $link_parent .= " : ".htmlentities($bul_titre,ENT_QUOTES, $charset) ;

			// Titre de la page
			$form.= '<script type="text/javascript">document.title = "'.addslashes($txt_drag.($bul_titre ? ' : '.strip_tags($bul_titre) : '')).'";</script>';

			$form.= "<div class='row'><div class='perio-barre'>".$link_parent."</div></div>";

			$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$bul_id.",'BULL');\" onMouseOut=\"set_flag_info_div(false);\"";
			$cart_link = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_bull $cart_over_out>";
			$cart_link = str_replace('!!item!!', $bul_id, $cart_link);
// 			$cart_link.="<span id='BULL_drag_".$bul_id."'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($txt_drag,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";

			$bul_action_bar = str_replace('!!bul_id!!', $bul_id, $bul_action_bar);
			$bul_action_bar = str_replace('!!serial_id!!', $myBul->bulletin_notice, $bul_action_bar);
			$bul_action_bar = str_replace('!!nb_expl!!', count($myBul->expl), $bul_action_bar);

			global $avis_quoifaire,$valid_id_avis;
			if($myBul->bull_num_notice) {
				$bul_isbd = str_replace('<!-- !!avis_notice!! -->', avis_notice($myBul->bull_num_notice,$avis_quoifaire,$valid_id_avis), $bul_isbd);
				$bul_isbd = str_replace('<!-- !!caddies_notice!! -->', caddie_controller::get_display_list_from_item('display', 'NOTI', $myBul->bull_num_notice), $bul_isbd);
			}

			if(!$flag_no_delete_bulletin)$bul_action_bar = str_replace("!!bulletin_delete_button!!", "<input type='button' class='bouton' onclick=\"confirm_bul_delete();\" value='$msg[63]' />", $bul_action_bar);
			else $bul_action_bar = str_replace("!!bulletin_delete_button!!", "", $bul_action_bar);

			if($myBul->bull_num_notice) {
				$form.= $liste_script;
			}

			$form.= "
			<div class='bulletins-perio'>
				<div class='row'>
					<h3>".$cart_link." ".$bul_isbd."</h3>
					</div>
				<div class='row'>
					".$bul_action_bar."
				</div>
			</div>";

			$form.= '<div id="expl_area_' . $bul_id . '">';
			// map
			if($pmb_map_activate){
				$form.= map_locations_controler::get_map_location(0, $bul_id);
			}
			$form.= caddie_controller::get_display_list_from_item('display', 'BULL', $bul_id);

			// affichage des exemplaires associés
			$list_expl  = "<div class='exemplaires-perio'>";
			$list_expl .= "<h3>".$msg[4012]." (".$cpt_expl.")</h3>";

			$list_expl .= "<div class='row'>".$affichage_expl."</div></div>";
			$form.= $list_expl;

			//état des collections
			$list_collstate_ui = new list_collstate_ui(array('serial_id' => 0, 'bulletin_id' => $bul_id), array('all_on_page' => true));
			if(count($list_collstate_ui->get_objects())) {
				$form.= "<br /><h3>".$msg["abts_onglet_collstate"]." (".count($list_collstate_ui->get_objects()).")</h3>";
				$form.= $list_collstate_ui->get_display_list();
			}

			if ($aff_expl_num) {
				$list_expl = "<div class='exemplaires-perio'><h3>".$msg['explnum_docs_associes']." (".$myBul->nbexplnum.")</h3>";
				$list_expl .= "<div class='row'>".$aff_expl_num."</div></div>";
				$form.= $list_expl;
			}
			$form.= '</div>';
			if ((!$explr_visible_mod)&&($pmb_droits_explr_localises==1)) {
				$etiquette_expl="";
				$btn_ajouter_expl="";
				$saisie_num_expl="<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
				$saisie_num_expl.= "<div class='colonne-suite'><span class='erreur'>".$msg["err_add_invis_expl"]."</span></div>";
			} else {
				$etiquette_expl="<div class='row'>
							<label class='etiquette' for='form_cb'>$msg[291]</label>
							</div>";
				$btn_ajouter_expl="<input type='submit' class='bouton' value=' $msg[expl_ajouter] ' onClick=\"return test_form(this.form)\">";
				global $pmb_numero_exemplaire_auto,$pmb_numero_exemplaire_auto_script,$include_path;

				$num_exemplaire_auto = '';
				if($pmb_numero_exemplaire_auto==1 || $pmb_numero_exemplaire_auto==3){
					$num_exemplaire_auto=" $msg[option_num_auto] <INPUT type=checkbox name='option_num_auto' value='num_auto'";
					$checked=true;
					if ($pmb_numero_exemplaire_auto_script) {
						if (file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
							require_once($include_path."/$pmb_numero_exemplaire_auto_script");
							if (function_exists('is_checked_by_default')) {
								$checked=is_checked_by_default(0,$bul_id);
							}
						}
					}
					if ($checked) {
						$num_exemplaire_auto.=" checked='checked'";
					}
					$num_exemplaire_auto.=" >";
				}
				$saisie_num_expl="<input type='text' class='saisie-20em' name='noex' value=''>".$num_exemplaire_auto;
			}
			$req="select * from serialcirc_copy, bulletins where num_serialcirc_copy_bulletin=bulletin_id and bulletin_id= $bul_id";
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$btn_print_ask="<input type='button' class='bouton' value=' ".$msg["serialcirc_circ_list_reproduction_isdone_bt"]." ' onClick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=copy_isdone&bul_id=".$bul_id."';\" />";
			} else {
				$btn_print_ask="";
			}

			$bul_cb_form = str_replace('!!bul_id!!', $bul_id, $bul_cb_form);
			$bul_cb_form = str_replace('!!etiquette!!', $etiquette_expl, $bul_cb_form);
			$bul_cb_form = str_replace('!!saisie_num_expl!!', $saisie_num_expl, $bul_cb_form);
			$bul_cb_form = str_replace('!!btn_ajouter!!', $btn_ajouter_expl, $bul_cb_form);
			$bul_cb_form = str_replace('!!btn_print_ask!!', $btn_print_ask, $bul_cb_form);
			$form.= "<div class='row'>".$bul_cb_form."</div>";

			// zone d'affichage des dépouillements
			$liste = get_analysis($bul_id);
			if ($liste) {
				//Au dela de 6 depouillements on passe en ajax
				//6 est un choix defini dans la classe elements_list_ui dans la methode generate_elements_list
				if(intval($myBul->nb_analysis) > 6) {
					$liste_debut = str_replace("expandAll", "expandAll_ajax", $liste_debut);
				}
				$icones_exp = $liste_debut."&nbsp;<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".$msg[400]."' onClick=\"openPopUp('./cart.php?object_type=BULL&item=".$bul_id."&what=DEP', 'cart')\">";
				$liste_dep = $liste;
				$liste_dep .= $liste_fin;
				// inclusion du javascript inline
				$liste_dep .= (!$myBul->bull_num_notice ? $liste_script : "");
			} else {
				$icones_exp = "";
				$liste_dep = "<div class='row'>".$msg['bulletin_no_analysis']."</div>";
			}
			$link_new_dep = "<input type='button' class='bouton' value=' $msg[4021] ' onClick=\"document.location='./catalog.php?categ=serials&sub=analysis&action=analysis_form&bul_id=$bul_id&analysis_id=0';\" />";

			$form.="
				<div class='depouillements-perio'>
					<h3>".$msg[4013].$icones_exp." $link_new_dep</h3>
					<div id='bulletin_analysis_list' class='row'>
						$liste_dep
						</div>
					</div>";

			//reservations et previsions
			$rqt_nt="select count(*) from exemplaires
					JOIN docs_statut ON exemplaires.expl_statut=docs_statut.idstatut
					JOIN bulletins ON exemplaires.expl_bulletin=bulletins.bulletin_id
					WHERE statut_allow_resa=1 and bulletins.bulletin_id=".$bul_id;
			$result = pmb_mysql_query($rqt_nt) or die ($rqt_nt. " ".pmb_mysql_error()) ;
			$nb_expl_reservables = pmb_mysql_result($result,0,0);

			$aff_resa=resa_list(0, $bul_id, 0) ;
			$ouvrir_reserv = "onclick=\"parent.location.href='./circ.php?categ=resa_from_catal&id_bulletin=".$bul_id."'; return(false) \"";
			if ($aff_resa) {
				$form.="<b>".$msg['resas']."</b><br />";
				if($nb_expl_reservables) $form.= "<input type='button' class='bouton' value='".$msg[351]."' $ouvrir_reserv><br /><br />";
				$form.= $aff_resa."<br />";
			} else {
				if ($nb_expl_reservables) {
					$form.="<b>".$msg['resas']."</b><br /><input type='button' class='bouton' value='".$msg[351]."' $ouvrir_reserv><br /><br />";
				}
			}
			if($pmb_resa_planning) {
				$aff_resa_planning=planning_list(0,$bul_id,0);
				//TODO
				$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_planning_from_catal&id_bulletin=".$bul_id."'; return(false) \"";
				if ($aff_resa_planning){
					$form .= "<b>".$msg['resas_planning']."</b><br />";
					if($nb_expl_reservables ) $form.= "<input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
					$form.= $aff_resa_planning."<br />";
				} else {
					if ($nb_expl_reservables && !($categ=="resa_planning") && $nb_expl_reservables) $form.= "<b>".$msg['resas_planning']."</b><br /><input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
				}
			}

		} else {

			$form.= "<div class='notice-parent' id='_bull_'>
						<img onclick=\"expandBase('_bull_', true); return false;\" title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' id='_bull_Img' name='imEx' class='img_plus' src='".get_url_icon('minus.gif')."' />
						<span class='notice-heada'>".htmlentities($myBul->get_serial()->tit1.'.'.$txt_drag, ENT_QUOTES, $charset);
			if ($bul_titre) $form.= " : ".htmlentities($bul_titre, ENT_QUOTES,$charset);
			$form.= "	</span>
					</div>
					<div style='margin-bottom: 6px; display: none; width: 94%;' class='notice-child' id='_bull_Child'>
						<br /><b>".htmlentities($msg[4012], ENT_QUOTES, $charset)."</b>
						$affichage_expl
						<br ><b>".htmlentities($msg['explnum_docs_associes'], ENT_QUOTES, $charset)."</b>
						$aff_expl_num;
					</div>";
		}

		/**
		 * TODO : onglets
		 */
		if($myBul->bull_num_notice){
			$template_path_records_tabs =  "./includes/templates/records/records_elements_tabs.html";
			if(file_exists("./includes/templates/records/records_elements_tabs_subst.html")){
				$template_path_records_tabs =  "./includes/templates/records/records_elements_tabs_subst.html";
			}
			if(file_exists($template_path_records_tabs)){
				$h2o_record_tabs = H2o_collection::get_instance($template_path_records_tabs);
				$records_tabs = new records_tabs(new notice($myBul->bull_num_notice));
				$records_list_ui = $records_tabs->get_record()->get_records_list_ui();
				if ($records_list_ui) $records_list_ui->set_current_url($pmb_url_base.'catalog.php?categ='.$categ.'&bul_id='.$myBul->bulletin_id.'&sub='.$sub.'&action='.$action.'&quoi='.$quoi);
				$form.= $h2o_record_tabs->render(array('records_tabs' => $records_tabs, 'bulletin_id' => $myBul->bulletin_id));
			}
		}

	}
	return $form;
}
