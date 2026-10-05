<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_page.class.php,v 1.21 2022/01/21 08:37:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/frbr/frbr_page.tpl.php");
require_once($class_path."/frbr/frbr_entities.class.php");
require_once($class_path."/opac_views.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/auth_templates.class.php");
require_once($class_path."/notice_tpl.class.php");

class frbr_page {
	
	/**
	 * Identifiant de la page
	 */
	protected $id;
	
	/**
	 * Libellé de la page
	 * @var string
	 */
	protected $name;
	
	/**
	 * Description de la page
	 */
	protected $comment;
	
	/**
	 * Type d'entité
	 */
	protected $entity;
	
	/**
	 * Paramètres spécifiques
	 */
	protected $parameters;
	
	/**
	 * Vues OPAC
	 * @var string
	 */
	protected $opac_views;
	
	protected $order;
	
	/**
	 * Cadres opac permanents d'une page
	 * @var array
	 */
	protected $cadre_opac_types;
	
	public function __construct($id=0) {
	    $this->id = (int) $id;
		$this->fetch_data();
	}
	
	protected static function _init_parameters($type='') {
		global $opac_authorities_templates_folder;
		global $opac_notices_format_django_directory;
		
		$parameters = new stdClass();
		if($type == "authperso") {
			$parameters->authperso = new stdClass();
			$parameters->authperso->value = 1;
			$parameters->authperso->field_type = 'authperso_selector';
		}
		$parameters->isbd = new stdClass();
		$parameters->isbd->value = 1;
		$parameters->isbd->field_type = 'checkbox';
		if($type != "records") {
			$parameters->records_list = new stdClass();
			$parameters->records_list->value = 1;
			$parameters->records_list->field_type = 'checkbox';
			$parameters->facettes_list = new stdClass();
			$parameters->facettes_list->value = 1;
			$parameters->facettes_list->field_type = 'checkbox';
			$parameters->template_directory = new stdClass();
			$parameters->template_directory->value = $opac_authorities_templates_folder;
			$parameters->template_directory->field_type = 'auth_templates';
			$parameters->record_template_directory = new stdClass();
			$parameters->record_template_directory->value = $opac_notices_format_django_directory;
			$parameters->record_template_directory->field_type = 'record_templates';
		}
		return $parameters;
	}
	
	protected function fetch_data() {
		$this->name = '';
		$this->comment = '';
		$this->entity = '';
		$this->parameters = static::_init_parameters();
		$this->opac_views = '';
		$this->order = 1;
		if($this->id) {
			$query = 'select * from frbr_pages where id_page ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->page_name;
			$this->comment = $row->page_comment;
			$this->entity = $row->page_entity;
			$this->set_parameters(json_decode($row->page_parameters));
			$this->opac_views = $row->page_opac_views;
			$this->order = $row->page_order;
		}
	}
		
	public static function get_parameters_form($type, $parameters = null) {
		global $msg;
		
		if($parameters == null) {
			$parameters = static::_init_parameters($type);
		}
		$form = '';
		foreach ($parameters as $property=>$parameter) {
			$form .= "
				<div class='row'>
					<label class='etiquette' for='page_parameter_".$property."'>".$msg['frbr_page_parameter_'.$property]."</label>";
			switch ($parameter->field_type) {
				case 'checkbox':
					$form .= "<input type='checkbox' name='page_parameters[".$property."]' id='page_parameter_".$property."' value='1' ".(isset($parameters->{$property}->value) && $parameters->{$property}->value ? "checked='checked'" : "")." />";
					break;
				case 'auth_templates':
					$form .= "<select name='page_parameters[".$property."]'>".auth_templates::get_directories_options($parameters->{$property}->value)."</select>";
					break;
				case 'record_templates':
					$form .= "<select name='page_parameters[".$property."]'>".notice_tpl::get_directories_options($parameters->{$property}->value)."</select>";
					break;
				case 'authperso_selector':
					$form .= "<select name='page_parameters[".$property."]'>";
					$authpersos = authpersos::get_authpersos();
					foreach ($authpersos as $authperso) {
						$form .= "<option value='".$authperso['id']."' ".($authperso['id'] == $parameters->{$property}->value ? "selected='selected'" : "").">".$authperso['name']."</option>";
					}
					$form .= "</select>";
					break;
			}
			$form .= "</div>";
		}
		return $form;
	}
	
	public function get_form() {
		global $msg, $charset;
		global $frbr_page_form_tpl;
		global $pmb_opac_view_activate;
		
		$form = $frbr_page_form_tpl;
		if($this->id) {
			$form = str_replace('!!title!!', htmlentities($msg['frbr_page_edit'], ENT_QUOTES, $charset), $form);
			$form = str_replace('!!delete!!', "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\" />", $form);
		} else {
			$form = str_replace('!!title!!', htmlentities($msg['frbr_page_add'], ENT_QUOTES, $charset), $form);
			$form = str_replace('!!delete!!', '', $form);
		}
		$form = str_replace('!!name!!', htmlentities($this->name, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!comment!!', htmlentities($this->comment, ENT_QUOTES, $charset), $form);
		if($this->id) {
			$form = str_replace('!!entities_selector!!', frbr_entities::get_hidden_field('page_entity', $this->entity), $form);
		} else {
			$form = str_replace('!!entities_selector!!', frbr_entities::get_selector('page_entity', $this->entity, 'load_entity_parameters(this.value);'), $form);
		}
		$form = str_replace('!!parameters_form!!', static::get_parameters_form($this->entity, $this->parameters), $form);
		if($pmb_opac_view_activate){
			if($this->opac_views) $selected = explode(',', $this->opac_views);
			else $selected = array();
			$form = str_replace('!!opac_views_selector!!', opac_views::get_selector('page_opac_views', $selected), $form);
		} else {
			$form = str_replace('!!opac_views_selector!!', '', $form);
		}
		$form = str_replace('!!id!!', $this->id, $form);
		
		return $form;
	}
	
	public function set_parameters_from_form() {
		global $page_parameters;
		$parameters = stripslashes_array($page_parameters);
		$this->parameters = static::_init_parameters($this->entity);
		foreach ($this->parameters as $property=>$data) {
			if(isset($parameters[$property])) {
				$this->parameters->{$property}->value = $parameters[$property];
			} else {
				$this->parameters->{$property}->value = '';
			}
		}
	}
	
	/**
	 * Données provenant d'un formulaire
	 */
	public function set_properties_from_form() {
		global $page_name;
		global $page_comment;
		global $page_entity;
		global $page_opac_views;
		
		$this->name = stripslashes($page_name);
		$this->comment = stripslashes($page_comment);
		$this->entity = stripslashes($page_entity);
		$this->set_parameters_from_form();
		if(isset($page_opac_views) && is_array($page_opac_views)) {
			$this->opac_views = implode(',', $page_opac_views);
		} else {
			$this->opac_views = "";
		}
	}
	
	/**
	 * Sauvegarde
	 */
	public function save(){
	
		if($this->id) {
			$query = 'update frbr_pages set ';
			$where = 'where id_page= '.$this->id;
		} else {
			$query = 'insert into frbr_pages set ';
			$where = '';
			$this->order = $this->get_max_order()+1;
		}
		$query .= '
				page_name = "'.addslashes($this->name).'",
				page_comment = "'.addslashes($this->comment).'",
				page_entity = "'.addslashes($this->entity).'",
				page_parameters = "'.addslashes(encoding_normalize::json_encode($this->parameters)).'",
				page_opac_views = "'.$this->opac_views.'",
				page_order = "'.$this->order.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			$this->update_place_from_parameters();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Suppression
	 */
	public static function delete($id=0){
		$id = intval($id);
		if($id) {
			//suppression des datanodes associés
			$query = "SELECT id_datanode FROM frbr_datanodes WHERE datanode_num_page = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_object($result)) {
					frbr_entity_common_entity_datanode::delete($row->id_datanode, true);
				}
			}
			//suppression des cadres associés
			$query = "SELECT id_cadre FROM frbr_cadres WHERE cadre_num_datanode = 0 AND cadre_num_page = '".$id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_object($result)) {
					frbr_entity_common_entity_cadre::delete($row->id_cadre);
				}
			}
			//suppresion de la page			
			$query = "delete from frbr_pages where id_page = ".$id;
			$result = pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_entity() {
		return $this->entity;
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function set_parameters($parameters) {
		$this->parameters = static::_init_parameters($this->entity);
		foreach ($this->parameters as $property=>$data) {
			if(isset($parameters->{$property}->value)) {
				$this->parameters->{$property}->value = $parameters->{$property}->value;
			} else {
				switch ($this->parameters->{$property}->field_type) {
					case 'checkbox':
						$this->parameters->{$property}->value = 0;
						break;
					default:
						$this->parameters->{$property}->value = '';
						break;
				}
			}
		}
	}
	
	protected function get_max_order() {
		$query = "select max(page_order) as max_order from frbr_pages where page_entity = '".$this->entity."'";
		$result = pmb_mysql_query($query);
		return intval(pmb_mysql_result($result, 0, 'max_order'));
	}
	
	public static function get_id_from_order($entity, $order) {
		$query = "select id_page from frbr_pages where page_entity='".$entity."' and page_order=".$order." limit 1";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result,0, 'id_page');
	}
	
	public static function set_order_from_id($id, $order) {
		$query = "update frbr_pages set page_order='".$order."' where id_page=".$id;
		pmb_mysql_query($query);
	}
	
	public function up_order() {
		$query = "select max(page_order) as max_order from frbr_pages where page_entity='".$this->entity."' and page_order < ".$this->order;
		$result = pmb_mysql_query($query);
		$max_order = pmb_mysql_result($result,0,0);
		if($max_order) {
			self::set_order_from_id(self::get_id_from_order($this->entity, $max_order), $this->order);
			self::set_order_from_id($this->id, $max_order);
		}
	}
	
	public function down_order() {
		$query = "select min(page_order) as min_order from frbr_pages where page_entity='".$this->entity."' and page_order > ".$this->order;
		$result = pmb_mysql_query($query);
		$min_order = pmb_mysql_result($result,0,0);
		if($min_order) {
			self::set_order_from_id(self::get_id_from_order($this->entity, $min_order), $this->order);
			self::set_order_from_id($this->id, $min_order);
		}
	}
	
	public function get_parameter_value($property='') {
		$parameter = '';
		if(isset($this->parameters->$property)) {
			$parameter = $this->parameters->$property->value;
		}
		return $parameter;
	}
	
	protected function get_cadre_opac_types() {
		if(!isset($this->cadre_opac_types)) {
			switch ($this->entity) {
				case 'records':
					$this->cadre_opac_types = array('isbd', 'frbr_graph');
					break;
				default:
					$this->cadre_opac_types = array('isbd', 'records_list', 'frbr_graph');
					break;
			}
		}
		return $this->cadre_opac_types;
	}
	
	public function update_place_from_parameters() {
		foreach ($this->get_cadre_opac_types() as $cadre_type) {
			$query = '	UPDATE frbr_place 
						SET place_visibility = "'.(isset($this->parameters->{$cadre_type}) ? $this->parameters->{$cadre_type}->value : 0).'" 
						WHERE place_num_page = "'.$this->id.'"
						AND place_cadre_type = "'.$cadre_type.'"';
			pmb_mysql_query($query);
		}
	}
}