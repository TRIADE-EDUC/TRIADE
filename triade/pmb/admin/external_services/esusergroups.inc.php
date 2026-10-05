<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: esusergroups.inc.php,v 1.7 2021/02/19 12:44:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg;
global $action, $id, $es_group_pmbuserid;

//Initialisation des classes
require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_esusers.class.php");
require_once($include_path."/templates/external_services.tpl.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_esusergroups_ui.class.php");

function list_groups() {
	print list_configuration_external_services_esusergroups_ui::get_instance()->get_display_list();
}

function show_esgroup_form_anonymous() {
	global $msg, $charset;

	print '<form method="POST" action="admin.php?categ=external_services&sub=esusergroups&action=updateanonymous" name="form_esgroup">';
	print '<h3>'.$msg['es_groups_edit'].'</h3>';
		
	print '<div class="form-contenu">';

	//name
	print '<div class=row><label class="etiquette" for="es_group_name">'.$msg["es_group_name"].'</label><br />';
	print $msg["admin_connecteurs_outauth_anonymgroupname"];
	print '</div>';

	//fullname
	print '<div class=row><label class="etiquette" for="es_group_fullname">'.$msg["es_group_fullname"].'</label><br />';
	print $msg["admin_connecteurs_outauth_anonymgroupfullname"];
	print '</div>';

	$pmbusers_sql = "SELECT userid, username, nom, prenom FROM users";
	$pmbusers_res = pmb_mysql_query($pmbusers_sql);
	$pmbusers = array();
	while($pmbusers_row = pmb_mysql_fetch_assoc($pmbusers_res)) {
		$pmbusers[] = $pmbusers_row;
	}
	
	$sql = "SELECT esgroup_pmbusernum FROM es_esgroups WHERE esgroup_id = -1";
	$res = pmb_mysql_query($sql);
	if (!pmb_mysql_num_rows($res))
		 $esg_pmbuserid = 1;
	else
		$esg_pmbuserid = pmb_mysql_result($res, 0, 0);
	
	//pmbuser
	print '<div class=row><label class="etiquette" for="es_group_pmbuserid">'.$msg["es_group_pmbuserid"].'</label><br />';
	print '<select name="es_group_pmbuserid">';
	foreach ($pmbusers as $apmbuser) {
		print '<option '.($apmbuser["userid"] == $esg_pmbuserid ? ' selected ' : '').' value="'.$apmbuser["userid"].'">'.htmlentities($apmbuser["username"].' ('.$apmbuser["nom"].' '.$apmbuser['prenom'].')' ,ENT_QUOTES, $charset).'</option>';
	}
	print '</select></div>';

		
	//buttons
	print "<br /><div class='row'>
	<div class='left'>";
	print "<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=external_services&sub=esusergroups'\" />&nbsp";
	print '<input class="bouton" type="submit" value="'.$msg[77].'">';
	print "</div>
	<br /><br /></div>";
	
	print '</form>';
	
}

function update_esgroup_from_form() {
	global $msg,$id;
	global $es_group_name, $es_group_pmbuserid;

	if (!$id) {
		//Ajout d'un nouveau groupe

		if (!$es_group_name) {
			print $msg['es_group_error_emptyfield'];
			$the_user = new es_esgroup();
			$the_user->set_properties_from_form();
			print $the_user->get_form();
			return false;
		}
		if (es_esgroup::name_exists($es_group_name)) {
			print $msg['es_group_error_namealreadyexists'];
			$the_user = new es_esgroup();
			$the_user->set_properties_from_form();
			print $the_user->get_form();
			return false;
		}
		$new_esgroup = es_esgroup::add_new();
		$new_esgroup->set_properties_from_form();
		$new_esgroup->commit_to_db(); 
	}
	else {
		$the_group = new es_esgroup($id);
			if ($the_group->error) {
				return false;
		}
		$the_group->set_properties_from_form();
		$the_group->commit_to_db(); 
	}
	return true;
}

switch ($action) {
	case "add":
		$the_group = new es_esgroup();
		print $the_group->get_form();
		break;
	case "edit":
		$the_group = new es_esgroup($id);
		print $the_group->get_form();
		break;
	case "editanonymous":
		show_esgroup_form_anonymous();
		break;
	case "update":
		if (update_esgroup_from_form())
			list_groups();
		break;
	case 'updateanonymous':
		$es_group_pmbuserid = intval($es_group_pmbuserid);
		if ($es_group_pmbuserid) {
			$sql = "REPLACE INTO es_esgroups SET esgroup_id = -1, esgroup_name = '".$msg["admin_connecteurs_outauth_anonymgroupname"]."', esgroup_fullname = '".$msg["admin_connecteurs_outauth_anonymgroupfullname"]."', esgroup_pmbusernum = ".$es_group_pmbuserid;
			pmb_mysql_query($sql);
		}
		list_groups();
		break;
	case "del":
		if ($id) {
			$the_group = new es_esgroup($id);
			$the_group->delete();
		}
		list_groups();
		break;
	default:
		list_groups();
		break;
}

?>