<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_info.inc.php,v 1.71.4.2 2025/05/02 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once("$class_path/audit.class.php");
require_once($class_path."/sur_location.class.php");
require_once($class_path."/encoding_normalize.class.php");

// affichage des infos exemplaires
function print_info ($expl, $mode_affichage = 0, $affichage_emprunteurs = 1, $affichage_zone_notes = 1) {
	global $msg;
	global $pmb_sur_location_activate;
	global $pmb_location_resa_planning;
	global $pmb_expl_show_lastempr;
	global $pmb_droits_explr_localises,$explr_visible_mod;
	// $expl est l'objet exemplaire rempli avec ce qu'il faut
	// $mode_affichage : 
	//	0 en liste dépliable : le contenu est affiché dans le div
	//	1 : le contenu est affiché APRES l'isbd, sans liste dépliable
	//	2 : le contenu n'est pas affiché du tout
	//  3 : mode simplifié pour les exports HTML et tableur 
	
	if(!is_object($expl)) die("serious application error occured in ./circ/visu_ex.inc [print_info()]. Please contact developpment team");

	if ($mode_affichage == 3) {
	    return $msg[376]."&nbsp;".$expl->expl_cb." / ".$expl->aff_reduit;
	}
	switch($mode_affichage) {
		case '0':
			$temp= "
				<div id='el!!id!!Parent' class='notice-parent'>
                    ".get_expandBase_button('el!!id!!')."
	    			<span class='notice-heada'>!!heada!!</span>
	    			<br />
				</div>
				<div id='el!!id!!Child' class='notice-child' style='margin-bottom:6px;display:none;'>
	        	   	!!contenu!!
	 			</div>
				";
			$temp = str_replace('!!id!!', $expl->expl_id, $temp);
			if ($expl->expl_bulletin) {
				if (SESSrights & CATALOGAGE_AUTH) $heada = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=".$expl->expl_bulletin."&expl_id=".$expl->expl_id."'>".$msg[376]."&nbsp;".$expl->expl_cb."</a> / ".$expl->aff_reduit ;
				else $heada = "<a href='./circ.php?categ=visu_ex&form_cb_expl=".rawurlencode($expl->expl_cb)."'>".$msg[376]."&nbsp;".$expl->expl_cb."</a> / ".$expl->aff_reduit ;
			} else {
				if (SESSrights & CATALOGAGE_AUTH) $heada = "<a href='./catalog.php?categ=edit_expl&id=".$expl->expl_notice."&expl_id=".$expl->expl_id."'>".$msg[376]."&nbsp;".$expl->expl_cb."</a> / ".$expl->aff_reduit ;
				else $heada = "<a href='./circ.php?categ=visu_ex&form_cb_expl=".rawurlencode($expl->expl_cb)."'>".$msg[376]."&nbsp;".$expl->expl_cb."</a> / ".$expl->aff_reduit ;
			}
			if(!isset($expl->lien_suppr_cart)) $expl->lien_suppr_cart = '';
			$temp = str_replace('!!heada!!', $expl->lien_suppr_cart.$heada, $temp);
			break;
		case '1':
			$cart_click_expl = "onClick=\"openPopUp('./cart.php?object_type=EXPL&item=".$expl->expl_id."', 'cart')\"";
			$cart_click_expl = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_expl>" ;
			if ($expl->expl_notice) {
				$cart_click_isbd = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=$expl->expl_notice', 'cart')\"";
			} elseif ($expl->expl_bulletin) {
				$cart_click_isbd = "onClick=\"openPopUp('./cart.php?object_type=BULL&item=".$expl->expl_bulletin."', 'cart')\"";
			} 
			$cart_click_isbd = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_isbd>" ;
			if (SESSrights & CATALOGAGE_AUTH) {
				$link_cb_not="<a href='./catalog.php?categ=edit_expl&id=".$expl->expl_notice."&expl_id=".$expl->expl_id."'>";
				$link_cb_bull="<a href='./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=".$expl->expl_bulletin."&expl_id=".$expl->expl_id."'>";
				$link_cb_end="</a>";
				if ($pmb_droits_explr_localises) {
					$explr_tab_modif=explode(",",$explr_visible_mod);
					if (array_search($expl->expl_location,$explr_tab_modif)===false) {
						$link_cb_not="";
						$link_cb_bull="";
						$link_cb_end="";
					}
				}
				if ($expl->expl_bulletin){
					$temp= "<div class='row'><h1>$cart_click_expl&nbsp;$link_cb_bull{$msg[376]}&nbsp;".$expl->expl_cb."$link_cb_end : $cart_click_isbd&nbsp;".$expl->aff_reduit."</h1></div><div class='row'><b>".$expl->isbd."</b></div>";
				} else {
					$temp= "<div class='row'><h1>$cart_click_expl&nbsp;$link_cb_not{$msg[376]}&nbsp;".$expl->expl_cb."$link_cb_end : $cart_click_isbd&nbsp;".$expl->aff_reduit."</h1></div><div class='row'><b>".$expl->isbd."</b></div>";
				}
			} else $temp= "<div class='row'><h1>$cart_click_expl&nbsp;{$msg[376]}&nbsp;".$expl->expl_cb." : $cart_click_isbd&nbsp;".$expl->aff_reduit."</h1></div><div class='row'><b>".$expl->isbd."</b></div>";
			break;
		}
	
	// isbd complet
	$__isbd = "<div class=\"row\">";
	$__isbd.= $expl->aff_reduit ;
	$__isbd.= "</div>";
	$__modif_cb = '';
	if(SESSrights & (CATALOGAGE_AUTH + CATAL_MODIF_CB_EXPL_AUTH)){
	    $__modif_cb.= "<hr /><div class='row'><input type='button' id='button_edit_cb_".$expl->expl_id."' class='bouton' value='$msg[circ_edit_cb]'/><br/>";
		$__modif_cb.= "<input type='text' id='input_edit_cb_".$expl->expl_id."' style='display:none;'/>";
		$__modif_cb.= "<input type='button' class='bouton' value='".$msg['transferts_popup_btValider']."' id='button_send_edit_".$expl->expl_id."' style='display:none;'/>";
		$__modif_cb.= "</div>";
		$__modif_cb.="<script>
						var editButton = document.getElementById('button_edit_cb_".$expl->expl_id."');
						var callbackEnter = function(evt){
							var key = evt.which || evt.keyCode;
							if (key === 13) { 
								if(evt.target.value.replace(/^\s+$/g,'').length == 0) {
									alert('$msg[326]');
									evt.target.focus();
									return false;
								}else{
									launchUpdateRequest();
								}
							}
						}
						
						var launchUpdateRequest = function(){
							var request = new http_request();
							var inputCb = document.getElementById('input_edit_cb_".$expl->expl_id."');
							var callback = function(response){
								response = JSON.parse(response);
								if(response.status == 1){
									document.location.href = './circ.php?categ=visu_ex&form_cb_expl=' + encode_URL(inputCb.value);
								}else{ //Print message derreur
									alert(response.message);
								}
							}
							request.request('./ajax.php?module=circ&categ=expl&sub=update_cb&old_cb=".rawurlencode(encoding_normalize::utf8_normalize($expl->expl_cb))."&new_cb='+encodeURIComponent(inputCb.value), false,'', true, callback);
						}
						
						var callbackButton = function(evt){
							var inputCb = document.getElementById('input_edit_cb_".$expl->expl_id."');
							if(inputCb.value.replace(/^\s+$/g,'').length == 0) {
								alert('$msg[326]');
								inputCb.focus();
								return false;
							}else{
								launchUpdateRequest();
							}
						}
						var showCbInput = function(evt){
							var inputCb = document.getElementById('input_edit_cb_".$expl->expl_id."');
							var buttonValid = document.getElementById('button_send_edit_".$expl->expl_id."');
							if(inputCb.style.display == 'none' && buttonValid.style.display == 'none'){
								inputCb.style.display = '';
								inputCb.addEventListener('keypress', callbackEnter, true);
								inputCb.focus();
											
								buttonValid.style.display = '';
								buttonValid.addEventListener('click', callbackButton, true);
								
							}else{
								inputCb.removeEventListener('keypress', callbackEnter, false);
								inputCb.style.display = 'none';
								
								buttonValid.removeEventListener('click', callbackButton, false);
								buttonValid.style.display = 'none';
							}
						}
						editButton.addEventListener('click', showCbInput, false);					
					  </script>";
	}
	
	// informations de localisation
	$__local = "<hr /><div class=\"row\">";
	if($pmb_sur_location_activate){
		$__local.= $msg["sur_location_expl"].":&nbsp;<b>".$expl->sur_loc_libelle."</b>&nbsp;&nbsp;";
	}
	$__local.= "$msg[298]:&nbsp;<b>".$expl->location_libelle."</b>&nbsp;&nbsp;
			$msg[295]:&nbsp;<b>".$expl->section_libelle."</b>&nbsp;&nbsp;
			$msg[296]:&nbsp;<b>".$expl->expl_cote."</b><br />";
	$__local.= "$msg[297]:&nbsp;".$expl->statut_libelle;
	// tester si réservé
	$sql="SELECT resa_cb from resa_ranger where resa_cb='".addslashes($expl->expl_cb)."'";
	$execute_query=pmb_mysql_query($sql);
	if(pmb_mysql_num_rows($execute_query))$situation = $msg['resa_menu_a_ranger'];  // exemplaire à ranger
	elseif($expl->expl_retloc)$situation = $msg['resa_menu_a_traiter'];  // exemplaire à traiter
	elseif(verif_cb_utilise($expl->expl_cb)) $situation = $msg['expl_reserve']; // exemplaire réservé
	elseif ($expl->pret_flag && !$expl->pret_idempr) $situation = "{$msg[359]}"; // exemplaire disponible
	else $situation = "";
	$__local.= "&nbsp;&nbsp;<b>".$situation."</b><br />";
	$__local.=$msg[299].":&nbsp;<b>".$expl->codestat_libelle."</b><br />";
	
	
	$__local.= "</div>";

	$__empr = "";
	if ($affichage_emprunteurs) {
		// zone de l'emprunteur
		if($expl->pret_idempr) {
			$__empr.= "<hr /><div class='row'><b>$msg[380]</b><br /> ";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($expl->empr_cb)."'>";
			$__empr.= $link.$expl->empr_prenom." ".$expl->empr_nom." (".$expl->empr_cb.")</a>";
			$__empr.= "&nbsp;{$msg[381]}&nbsp;".$expl->aff_pret_date;
			$__empr.= ".&nbsp;{$msg[358]}&nbsp;".$expl->aff_pret_retour.".";
			$__empr.= "</div>";
		}
		
		// zone du dernier emrunteur
		if($pmb_expl_show_lastempr && $expl->expl_lastempr) {
			$__empr.= "<hr /><div class='row'><b>$msg[expl_lastempr]</b><br /> ";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($expl->lastempr_cb)."'>";
			$__empr.= $link.$expl->lastempr_prenom.' '.$expl->lastempr_nom.' ('.$expl->lastempr_cb.')</a>';
			$__empr.= "</div>";
		}
	}
	$__note = "";
	if ($affichage_zone_notes) {
		// zone du message exemplaire
		$__note = "<hr /><div class='row'>";
		$__note.= "<b>{$msg[377]}</b><br />";
		if ($expl->expl_note) $__note.= "<div class='message_important'>".nl2br($expl->expl_note)."</div>";
		if ($expl->expl_comment) {
			$__note.= "<b>".$msg['expl_zone_comment']."</b><br />";
			$__note.= "<div class='expl_comment'>".nl2br($expl->expl_comment)."</div>";
		}
		$__note.= "<br /><input type='button' class='bouton' value='$msg[378]' onclick=\"document.location='./circ.php?categ=note_ex&cb=".rawurlencode($expl->expl_cb)."&id=".$expl->expl_id."'\" />";
		$__note.= "</div><hr />";
	}
	// zone des réservations
	$__resa = check_resa_liste($expl);
	if ($__resa) {
		$__resa = "<div class=\"row\"><b>".$msg["reserv_en_cours_doc"]."</b><br />".$__resa;
		$__resa.= "</div>";
	} else {
	    // dernière réservation validée archivée
	    $__resa = check_resa_archive_liste($expl, 1);
	    if ($__resa) {
	        $__resa = "<hr /><div class=\"row\"><b>".$msg["reserv_last_doc"]."</b><br />".$__resa;
	        $__resa.= "</div>";
	    }
	}
	// zone des réservations prévisionnelles
	if ($pmb_location_resa_planning) {
		$__resa_planning = check_resa_planning_liste($expl);
		if ($__resa_planning) {
			$__resa_planning = "<div class=\"row\"><b>".$msg["previsions_en_cours_doc"]."</b><br />".$__resa_planning;
			$__resa_planning.= "</div>";
		}
	} else {
		$__resa_planning = "";
	}
	switch($mode_affichage) {
		case '0':
			$temp = str_replace('!!contenu!!', $__isbd.$__modif_cb.$__local.$__empr.$__note.$__resa.$__resa_planning, $temp);
			break;
		case '1':
			$temp = str_replace('!!contenu!!', "", $temp);
			$temp .= $__modif_cb.$__local.$__empr.$__note.$__resa.$__resa_planning ;
			break;
		case '2':
			$temp = str_replace('!!contenu!!', "", $temp);
			break;
	}
	return $temp;
}

// récupération des infos exemplaires
function get_expl_info($id, $lien_notice=1) {
	global $cart_link_non;
	global $pmb_sur_location_activate;
	
	$query = " select * from exemplaires expl, docs_location location";
	$query .= ", docs_section section, docs_statut statut, docs_type dtype, docs_codestat codestat";
	$query .= " where expl.expl_id='$id'";
	$query .= " and location.idlocation=expl.expl_location";
	$query .= " and section.idsection=expl.expl_section";
	$query .= " and statut.idstatut=expl.expl_statut";
	$query .= " and dtype.idtyp_doc=expl.expl_typdoc";
	$query .= " and codestat.idcode=expl.expl_codestat";
	$result = pmb_mysql_query($query);
	if(pmb_mysql_num_rows($result)) {
		$expl = pmb_mysql_fetch_object($result);
		if($expl->expl_notice) {
			if ((SESSrights & CATALOGAGE_AUTH) && $lien_notice) $notice = new mono_display($expl->expl_notice, 1, "./catalog.php?categ=isbd&id=".$expl->expl_notice, 0);
			else $notice = new mono_display($expl->expl_notice, 1, "", 0);
			$expl->isbd = $notice->isbd;
			$expl->code = $notice->notice->code ;
			$expl->aff_reduit = $notice->header;
			$expl->titre=$notice->tit1;
		} elseif ($expl->expl_bulletin) {
			$bl = new bulletinage_display($expl->expl_bulletin);
			$expl->isbd  = $bl->display;
			if ($cart_link_non) $expl->aff_reduit = $bl->header;
			else $expl->aff_reduit = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($expl->expl_bulletin) . "'>".$bl->header."</a>";
		}
		if ($expl->expl_lastempr) {
			$lastempr = new emprunteur($expl->expl_lastempr, '', FALSE, 0) ;
			$expl->lastempr_nom = $lastempr->nom;
			$expl->lastempr_prenom = $lastempr->prenom;
			$expl->lastempr_cb = $lastempr->cb;
		} else {
			$expl->lastempr_nom = '';
			$expl->lastempr_prenom = '';
			$expl->lastempr_cb = '';
		}
		if($pmb_sur_location_activate){
			$sur_loc= sur_location::get_info_surloc_from_location($expl->expl_location);
			$expl->sur_loc_libelle=$sur_loc->libelle;
			$expl->sur_loc_id=$sur_loc->id;
		} else {
			$expl->sur_loc_libelle='';
			$expl->sur_loc_id=0;
		}
		return $expl;
	} else {
		return FALSE;
	}

}

// récupére les réservations associées à la notice
// de l'exemplaire concerné
function check_resa_liste($expl) {
	global $msg ;
	
	if(!$expl || !is_object($expl))
		return '';
	
	$resa_list = '';
	$requete = "select empr_nom, empr_prenom, empr_cb, resa_date, resa_date_debut, resa_date_fin, IF(resa_date_fin>sysdate(),0,1) as perimee, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date from empr, resa";
	if($expl->expl_notice) $requete .= " where resa.resa_idnotice=".$expl->expl_notice;
	elseif($expl->expl_bulletin) $requete .= " where resa.resa_idbulletin=".$expl->expl_bulletin;
	$requete .= " and empr.id_empr=resa.resa_idempr";
	$requete .= " and (resa.resa_cb = '".addslashes($expl->expl_cb)."' or resa.resa_cb='')";
	$requete .= " order by resa.resa_date";
	$query = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($query)) {
		while($resa = pmb_mysql_fetch_object($query)) {
			$link = "<a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($resa->empr_cb)."\">";
			$resa_list .= $link.$resa->empr_prenom.'&nbsp;'.$resa->empr_nom;
			$resa_list .= "&nbsp;(".$resa->empr_cb.')</a>';
			$resa_list .= '&nbsp;<i>'.$resa->aff_resa_date.'</i>';
			if ($resa->resa_date_debut == "0000-00-00") {
				$resa_list .= " &gt;&gt; ".$msg['resa_attente_validation']." " ;
			} else {
				$resa_list .= " &gt;&gt; <b>".$msg['resa_date_debut'].":</b> ".formatdate($resa->resa_date_debut)."&nbsp;<b>".$msg['resa_date_fin'].":</b> ".formatdate($resa->resa_date_fin)."&nbsp;" ;
			}
			$resa_list .= "<br />";
		}
	}
	
	return $resa_list;
} 

// teste les réservations sur l'exemplaire et le cas échéant,
// retourne les infos de réservation dans l'objet spécifié
function check_resa($expl) {
	global $msg; 
	
	if(!is_object($expl))
		die("serious application error occured in ./circ/retour.inc [check_resa()]. Please contact developpment team");
	
	$expl->id_resa = '';
	$expl->resa_idempr = '';
	$expl->resa_idnotice = '';
	$expl->resa_idbulletin = '';
	$expl->resa_date = '';
	$expl->resa_date_fin = '';
	$expl->aff_resa_date = '';
	$expl->aff_resa_date_fin = '';
	$expl->resa_cb = '';
	$expl->cb_reservataire = '';
	$expl->nom_reservataire = '';
	$expl->prenom_reservataire = '';
	$expl->id_reservataire = '';
	
	if (!$expl->expl_notice) $expl->expl_notice=0;
	if (!$expl->expl_bulletin) $expl->expl_bulletin=0 ;
	$rqt = "select *, IF(resa_date_fin>sysdate(),0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date from resa where resa_idnotice='".$expl->expl_notice."' and resa_idbulletin='".$expl->expl_bulletin."' order by resa_date limit 1 ";
	
	$result = pmb_mysql_query($rqt) or die (pmb_mysql_error()) ;
	if(pmb_mysql_num_rows($result)) {

		// des réservations ont été trouvées ->
		// récupération des infos résa
		$resa = pmb_mysql_fetch_object($result);
		$expl->id_resa = $resa->id_resa;
		$expl->resa_idempr = $resa->resa_idempr;
		$expl->resa_idnotice = $resa->resa_idnotice;
		$expl->resa_idbulletin = $resa->resa_idbulletin;
		$expl->resa_date = $resa->resa_date;
		$expl->resa_date_fin = $resa->resa_date_fin;
		$expl->aff_resa_date = $resa->aff_resa_date;
		$expl->aff_resa_date_fin = $resa->aff_resa_date_fin;
		$expl->resa_cb = $resa->resa_cb;
		
		// récupération des infos sur le réservataire
		$query = "select empr_nom, empr_prenom, empr_cb, id_empr from empr where id_empr=".$resa->resa_idempr." limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			// stockage des infos sur le réservataire
			$empr = pmb_mysql_fetch_object($result);
			$expl->cb_reservataire = $empr->empr_cb;
			$expl->nom_reservataire = $empr->empr_nom;
			$expl->prenom_reservataire = $empr->empr_prenom;
			$expl->id_reservataire = $empr->id_empr;
		}

	}
	return $expl;
}

// récupére les réservations validées archivées associées à la notice
// de l'exemplaire concerné
function check_resa_archive_liste($expl, $limit=0) {
    global $msg;
    
    if(!$expl || !is_object($expl))
        return '';
        
        $resa_archive_list = '';
        $requete = "select empr_nom, empr_prenom, empr_cb, resarc_date, resarc_debut, resarc_fin, date_format(resarc_date, '".$msg["format_date"]."') as aff_resarc_date from empr, resa_archive";
        if($expl->expl_notice) $requete .= " where resa_archive.resarc_idnotice=".$expl->expl_notice;
        elseif($expl->expl_bulletin) $requete .= " where resa_archive.resarc_idbulletin=".$expl->expl_bulletin;
        $requete .= " and empr.id_empr=resa_archive.resarc_id_empr";
        $requete .= " and (resa_archive.resarc_cb = '".addslashes($expl->expl_cb)."')";
        $requete .= " order by resa_archive.resarc_date DESC";
        if($limit) {
            $requete .= " limit ".$limit;
        }
        $query = @pmb_mysql_query($requete);
        if(pmb_mysql_num_rows($query)) {
            while($resa = pmb_mysql_fetch_object($query)) {
                $link = "<a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($resa->empr_cb)."\">";
                $resa_archive_list .= $link.$resa->empr_prenom.'&nbsp;'.$resa->empr_nom;
                $resa_archive_list .= "&nbsp;(".$resa->empr_cb.')</a>';
                $resa_archive_list .= '&nbsp;<i>'.$resa->aff_resarc_date.'</i>';
                $resa_archive_list .= " &gt;&gt; <b>".$msg['resa_date_debut'].":</b> ".formatdate($resa->resarc_debut)."&nbsp;<b>".$msg['resa_date_fin'].":</b> ".formatdate($resa->resarc_fin)."&nbsp;" ;
                $resa_archive_list .= "<br />";
            }
        }
        
        return $resa_archive_list;
}

// récupére les réservations plannifiées associées à la notice
// de l'exemplaire concerné
function check_resa_planning_liste($expl) {
	global $msg ;
	
	if(!$expl || !is_object($expl) || !$expl->expl_notice) {
		return '';
	}
	$resa__planning_list = '';
	
	$requete = "select empr_nom, empr_prenom, empr_cb, resa_date, resa_date_debut, resa_date_fin, IF(resa_date_fin>sysdate(),0,1) as perimee, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date, resa_validee from empr, resa_planning";
	$requete .= " where resa_planning.resa_idnotice=".$expl->expl_notice;
	$requete .= " and empr.id_empr=resa_planning.resa_idempr";
	$requete .= " order by resa_planning.resa_date";
	$query = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($query)) {
		while($resa_planning = pmb_mysql_fetch_object($query)) {
			$link = "<a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($resa_planning->empr_cb)."\">";
			$resa__planning_list .= $link.$resa_planning->empr_prenom.'&nbsp;'.$resa_planning->empr_nom;
			$resa__planning_list .= "&nbsp;(".$resa_planning->empr_cb.')</a>';
			$resa__planning_list .= '&nbsp;<i>'.$resa_planning->aff_resa_date.'</i>';
			$resa__planning_list .= " &gt;&gt; <b>".$msg['resa_planning_date_debut']."</b> ".formatdate($resa_planning->resa_date_debut)."&nbsp;<b>".$msg['resa_planning_date_fin']."</b> ".formatdate($resa_planning->resa_date_fin)."&nbsp;" ;
			if (!$resa_planning->perimee) {
				if ($resa_planning->resa_validee)  $resa__planning_list .= " ".$msg['resa_validee'] ;
					else $resa__planning_list .= " ".$msg['resa_attente_validation']." " ;
			} else  $resa__planning_list .= " ".$msg['resa_overtime']." " ;
			$resa__planning_list .= "<br />";
		}
	}
	return $resa__planning_list;
} 

// teste les réservations plannifiées sur l'exemplaire et le cas échéant,
// retourne les infos de réservation dans l'objet spécifié
function check_resa_planning($expl) {
	global $msg; 
	
	if(!is_object($expl))
		die("serious application error occured in ./circ/retour.inc [check_resa_planning()]. Please contact developpment team");
	
	$expl->id_resa = '';
	$expl->resa_idempr = '';
	$expl->resa_idnotice = '';
	$expl->resa_date = '';
	$expl->resa_date_fin = '';
	$expl->aff_resa_date = '';
	$expl->aff_resa_date_fin = '';
	$expl->resa_cb = '';
	$expl->cb_reservataire = '';
	$expl->nom_reservataire = '';
	$expl->prenom_reservataire = '';
	$expl->id_reservataire = '';
	
	if (!$expl->expl_notice) $expl->expl_notice=0;
	$rqt = "select *, IF(resa_date_fin>sysdate(),0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date from resa_planning where resa_idnotice='".$expl->expl_notice."' order by resa_date limit 1 ";
	
	$result = pmb_mysql_query($rqt) or die (pmb_mysql_error()) ;
	if(pmb_mysql_num_rows($result)) {

		// des réservations prévisionnelles ont été trouvées ->
		// récupération des infos résa
		$resa_planning = pmb_mysql_fetch_object($result);
		$expl->id_resa = $resa_planning->id_resa;
		$expl->resa_idempr = $resa_planning->resa_idempr;
		$expl->resa_idnotice = $resa_planning->resa_idnotice;
		$expl->resa_date = $resa_planning->resa_date;
		$expl->resa_date_fin = $resa_planning->resa_date_fin;
		$expl->aff_resa_date = $resa_planning->aff_resa_date;
		$expl->aff_resa_date_fin = $resa_planning->aff_resa_date_fin;
		$expl->resa_cb = $resa_planning->resa_cb;
		
		// récupération des infos sur le réservataire
		$query = "select empr_nom, empr_prenom, empr_cb, id_empr from empr where id_empr=".$resa_planning->resa_idempr." limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			// stockage des infos sur le réservataire
			$empr = pmb_mysql_fetch_object($result);
			$expl->cb_reservataire = $empr->empr_cb;
			$expl->nom_reservataire = $empr->empr_nom;
			$expl->prenom_reservataire = $empr->empr_prenom;
			$expl->id_reservataire = $empr->id_empr;
		}

	}
	return $expl;
}

// teste la situation de l'exemplaire et le cas échéant,
// retourne les infos de pret dans l'objet spécifié
function check_pret($expl) {
	global $msg;
	
	if(!is_object($expl))
		die("serious application error occured in ./circ/retour.inc [check_pret()]. Please contact developpment team");
	$expl->pret_idempr = '';
	$expl->pret_idexpl = '';
	$expl->pret_date = '';
	$expl->pret_retour = '';
	$expl->aff_pret_date = '';
	$expl->aff_pret_retour = '';
	$expl->pret_arc_id = '';
	$expl->niveau_relance = '';
	$expl->date_relance = '';
	$expl->printed = '';
	$expl->cpt_prolongation  = '';
	$expl->short_loan_flag = '';
	$expl->empr_cb = '';
	$expl->id_empr = '';
	$expl->empr_nom = '';
	$expl->empr_prenom = '';
	$expl->id_empr = '';
	$expl->empr_cp = '';
	$expl->empr_ville = '';
	$expl->empr_pays = '';
	$expl->empr_prof = '';
	$expl->empr_year = '';
	$expl->empr_categ = '';
	$expl->empr_codestat = '';
	$expl->empr_sexe = '';
	$expl->empr_statut = '';
	$expl->empr_location = '';
	$expl->type_abt = '';
	$expl->empr_msg = '';
	$expl->groupes = '';
	$expl->pnb_flag = '';
	// récupération des infos du prêt
	$query = "select *, date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, IF(pret_retour>sysdate(),0,1) as retard from pret where pret_idexpl=".$expl->expl_id." limit 1";
	$result = pmb_mysql_query($query);

	if(pmb_mysql_num_rows($result)) {
		$pret = pmb_mysql_fetch_object($result);

		// le document était bien en prêt ->
		// récupération des infos du prêt
		$expl->pret_idempr = $pret->pret_idempr;
		$expl->pret_idexpl = $pret->pret_idexpl;
		$expl->pret_date = $pret->pret_date;
		$expl->pret_retour = $pret->pret_retour;
		$expl->aff_pret_date = $pret->aff_pret_date;
		$expl->aff_pret_retour = $pret->aff_pret_retour;
		$expl->pret_arc_id = $pret->pret_arc_id;
		$expl->niveau_relance = $pret->niveau_relance;
		$expl->date_relance = $pret->date_relance;
		$expl->printed = $pret->printed;
		$expl->cpt_prolongation  = $pret->cpt_prolongation;	
		$expl->short_loan_flag = $pret->short_loan_flag;
		// récupération des infos emprunteur
		$query = "select * from empr where id_empr=".$pret->pret_idempr." limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {

			// stockage des infos sur l'emprunteur
			$empr = pmb_mysql_fetch_object($result);
			$expl->empr_cb = $empr->empr_cb;
			$expl->id_empr = $empr->id_empr;
			$expl->empr_nom = $empr->empr_nom;
			$expl->empr_prenom = $empr->empr_prenom;
			$expl->id_empr = $empr->id_empr;
			$expl->empr_cp = $empr->empr_cp;
			$expl->empr_ville = $empr->empr_ville;
			$expl->empr_pays = $empr->empr_pays;
			$expl->empr_prof = $empr->empr_prof;
			$expl->empr_year = $empr->empr_year;
			$expl->empr_categ = $empr->empr_categ;
			$expl->empr_codestat = $empr->empr_codestat;
			$expl->empr_sexe = $empr->empr_sexe;
			$expl->empr_statut = $empr->empr_statut;
			$expl->empr_location = $empr->empr_location;
			$expl->type_abt = $empr->type_abt;
			$expl->empr_msg = $empr->empr_msg;
			$query_groupe = "select libelle_groupe from groupe, empr_groupe where empr_id='".$pret->pret_idempr."' and groupe_id=id_groupe";
			$result_g = pmb_mysql_query($query_groupe);
			$groupesarray = array();
			while ($groupes=pmb_mysql_fetch_object($result_g)) $groupesarray[]=$groupes->libelle_groupe ;
			$expl->groupes = @implode("/",$groupesarray);
		}
	}
	return $expl;
}

// permet de savoir si un CB expl est déjà en prêt
function verif_cb_utilise_en_pret ($cb) {
	$rqt = "select count(1) from pret, exemplaires where expl_cb='".$cb."' and pret_idexpl=expl_id";
	$res = pmb_mysql_query($rqt) ;
	return pmb_mysql_result($res, 0, 0) ;
}
	
// permet de savoir si un CB expl existe simplement
function verif_cb_expl ($cb) {
	$rqt = "select count(1) from exemplaires where expl_cb='".$cb."' ";
	$res = pmb_mysql_query($rqt) ;
	return pmb_mysql_result($res, 0, 0) ;
}
	
