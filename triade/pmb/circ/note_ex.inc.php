<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: note_ex.inc.php,v 1.11.8.1 2025/05/30 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// ajout message sur un exemplaire
global $base_path, $class_path, $include_path, $action;
global $cb, $message_content, $f_ex_comment, $id;

$id = intval($id);

require_once("$class_path/expl.class.php");
require_once("$include_path/templates/expl.tpl.php");

if(!$action) {
	// si l'action n'est pas définie, afficher le form de saisie message
	$expl = new exemplaire($cb);
	print $expl->get_note_form();
} else {
    // action définie : mettre à jour le message pour l'exemplaire
    exemplaire::set_note_in_database($id, stripslashes($message_content), stripslashes($f_ex_comment));
	$location_url = $base_path."/circ.php?categ=visu_ex&form_cb_expl=".$cb;
	if(headers_sent()) {
	    print "
				<script type='text/javascript'>
					window.location.href='".$location_url."';
				</script>";
	} else {
	    header('Location: '.$location_url);
	}
}
