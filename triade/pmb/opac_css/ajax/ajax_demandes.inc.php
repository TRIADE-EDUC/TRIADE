<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_demandes.inc.php,v 1.17.4.1 2025/04/16 08:15:53 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $quoifaire;
global $id_demande, $id_action;

require_once($base_path."/classes/demandes.class.php");
require_once($base_path."/classes/demandes_actions.class.php");
require_once($base_path."/classes/demandes_notes.class.php");
require_once($base_path."/includes/templates/demandes.tpl.php");
require_once($base_path."/includes/templates/demandes_actions.tpl.php");
require_once($base_path."/includes/templates/demandes_notes.tpl.php");
require_once($base_path."/includes/mail.inc.php");
require_once($class_path.'/audit.class.php');

switch($quoifaire){
	case 'show_list_action':
		$demande=new demandes($id_demande,false);
		ajax_http_send_response(demandes_actions::show_list_actions($demande->actions,$id_demande,0,false));
		break;
	case 'show_dialog':
		$action=new demandes_actions($id_action,false);
		ajax_http_send_response(demandes_notes::show_dialog($action->notes, $action->id_action,$action->num_demande,"demandes-show_consult_form"));
		break;
	case "get_pperso_form":
		global $type_demande;
		$type_demande = intval($type_demande);
		$demande = new demandes();
		ajax_http_send_response($demande->get_pperso_form_content($id_demande, $type_demande));
		break;
}

/*
 * Affiche le formulaire d'ajout d'un action
 */
function show_form($id,$type){
	global $msg; 
	
	if($type == 'ask'){
		$title = $msg['demandes_question_form'];
		$btn = $msg['demandes_save_question'];
	} elseif($type == 'info'){
		$title = $msg['demandes_info_form'];
		$btn = $msg['demandes_save_info'];		
	} elseif($type == 'rdv'){
		$title = $msg['demandes_rdv_form'];
		$btn = $msg['demandes_save_rdv'];
		$date = date('Ymd',time());
		$div_date= "
		<div class='row' >
			<label class='etiquette' >".$msg['demandes_action_date_rdv']."</label>
		</div>
		<div class='row'>
			<blockquote role='presentation'>
				<input type='date' name='date_rdv' id='date_rdv' value='".$date."' />
			</blockquote>
		</div>";	
	}
	$display .= "
		<div class='row'>
			<h3>".$title."</h3>
		</div>";
	if($div_date) $display .= $div_date;
	$display .="
		<div class='row'>
			<label class='etiquette' >".$msg['demandes_action_sujet']."</label>
		</div>
		<div class='row'>
			<blockquote role='presentation'>
			<input type='text' name='sujet' id='sujet' />
			</blockquote>
		</div>
		<div class='row'>
			<label class='etiquette' >".$msg['demandes_action_detail']."</label>
		</div>
		<div class='row'>
			<blockquote role='presentation'>
				<textarea style='vertical-align:top' id='detail' name='detail' cols='50' rows='5'></textarea>
			</blockquote>
		</div>				
		<input type='button' class='bouton' name='ask' id='ask' value='".$btn."' />
		<input type='button' class='bouton' name='cancel' id='cancel' value='".$msg['demandes_cancel']."' />
		";

	ajax_http_send_response($display);
}

/*
 * Enregistrement de la nouvelle action question/réponse
 */
function save_ask($id,$type){
	global $sujet, $detail, $date_rdv,$id_empr, $pmb_type_audit;
	
	$date = date("Y-m-d",time());
	if($type=='ask'){
		$req = "insert into demandes_actions set 
			sujet_action = '".$sujet."',
			detail_action = '".$detail."',
			date_action = '".$date."',
			deadline_action = '".$date."',
			type_action=1,
			statut_action=1,
			num_demande = '".$id."',
			actions_num_user ='".$id_empr."',
			actions_type_user=1,
			actions_read=1,
			actions_read_gestion=1	
		";
	} elseif($type=='info'){
		$req = "insert into demandes_actions set 
			sujet_action = '".$sujet."',
			detail_action = '".$detail."',
			date_action = '".$date."',
			deadline_action = '".$date."',
			type_action=3,
			statut_action=1,
			num_demande = '".$id."',
			actions_num_user ='".$id_empr."',
			actions_type_user=1,
			actions_read=1,
			actions_read_gestion=1	
		";
	} elseif($type=='rdv'){
		$req = "insert into demandes_actions set 
			sujet_action = '".$sujet."',
			detail_action = '".$detail."',
			date_action = '".$date_rdv."',
			deadline_action = '".$date_rdv."',
			type_action=4,
			statut_action=2,
			num_demande = '".$id."',
			actions_num_user ='".$id_empr."',
			actions_type_user=1	,
			actions_read=1,
			actions_read_gestion=1		
		";
	}
	pmb_mysql_query($req);
	$idaction = pmb_mysql_insert_id();
	if($pmb_type_audit) audit::insert_creation(AUDIT_ACTION,$idaction);
	
	$dmde_act = new demandes_action($id,$idaction);
	$display = $dmde_act->getContenuForm();

	$update_dmde = "update demandes set dmde_read_gestion='1' where id_demande=".$id;
	pmb_mysql_query($update_dmde);
	
	ajax_http_send_response($display);
	
}

/*
 * Ajouter une note à une action
 */
function add_note(){
	global $msg, $id_action;
	
	$req = "select type_action from demandes_actions where id_action='".$id_action."'";
	$res = pmb_mysql_query($req);
	$action = pmb_mysql_fetch_object($res);
	if($action->type_action == '1'){
		$titre = $msg['demandes_notes_question_form'];
	} else $titre = $msg['demandes_notes_form'];
	$display .= "
		<div class='row'>
			<h3>".$titre."</h3>
		</div>
		<div class='row'>
			<label class='etiquette' >".$msg['demandes_notes_contenu']."</label>
		</div>
		<div class='row'>
			<blockquote role='presentation'>
				<textarea style='vertical-align:top' id='contenu' name='contenu' cols='50' rows='5'></textarea>
			</blockquote>
		</div>
		<input type='button' class='bouton' name='save_note' id='save_note' value='".$msg['demandes_notes_save']."' />
		<input type='button' class='bouton' name='cancel' id='cancel' value='".$msg['demandes_cancel']."' />";
		
		
	ajax_http_send_response($display);
}

/*
 * Enregistrer la note
 */
function save_note($idaction, $idnote=0, $id_demande=0){
	
	global $contenu, $id_empr;
	global $demandes_email_demandes, $pmb_type_audit;
	
	$date = date("Y-m-d",time());
	$req = " insert into demandes_notes 
		set contenu='".$contenu."',
		date_note='".$date."',";
	if($idnote) $req .= "num_note_parent='".$idnote."',";
	$req .= " num_action='".$idaction."',";
	$req .= " notes_num_user='".$id_empr."', notes_type_user=1, ";	
	$req .= " notes_read_gestion=1";
	pmb_mysql_query($req);
	
	$req_up = "update demandes_actions set actions_read=1 where id_action='".$idaction."'";
	pmb_mysql_query($req_up);
	
	$dmde_act = new demandes_action($id_demande,$idaction);
	$display = $dmde_act->getContenuForm();
	
	if ($demandes_email_demandes) $dmde_act->send_alert_by_mail($id_empr,$idnote);
	if($pmb_type_audit && $idnote) {
		audit::insert_modif(AUDIT_NOTE,$idnote);
	} elseif ($pmb_type_audit && !$idnote){
		$idnote = pmb_mysql_insert_id();
		audit::insert_creation(AUDIT_NOTE,$idnote);
	}
	
	// création d'une nouvelle note => alerte sur l'action + la demande
	$req_up1 = "update demandes_actions set actions_read_gestion='1' where id_action='".$idaction."';";
	$req_up2= "update demandes inner join demandes_actions on demandes_actions.num_demande = demandes.id_demande set demandes.dmde_read_gestion='1' where demandes_actions.id_action='".$idaction."'";
	
	pmb_mysql_query($req_up1);
	pmb_mysql_query($req_up2);
	
	ajax_http_send_response($display);
}
?>