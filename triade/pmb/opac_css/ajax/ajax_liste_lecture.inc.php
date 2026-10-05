<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_liste_lecture.inc.php,v 1.21.2.1 2024/07/10 08:19:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $quoifaire, $id, $id_empr_to_deleted, $id_empr_to_added, $id_notice, $nom_liste, $id_liste;
$id = intval($id);
$id_empr_to_deleted = intval($id_empr_to_deleted);
$id_empr_to_added = intval($id_empr_to_added);
$id_notice = intval($id_notice);
$id_liste = intval($id_liste);

require_once($class_path."/liste_lecture.class.php");
require_once($include_path."/mail.inc.php");

switch($quoifaire){
	case 'show_form':
		show_form($id);
		break;
	case 'send_demande':
		send_demande($id);
		break;
	case 'show_refus_form':
		show_refus_form();
		break;
	case 'delete_empr':
		$liste_lecture = new liste_lecture($id, 'fetch_empr');
		$id_empr_to_deleted = intval($id_empr_to_deleted);
		$liste_lecture->delete_empr_in_list($id_empr_to_deleted);
		print $liste_lecture->get_display_empr();
		break;
	case 'add_empr':
		$liste_lecture = new liste_lecture($id, 'fetch_empr');
		$id_empr_to_added = intval($id_empr_to_added);
		$liste_lecture->add_empr_in_list($id_empr_to_added);
		print $liste_lecture->get_display_empr();
		break;
	case 'add_notice':
		$liste_lecture = new liste_lecture($id);
		$id_notice = intval($id_notice);
		$added = $liste_lecture->add_notice($id_notice);
		if($added) {
			print '1';
		} else {
			print '0';
		}
		break;
	case 'unicite_nom_liste':
		unicite_nom_liste($nom_liste, $id_liste);
		break;
	case 'show_docnum':
		global $docnum_id, $id_liste;
		$liste_lecture = new liste_lecture($id_liste);
		if (!$liste_lecture->show_docnum($docnum_id)) {
			http_response_code(404);
		}
		break;
}

/**
 * Formulaire de saisie pour l'envoi d'une demande
 */
function show_form($id){
	global $msg, $charset;

	$req = "select id_empr from empr where empr_login='".$_SESSION['user_code']."'";
	$res = pmb_mysql_query($req);
	$idempr = pmb_mysql_result($res, 0, 0);
	$display = "<div class='row'>
					<span style='color:red;'><label class='etiquette'>".htmlentities($msg['list_lecture_mail_inscription'],ENT_QUOTES,$charset)."</label></span>
				</div>
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg['list_lecture_demande_inscription'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<blockquote role='presentation'>
						<textarea style='vertical-align:top' id='liste_demande_$id' name='liste_demande_$id' cols='50' rows='5'></textarea>
					</blockquote>
				</div>
				<input type='button' class='bouton' name='send_mail_$id' id='send_mail_$id' value='$msg[list_lecture_send_mail]' />
				<input type='button' class='bouton' name='cancel_$id' id='cancel_$id' value='$msg[list_lecture_cancel_mail]' />
				<input type='hidden' name='id_empr' id='id_empr' value='$idempr' />

				";
	print $display;
}

/*
 * Formulaire de saisie pour un motif de refus
 */
function show_refus_form(){
	global $msg, $charset;
	$display .= "
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg['list_lecture_motif_refus'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<blockquote role='presentation'>
						<textarea style='vertical-align:top' id='com' name='com' cols='50' rows='5'></textarea>
					</blockquote>
				</div>
				<input type='submit' class='bouton' name='refus_dmd_btn' id='refus_dmd_btn' value='$msg[list_lecture_send_refus]' onclick='this.form.lvl.value=\"demande_list\"; this.form.action.value=\"refus_acces\";'/>
				<input type='button' class='bouton' name='cancel' id='cancel' value='$msg[list_lecture_cancel_mail]' />";
	print $display;
}

/**
 * Envoyer un mail de demande d'accès à la liste confidentielle
 */
function send_demande($id_liste){
	global $com, $id_empr;

	$requete = "replace into  abo_liste_lecture (num_empr,num_liste,commentaire,etat) values ('".$id_empr."','".$id_liste."','".$com."','1')";
	pmb_mysql_query($requete);

	$mail_opac_reader_readinglist_request_access = new mail_opac_reader_readinglist_request_access();
	$liste_lecture = new liste_lecture($id_liste);
	$mail_opac_reader_readinglist_request_access->set_mail_to_id($liste_lecture->num_owner);
	$mail_opac_reader_readinglist_request_access->set_id_liste($id_liste);
	return $mail_opac_reader_readinglist_request_access->send_mail();
}

function unicite_nom_liste($nom_liste, $id_liste){
	$id_liste = intval($id_liste);
	$req = "select id_empr from empr where empr_login='".$_SESSION['user_code']."'";
	$res=pmb_mysql_query($req);
	$idempr = pmb_mysql_result($res,0,0);

	if (!$id_liste) {
		$id_liste = 0;
	}
	$req = "select * from opac_liste_lecture where num_empr='".$idempr."' and nom_liste='".addslashes($nom_liste)."' and id_liste<>".$id_liste;
	$res = pmb_mysql_query($req);

	print pmb_mysql_num_rows($res);

}
?>