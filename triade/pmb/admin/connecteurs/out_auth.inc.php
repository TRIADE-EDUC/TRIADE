<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: out_auth.inc.php,v 1.10.4.1 2025/03/12 13:49:00 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $id, $authorized_sources;

require_once($class_path."/external_services_esusers.class.php");

function list_esgroups() {
	print list_configuration_connecteurs_out_auth_ui::get_instance()->get_display_list();
}

function get_current_sources_auth($group_id=-1) {
    $current_sources=array();
    $current_sql = "SELECT connectors_out_source_esgroup_sourcenum FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = ".$group_id;
    $current_res = pmb_mysql_query($current_sql);
    while($row = pmb_mysql_fetch_assoc($current_res)) {
        $current_sources[] = $row["connectors_out_source_esgroup_sourcenum"];
    }
    return $current_sources;
}

function get_sources_auth($group_id=-1) {
    $sources = array();
    $data_sql = "SELECT connectors_out_sources_connectornum, connectors_out_source_id, connectors_out_source_name, EXISTS(SELECT 1 FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_sourcenum = connectors_out_source_id AND connectors_out_source_esgroup_esgroupnum = ".$group_id.") AS authorized FROM connectors_out_sources ORDER BY connectors_out_sources_connectornum";
    $data_res = pmb_mysql_query($data_sql);
    while($asource=pmb_mysql_fetch_assoc($data_res)) {
        $sources[] = array( 
            'connectornum' => $asource["connectors_out_sources_connectornum"],
            'id' => $asource["connectors_out_source_id"],
            'name' => $asource["connectors_out_source_name"]
        );
    }
    return $sources;
}

function show_auth_edit_sources($group_id=-1) {
    $display = '';
    $current_sources=get_current_sources_auth($group_id);
    $sources=get_sources_auth($group_id);
    foreach ($sources as $source) {
        $display .= '<div class="row" style="padding:5px 5px">';
        $display .= '<input '.(in_array($source["id"], $current_sources) ? 'checked' : '').' type="checkbox" id="authorized_sources_'.$source["id"].'" name="authorized_sources[]" value="'.$source["id"].'">';
        $display .= '<label for="authorized_sources_'.$source["id"].'" style="all:unset">'.$source["name"].'</label>';
        $display .= '</div>';
    }
    return $display;
}

function show_auth_edit_content_form($group_id, $the_group) {
	$interface_content_form = new interface_content_form();
	
	//Nom du groupe
	$interface_content_form->add_element('esgroup_name', 'admin_connecteurs_outauth_groupname')
	->add_html_node('<div id="esgroup_name" class="row" style="padding:5px 5px">'.$the_group->esgroup_name.'</div>');
	//Nom complet du groupe
	$interface_content_form->add_element('esgroup_fullname', 'admin_connecteurs_outauth_groupfullname')
	->add_html_node('<div id="esgroup_fullname" class="row" style="padding:5px 5px">'.$the_group->esgroup_fullname.'</div>');
	
	
	$interface_content_form->add_element('authorized_sources', 'admin_connecteurs_outauth_usesource')
	->add_html_node(show_auth_edit_sources($group_id));
	return $interface_content_form->get_display();
}

function show_auth_edit_form($group_id) {
	global $msg;
	
	$the_group = new es_esgroup($group_id);
	if ($the_group->error) {
		exit();
	}
	$content_form = show_auth_edit_content_form($group_id, $the_group);
	$interface_form = new interface_admin_form('form_outauth');
	$interface_form->set_label($msg["admin_connecteurs_outauth_edit"]);
	$interface_form->set_object_id($group_id)
	->set_content_form($content_form);
	print $interface_form->get_display_parameters();
}

function show_auth_edit_content_form_anonymous() {
    global $msg;
    
    $interface_content_form = new interface_content_form();
    
    //Nom du groupe
    $interface_content_form->add_element('esgroup_name', 'admin_connecteurs_outauth_groupname')
    ->add_html_node('<div id="esgroup_name" class="row" style="padding:5px 5px">&lt;'.$msg["admin_connecteurs_outauth_anonymgroupname"].'&gt;</div>');
    
    //Nom complet du groupe
    $interface_content_form->add_element('esgroup_fullname', 'admin_connecteurs_outauth_groupfullname')
    ->add_html_node('<div id="esgroup_fullname" class="row" style="padding:5px 5px">'.$msg["admin_connecteurs_outauth_anonymgroupfullname"].'</div>');
    
    //Autorisations à utiliser les sources des connecteurs sortants
    $interface_content_form->add_element('usesource', 'admin_connecteurs_outauth_usesource')
    ->add_html_node(show_auth_edit_sources());
    return $interface_content_form->get_display();
}

function show_auth_edit_form_anonymous() {
	global $msg;
	
	$content_form = show_auth_edit_content_form_anonymous();
	$interface_form = new interface_admin_connecteurs_out_auth_form('form_outauth_anonymous');
	$interface_form->set_label($msg["admin_connecteurs_outauth_edit"]);
	$interface_form->set_object_id(-1)
	->set_content_form($content_form);
	print $interface_form->get_display_parameters();
}

switch ($action) {
	case "edit":
		if (!isset($id) || !$id) {
			list_esgroups();
			exit();
		}
		show_auth_edit_form((int)$id);
		break;
	case "editanonymous":
		show_auth_edit_form_anonymous();
		break;
	case "update":
		if (isset($id) && $id) {
		    array_walk($authorized_sources, function(&$a) {$a = intval($a);}); //Virons de la liste ce qui n'est pas entier
			//Croisons ce que l'on nous propose avec ce qui existe vraiment dans la base
			//Vérifions que les sources existents
			$sql = "SELECT connectors_out_source_id FROM connectors_out_sources WHERE connectors_out_source_id IN (".implode(",", $authorized_sources).')';
			$res = pmb_mysql_query($sql);
			$final_authorized_sources = array();
			while ($row=pmb_mysql_fetch_assoc($res))
				$final_authorized_sources[] = $row["connectors_out_source_id"];

			//Vérifions que le groupe existe
			$esgroup = new es_esgroup($id);
			if ($esgroup->error) {
				exit();
			}
			
			//On vire ce qui existe déjà:
			$sql = "DELETE FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = ".$id;
			pmb_mysql_query($sql);
			
			//Tout est bon? On insert
			$values = array();
			$insert_sql = "INSERT INTO connectors_out_sources_esgroups (connectors_out_source_esgroup_sourcenum, connectors_out_source_esgroup_esgroupnum) VALUES ";
			foreach ($final_authorized_sources as $an_authorized_source) {
				$values[] = '('.$an_authorized_source.','.$id.')';
			}
			$insert_sql .= implode(",", $values);
			pmb_mysql_query($insert_sql);
		}
		list_esgroups();
		break;
	case "updateanonymous":
		if (!$authorized_sources)
			$final_authorized_sources=array();
		else {
		    array_walk($authorized_sources, function(&$a) {$a = intval($a);}); //Virons de la liste ce qui n'est pas entier
			//Croisons ce que l'on nous propose avec ce qui existe vraiment dans la base
			//Vérifions que les sources existents
			$sql = "SELECT connectors_out_source_id FROM connectors_out_sources WHERE connectors_out_source_id IN (".implode(",", $authorized_sources).')';
			$res = pmb_mysql_query($sql);
			$final_authorized_sources = array();
			while ($row=pmb_mysql_fetch_assoc($res))
				$final_authorized_sources[] = $row["connectors_out_source_id"];
			
		}

		//On vire ce qui existe déjà:
		$sql = "DELETE FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = -1";
		pmb_mysql_query($sql);
		
		//Tout est bon? On insert
		$values = array();
		$insert_sql = "INSERT INTO connectors_out_sources_esgroups (connectors_out_source_esgroup_sourcenum, connectors_out_source_esgroup_esgroupnum) VALUES ";
		foreach ($final_authorized_sources as $an_authorized_source) {
			$values[] = '('.$an_authorized_source.', -1)';
		}
		if(!empty($values)) {
			$insert_sql .= implode(",", $values);
			pmb_mysql_query($insert_sql);
		}
		list_esgroups();
		break;
	default:
		list_esgroups();
		break;
}


?>