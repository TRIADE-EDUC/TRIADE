<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_liste_lecture.inc.php,v 1.15 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

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
		$liste_lecture->delete_empr_in_list($id_empr_to_deleted);
		print $liste_lecture->get_display_empr();
		break;
	case 'add_empr':
		$liste_lecture = new liste_lecture($id, 'fetch_empr');
		$liste_lecture->add_empr_in_list($id_empr_to_added);
		print $liste_lecture->get_display_empr(); 
		break;
	case 'add_notice':
		$liste_lecture = new liste_lecture($id);
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
}

/**
 * Formulaire de saisie pour l'envoi d'une demande
 */
function show_form($id){
	global $dbh, $msg, $charset; 
	
	$req = "select id_empr from empr where empr_login='".$_SESSION['user_code']."'";
	$res=pmb_mysql_query($req,$dbh);
	$idempr = pmb_mysql_result($res,0,0);
	
	$display .= "<div class='row'>
					<span style='color:red;'><label class='etiquette'>".htmlentities($msg['list_lecture_mail_inscription'],ENT_QUOTES,$charset)."</label></span>
				</div>
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg['list_lecture_demande_inscription'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<blockquote>
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
	global $msg;
	$display .= "
				<div class='row'>
					<label class='etiquette' >".htmlentities($msg['list_lecture_motif_refus'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='row'>
					<blockquote>
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
	global $dbh, $com, $id_empr, $empr_nom, $empr_prenom,$empr_mail, $msg, $opac_url_base, $opac_connexion_phrase;
	
	$requete = "replace into  abo_liste_lecture (num_empr,num_liste,commentaire,etat) values ('".$id_empr."','".$id_liste."','".$com."','1')";
	pmb_mysql_query($requete,$dbh);
	
	//Coordonnées du diffuseur de la liste
	$req = "select empr_login, empr_mail, concat(empr_prenom,' ',empr_nom) as nom, nom_liste from opac_liste_lecture, empr where num_empr=id_empr and id_liste='".$id_liste."'";
	$res = pmb_mysql_query($req,$dbh);
	$diffuseur = pmb_mysql_fetch_object($res);
	
	$objet = sprintf($msg['list_lecture_objet_mail'],$diffuseur->nom_liste);
	$date = time();
	$login = $diffuseur->empr_login;
	$code=md5($opac_connexion_phrase.$login.$date);
	$corps = sprintf($msg['list_lecture_intro_mail'],$diffuseur->nom,$sender->nom_liste).", <br />".sprintf($msg['list_lecture_corps_mail'],$empr_prenom." ".$empr_nom,$diffuseur->nom_liste);
	if($com) $corps .= sprintf("<br />".$msg['list_lecture_corps_com_mail'],$empr_prenom." ".$empr_nom,"<br />".$com);
	$corps .= "<br /><br /><a href='".$opac_url_base."empr.php?code=$code&emprlogin=$login&date_conex=$date&tab=lecture&lvl=demande_list' >".$msg['list_lecture_activation_mail']."</a>";
	
	mailpmb($diffuseur->nom,$diffuseur->empr_mail,$objet,stripslashes($corps),$empr_prenom." ".$empr_nom,$empr_mail);
}

function unicite_nom_liste($nom_liste, $id_liste){
	global $dbh;

	$req = "select id_empr from empr where empr_login='".$_SESSION['user_code']."'";
	$res=pmb_mysql_query($req,$dbh);
	$idempr = pmb_mysql_result($res,0,0);

	if (!$id_liste) {
		$id_liste = 0;
	}
	$req = "select * from opac_liste_lecture where num_empr='".$idempr."' and nom_liste='".addslashes($nom_liste)."' and id_liste<>".$id_liste;
	$res = pmb_mysql_query($req,$dbh);

	print pmb_mysql_num_rows($res);

}
?>