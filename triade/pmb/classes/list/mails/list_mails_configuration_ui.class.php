<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_mails_configuration_ui.class.php,v 1.23.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/mails/mail_configuration.class.php');

class list_mails_configuration_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT DISTINCT mail_address FROM (
                SELECT distinct name_mail_configuration AS mail_address FROM mails_configuration WHERE name_mail_configuration LIKE "%@%" 
				UNION
				SELECT distinct user_email AS mail_address FROM users WHERE user_email LIKE "%@%" 
				UNION
				SELECT distinct email AS mail_address FROM docs_location WHERE email LIKE "%@%" 
				UNION
				SELECT SUBSTRING(valeur_param, 1, IF(POSITION(";" IN valeur_param), POSITION(";" IN valeur_param)-1, LENGTH(valeur_param))) AS mail_address FROM parametres WHERE type_param="pmb" AND sstype_param = "mail_adresse_from" AND valeur_param LIKE "%@%"
				UNION
				SELECT SUBSTRING(valeur_param, 1, IF(POSITION(";" IN valeur_param), POSITION(";" IN valeur_param)-1, LENGTH(valeur_param))) AS mail_address FROM parametres WHERE type_param="opac" AND sstype_param = "mail_adresse_from" AND valeur_param LIKE "%@%" 
				UNION
				SELECT valeur_param AS mail_address FROM parametres WHERE type_param = "opac" AND sstype_param = "biblio_email" AND valeur_param LIKE "%@%"
				UNION
				SELECT distinct email AS mail_address FROM coordonnees JOIN entites ON entites.id_entite = coordonnees.num_entite WHERE type_entite = 1 and email LIKE "%@%"
				) AS mails LEFT JOIN mails_configuration ON mails_configuration.name_mail_configuration = mails.mail_address AND mails_configuration.mail_configuration_type = "address"';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new mail_configuration($row->mail_address);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'domains' => 'domains',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'domains' => array(),
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'domain' => 'mail_configuration_domain',
					'name' => 'mail_configuration_address',
					'protocol' => 'mail_configuration_protocol',
					'hote' => 'mail_configuration_hote',
					'port' => 'mail_configuration_port',
					'authentification' => 'mail_configuration_authentification',
					'user' => 'mail_configuration_user',
					'secure_protocol' => 'mail_configuration_secure_protocol',
					'authentification_type' => 'mail_configuration_authentification_type',
					'validated' => 'mail_configuration_validated',
					'uses' => 'mail_configuration_uses',
					'actions' => 'mail_configuration_actions'
			)
		);
	}
	
	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
		$this->available_editable_columns = array(
				'hote',
				'port',
		);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'domain_name');
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('domains');
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name', 'asc');
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'name':
	            return $sort_by.'_mail_configuration';
	        case 'protocol' :
	        case 'hote' :
	        case 'port' :
	        case 'authentification' :
	        case 'user' :
	        case 'secure_protocol' :
	        case 'authentification_type' :
	        case 'validated' :
	            return 'mail_configuration_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function _add_query_filters() {
		$this->query_filters [] = 'mail_address <> ""';
		if(!empty($this->filters['domains'])) {
			foreach ($this->filters['domains'] as $domain) {
				$this->query_filters [] = 'name_mail_configuration LIKE "%'.addslashes($domain).'"';
			}
		}
	}
	
	public function set_filters_from_form() {
		$this->set_filter_from_form('domains');
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('name');
		$this->add_column('protocol');
		$this->add_column('hote');
		$this->add_column('port');
		$this->add_column('secure_protocol');
		$this->add_column('authentification');
		$this->add_column('user');
		$this->add_column('authentification_type');
		$this->add_column('validated');
		$this->add_column('uses');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('uses', 'align', 'left');
		$this->set_setting_column('authentification', 'datatype', 'boolean');
		$this->set_setting_column('validated', 'datatype', 'boolean');
		$this->set_setting_selection_actions('edit', 'visible', false);
	}
	
	protected function get_search_filter_domains() {
		global $msg;
		$objects = list_mails_configuration_domains_ui::get_instance()->get_objects();
		$options = array();
		foreach ($objects as $object) {
			$options[$object->get_name()] = $object->get_name();
		}
		return $this->get_search_filter_multiple_selection('', 'domains', $msg["all"], $options);
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'protocol', 'hote', 'port', 'secure_protocol',
				'authentification', 'user', 'authentification_type',
				'validated', 'uses', 'actions'
		);
	}
	
	protected function _get_object_property_secure_protocol($object) {
		if(!empty($object->get_secure_protocol())) {
			return mail_configuration::SMTP_SECURE_PROTOCOLS[$object->get_secure_protocol()];
		}
	}
	
	protected function get_cell_content_use($label, $link) {
		global $charset;
		
		return "<li><a href='".$link."' target='_blank'>".htmlentities($label, ENT_QUOTES, $charset)."</a></li>";
		
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset, $base_path;
		
		$content = '';
		switch($property) {
			case 'protocol':
			case 'hote' :
			case 'port':
			case 'secure_protocol':
			    if(($object->get_type() == 'domain' && $object->is_in_database()) || ($object->get_type() == 'address' && $object->is_super_admin()) || ($object->get_type() == 'address' && $object->get_domain()->is_allowed_hote_override())) {
					$content .= parent::get_cell_content($object, $property);
				}
				break;
			case 'authentification' :
				if(($object->get_type() == 'domain' && !$object->get_domain()->is_allowed_authentification_override()) || ($object->get_type() == 'address' && $object->get_domain()->is_allowed_authentification_override())) {
					$content .= parent::get_cell_content($object, $property);
				}
				break;
			case 'user':
				if($object->get_type() == 'domain' || ($object->get_type() == 'address' && $object->get_domain()->is_allowed_authentification_override())) {
					$content .= parent::get_cell_content($object, $property);
				}
				break;
			case 'authentification_type':
				if(($object->get_type() == 'domain' && !$object->get_domain()->is_allowed_authentification_override()) || ($object->get_type() == 'address' && $object->get_domain()->is_allowed_authentification_override())) {
					$content .= parent::get_cell_content($object, $property);
				}
				break;
			case 'validated':
				if(($object->get_type() == 'domain' && !$object->get_authentification())
					|| ($object->get_type() == 'domain' && $object->get_authentification() && !$object->is_allowed_authentification_override())
					|| ($object->get_type() == 'address' && $object->get_domain()->is_allowed_authentification_override())) {
					if($object->is_validated()) {
						$content .= "<img src='".get_url_icon('tick.gif')."' title='".htmlentities($msg["mail_configuration_validated"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["mail_configuration_validated"], ENT_QUOTES, $charset)."' />";
					} else {
						$title = $object->get_information('smtpConnect_error');
						if(empty($title)) {
							$title = $msg["mail_configuration_unvalidated"];
						}
						$content .= "<img src='".get_url_icon('cross.png')."' title='".htmlentities($title, ENT_QUOTES, $charset)."' alt='".htmlentities($title, ENT_QUOTES, $charset)."' />";
					}
				}
				break;
			case 'uses':
			    if($object->is_used()) {
				    $uses = $object->get_uses();
					$content .= "<ul>";
					if(!empty($uses['users'])) {
						foreach ($uses['users'] as $user) {
							$user_label = trim($user->get_prenom()." ".$user->get_nom());
							if(!$user_label) $user_label = $user->get_username();
							$content .= $this->get_cell_content_use($user_label, $base_path."/admin.php?categ=users&sub=users&action=modif&id=".$user->get_id());
						}
						
					}
					if(!empty($uses['locations'])) {
						foreach ($uses['locations'] as $location) {
							$content .= $this->get_cell_content_use($location->libelle, $base_path."/admin.php?categ=docs&sub=location&action=modif&id=".$location->id);
						}
					}
					if(!empty($uses['parameters'])) {
						foreach ($uses['parameters'] as $parameter) {
							$section = substr($parameter, 0, strpos($parameter, '_'));
							$sstype = substr($parameter, strpos($parameter, '_')+1);
							$content .= $this->get_cell_content_use($msg['param_'.$section]." : ".$sstype, $base_path."/admin.php?categ=param&form_type_param=".$section."&form_sstype_param=".$sstype."#justmodified");
						}
					}
					if(!empty($uses['coords'])) {
						foreach ($uses['coords'] as $id_entite=>$raison_sociale) {
							$content .= $this->get_cell_content_use($raison_sociale, $base_path."/admin.php?categ=acquisition&sub=entite&action=modif&id=".$id_entite);
						}
					}
					$content .= "</ul>";
			    } elseif($object->get_type() == 'address') {
			        $content .= "<strong>".htmlentities($msg['mail_configuration_uses_none'], ENT_QUOTES, $charset)."</strong>";
			    }
				break;
			case 'actions':
			    if($object->get_type() == 'address' && !$object->is_used()) {
			        $button_event = [
			            'location' => static::get_controller_url_base()."&action=delete&name=".$object->get_name(),
			            'confirm_msg' => $msg['mail_configuration_action_delete_confirm']
			            
			        ];
			        $content .= $this->get_interface_button_small($msg['mail_configuration_action_delete'], $button_event);
			    } elseif($object->is_confidential()) {
					$content .= "<img src='".get_url_icon('lock.png')."' title='".htmlentities($msg["mail_configuration_action_edit_locked"], ENT_QUOTES, $charset)."' />";
				} else {
				    $content .= $this->get_interface_button_small($msg["mail_configuration_action_edit"], ['location' => static::get_controller_url_base()."&action=edit&name=".$object->get_name()]);
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		$object = new mail_configuration($group_label);
		$display = "
		<tr id='".$uid."_group_header' style='font-weight:bold;font-size: 1.1em;'>";
		foreach ($this->columns as $column) {
			$display .= $this->get_display_cell($object, $column['property']);
		}
		$display .= "
		</tr>";
		return $display;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$edit_link = array(
				'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
		$check_configuration_link = array(
				'href' => static::get_controller_url_base()."&action=list_check_configuration",
				'confirm' => $msg['check_configuration_confirm']
		);
		$this->add_selection_action('check_configuration', $msg['check_configuration'], '', $check_configuration_link);
		$initialize_link = array(
				'href' => static::get_controller_url_base()."&action=list_initialization",
				'confirm' => $msg['initialization_confirm']
		);
		$this->add_selection_action('delete', $msg['initialize'], 'interdit.gif', $initialize_link);
	}
	
	protected function save_object($object, $property, $value) {
		switch ($property) {
			case 'hote':
			case 'port':
				if (!$object->is_allowed_hote_override()) {
					return false;
				}
				break;
		}
		parent::save_object($object, $property, $value);
	}
	
	public static function delete_object($name) {
		mail_configuration::delete($name);
	}
	
	public static function run_action_list($action='') {
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach ($selected_objects as $name) {
				$model_class_instance = new mail_configuration($name);
				if($model_class_instance->get_id()) {
					switch ($action) {
						case 'check_configuration':
							$model_class_instance->check_configuration();
							break;
						case 'initialization':
						    if($model_class_instance->is_used()) {
						        $model_class_instance->initialization();
						    } else {
						        mail_configuration::delete($name);
						    }
							break;
					}
				}
			}
		}
	}
}