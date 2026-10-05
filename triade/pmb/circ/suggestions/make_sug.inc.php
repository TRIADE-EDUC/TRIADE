<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: make_sug.inc.php,v 1.15 2021/04/23 07:05:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $action, $msg, $id_bibli, $id_sug, $acquisition_sugg_display;

//URL de retour du form de création/modification de suggestion
$back_url = "./circ.php?";

if (!isset($id_sug)) $id_sug = '';

// page de switch création suggestion
require_once($base_path.'/acquisition/suggestions/func_suggestions.inc.php');
require_once($class_path.'/suggestions_map.class.php');


if ($acquisition_sugg_display) {
	require_once($base_path.'/acquisition/suggestions/'.$acquisition_sugg_display);
} else {
	require_once($base_path.'/acquisition/suggestions/suggestions_display.inc.php');
}

$sug_map = new suggestions_map();

//Traitement des actions
switch($action) {
	case 'modif':
		$update_action = "./circ.php?categ=sug&action=update&id_bibli=".$id_bibli."&id_sug=".$id_sug;
		show_form_sug($update_action);
		break;
	case 'update' :
		update_sug();
		print "<script type='text/javascript'>alert('".$msg['acquisition_sugg_ok']."');
			document.location='".$back_url."'</script>";
		break;
}

?>