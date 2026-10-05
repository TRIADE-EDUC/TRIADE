<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: isbd.inc.php,v 1.85 2023/12/26 08:09:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $class_path, $include_path, $msg, $charset;
global $valid_id_avis, $avis_quoifaire;
global $gestion_acces_active, $gestion_acces_user_notice, $z3950_accessible;
global $pmb_allow_external_search, $pmb_url_base, $PMBuserid;
global $acquisition_active, $pmb_scan_request_activate;

require_once ($include_path."/avis_notice.inc.php");
require_once ($include_path."/h2o/pmb_h2o.inc.php");
require_once ($class_path."/records_tabs.class.php");
require_once ($class_path."/nomenclature_records_tabs.class.php");
require_once ($class_path."/audit.class.php");
require_once ($class_path."/explnum.class.php");
require_once ($class_path."/event/events/event_display_overload.class.php");
require_once ($class_path."/caddie/caddie_controller.class.php");
require_once ($class_path."/notice.class.php");

$id = intval($id);
if($id) {
	$query = "SELECT bulletin_id FROM bulletins WHERE num_notice=".$id;
	$result = pmb_mysql_query($query);
	// si c'est une notice de bulletin on bascule sur categ=serials ...
	if(($bull = pmb_mysql_fetch_object($result))) {
		print "
		<script type=\"text/javascript\">
			document.location = '".bulletinage::get_permalink($bull->bulletin_id)."'
		</script>";
		exit;
	}
}

//droits d'acces utilisateur/notice (lecture)
$acces_l=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_l = $dom_1->getRights($PMBuserid,$id,4);	//lecture
}

if ($acces_l==0) {

	error_message('', htmlentities($dom_1->getComment('view_noti_error'), ENT_QUOTES, $charset), 1, '');

} else {
	$sort_children = 1;
	
	// définition de quelques variables
	$libelle = $msg[270];
	
	notice::init_globals_patterns_links();
	
	$expl_link = exemplaire::get_pattern_link();
	$link_explnum = explnum::get_pattern_link(); 
	$isbd = new mono_display($id, 6, '', 1, $expl_link, '', $link_explnum,1,0,1,0,'',0,false,true,0,1);
	
	$cart_click_isbd = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=$id', 'cart')\"";
	$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$id.");\" onMouseOut=\"set_flag_info_div(false);\"";
	$cart_click_isbd = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"".htmlentities($msg['400'], ENT_QUOTES, $charset)."\" $cart_click_isbd $cart_over_out>" ;
	
	if ($current!==false) {
	    $print_action = "&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&notice_id=".$id."&action_print=print_prepare','print'); w.focus(); return false;\"><img src='".get_url_icon('print.gif')."' border='0' class='center' alt=\"".htmlentities($msg["histo_print"], ENT_QUOTES, $charset)."\" title=\"".htmlentities($msg["histo_print"], ENT_QUOTES, $charset)."\"/></a>";
	}
	$visualise_click_notice="
		<script type=\"text/javascript\" src='./javascript/select.js'></script>
		
		<a href='#' onClick='show_frame(\"$pmb_url_base"."opac_css/notice_view.php?id=$id\")'><img src='".get_url_icon('search.gif')."' class='align_middle' title=\"".htmlentities($msg['noti_see_gestion'], ENT_QUOTES, $charset)."\" name='imEx'  border='0' /></a>";   
	    
	print pmb_bidi("<h1 class='section-title'>".$msg['record_see_title']."</h1>");
	
	// header
	print pmb_bidi("
	<div class='row notice-perio'>
			<h3 class='section-record-title' style='display: inline;'>".$isbd->aff_statut.$cart_click_isbd.$print_action.$visualise_click_notice.$isbd->header."</h3>");
	
	$boutons  = "<div class='row'><div class='left'><input type='button' name='modifier' class='bouton' value='".htmlentities($msg['62'], ENT_QUOTES, $charset)."' onClick=\"document.location='./catalog.php?categ=modif&id=$id';\" />&nbsp;";
	$boutons .= "<input type='button' class='bouton' value='".htmlentities($msg['158'], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=remplace&id=".$id."\"' />&nbsp;";
	if ($z3950_accessible) {
	    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg['notice_z3950_update_bouton'], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=z3950&id_notice=".$id."&isbn=".$isbd->isbn."\"' />&nbsp;";
	}
	if ($pmb_allow_external_search) {
	    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg['notice_replace_external'], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=search&mode=7&external_type=simple&notice_id=".$id."&from_mode=0&code=".$isbd->isbn."\"' />&nbsp;";
	}
	if ($pmb_allow_external_search) {
	    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg["harvest_notice_replace"], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=harvest&notice_id=".$id."\"' />&nbsp;";
	}
    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg['notice_duplicate_bouton'], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=duplicate&id=".$id."\"' />&nbsp;";
    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg['notice_child_bouton'], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=create_form&id=0&notice_parent=".$id."\"' />&nbsp;";

	$boutons.= form_mapper::get_action_button('notice', $id);

	if($acquisition_active) {
	    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg["acquisition_sug_do"], ENT_QUOTES, $charset)."' onclick='document.location=\"./catalog.php?categ=sug&action=modif&id_bibli=0&id_notice=".$id."\"' />";
	}
	
	if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
	    $boutons .= "<input type='button' class='bouton' value='".htmlentities($msg["scan_request_record_button"], ENT_QUOTES, $charset)."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=".$id."\"' />";
	}
	$evth = events_handler::get_instance();
	$evt = new event_display_overload('notice', 'show_isbd_action');
	$evt->set_entity_id($id);
	$evth->send($evt);
	$evt_result = $evt->get_overloads();
	if(isset($evt_result)){
	    foreach($evt_result as $overload){
	        $boutons.= $overload;
	    }
	}
	
	if ($pmb_type_audit) { 
		$boutons .= audit::get_dialog_button($id, 1);
	}
	
	$boutons .="</div>";
	
	global $at_least_one_has_expl;
	
	$requete_compte_expl_id="select 1 from exemplaires where expl_notice='".$id."'";
	$resultat_compte_expl_id=pmb_mysql_query($requete_compte_expl_id);
	
    $hasSignedDocnum = DocnumCertifier::hasSignedDocnumFromNoticeId($id);
	if(!$hasSignedDocnum) {
    	if (!pmb_mysql_num_rows($resultat_compte_expl_id)) {
    		$message=$msg["confirm_suppr_notice"];
    		if ($isbd->nb_expl!=0) $at_least_one_has_expl++;
    		if ($at_least_one_has_expl) $message=$msg["del_expl_noti_child"];
    		$boutons .= "<div class='right'><script type=\"text/javascript\">
    						function confirm_delete() {
    							result = confirm(\"$message\");
    		       			if(result)
    		           			document.location = './catalog.php?categ=delete&id=".$id."'
    						}
    					</script>
    					<input type='button' class='bouton' value=\"".$msg['supprimer']."\" onClick=\"confirm_delete();\" />
    				</div>";
    		
    	} 
	}
	$boutons .="</div>";

	if($boutons) $isbd->isbd = str_replace('<!-- !!bouton_modif!! -->', $boutons, $isbd->isbd);
	else $isbd->isbd = str_replace('<!-- !!bouton_modif!! -->', "", $isbd->isbd);

	$isbd->isbd = str_replace('<!-- !!avis_notice!! -->', avis_notice($id,$avis_quoifaire,$valid_id_avis), $isbd->isbd);
	
	$isbd->isbd = str_replace('<!-- !!caddies_notice!! -->', caddie_controller::get_display_list_from_item('display', 'NOTI', $id), $isbd->isbd);
	
	// Titre de la page
	if ($isbd->tit1) {
		print '<script type="text/javascript">document.title = "'.strip_tags(addslashes(pmb_bidi($isbd->tit1))).'"</script>';
	}
	
	// isbd + exemplaires existants
	print pmb_bidi("
		$isbd->isbd
		</div>");
	// form de création d'exemplaire
	if ((!$explr_visible_mod)&&($pmb_droits_explr_localises)) {
		$etiquette_expl="";
		$btn_ajouter_expl="";	
		$saisie_num_expl="<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
		$saisie_num_expl.= "<div class='colonne-suite'><span class='erreur'>".$msg["err_add_invis_expl"]."</span></div>";
	} else {
		global $pmb_numero_exemplaire_auto,$pmb_numero_exemplaire_auto_script;
		$num_exemplaire_auto="";
		//if($pmb_numero_exemplaire_auto>0) $num_exemplaire_auto=" $msg[option_num_auto] <INPUT type=checkbox name='option_num_auto' value='num_auto' checked >";
		if($pmb_numero_exemplaire_auto==1 || $pmb_numero_exemplaire_auto==2){
			$num_exemplaire_auto=" $msg[option_num_auto] <INPUT type=checkbox name='option_num_auto' value='num_auto' ";
			$checked=true;
			if ($pmb_numero_exemplaire_auto_script) {
				if (file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
					require_once($include_path."/$pmb_numero_exemplaire_auto_script");
					if (function_exists('is_checked_by_default')) {
						$checked=is_checked_by_default($id,0);
					}
				}
			}
			if ($checked) {
				$num_exemplaire_auto.=" checked='checked'";
			}
			$num_exemplaire_auto.=" >";
		}		
		$etiquette_expl="<label class='etiquette' for='form_cb'>$msg[291]</label>";
		$btn_ajouter_expl="<input type='submit' class='bouton' value=' $msg[expl_ajouter] ' onClick=\"return test_form(this.form)\">";
		$saisie_num_expl="<input type='text' class='saisie-20em' name='noex' value=''>".$num_exemplaire_auto;
	}
	$expl_new = str_replace ('!!etiquette!!',$etiquette_expl,$expl_new);
	$expl_new = str_replace ('!!saisie_num_expl!!',$saisie_num_expl,$expl_new);
	
	$evth = events_handler::get_instance();
	$evt = new event_display_overload('expl', 'expl_add_button');
	$evt->set_entity_id($id);
	$evth->send($evt);
	$evt_result = $evt->get_overloads();
	if(isset($evt_result)){
	    foreach($evt_result as $overload){
	        $btn_ajouter_expl.= $overload;
	    }
	}
	
	
	$expl_new = str_replace ('!!btn_ajouter!!',$btn_ajouter_expl,$expl_new);
	$expl_new = str_replace('!!id!!', $id, $expl_new);
	if(explnum::get_default_upload_directory()){
	    $expl_new.= explnum::get_drop_zone($id, 'record');
	}
	if($pmb_enable_explnum_edition_popup){
	    $expl_new.= $explnum_popup_edition_script;
	}
	
	print "<div class=\"row\">";
	print $expl_new;
	print "</div>";
	if($categ == 'update'){ //Modification de la globale categ pour ne pas retomber dans le cas update au changement de page affichage onglet
		$categ = 'isbd';
	}
	$template_path_records_tabs =  "./includes/templates/records/records_elements_tabs.html";
	if(file_exists("./includes/templates/records/records_elements_tabs_subst.html")){
		$template_path_records_tabs =  "./includes/templates/records/records_elements_tabs_subst.html";
	}
	if(file_exists($template_path_records_tabs)){
		$h2o_record_tabs = H2o_collection::get_instance($template_path_records_tabs);
		$record = new notice($isbd->notice_id);
		if ($pmb_nomenclature_activate && $pmb_nomenclature_music_concept_blank && $record->get_nomenclature_record_formations() && count($record->get_nomenclature_record_formations()->get_record_formations())) {
			$records_tabs = new nomenclature_records_tabs($record);
		} else {
			$records_tabs = new records_tabs($record);
		}
		$records_list_ui = $records_tabs->get_record()->get_records_list_ui();
		if ($records_list_ui) $records_list_ui->set_current_url($pmb_url_base.'catalog.php?categ='.$categ.'&id='.$isbd->notice_id.'&quoi='.$quoi);
		print $h2o_record_tabs->render(array('records_tabs' => $records_tabs));
	}
	
}
