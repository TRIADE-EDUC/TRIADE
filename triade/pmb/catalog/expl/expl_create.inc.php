<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_create.inc.php,v 1.26 2021/04/23 06:26:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $noex, $option_num_auto, $id, $pmb_droits_explr_localises, $explr_visible_mod;

// gestion des exemplaires
// on checke si l'exemplaire n'existe pas déjà
$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$noex'";
$res = pmb_mysql_query($requete);

if((!pmb_mysql_result($res, 0, 0)) || (!empty($option_num_auto) && ($noex==''))) {
	$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE);
	print pmb_bidi("<div class='row'><b>".$notice->header."</b><br />");
	print pmb_bidi($notice->isbd.'</div>');

	// visibilité des exemplaires
	// On ne vérifie que si l'utlisateur peut créer sur au moins une localisation : 
	if (!$pmb_droits_explr_localises||$explr_visible_mod) {
		$nex = new exemplaire($noex, 0, $id);
		print "<div class='row'>";
		print $nex->expl_form('./catalog.php?categ=expl_update&sub=create', notice::get_permalink($id));
		print "</div>";
	} 
} else {
	error_message($msg[301], $msg[302], 1, "./catalog.php?categ=expl&id=$id");
}
?>