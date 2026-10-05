<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_update.inc.php,v 1.34 2021/03/17 13:32:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $expl_id, $msg, $sub, $f_ex_cb, $org_cb, $f_ex_nbparts, $f_ex_location, $f_ex_section, $id, $id_form, $retour, $current_module;

require_once($class_path."/notice.class.php");
require_once($class_path."/expl.class.php");

$expl_id="";

//Vérification des champs personalisés
$p_perso=new parametres_perso("expl");
$nberrors=$p_perso->check_submited_fields();
if ($nberrors) {
	error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
	exit();
}

switch($sub) {
	case 'create':
		$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$f_ex_cb' ";
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		$nbr_lignes ? $valid_requete = FALSE : $valid_requete = TRUE;
		$libelle = $msg[4007];
		break;
	case 'update':
		// ceci teste si l'exemplaire cible existe bien
		$requete = "SELECT expl_id FROM exemplaires WHERE expl_cb='$org_cb' ";
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_num_rows($res);
		$nbr_lignes ? $valid_requete = TRUE : $valid_requete = FALSE;
		if ($nbr_lignes) $expl_id = pmb_mysql_result($res,0,0);
		 
		// remplacement code-barre : test sur le nouveau numéro
		if($org_cb != $f_ex_cb) {
			$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$f_ex_cb' ";
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			$nbr_lignes ? $valid_requete = FALSE : $valid_requete = TRUE;
		}
		$libelle = $msg[4007];
		break;
}

print pmb_bidi("<div class=\"row\"><h1>$libelle</h1></div>");

if(!is_numeric($f_ex_nbparts) || !$f_ex_nbparts) $f_ex_nbparts=1;

$formlocid="f_ex_section".$f_ex_location ;
$f_ex_section=${$formlocid};

if($valid_requete) {
	switch($sub) {
		case 'create':
			$exemplaire = new exemplaire($f_ex_cb, 0, $id);
			break;
		case 'update':
			$exemplaire = new exemplaire($org_cb, $expl_id, $id);
			break;
	}
	$exemplaire->set_properties_from_form();
	$exemplaire->save();
	// tout va bene, on réaffiche l'ISBD
	print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
	$id_form = md5(microtime());
	$retour = notice::get_permalink($id);
	print "
		<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
		</form>
		<script type=\"text/javascript\">document.dummy.submit();</script>
		";
} else {
	error_message($msg[301], $msg[303], 1, notice::get_permalink($id));
}
?>
