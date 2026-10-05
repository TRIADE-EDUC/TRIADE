<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: peruser.inc.php,v 1.8 2022/05/04 12:34:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $iduser;
global $es_admin_peruser, $is_not_first, $action;
global $grp_right, $mth_right;

//Initialisation des classes
require_once($class_path."/external_services.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_peruser_ui.class.php");
require_once($include_path."/templates/external_services.tpl.php");

$es=new external_services();
$es_rights=new external_services_rights($es);

//Mise à jour des droits d'un objet en fonction de la demande particulière d'un utilisateur
function update_rights_for_user(&$es_r,$val) {
	global $iduser;
	
	//Selon la valeur de $val : 0=pas de droits, 1=droit normal, 2=anonyme
	switch ($val) {
		case 0:
			if ($es_r->anonymous_user==$iduser) 
				$es_r->anonymous_user=0;
			else if (array_search($iduser,$es_r->users)!==false) {
				//Réécriture du tableau des users
				//Copie
				$tusers=$es_r->users;
				$es_r->users=array();
				for ($j=0; $j<count($tusers); $j++) {
					if ($tusers[$j]!=$iduser) $es_r->users[]=$tusers[$j];
				}
			}
			break;
		case 1:
			if ($es_r->anonymous_user==$iduser) {
				$es_r->anonymous_user=0;
				//Insertion dans le tableau
				$es_r->users[]=$iduser;
			} else if (array_search($iduser,$es_r->users)===false) {
				$es_r->users[]=$iduser;
			}
			break;
		case 2:
			if (array_search($iduser,$es_r->users)!==false) {
				//Si il existe dans les users, on le supprime
				//Réécriture du tableau des users
				//Copie
				$tusers=$es_r->users;
				$es_r->users=array();
				for ($j=0; $j<count($tusers); $j++) {
					if ($tusers[$j]!=$iduser) $es_r->users[]=$tusers[$j];
				}
			}
			$es_r->anonymous_user=$iduser;
			break;
	}
}

//Enregistrement des droits si nécessaire
if ((isset($is_not_first) && $is_not_first) || ($action == "update")) {
	foreach ($es->catalog->groups as $group_name => &$group_content) {
		$val = isset($grp_right[$group_name]) && $grp_right[$group_name];
		$es_r=$es_rights->get_rights($group_name,"");
		update_rights_for_user($es_r,$val);
		//On enregistre les droits pour ce groupe
		$es_rights->set_rights($es_r);
		if ($es_rights->error) print "<script>alert(\"Il y a eu une erreur lors de l'insertion des droits du groupe $group_name : ".$es_rights->error_message."\");</script>";
		
		//On fait la même chose pour les méthodes du groupe !
		foreach ($group_content->methods as $method_name => &$method_content) {
			$val = isset($mth_right[$group_name][$method_name]) && $mth_right[$group_name][$method_name];
			$es_r=$es_rights->get_rights($group_name,$method_name);
			update_rights_for_user($es_r,$val);
			//On enregistre les droits pour ce groupe
			$es_rights->set_rights($es_r);
			if ($es_rights->error) print "<script>alert(\"Il y a eu une erreur lors de l'insertion des droits de la methode ".$method_name." du groupe $group_name : ".$es_rights->error_message."\");</script>";
		}
	}
}

if (empty($iduser)) {
	$iduser=array_key_first($es_rights->users);
}

//Génération de la liste des utilisateurs
$list_users = list_configuration_external_services_peruser_ui::get_instance()->get_users_selector($iduser);

//Génération du tableau des droits
list_configuration_external_services_peruser_ui::set_num_user($iduser);
$table_rights = list_configuration_external_services_peruser_ui::get_instance()->get_display_list();

$js_funcs = <<<JS
	<script type="text/javascript">
	function enable_or_disable_group_checboxes(group_name) {
      var enable_or_disable = document.getElementById("nonavailable_"+group_name).checked;
	  var c = new Array();
	  c = document.getElementsByTagName('input');
	  for (var i = 0; i < c.length; i++)
	  {
	  	var es_group = "";
	  	if (c[i].attributes.getNamedItem('es_group') != null)
	  		es_group = c[i].attributes.getNamedItem('es_group').nodeValue;
	    if ((c[i].type == 'checkbox') && (es_group == group_name)) {
		    if (enable_or_disable) {
		      c[i].checked = false;
		      c[i].disabled = true;
		    }
		    else {
		      c[i].disabled = false;
		      c[i].checked = true;
		    }
	    }
	  }
	}
	</script>

JS;

echo $js_funcs;

$interface_form = new interface_admin_form('es_rights');
$interface_form->set_label("D&eacute;finition des droits pour l'utilisateur ".$list_users);
$interface_form->set_content_form(str_replace("!!table_rights!!",$table_rights,$es_admin_peruser));
print $interface_form->get_display_parameters();
?>