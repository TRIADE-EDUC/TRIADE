<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: out.inc.php,v 1.13.8.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $msg;
global $action, $connector_id, $id, $source_id;

require_once $class_path."/connecteurs_out.class.php" ;

function list_connectors_out() {
	print list_configuration_connecteurs_out_ui::get_instance()->get_display_list();
}

function show_connector_out_form($connector_id) {
	global $msg;

	$content_form = '';
	$daconn = instantiate_connecteur_out($connector_id);
	if ($daconn) {
		$content_form .= $daconn->get_config_form();
	}

	$interface_form = new interface_admin_form('form_connectorout');
	$interface_form->set_label($msg['connector_out_edit']);
	$interface_form->set_object_id($connector_id)
					->set_content_form($content_form);
	print $interface_form->get_display_parameters();

}

function show_sourceout_form($source_id=0, $connector_id=0, $name="", $comment="", $config_form=NULL) {

	global $msg;
	print '<form method="POST" action="admin.php?categ=connecteurs&sub=out&action=source_update" name="form_connectorout" enctype="multipart/form-data">';
	if ($source_id)
		print '<h3>'.$msg['connector_out_sourceedit'].'</h3>';
	else
		print '<h3>'.$msg['connector_out_sourceadd'].'</h3>';

	print '<div class="form-contenu">';

	//id
	print '<input type="hidden" name="id" value="'.$source_id.'" />';
	print '<input type="hidden" name="connector_id" value="'.$connector_id.'" />';

	if ($config_form) {
		print '<br />';
		print call_user_func($config_form);
		print '<br />';
	}

	//buttons
	print "<div class='row'>";
	print '<div class="left">';
	print "<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=connecteurs&sub=out'\" />&nbsp;";
	print '<input class="bouton" type="submit" value="'.$msg[77].'" />';
	print "</div><div class='right'>";
	if ($source_id) {
		print confirmation_delete("./admin.php?categ=connecteurs&sub=out&action=source_del&id=");
		print "<input class='bouton' type='button' value=' ".$msg['supprimer']." ' onClick=\"javascript:confirmation_delete('".$source_id."','".addslashes($name)."')\" />";
	}
	print "</div></div><div class='row'></div></div>";
	print '</form>';
}

/*$conn = new connecteur_out(0, "dummy");
highlight_string(print_r($conn, true));
echo $conn->ckeck_api_requirements();*/

/*$conns = new connecteurs_out();
highlight_string(print_r($conns, true));*/

switch ($action)  {
	case "update":
		$daconn = instantiate_connecteur_out($id);
		if ($daconn) {
			$daconn->update_config_from_form();
			$daconn->commit_to_db();
		}
		list_connectors_out();
		break;
	case "edit":
		show_connector_out_form($id);
		break;
	case "source_add":
		if (!$connector_id) {
			list_connectors_out();
			break;
		}
		$daconn = instantiate_connecteur_out($connector_id);
		if (!$daconn) {
			list_connectors_out();
			break;
		}
		$source_object = $daconn->instantiate_source_class(0);
		show_sourceout_form($id, $connector_id, "", "", array($source_object, 'get_config_form'));
		break;
	case "source_del":
		if (!$id) {
			list_connectors_out();
			break;
		}
		connecteur_out_source::staticDelete($id);
		list_connectors_out();
		break;
	case "source_edit":
		if (!$connector_id || !$source_id) {
			list_connectors_out();
			break;
		}
		$daconn = instantiate_connecteur_out($connector_id);
		if (!$daconn) {
			list_connectors_out();
			break;
		}
		$source_object = $daconn->instantiate_source_class($source_id);
		show_sourceout_form($source_object->id, $connector_id, $source_object->name, $source_object->comment, array($source_object, 'get_config_form'));

		break;
	case "source_update":
		if (!$connector_id) {
			list_connectors_out();
			break;
		}
		if (!$id) {
			//Création d'une nouvelle source
				//Récupération d'un nouvel id d'une nouvelle source générique vide
			$new_source = connecteur_out_source::add_new($connector_id);
			$new_source_id = $new_source->id;

			//Instantiation de cette nouvelle source en tant que source du connecteur
			$daconn = instantiate_connecteur_out($connector_id);
			if (!$daconn) {
				list_connectors_out();
				break;
			}
			$source_object = $daconn->instantiate_source_class($new_source_id);

			//Mise à jour
			$source_object->update_config_from_form();
			$source_object->commit_to_db();
		}
		else {
			//Modification d'une source existante
			if (!$connector_id || !$id) {
				list_connectors_out();
				break;
			}
			$daconn = instantiate_connecteur_out($connector_id);
			if (!$daconn) {
				list_connectors_out();
				break;
			}
			$source_object = $daconn->instantiate_source_class($id);
			$source_object->update_config_from_form();
			$source_object->commit_to_db();
		}

		list_connectors_out();
		break;
	default:
		list_connectors_out();
		break;
}
