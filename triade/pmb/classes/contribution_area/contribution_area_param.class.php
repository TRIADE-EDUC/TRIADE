<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_param.class.php,v 1.8 2021/04/14 14:34:17 gneveu Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($include_path . '/templates/contribution_area/contribution_area_param.tpl.php');

/**
 * class contribution_area
 * Représente un espace de contribution
 */
class contribution_area_param {
	
	public function __construct() {
	} // end of member function __construct
	


	public function get_form() {
		global $contribution_area_param_form;
		global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password, $pmb_contribution_opac_show_sub_form, $pmb_contribution_opac_accordion_result, $pmb_contribution_opac_auto_save_draft, $pmb_contribution_opac_edit_entity;
		global $charset, $msg;
		
		$quick_param_link = '';
		if (!$pmb_contribution_ws_url) {
			$quick_param_link = '<a href="./modelling.php?categ=contribution_area&sub=param&action=quick_param" title="'.htmlentities($msg['admin_contribution_area_quick_param'], ENT_QUOTES, $charset).'">'.htmlentities($msg['admin_contribution_area_quick_param'], ENT_QUOTES, $charset).'</a>';
		}
		$contribution_area_param_form = str_replace('!!quick_param_link!!', $quick_param_link, $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!user_name!!', ($pmb_contribution_ws_username ? $pmb_contribution_ws_username : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!user_password!!', ($pmb_contribution_ws_password ? $pmb_contribution_ws_password : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!source_url!!', ($pmb_contribution_ws_url ? $pmb_contribution_ws_url : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!show_sub_form!!', ($pmb_contribution_opac_show_sub_form ? "checked='checked'" : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!accordion_result!!', ($pmb_contribution_opac_accordion_result ? "checked='checked'" : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!contribution_edit_entity!!', ($pmb_contribution_opac_edit_entity ? "checked='checked'" : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!auto_save_draft!!', ($pmb_contribution_opac_auto_save_draft ? "checked='checked'" : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!contribution_docnum_directory_options!!', $this->get_contribution_docnum_directory(), $contribution_area_param_form);
		return $contribution_area_param_form;
	}

	public function save_from_form(){
	    global $source_url, $user_name, $user_password, $show_sub_form, $accordion_result, $auto_save_draft, $contribution_edit_entity, $contribution_docnum_directory;
	    global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password, $pmb_contribution_opac_show_sub_form;
		
		$query = "UPDATE parametres SET valeur_param = '".addslashes($user_name)."' WHERE sstype_param = 'contribution_ws_username'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($user_password)."' WHERE sstype_param = 'contribution_ws_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($source_url)."' WHERE sstype_param = 'contribution_ws_url'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($show_sub_form)."' WHERE sstype_param = 'contribution_opac_show_sub_form'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($accordion_result)."' WHERE sstype_param = 'contribution_opac_accordion_result'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($auto_save_draft)."' WHERE sstype_param = 'contribution_opac_auto_save_draft'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($contribution_edit_entity)."' WHERE sstype_param = 'contribution_opac_edit_entity'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($contribution_docnum_directory)."' WHERE sstype_param = 'contribution_opac_docnum_directory'";
		pmb_mysql_query($query);
		
		$pmb_contribution_ws_url = $source_url;
		$pmb_contribution_ws_username = $user_name;
		$pmb_contribution_ws_password = $user_password;
		$pmb_contribution_opac_show_sub_form = $show_sub_form;
	}
	
	public function set_quick_param($pmb_user_num = 1) {
		global $pmb_url_base;
		
		$user_name = 'contribution_user';
		
		// Connecteur externe
		// connectors_out_sources_connectornum = 5 : connecteur json/rpc
		$config = array(
				'exported_functions' => array(
						array(
								'group' => 'pmbesContributions',
								'name' => 'integrate_entity'
						)
				)
		);
		pmb_mysql_query("INSERT INTO connectors_out_sources (connectors_out_sources_connectornum, connectors_out_source_name, connectors_out_source_config) 
				VALUES (5, 'Contributions', '".addslashes(serialize($config))."')");
		$external_source_id = pmb_mysql_insert_id();
		$external_source_url = $pmb_url_base.'ws/connector_out.php?source_id='.$external_source_id.'&database='.LOCATION;
		
		// Groupe externe
		pmb_mysql_query("INSERT INTO es_esgroups (esgroup_name, esgroup_fullname, esgroup_pmbusernum)
				VALUES ('contribution_group', 'Contribution Group', ".($pmb_user_num*1).")");
		$external_group_id = pmb_mysql_insert_id();
		
		// Utilisateur externe
		$user_password = crypt(microtime(true), 'contribution');
		pmb_mysql_query("INSERT INTO es_esusers (esuser_username, esuser_password, esuser_fullname, esuser_groupnum)
				VALUES ('".addslashes($user_name)."', '".addslashes($user_password)."', 'Contribution User', ".$external_group_id.")");
		
		// Autorisations utilisateur / groupes
		$method_id = 0;
		$query = "SELECT id_method FROM es_methods WHERE groupe = 'pmbesContributions' AND method = 'integrate_entity'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$method_id = pmb_mysql_result($result, 0, 0);
		}
		if (!$method_id) {
			pmb_mysql_query("INSERT INTO es_methods (groupe, method, available)
					VALUES ('pmbesContributions', 'integrate_entity', 1)");
			$method_id = pmb_mysql_insert_id();
		}
		
		if (!pmb_mysql_num_rows(pmb_mysql_query('SELECT * FROM es_methods_users WHERE num_method = '.$method_id.' AND num_user = '.$pmb_user_num))) {
			pmb_mysql_query("INSERT INTO es_methods_users (num_method, num_user, anonymous)
					VALUES (".$method_id.", ".$pmb_user_num.", 0)");
		}
		
		pmb_mysql_query("INSERT INTO connectors_out_sources_esgroups (connectors_out_source_esgroup_sourcenum, connectors_out_source_esgroup_esgroupnum)
				VALUES (".$external_source_id.", ".$external_group_id.")");
		
		// Mise à jour des paramètres
		pmb_mysql_query("UPDATE parametres SET valeur_param = '".addslashes($user_name)."' WHERE sstype_param = 'contribution_ws_username'");
		pmb_mysql_query("UPDATE parametres SET valeur_param = '".addslashes($user_password)."' WHERE sstype_param = 'contribution_ws_password'");
		pmb_mysql_query("UPDATE parametres SET valeur_param = '".addslashes($external_source_url)."' WHERE sstype_param = 'contribution_ws_url'");
	}
	
	public function get_quick_param_form() {
		global $charset;
		global $contribution_area_quick_param_form;
		
		$html = $contribution_area_quick_param_form;
		
		$query = "SELECT userid, username, nom, prenom FROM users";
		$result = pmb_mysql_query($query);
		$user_id_options = '';
		while($row = pmb_mysql_fetch_assoc($result)) {
			$user_id_options.= '<option value="'.$row["userid"].'">'.htmlentities($row["username"].' ('.$row["nom"].' '.$row['prenom'].')', ENT_QUOTES, $charset).'</option>';
		}
		$html = str_replace('!!user_id_options!!', $user_id_options, $html);
		
		return $html;
	}
	
	public function get_contribution_docnum_directory() {
	    global $charset, $msg;
	    global $pmb_contribution_opac_docnum_directory;
	    
	    $query = "SELECT repertoire_id, repertoire_nom FROM upload_repertoire";
	    $result = pmb_mysql_query($query);
	    $repertoire_id_options = '';
        if (empty($pmb_contribution_opac_docnum_directory)) {
            $repertoire_id_options .= '<option value="0" selected>'.$msg['contribution_docnum_directory_select'].'</option>';
        }
	    while($row = pmb_mysql_fetch_assoc($result)) {
	        $repertoire_id_options .= '<option value="' . $row["repertoire_id"] . '"' . ($row["repertoire_id"] == $pmb_contribution_opac_docnum_directory ? "selected='selected'" : "") .'">'.htmlentities($row["repertoire_nom"], ENT_QUOTES, $charset).'</option>';
	    }
	    return $repertoire_id_options;
	}
} // end of contribution_area
