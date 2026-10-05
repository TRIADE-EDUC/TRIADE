<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bull_info.inc.php,v 1.95.2.1.2.3 2025/04/30 07:18:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage des infos bulletin
global $class_path, $include_path;

require_once($include_path."/resa_func.inc.php");
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/sur_location.class.php");
require_once($include_path."/avis_notice.inc.php");
require_once($class_path."/groupexpl.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/serials.class.php");

// get_expl : retourne un tableau HTML avec les exemplaires du bulletinage
function get_expl($expl, $show_in_reception=0, $return_count = false) {
	global $msg, $charset;
	global $class_path;
	global $cart_link_non;
	global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
	global $pmb_transferts_actif;
	global $pmb_expl_list_display_comments;
	global $pmb_sur_location_activate;
	global $pmb_expl_data;
	global $class_path;
	global $pmb_pret_groupement;

	// attention, $bul est un array
	if (!is_array($expl) || empty($expl)) {
		return $msg["bull_no_expl"];
	}
	$explr_tab_invis=explode(",",$explr_invisible);
	$explr_tab_unmod=explode(",",$explr_visible_unmod);
	$explr_tab_modif=explode(",",$explr_visible_mod);

//	$th_sur_location="";
//	if($pmb_sur_location_activate)$th_sur_location="<th>".$msg["sur_location_expl"]."</th>";
//
//	$result  = "<table style='border: 0px; border-spacing: 1px'>";
//	$result .= "<tr><th>".$msg[293]."</th><th>".$msg[4016]."</th>$th_sur_location<th>".$msg[4017]."</th><th>".$msg[4018]."</th><th>".$msg[4019]."</th><th>".$msg[4015]."</th><th></th>";
//	while(list($cle, $valeur) = each($expl)) {
//		$requete = "SELECT pret_idempr, ";
//		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour ";
//		$requete .= " FROM pret ";
//		$requete .= " WHERE pret_idexpl='$valeur->expl_id' ";
//		$result_prets = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);
//		if (pmb_mysql_num_rows($result_prets)) $expl_pret = pmb_mysql_fetch_object($result_prets) ;
//		else $expl_pret="";
//		$situation = "";
//		// prêtable ou pas s'il est prêté, on affiche son état
//		if (is_object($expl_pret) && $expl_pret->pret_idempr) {
//			// exemplaire sorti
//			$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl_pret->pret_idempr' ";
//			$res_empr = pmb_mysql_query($rqt_empr) ;
//			$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
//			$situation = "<strong>{$msg[358]} ".$expl_pret->aff_pret_retour."</strong>";
//			global $empr_show_caddie;
//			if (!$show_in_reception && $empr_show_caddie && (SESSrights & CIRCULATION_AUTH)) {
//				$img_ajout_empr_caddie="<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" onClick=\"openPopUp('./cart.php?object_type=EMPR&item=".$expl->pret_idempr."', 'cart')\">&nbsp;";
//			} else {
//				$img_ajout_empr_caddie="";
//			}
//			if (!$show_in_reception) {
//				$situation .= "<br />$img_ajout_empr_caddie<a href='./circ.php?categ=pret&form_cb=".rawurlencode($res_empr_obj->empr_cb)."'>$res_empr_obj->empr_prenom $res_empr_obj->empr_nom</a>";
//			} else {
//				$situation .= "<br />$res_empr_obj->empr_prenom $res_empr_obj->empr_nom";
//			}
//		} else {
//			// tester si réservé
//			$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($valeur->expl_cb)."' ") or die ();
//			$reserve = pmb_mysql_num_rows($result_resa);
//			if ($reserve)
//				$situation = "<strong>".$msg['expl_reserve']."</strong>"; // exemplaire réservé
//			elseif ($valeur->pret_flag)
//				$situation = "<strong>{$msg[359]}</strong>"; // exemplaire disponible
//			else
//				$situation = "";
//		}
//
//		if(!$show_in_reception && (SESSrights & CATALOGAGE_AUTH)){
//			$cart_click_expl = "onClick=\"openPopUp('./cart.php?object_type=EXPL&item=!!item!!', 'cart')\"";
//			$cart_link = "<img src='".get_url_icon('basket_small_20x20.gif')."' align='center' alt='middle' title=\"{$msg[400]}\" $cart_click_expl>";
//			$ajout_expl_panier = str_replace('!!item!!', $valeur->expl_id, $cart_link) ;
//		}else{
//			$ajout_expl_panier ="";
//		}
//
//		//si les transferts sont activés
//		if (!$show_in_reception && $pmb_transferts_actif) {
//			//si l'exemplaire n'est pas transferable on a une image vide
//			$dispo_pour_transfert = transfert::est_transferable ( $valeur->expl_id );
//			if (SESSrights & TRANSFERTS_AUTH && $dispo_pour_transfert)
//				//l'icon de demande de transfert
//				$ajout_expl_panier .= "<a href=\"#\" onClick=\"openPopUp('./catalog/transferts/transferts_popup.php?expl=" . $valeur->expl_id . "', 'transferts_popup');\">" . "<img src='".get_url_icon('peb_in.png')."' align='center' border=0 alt=\"" . $msg ["transferts_alt_libelle_icon"] . "\" title=\"" . $msg ["transferts_alt_libelle_icon"] . "\"></a>";
//			else
//				$ajout_expl_panier .= "<img src='".get_url_icon('spacer.gif')."' align='center' height=20 width=20>";
//
//		}
//
//		$as_invis = false;
//		$as_unmod = false;
//		$as_modif = true;
//		global $flag_no_delete_bulletin;
//		$flag_no_delete_bulletin=0;
//		//visibilité des exemplaires
//		if ($pmb_droits_explr_localises) {
//			$as_invis = in_array($valeur->expl_location,$explr_tab_invis);
//			$as_unmod = in_array($valeur->expl_location,$explr_tab_unmod);
//			//$as_modif = in_array($valeur->expl_location,$explr_tab_modif);
//
//			if(!($as_modif=in_array  ($valeur->expl_location,$explr_tab_modif) )) $flag_no_delete_bulletin=1;
//
//		}
//		if ($show_in_reception || $cart_link_non || !(SESSrights & CATALOGAGE_AUTH))
//			$link =  htmlentities($valeur->expl_cb,ENT_QUOTES, $charset);
//		else {
//			if ($as_modif) {
//				$link = "<a href=\"./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=".$valeur->expl_bulletin."&expl_id=".$valeur->expl_id."\">".htmlentities($valeur->expl_cb,ENT_QUOTES, $charset)."</a>";
//			} else {
//				$link = htmlentities($valeur->expl_cb,ENT_QUOTES, $charset);
//			}
//		}
//
//		if ($situation) $situation="<br />".$situation;
//		if(!$show_in_reception && SESSrights & CATALOGAGE_AUTH){
//			$ajout_expl_panier.="<span id='EXPL_drag_".$valeur->expl_id."'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($valeur->expl_cb,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
//		}
//
//		$line = "<tr>";
//		if (($valeur->expl_note || $valeur->expl_comment) && $pmb_expl_list_display_comments) $line .= "<td rowspan='2'>$link</td>";
//		else $line .= "<td>$link</td>";
//		$line .= "<td>$valeur->expl_cote</td>";
//		if($pmb_sur_location_activate) $line .= "<td>$valeur->sur_loc_libelle</td>";
//		$line .= "<td>$valeur->location_libelle</td>";
//		$line .= "<td>$valeur->section_libelle</td>";
//		$line .= "<td>$valeur->statut_libelle.$situation</td>";
//		$line .= "<td>$valeur->tdoc_libelle</td>";
//		$line .= "<td>$ajout_expl_panier</td>";
//		if (($valeur->expl_note || $valeur->expl_comment) && $pmb_expl_list_display_comments) {
//			$notcom=array();
//			$line .= "<tr><td colspan='6'>";
//			if ($valeur->expl_note && ($pmb_expl_list_display_comments & 1)) $notcom[] .= "<span class='erreur'>$valeur->expl_note</span>";
//			if ($valeur->expl_comment && ($pmb_expl_list_display_comments & 2)) $notcom[] .= "$valeur->expl_comment";
//			$line .= implode("<br />",$notcom);
//			$line .= "</tr>";
//		}
//		$result .= $line;
//	} //while(list($cle, $valeur) = each($expl))
//
//	$result .= "</table>";
//

	//maintenant
	//Liste des champs d'exemplaires
	if($pmb_sur_location_activate) $surloc_field="surloc_libelle,";
	if (!$pmb_expl_data) $pmb_expl_data="expl_cb,expl_cote,".$surloc_field."location_libelle,section_libelle,statut_libelle,tdoc_libelle";
	$colonnesarray=explode(",",$pmb_expl_data);
	if (!in_array("expl_cb", $colonnesarray)) array_unshift($colonnesarray, "expl_cb");
	$total_columns = count($colonnesarray);
	if ($pmb_pret_groupement || $pmb_transferts_actif) $total_columns++;
	//Présence de champs personnalisés
	if (strstr($pmb_expl_data, "#")) {
		require_once($class_path."/parametres_perso.class.php");
    	$cp=new parametres_perso("expl");
	}
	if ($return_count) {
		return count($expl);
	}
	if(count($expl)){
		$result = "";
		if($pmb_pret_groupement || $pmb_transferts_actif) {
			if ($pmb_pret_groupement) $on_click_groupexpl = "if(check_if_checked(document.getElementById('expl_list_id').value,'groupexpl')) openPopUp('./select.php?what=groupexpl&caller=form_expl&expl_list_id='+get_expl_checked(document.getElementById('expl_list_id').value), 'selector')";
			if ($pmb_transferts_actif) $on_click_transferts = "if(check_if_checked(document.getElementById('expl_list_id_transfer').value,'transfer')) openPopUp('./catalog/transferts/transferts_popup.php?expl='+get_expl_checked(document.getElementById('expl_list_id_transfer').value), 'selector')";
			$result .= "
					<script type='text/javascript' src='./javascript/expl_list.js'></script>
					<script type='text/javascript'>
 						var msg_select_all = '".$msg["notice_expl_check_all"]."';
 						var msg_unselect_all = '".$msg["notice_expl_uncheck_all"]."';
 						var msg_have_select_expl = '".$msg["notice_expl_have_select_expl"]."';
 						var msg_have_select_transfer_expl = '".$msg["notice_expl_have_select_transfer_expl"]."';
 						var msg_have_same_loc_expl = '".$msg["notice_expl_have_same_loc_expl"]."';
 					</script>
 					<table style='border: 0px; border-spacing: 1px'>
						<tr>
							<th colspan='".(count($colonnesarray)+2)."'>
								".$msg["notice_for_expl_checked"]."
								".($pmb_pret_groupement ? "<input class='bouton' type='button' value=\"".$msg["notice_for_expl_checked_groupexpl"]."\" onClick=\"".$on_click_groupexpl."\" />&nbsp;&nbsp;" : "")."
								".($pmb_transferts_actif ? "<input class='bouton' type='button' value=\"".$msg["notice_for_expl_checked_transfert"]."\" onClick=\"".$on_click_transferts."\" />" : "")."
							</th>
						</tr>
					</table>";
		}
		$result .= "<table style='border: 0px; border-spacing: 1px' class=\"sortable\">";
		//un premier tour pour aller chercher les libellés...
		$entry = '';
		for ($i=0; $i<count($colonnesarray); $i++) {
			if (substr($colonnesarray[$i],0,1)=="#") {
    			//champs personnalisés
    			if (!$cp->no_special_fields) {
    				$id=substr($colonnesarray[$i],1);
    				$entry.="<th>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</th>";
    			}
    		} else {
				$colencours = $msg['expl_header_' . $colonnesarray[$i]] ?? '';
				$entry.="<th>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
    		}
		}
		$result.="<tr>".$entry."<th>&nbsp;</th>";
		if($pmb_pret_groupement || $pmb_transferts_actif) {
			$expl_list_id = array();
			$expl_list_id_transfer = array();
			$result.="<th class='center'>
						<input type='checkbox' onclick=\"check_all_expl(this,document.getElementById('expl_list_id').value)\" title='".$msg["notice_expl_check_all"]."' id='select_all' name='select_all' />
					</th>";
		}
		$result.="</tr>";
		foreach($expl as $exemplaire){
			$requete = "SELECT pret_idempr, ";
			$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour ";
			$requete .= " FROM pret ";
			$requete .= " WHERE pret_idexpl='$exemplaire->expl_id' ";
			$result_prets = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);
			if (pmb_mysql_num_rows($result_prets)) $expl_pret = pmb_mysql_fetch_object($result_prets) ;
			else $expl_pret="";
			$situation = "";
			// prêtable ou pas s'il est prêté, on affiche son état
			if (is_object($expl_pret) && $expl_pret->pret_idempr) {
				// exemplaire sorti
				$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl_pret->pret_idempr' ";
				$res_empr = pmb_mysql_query($rqt_empr) ;
				$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
				$situation = "<strong>{$msg[358]} ".$expl_pret->aff_pret_retour."</strong>";
				global $empr_show_caddie;
				if (!$show_in_reception && $empr_show_caddie && (SESSrights & CIRCULATION_AUTH)) {
				    $img_ajout_empr_caddie="<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" onClick=\"openPopUp('./cart.php?object_type=EMPR&item=".$expl_pret->pret_idempr."', 'cart')\">&nbsp;";
				} else {
					$img_ajout_empr_caddie="";
				}
				if (!$show_in_reception) {
					$situation .= "<br />$img_ajout_empr_caddie<a href='./circ.php?categ=pret&form_cb=".rawurlencode($res_empr_obj->empr_cb)."'>$res_empr_obj->empr_prenom $res_empr_obj->empr_nom</a>";
				} else {
					$situation .= "<br />$res_empr_obj->empr_prenom $res_empr_obj->empr_nom";
				}
			} else {
				// tester si réservé
				$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($exemplaire->expl_cb)."' ") or die ();
				$reserve = pmb_mysql_num_rows($result_resa);
				if ($reserve) {
					$situation = "<strong>".$msg['expl_reserve']."</strong>"; // exemplaire réservé
				} elseif ($exemplaire->pret_flag) {
					$situation = "<strong>{$msg[359]}</strong>"; // exemplaire disponible
				} else {
					$situation = "";
				}
			}

			if(!$show_in_reception && (SESSrights & CATALOGAGE_AUTH)){
				$cart_click_expl = "onClick=\"openPopUp('./cart.php?object_type=EXPL&item=!!item!!', 'cart')\"";
				$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$exemplaire->expl_id.",'EXPL',1);\" onMouseOut=\"set_flag_info_div(false);\"";
				$cart_link = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='center' alt='middle' title=\"{$msg[400]}\" $cart_click_expl $cart_over_out>";
				$ajout_expl_panier = str_replace('!!item!!', $exemplaire->expl_id, $cart_link) ;
			}else{
				$ajout_expl_panier ="";
			}

			//si les transferts sont activés
			if (!$show_in_reception && $pmb_transferts_actif) {
				//si l'exemplaire n'est pas transferable on a une image vide
				$dispo_pour_transfert = transfert::est_transferable ( $exemplaire->expl_id );
				if (SESSrights & TRANSFERTS_AUTH && $dispo_pour_transfert) {
					//l'icon de demande de transfert
					$ajout_expl_panier .= "<a href=\"#\" onClick=\"openPopUp('./catalog/transferts/transferts_popup.php?expl=" . $exemplaire->expl_id . "', 'cart', 600, 450, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');\">" . "<img src='".get_url_icon('peb_in.png')."' class='center' border=0 alt=\"" . $msg ["transferts_alt_libelle_icon"] . "\" title=\"" . $msg ["transferts_alt_libelle_icon"] . "\"></a>";
					$expl_list_id_transfer[] = $exemplaire->expl_id;
				} else {
					$ajout_expl_panier .= "<img src='".get_url_icon('spacer.gif')."' class='center' height=20 width=20>";
				}
			}

			$as_invis = false;
			$as_unmod = false;
			$as_modif = true;
			global $flag_no_delete_bulletin;
			$flag_no_delete_bulletin=0;
			//visibilité des exemplaires
			if ($pmb_droits_explr_localises) {
				$as_invis = in_array($exemplaire->expl_location,$explr_tab_invis);
				$as_unmod = in_array($exemplaire->expl_location,$explr_tab_unmod);
				//$as_modif = in_array($exemplaire->expl_location,$explr_tab_modif);

				if(!($as_modif=in_array  ($exemplaire->expl_location,$explr_tab_modif) )) {
					$flag_no_delete_bulletin=1;
				}

			}
			if ($show_in_reception || $cart_link_non || !(SESSrights & CATALOGAGE_AUTH)) {
				$link =  htmlentities($exemplaire->expl_cb,ENT_QUOTES, $charset);
			} else {
				if ($as_modif) {
					$link = "<a href=\"./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=".$exemplaire->expl_bulletin."&expl_id=".$exemplaire->expl_id."\">".htmlentities($exemplaire->expl_cb,ENT_QUOTES, $charset)."</a>";
				} else {
					$link = htmlentities($exemplaire->expl_cb,ENT_QUOTES, $charset);
				}
			}

			if ($situation) {
				$situation="<br />".$situation;
			}
			if(!$show_in_reception && SESSrights & CATALOGAGE_AUTH){
				$ajout_expl_panier.="<span id='EXPL_drag_".$exemplaire->expl_id."'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($exemplaire->expl_cb,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
			}
			global $pmb_serialcirc_subst;
			if ($pmb_serialcirc_subst){
				$ajout_expl_panier.="<img src='".get_url_icon('print.gif')."' alt='Imprimer...' title='Imprimer...' class='align_middle' border='0'	style='padding-left:7px'
					onclick=\"openPopUp('./ajax.php?module=circ&categ=periocirc&sub=print_cote&expl_id=".$exemplaire->expl_id."', 'circulation');\"
				>";

			}
			$line="<tr>";
			for ($i=0; $i<count($colonnesarray); $i++) {
     			if (($i == 0) && ($exemplaire->expl_note || $exemplaire->expl_comment) && $pmb_expl_list_display_comments) $expl_rowspan = "rowspan='2'";
				else $expl_rowspan = "";
				$aff_column ="";
				$id_column = "";
				if (substr($colonnesarray[$i],0,1)=="#") {
    				//champs personnalisés
    				$id=substr($colonnesarray[$i],1);
					$cp->get_values($exemplaire->expl_id);
    				if (!$cp->no_special_fields) {
    					$temp=$cp->get_formatted_output((isset($cp->values[$id]) ? $cp->values[$id] : array()), $id);
    					if (!$temp) {
    						$temp="&nbsp;";
    					}
    					$aff_column.=$temp;
    				}
    			}else{
    				if($colonnesarray[$i] != "groupexpl_name") {
    					eval ("\$colencours=\$exemplaire->".$colonnesarray[$i].";");
    				}
	    			if ($colonnesarray[$i]=="expl_cb") {
    					$id_column = "id='expl_" . $exemplaire->expl_id . "'";
						$aff_column = $link;
					} else if ($colonnesarray[$i]=="expl_cote") {
						$aff_column="<strong>".htmlentities($colencours,ENT_QUOTES, $charset)."</strong>";
					} else if ($colonnesarray[$i]=="surloc_libelle") {
 						$aff_column=htmlentities($exemplaire->sur_loc_libelle,ENT_QUOTES, $charset);
	    			}else if($colonnesarray[$i]=="statut_libelle"){
	    				$aff_column = htmlentities($colencours,ENT_QUOTES, $charset).$situation;
	    			}else if ($colonnesarray[$i]=="groupexpl_name") {
    					$id_column = "id='groupexpl_name_".$exemplaire->expl_cb."'";
    					$colencours = groupexpls::get_group_name_expl($exemplaire->expl_cb);
    					$aff_column = htmlentities($colencours,ENT_QUOTES, $charset);
	    			}else if ($colonnesarray[$i]=="nb_prets") {
						$colencours = exemplaire::get_nb_prets_from_id($exemplaire->expl_id);
						$aff_column = ($colencours ? htmlentities($colencours,ENT_QUOTES, $charset) : '');
					}else {
						$aff_column = htmlentities($colencours,ENT_QUOTES, $charset);
	    			}
    			}
				$line.="<td $expl_rowspan $id_column class='expl-column-".$i."'>".$aff_column."</td>";
			}
			$line .= "<td>$ajout_expl_panier</td>";
			if ($pmb_pret_groupement || $pmb_transferts_actif) {
				$line .= "<td class='center'><input type='checkbox' id='checkbox_expl[".$exemplaire->expl_id."]' name='checkbox_expl[".$exemplaire->expl_id."]' /></td>";
				$expl_list_id[] = $exemplaire->expl_id;
			}
			$line.="</tr>";
			if (($exemplaire->expl_note || $exemplaire->expl_comment) && $pmb_expl_list_display_comments) {
				$notcom=array();
				$line .= "<tr><td colspan='".$total_columns."'>";
				if ($exemplaire->expl_note && ($pmb_expl_list_display_comments & 1)) {
				    $notcom[] = "<span class='erreur'>$exemplaire->expl_note</span>";
				}
				if ($exemplaire->expl_comment && ($pmb_expl_list_display_comments & 2)) {
				    $notcom[] = "<span class='expl_list_comment'>$exemplaire->expl_comment</span>";
				}
				$line .= implode("<br />",$notcom);
				$line .= "</tr>";
			}
			$result.= $line;
		}
		if ($pmb_pret_groupement || $pmb_transferts_actif) {
			$result .= "<input type='hidden' id='expl_list_id' name='expl_list_id' value='".implode(",", $expl_list_id)."' 	/>
			<input type='hidden' id='expl_list_id_transfer' name='expl_list_id_transfer' value='".implode(",", $expl_list_id_transfer)."' />";
		}
		$result .= "</table>";
	}
	return $result;
}


// get_analysis : retourne les dépouillements pour un bulletinage donné
function get_analysis($bul_id) {
	global $explnum_popup_edition_script;
	global $pmb_enable_explnum_edition_popup;
	global $link_analysis, $link_serial, $link_bulletin;

	if(!$bul_id) return '';

	$requete = "SELECT * FROM analysis WHERE analysis_bulletin=$bul_id ORDER BY analysis_notice";
	$myQuery = pmb_mysql_query($requete);

	$link_serial = serial::get_pattern_link();
	$link_analysis = "./catalog.php?categ=serials&sub=analysis&action=analysis_form&bul_id=$bul_id&analysis_id=!!id!!";
	$link_bulletin = bulletinage::get_pattern_link();

	$analysis_list = '';
	$list = [];
	while($analysis=pmb_mysql_fetch_object($myQuery)) {
	    $list[] = $analysis->analysis_notice;
	}
	$elements_records_list_ui = new elements_records_list_ui($list, count($list), false);
	$elements_records_list_ui->set_button_explnum(1);
	$elements_records_list_ui->set_link_serial($link_serial);
	$elements_records_list_ui->set_link_analysis($link_analysis);
	$elements_records_list_ui->set_link_bulletin($link_bulletin);

	$analysis_list .= $elements_records_list_ui->get_elements_list();
	if($pmb_enable_explnum_edition_popup){
	    $analysis_list.= $explnum_popup_edition_script;
	}
	return $analysis_list;
}

// affichage d'informations pour une entrée de bulletinage
function show_bulletinage_info($bul_id, $lien_cart_ajout=1, $lien_cart_suppr=0, $flag_pointe=0, $lien_pointe=0 ) {
	global $msg, $charset;
	global $liste_script;
	global $liste_debut;
	global $liste_fin;
	global $bul_action_bar;
	global $bul_cb_form;
	global $url_base_suppr_cart ;
	global $page, $nbr_lignes, $nb_per_page;
	global $idcaddie;

	$cart_click_bull = "onClick=\"openPopUp('./cart.php?object_type=BULL&item=!!item!!', 'cart')\"";
	$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$bul_id.",'BULL');\" onMouseOut=\"set_flag_info_div(false);\"";
	$nb_per_page = $nb_per_page ?? 0;
	$nb_after_suppr = 0;
	$page_suppr = 0;
	//Calcul des variables pour la suppression d'items
	if($nb_per_page){
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
	}

	$affichage_final = '';
	if ($bul_id) {
		if (SESSrights & CATALOGAGE_AUTH) {
			$myBul = new bulletinage($bul_id, 0, "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=$bul_id&explnum_id=!!explnum_id!!", 0, false);
			$myBul->notice_show_expl = 0;
			$myBul->make_display();

			// lien vers la notice chapeau
			$link_parent = "<a href=\"./catalog.php?categ=serials\">".$msg[4010]."</a>";
			$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' style='margin: 5px 5px'>";
			$link_parent .= "<a href=\"".serial::get_permalink($myBul->bulletin_notice)."\">".$myBul->get_serial()->tit1.'</a>';
			$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' style='margin: 5px 5px'>";

			if ($myBul->bulletin_numero) {
				$link_bulletin = $myBul->bulletin_numero." ";
			}
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			$date_affichee='';
			if ($myBul->mention_date) {
				$date_affichee = " (".$myBul->mention_date.")";
			} else if ($myBul->date_date) {
				$date_affichee = " [".formatdate($myBul->date_date)."]";
			}

			$link_bulletin .= $date_affichee;

			$link_parent .= "<a href='".bulletinage::get_permalink($bul_id)."'>$link_bulletin</a>" ;
			$affichage_final .= "<div class='row'><div class='perio-barre'>".$link_parent."</div></div>";

			if ($lien_cart_ajout) {
				$cart_link = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_bull $cart_over_out>";
				$cart_link = str_replace('!!item!!', $bul_id, $cart_link);
// 				$cart_link.="<span id='BULL_drag_".$bul_id."'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($link_bulletin,ENT_QUOTES,$charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
			} else {
				$cart_link="" ;
			}
			if ($lien_cart_suppr) {
				if ($lien_pointe) {
					if ($flag_pointe) {
						$marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$idcaddie."_item_".$bul_id."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$idcaddie.",".$bul_id.");' style='cursor: pointer'/>" ;
					} else {
						$marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$idcaddie."_item_".$bul_id."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$idcaddie.",".$bul_id.");' style='cursor: pointer'/>" ;
					}
				} else {
					if ($flag_pointe) {
						$marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
					} else {
						$marque_flag ="" ;
					}
				}
				$cart_link .= "<a href='$url_base_suppr_cart&action=del_item&object_type=BULL&item=$bul_id&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg["caddie_icone_suppr_elt"]."\" /></a> $marque_flag";
			}

		}else{
			$myBul = new bulletinage($bul_id, 0, '');
			$cart_link='';
		}

		$bul_action_bar = str_replace('!!bul_id!!', $bul_id, $bul_action_bar);
		$bul_action_bar = str_replace('!!nb_expl!!', count($myBul->expl), $bul_action_bar);

		$bul_isbd = $myBul->display;

		$javascript_template ="
		<div id=\"el!!id!!Parent\" class=\"notice-parent\">
            ".get_expandBase_button('el!!id!!')."
    		<span class=\"notice-heada\">!!heada!!</span>
    		<br />
		</div>
		<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
           		!!ISBD!!
 		</div>";
		$aff_expandable = str_replace('!!id!!', $bul_id, $javascript_template);
		$aff_expandable = str_replace('!!heada!!', $cart_link." ".$bul_isbd, $aff_expandable);

		// affichage des exemplaires associés
		$list_expl  = "<div class='exemplaires-perio'>";
		$list_expl .= "<h3>".$msg[4012]."</h3>";
		$list_expl .= "<div class='row'>".get_expl($myBul->expl)."</div></div>";
		$affichage_final .= $list_expl;

		// affichage des documents numeriques
		$aff_expl_num=$myBul->explnum ;
		if ($aff_expl_num) {
			$list_expl = "<div class='exemplaires-perio'><h3>".$msg['explnum_docs_associes']."</h3>";
			$list_expl .= "<div class='row'>".$aff_expl_num."</div></div>";
			$affichage_final .=  $list_expl;
		}

		//affichage des dépouillements
		$liste = get_analysis($bul_id);
		if($liste) {
			$liste_dep = $liste;
			$liste_dep .= $liste_fin;
			// inclusion du javascript inline
			$liste_dep .= $liste_script;
		} else {
			$liste_dep = "<div class='row'>".htmlentities($msg['bull_no_item'],ENT_QUOTES,$charset)."</div>";
		}
		$affichage_final .= "
			<div class='depouillements-perio'>
				<h3>".$msg[4013]."</h3>
				<div id='bulletin_analysis_list' class='row'>
					$liste_dep
					</div>
				</div>";

		// affichage des résas
		$aff_resa=resa_list (0, $bul_id, 0) ;
		if ($aff_resa) {
			$affichage_final .= "<h3>".$msg['resas']."</h3>".$aff_resa;
		}
	}
	$aff_expandable = str_replace('!!ISBD!!', $affichage_final, $aff_expandable);

	return $aff_expandable ;
}


// affichage d'informations pour une entrée de bulletinage en resas
function show_bulletinage_info_resa($bul_id, $link_header='') {
	global $msg, $charset;

	$affichage_final = '';
	if ($bul_id) {

		$myBul = new bulletinage($bul_id, 0, '', 0);
		$bul_header = $myBul->header;

		if($link_header) {
			$bul_header = '<a href="'.$link_header.'" >'.$bul_header.'</a>';
		}
		$javascript_template ="
			<div id=\"el".$bul_id."Parent\" class=\"notice-parent\">
                ".get_expandBase_button("el".$bul_id)."
	    		<span class=\"notice-heada\">!!header!!</span>
			</div>
			<div id=\"el".$bul_id."Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
	           		!!expl!!
	 		</div>";

		$aff_expandable = str_replace('!!header!!', $bul_header, $javascript_template);

		// affichage des exemplaires associés
		$list_expl  = "<div class='exemplaires-perio'>";
		$list_expl .= "<h3>".$msg[4012]."</h3>";
		$list_expl .= "<div class='row'>".get_expl($myBul->expl,1)."</div></div>";
		$affichage_final .= $list_expl;

		// affichage des résas
		$aff_resa=resa_list(0, $bul_id, 0);
		if ($aff_resa) {
			$affichage_final .= "<h3>".$msg['resas']."</h3>".$aff_resa;
		}
	}
	$aff_expandable = str_replace('!!expl!!', $affichage_final, $aff_expandable);
	return $aff_expandable ;
}
