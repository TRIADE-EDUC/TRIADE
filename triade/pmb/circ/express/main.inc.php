<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8.12.1 2025/05/30 12:37:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $id_empr, $msg, $database_window_title, $layout_begin, $express_content_form;

// inclusions principales
require_once("$include_path/templates/express.tpl.php");

$id_empr = intval($id_empr);
if (!$id_empr) {
	// pas d'id empr, quelque chose ne va pas
	error_message($msg[350], $msg[54], 1 , './circ.php');
} else {
	// récupération nom emprunteur
	$query = "SELECT empr_nom, empr_prenom, empr_cb FROM empr WHERE id_empr=$id_empr";
	$result = pmb_mysql_query($query);
	if(!pmb_mysql_num_rows($result)) {
		// pas d'emprunteur correspondant, quelque chose ne va pas
		error_message($msg[350], $msg[54], 1 , './circ.php');
	} else {
		$empr = pmb_mysql_fetch_object($result);
		$name = $empr->empr_prenom;
		if ($name) {
		    $name .= ' '.$empr->empr_nom;
		} else {
		    $name = $empr->empr_nom;
		}
		echo window_title($database_window_title.$name.$msg['pret_express_wtit']);

        $layout_begin = preg_replace('/!!nom_lecteur!!/m', $name, $layout_begin);
        $layout_begin = preg_replace('/!!cb_lecteur!!/m', $empr->empr_cb, $layout_begin);
		print $layout_begin;
		
		$interface_circ_express_form = new interface_circ_express_form('pret_express_form');
		$interface_circ_express_form->set_label($msg['pret_express_cap'])
		->set_content_form($express_content_form)
		->set_field_focus('pe_titre');
		print $interface_circ_express_form->get_display();
	}
}
