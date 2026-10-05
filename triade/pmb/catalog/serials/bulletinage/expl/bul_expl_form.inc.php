<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_expl_form.inc.php,v 1.64 2023/07/26 15:07:59 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $action;
global $bul_id, $expl_id, $noex, $option_num_auto, $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $serial_header;

require_once($class_path."/expl.class.php");
require_once($include_path."/templates/expl.tpl.php");

if (!$expl_id) {
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4007], $serial_header); // pas d'id, c'est une création
} else {
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4008], $serial_header);
}

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
	$q = "select count(1) from bulletins $acces_j  where bulletin_id=".$bul_id;
	$r = pmb_mysql_query($q);
	if(pmb_mysql_result($r,0,0)==0) {
		$acces_m=0;
	}
}

if ($acces_m==0) {

	if (!$expl_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_expl_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {

	// affichage des infos du bulletinage pour rappel
	$bulletinage = new bulletinage_display($bul_id);
	print pmb_bidi("<div class='row'><h2>".$bulletinage->display.'</h2></div>');
	
	if ($expl_id) {
		// c'est une modif
		$requete = "SELECT * FROM exemplaires WHERE expl_id=".$expl_id." AND expl_notice=0 LIMIT 1";
		$myQuery = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($myQuery)) {
			// visibilité des exemplaires
			// $nex->explr_acces_autorise contient INVIS, MODIF ou UNMOD
			$exemplaire = new exemplaire('', $expl_id, 0, $bul_id);
			if ($exemplaire->explr_acces_autorise!="INVIS") {
				switch($action) {
					case 'dupl_expl':
						$exemplaire->cb = '';
						$exemplaire->expl_id = 0;
						print $exemplaire->expl_form("./catalog.php?categ=serials&sub=bulletinage&action=expl_update&expl_id=".$exemplaire->expl_id);
						break;
					case 'expl_form':
						print $exemplaire->expl_form("./catalog.php?categ=serials&sub=bulletinage&action=expl_update&expl_id=".$exemplaire->expl_id);
						break;
				}
			} else {
				print "<div class='row'><div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
				print "<div class='colonne-suite'><span class='erreur'>".$msg["err_mod_expl"]."</span>&nbsp;&nbsp;&nbsp;";
				print "<input type='button' class='bouton' value=\"{$msg['bt_retour']}\" name='retour' onClick='history.back(-1);'></div></div>";
			}
		} else {
			print "impossible d'accéder à cet exemplaire.";
		}
	} else {
		//création d'un exemplaire
		//avant toute chose, on regarde si ce cb n'existe pas déjà
		$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='".$noex."' ";
		$myQuery = pmb_mysql_query($requete);
		if(!pmb_mysql_result($myQuery, 0, 0) || (!empty($option_num_auto) && ($noex==''))) {
			$exemplaire = new exemplaire($noex, $expl_id, 0, $bul_id);
			print $exemplaire->expl_form("./catalog.php?categ=serials&sub=bulletinage&action=expl_update&expl_id=".$exemplaire->expl_id);
		} else {
			print "<div class=\"row\"><div class=\"msg-perio\" size=\"+2\">".$msg["expl_message_code_utilise"]."</div></div>";
			print "<div class=\"row\"><a href=\"".bulletinage::get_permalink($bulletinage->bul_id)."\">Retour</a></div>";
		}
	}
}
?>