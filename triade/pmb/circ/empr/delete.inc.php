<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delete.inc.php,v 1.16 2022/01/07 14:23:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id, $form_cb, $groupID;

require_once("$class_path/bannette.class.php");
require_once("$class_path/emprunteur.class.php");

// suppression d'un lecteur
$erreur=0;
$id = intval($id);
if ($id) {
	$total = 0;
	$total = pmb_mysql_result(pmb_mysql_query("select count(1) from pret where pret_idempr='".$id."' "), 0, 0);
	if ($total==0) {
		emprunteur::del_empr($id);
	} else {
		error_message(	$msg[294], $msg[1709], 1, 'circ.php?categ=pret&form_cb='.rawurlencode($form_cb));
		$erreur=1;
	}
}

if (!$erreur) {
	if ($groupID) print "<script type=\"text/javascript\">
			document.location ='./circ.php?categ=groups&action=showgroup&groupID=$groupID';
            	</script>";
	else get_cb($msg[13], $msg[34], $msg['circ_tit_form_cb_empr'], './circ.php?categ=pret', 0);
}


