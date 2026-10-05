<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_saisie.inc.php,v 1.16 2022/01/07 14:23:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $database_window_title, $id, $form_cb, $groupID;

// update d'un emprunteur
echo window_title($database_window_title.$msg[42]);

if (!isset($form_cb)) $form_cb = '';

//Hook externe
if (!$id) {
	if (is_file($base_path."/circ/empr/empr_saisie_ext_check.inc.php")) {
		require_once($base_path."/circ/empr/empr_saisie_ext_check.inc.php");
		$cb=ext_check_empr(stripslashes($form_cb));
		if ($cb) $form_cb=$cb;
	}
}

// si $id n'est pas fourni, c'est une création
if(!$id) {
	// regarder si le code-barre existe déjà
	$requete = "SELECT empr_cb FROM empr WHERE empr_cb='$form_cb' LIMIT 1 ";
	$res = pmb_mysql_query($requete);
	$nbr_lignes = pmb_mysql_num_rows($res);

	if(!$nbr_lignes) {
		show_empr_form("./circ.php?categ=empr_update","./circ.php?categ=empr_create", 0, $form_cb);
	} else {
		// numéro déjà utilisé
		error_message($msg[44], $msg[45], 1, './circ.php?categ=empr_create');
	}
} else {
	// si $id est fourni, c'est une modification
	show_empr_form("./circ.php?categ=empr_update&groupID=$groupID","./circ.php?categ=pret&groupID=$groupID", $id, $form_cb);
}
print "
	<script type='text/javascript'>
		if((typeof ajax_parse_dom == 'function')) ajax_parse_dom();
	</script>";
