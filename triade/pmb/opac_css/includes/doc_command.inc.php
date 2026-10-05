<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: doc_command.inc.php,v 1.10 2023/11/21 14:50:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $msg, $charset;
global $pmb_url_base, $id_empr, $id;
global $opac_rgaa_active;
global $nom, $telephone, $mail, $nbre, $commentaire;
global $opac_photo_email_form;
global $opac_biblio_name, $opac_biblio_email;

require_once("$include_path/mail.inc.php");

$form_mailing="
<script>
	function check_form() {
		var message='';
		f=document.mailing;
		if (f.nom.value=='') message='".addslashes($msg["command_phototeque_need_name"])."';
		if ((f.mail.value=='')&&(f.telephone.value=='')&&(!message)) {
			message='".addslashes($msg["command_phototeque_need_phone"])."';
		}
		if (message) {
			alert(message);
			return false;
		}
		return true;
	}
</script>
<form name='mailing' action='index.php?lvl=doc_command&id=$id&mode_phototeque=1' method='post'>";

if ($opac_rgaa_active) {
    $form_mailing .= "<h1>".htmlentities($msg["command_phototeque_command"],ENT_QUOTES,$charset)."</h1>";
    $form_mailing .= "<h2>".htmlentities($msg["command_phototeque_coord"],ENT_QUOTES,$charset)."</h2>";
} else {
    $form_mailing .= "<h3>".htmlentities($msg["command_phototeque_coord"],ENT_QUOTES,$charset)."</h3>";
}

//<h3>".htmlentities($msg["command_phototeque_coord"],ENT_QUOTES,$charset)."</h3>
$form_mailing .="<table style='width:100%' role='presentation'>
		<tr>
			<td>".htmlentities($msg["command_phototeque_name"],ENT_QUOTES,$charset)."</td><td><input type='text' name='nom' id='nom' size='40' value='!!nom!!'></td>
		</tr>
		<tr>
			<td>".htmlentities($msg["command_phototeque_phone"],ENT_QUOTES,$charset)."</td><td><input type='text' name='telephone' size='16' value='!!telephone!!'></td>
		</tr>
		<tr>
			<td>".htmlentities($msg["command_phototeque_email"],ENT_QUOTES,$charset)."</td><td><input type='text' name='mail' size='30' value='!!mail!!'></td>
		</tr>
		<tr>
			<td>".htmlentities($msg["command_phototeque_nbre"],ENT_QUOTES,$charset)."</td><td><input type='text' name='nbre' size='3'></td>
		</tr>
		<tr>
			<td>".htmlentities($msg["command_phototeque_comment"],ENT_QUOTES,$charset)."</td><td><textarea cols='40' rows='5' wrap='virtual'></textarea></td>
		</tr>
	</table>
	<table style='width:100%'>
		<tr>
			<td class='center' style='width:50%;height:50px'><input type='submit' class='bouton' value='".htmlentities($msg["command_phototeque_send_demand"],ENT_QUOTES,$charset)."' onClick='return check_form();'/></td><td class='center'><input type='button' class='bouton' value='".htmlentities($msg["command_phototeque_cancel"],ENT_QUOTES,$charset)."' onClick='document.location=\"index.php?lvl=notice_display&id=$id&mode_phototeque=1\"'/></td>
		</tr>
	</table>
</form>";
$id_empr = intval($id_empr);
if ($nom) {
	$corps="";
	if ($id_empr) {
		$requete="select empr_cb,empr_nom,empr_prenom,empr_login from empr where id_empr=".$id_empr;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$corps.="<a href='".$pmb_url_base."circ.php?categ=pret&id_empr=$id_empr'>".htmlentities(sprintf($msg["command_phototeque_send_by"],$r->empr_cb." ".$r->empr_nom." ".$r->empr_prenom),ENT_QUOTES,$charset)."</a><br />";
		}
	}
	$corps.="<a href='".$pmb_url_base."catalog.php?categ=isbd&id=$id'>".htmlentities(sprintf($msg["command_phototeque_doc_id"],$id),ENT_QUOTES,$charset)."</a><br />";
	$corps.=htmlentities($msg["command_phototeque_name"]." : ".$nom,ENT_QUOTES,$charset)."<br />";
	$corps.=htmlentities($msg["command_phototeque_phone"]." : ".$telephone,ENT_QUOTES,$charset)."<br />";
	$corps.=htmlentities($msg["command_phototeque_email"]." : ".$mail,ENT_QUOTES,$charset)."<br />";
	$corps.=htmlentities($msg["command_phototeque_nbre"]." : ".$nbre,ENT_QUOTES,$charset)."<br />";
	$corps.=htmlentities($msg["command_phototeque_comment"]." : ".$commentaire,ENT_QUOTES,$charset)."<br />";
	//Envoi du mail
	$mails=explode(" ",$opac_photo_email_form);
	$headers  = "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
	if (count($mails)) $ret=1; else $ret=0;
	for ($i=0; $i<count($mails); $i++) {
		$ret*=@mailpmb($mail, $mail, $msg["command_phototeque_command"], $corps, $opac_biblio_name, $opac_biblio_email , $headers);		
	}
	print "<script>
	
    alert('".($ret?htmlentities($msg["command_phototeque_command_transmited"],ENT_QUOTES,$charset):htmlentities($msg["command_phototeque_command_no_transmited"],ENT_QUOTES,$charset))."');
	document.location='index.php?lvl=notice_display&id=$id&mode_phototeque=1';
</script>";
} else {
	$flag=false;
	if ($id_empr) {
		$requete="select empr_login,concat(empr_prenom,' ',empr_nom) as nom, empr_tel1,empr_mail from empr where id_empr=$id_empr";
		$resultat=pmb_mysql_query($requete); 
		if (pmb_mysql_num_rows($resultat)) {
			$flag=true;
			$r=pmb_mysql_fetch_object($resultat);
			$form_mailing=str_replace("!!nom!!",htmlentities($r->nom,ENT_QUOTES,$charset),$form_mailing);
			$form_mailing=str_replace("!!telephone!!",htmlentities($r->empr_tel1,ENT_QUOTES,$charset),$form_mailing);
			$form_mailing=str_replace("!!mail!!",htmlentities($r->empr_mail,ENT_QUOTES,$charset),$form_mailing);
		}
	}
	if (!$flag) {
		$form_mailing=str_replace("!!nom!!","",$form_mailing);
		$form_mailing=str_replace("!!telephone!!","",$form_mailing);
		$form_mailing=str_replace("!!mail!!","",$form_mailing);
	}
	print $form_mailing;
}
