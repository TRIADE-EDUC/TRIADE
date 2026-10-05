<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: make_sug.inc.php,v 1.15 2021/04/22 11:40:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $action, $msg, $id_bibli, $id_sug, $acquisition_sugg_display;

if(!isset($id_notice)) $id_notice = 0;
if(!isset($id_sug)) $id_sug = 0;

//URL de retour du form de création/modification de suggestion
$back_url="onClick=\"document.location='./catalog.php?'\"";

if($id_notice && ($action=='modif')) $back_url= " onClick=\"history.go(-1);\" ";
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
		$update_action = "./catalog.php?categ=sug&action=update&id_bibli=".$id_bibli."&id_sug=".$id_sug."&id_notice=$id_notice";
		show_form_sug($update_action);
		break;
	case 'update' :
		update_sug();
		$back_url = "document.location='./catalog.php?'";
		print "<script type='text/javascript'>alert('".$msg['acquisition_sugg_ok']."');$back_url</script>";
		break;
}