<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: out_sets.inc.php,v 1.7 2021/02/25 08:29:22 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id;

require_once($class_path."/connecteurs_out_sets.class.php");
require_once ($class_path."/search.class.php");
require_once($class_path."/configuration/configuration_connecteurs_controller.class.php");

function list_out_sets() {
	print list_configuration_connecteurs_out_sets_ui::get_instance()->get_display_list();
}

function show_set_form($id=0, $set_type=0, $set_caption='') {
	$connector_out_set = new connector_out_set($id);
	$connector_out_set->type = $set_type;
	$connector_out_set->caption = $set_caption;
	print $connector_out_set->get_form();	
}

function update_set_from_form() {
	global $msg,$id;
	global $set_type, $set_caption;
	if (!$id) {
		//Ajout d'un nouveau set
		if (!$set_caption) {
			print $msg['admin_connecteurs_set_emptyfield'];
			show_set_form(0, stripslashes($set_type), stripslashes($set_caption));
			return false;
		}
		if (connector_out_set::caption_exists($set_caption)) {
			print $msg['admin_connecteurs_set_namealreadyexists'];
			show_set_form(0, stripslashes($set_type), stripslashes($set_caption));
			return false;
		}
		$new_set = connector_out_set::add_new();
		$new_set->set_properties_from_form();
		$new_set->commit_to_db(); 
	}
	else {
		$theset = new_connector_out_set_typed($id);
			if ($theset->error) {
				return false;
		}
		$theset->set_properties_from_form();
		$theset->update_config_from_form();
		$theset->cache->update_from_form();
		$theset->commit_to_db();
		$theset->cache->commit_to_db();
	}
	return true;
}

function show_import_noticesearch_into_multicritere_set_form() {
	global $charset, $toset_search, $msg, $candidate_id;
	
	$candidate_id = intval($candidate_id);

	$serialized_search = stripslashes($toset_search);

	//Un petit tour dans la classe search histoire de filtrer la recherche
	$sc = new search(false);
	$sc->unserialize_search($serialized_search);
	$serialized_search = $sc->serialize_search();
	
	$human_query = $sc->make_human_query();

	print '<form method="POST" action="admin.php?categ=connecteurs&sub=out_sets&action=import_notice_search_into_set_do" name="form_outset">';
	print '<h3>'.$msg['search_notice_to_connector_out_set_formtitle'].'</h3>';
		
	print '<div class="form-contenu">';
	
	//la recherche
	print '<input type="hidden" name="toset_search" value="'.htmlentities($serialized_search ,ENT_QUOTES, $charset).'">';
	
	//caption
	print '<div class=row><label class="etiquette" for="set_caption">'.$msg["search_notice_to_connector_out_set_search"].':</label><br />';
	print $human_query;
	print '</div><br />';

	//set d'acceuil
	$set_list = '<select name="set_id">';
	$sets = new connector_out_sets();
	foreach ($sets->sets as &$aset) {
		if ($aset->type != 2)
			continue;
		$set_list .= '<option '.($candidate_id == $aset->id ? 'selected' : '').' value="'.$aset->id.'">'.htmlentities($aset->caption ,ENT_QUOTES, $charset).'</option>';
	}
	$set_list .= '</select>';
	
	//caption
	print '<div class=row><label class="etiquette" for="set_caption">'.$msg["search_notice_to_connector_out_set_set"].':</label><br />';
	print $set_list;
	print '</div><br />';
	
	//buttons
	print "</div><div class='row'>
	<div class='left'>";
	print "<input class='bouton' type='button' value=' $msg[76] ' onClick=\"history.go(-1)\" />&nbsp";
	print '<input class="bouton" type="submit" value="'.$msg[77].'">';
	print "</div>
	<div class='right'>";
	print "</div>
	<br /><br /></div>";
	
	print '</form>';
}

function import_noticesearch_into_multicritere_set() {
	global $toset_search, $set_id;
	
	$set_id=intval($set_id);
	//Pas de set spécifié?
	if (!$set_id)
		return;
		
	//Vérifions que le set spécifié est bien un bon set multicritère
	$the_set = new connector_out_set($set_id, true);
	if ($the_set->type != 2)
		return;
	
	$serialized_search = stripslashes($toset_search);

	//Un petit tour dans la classe search histoire de filtrer la recherche
	$sc = new search(false);
	$sc->unserialize_search($serialized_search);
	$serialized_search = $sc->serialize_search();
	
	//Mettons à jour le set
	$the_set_m = new connector_out_set_noticemulticritere($set_id, true);
	$the_set_m->config["search"] = $serialized_search;
	$the_set_m->commit_to_db();
	$the_set_m->clear_cache(true);
}

switch ($action) {
	case "update":
		if (update_set_from_form())
			list_out_sets();
		break;
	case "manual_update":
		$theset = new_connector_out_set_typed($id);
			if ($theset->error) {
				return false;
		}
		$theset->update_cache();
		list_out_sets();
		break;
	case "import_notice_search_into_set":
		show_import_noticesearch_into_multicritere_set_form();
		break;
	case "import_notice_search_into_set_do":
		import_noticesearch_into_multicritere_set();
		list_out_sets();
		break;
	default:
		if($id) {
			$model_class_name = class_connector_out_set_typed($id);
		} else {
			$model_class_name = 'connector_out_set';
		}
		configuration_connecteurs_controller::set_model_class_name($model_class_name);
		configuration_connecteurs_controller::set_list_ui_class_name('list_configuration_connecteurs_out_sets_ui');
		configuration_connecteurs_controller::proceed($id);
		break;
}

?>