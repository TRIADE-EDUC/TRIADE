<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_entity_page.class.php,v 1.14 2023/08/17 09:47:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/frbr/frbr_entities.class.php");
//require_once($class_path."/opac_views.class.php");
require_once($class_path."/encoding_normalize.class.php");
//require_once($class_path."/auth_templates.class.php");
//require_once($class_path."/notice_tpl.class.php");

class frbr_entity_common_entity_page extends frbr_entity_common_entity {
	
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
	
	protected $type = 'page';
	
	protected $backbone = array();
	
	/**
	 * Cadres opac permanents d'une page
	 * @var array
	 */
	protected $cadre_opac_types;
	
	public function __construct($id=0) {
		parent::__construct($id);
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
			$parameters->frbr_graph = new stdClass();
			$parameters->frbr_graph->value = 1;
			$parameters->frbr_graph->field_type = 'checkbox';
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
			$query = "select id_page_content,page_content_object,page_content_type, page_content_data from frbr_pages_content where page_content_num_page = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)){
				while ($ligne=pmb_mysql_fetch_object($result)) {
					switch ($ligne->page_content_type) {
						case "backbone":
							$this->backbone = array(
							'id' => (int) $ligne->id_page_content,
							'name' => $ligne->page_content_object,
							'data' => json_decode($ligne->page_content_data)
							);
							break;
					}
				}
			}
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
	
	public function get_form($ajax= true) {
		global $msg, $charset;
		global $current_module;
		global $pmb_opac_view_activate;
		
		if($ajax){
			$action = "./ajax.php?module=cms&categ=frbr_entities&elem=".$this->class_name."&action=save_form";
		}
		$form = $this->get_js_form();
		$form .= "
			<form class='form-".$current_module."' id='".$this->class_name."_form' name='".$this->class_name."_form'  method='post' action=\"".htmlentities($action, ENT_QUOTES, $charset)."\" >
				<h3>".$this->format_text(($this->id && isset($this->informations['name']) ? sprintf($this->msg['frbr_entity_common_entity_'.$this->type.'_edit'],$this->informations['name']." : ".$this->name) : $this->msg['frbr_entity_common_entity_'.$this->type.'_add']))."</h3>
				".($this->id ? '<input type="hidden" value="'.$this->id.'" name="id_element" />': '')."
				<div class='form-contenu'>
					<div class='row'>
						<label class='etiquette' for='page_name'>".$msg['frbr_page_name']."</label>
					</div>
					<div class='row'>
						<input type='text' class='saisie-50em' name='page_name' id='page_name' value='".htmlentities($this->name, ENT_QUOTES, $charset)."' />
					</div>
					<div class='row'>
						<label class='etiquette' for='page_comment'>".$msg['frbr_page_comment']."</label>
					</div>
					<div class='row'>
						<textarea name='page_comment' id='page_comment' cols='55' rows='5'>".htmlentities($this->comment, ENT_QUOTES, $charset)."</textarea>
					</div>
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<label class='etiquette' for='page_entity'>".$msg['frbr_page_entity']."</label>
					</div>
					<div class='row'>
						".($this->id ? frbr_entities::get_hidden_field('page_entity', $this->entity) : frbr_entities::get_selector('page_entity', $this->entity, 'load_entity_parameters(this.value);'))."
					</div>
					<div id='parameters_form'>			
						".static::get_parameters_form($this->entity, $this->parameters)."
					</div>";
		if($pmb_opac_view_activate) {
			if($this->opac_views) $selected = explode(',', $this->opac_views);
			else $selected = array();
			$form .= "
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<label for='opac_views'>".htmlentities($msg['frbr_page_opac_views'],ENT_QUOTES,$charset)."</label></br>
					</div>
					<div class='row'>
						".opac_views::get_selector('page_opac_views', $selected)."
					</div>";
		}
		$form .= "
					<div class='row'>
						".$this->get_backbones_list_form()."
					</div>
					<div class='row'>
					</div>
				</div>
				<hr />
			</div>
			<div class='row'>
				".$this->get_buttons_form()."
			</div>
			<div class='row'></div>
		</form>
		<script>
			document.forms['".$this->class_name."_form'].elements['".$this->type."_name'].focus();
		</script>
			";
		return $form;
	}
	
	protected function get_backbones_list_form(){
		global $msg, $charset;
	
		$form = "";
		$form.="
			<div class='row'>
				<label>".htmlentities($msg['frbr_page_backbone_choice'], ENT_QUOTES, $charset)."
			</div>
			<div class='row'>
				<select id='page_backbone_choice' name='page_backbone_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadManagedElemForm\", \"parameters\":{\"elem\":\"frbr_entity_common_backbone\", \"id\":\"0\", \"domId\":\"backbone_form\", \"numPage\":\"".$this->id."\"}}'>
					<option value=''>".htmlentities($msg['frbr_page_backbone_choice'], ENT_QUOTES, $charset)."</option>";
		if(isset($this->managed_datas['backbones'])) {
			foreach($this->managed_datas['backbones'] as $key => $infos) {
				$form.= "
	 			<option value='".$key."' ".(isset($this->backbone['data']) && $key == "backbone".$this->backbone['data']->id ? "selected='selected'" : "").">".$infos['name']."</option>";
			}
		}
		$form.="
 				</select>
				<img src='".get_url_icon('add.png')."' alt='".$msg["925"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"backbone\", \"idElement\":\"".$this->id."\", \"manageId\": \"0\", \"quoi\" : \"backbones\", \"numPage\":\"".$this->id."\"}}' title=\"Creation pivot\"/>
 			</div>
 			<div class='row' id='backbone_form' dojoType='dijit/layout/ContentPane'></div>
 			<div class='row'>&nbsp;</div>";
		if(isset($this->backbone['data']->id) && $this->backbone['data']->id) {
			$form.="
				<script>
						require(['dojo/topic'],
						function(topic){
							topic.publish('ParametersFormsReady', 'frbrEntityLoadManagedElemForm', {elem:'frbr_entity_common_backbone',selectedIndex:'backbone".$this->backbone['data']->id."',id: '".$this->backbone['id']."', domId:'backbone_form',numPage:'".$this->id."'})	  
						});
				</script>";
		}
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
	 * Formatage pour la sauvegarde dans la table '_content'
	 */
	public function save_content($type='') {
		$page_type = 'page_'.$type.'_choice';
		global ${$page_type};
	
		if(isset(${$page_type}) && isset($this->{$type}['name']) && ${$page_type} == $this->{$type}['name']){
			$type_id = $this->{$type}['id'];
		}else{
			$type_id = 0;
		}
		if(${$page_type}) {
			switch ($type) {
				case 'backbone':
					$type_instance = new frbr_entity_common_backbone($type_id);
					break;
				default:
					$type_instance = new ${$page_type}($type_id);
					break;
			}
			$type_instance->set_num_page($this->id);
			$result = $type_instance->save_form();
			if($result) {
				$this->{$type} = array(
						'id' => $type_instance->id,
						'name' => ${$page_type}
				);
				return true;
			} else {
				return false;
			}
		} else {
			if(!isset($this->{$type}['id'])) $this->{$type}['id'] = 0;
			if($this->{$type}['id']){
				$query = "delete from frbr_pages_content
					where id_page_content = '".($this->{$type}['id']*1)."'
					and page_content_type='".$type."'
					and page_content_num_page='".$this->id."'";
				pmb_mysql_query($query);
			}
		}
		return false;
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
			//la condition / pivot
			$this->save_content('backbone');
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
	
	public function get_type() {
		return $this->type;
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
	
	/**
	 * modification d'un seul paramètre
	 * @param stdClass $parameters
	 */
	public function set_parameter($parameter) {
		foreach ($parameter as $property=>$data) {
			if(!isset($this->parameters->{$property})) {
				$this->parameters->{$property} = new stdClass();
			}
			$this->parameters->{$property}->value = $parameter->{$property}->value;
		}
	}
	
	protected function get_datanodes() {
		$query = 'SELECT * FROM frbr_datanodes WHERE datanode_num_page = "'.$this->id.'"';
		$result = pmb_mysql_query($query);
		$datanodes = array();
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$datanodes[] = array(
						'id' => $row->id_datanode,
						'name' => $row->datanode_name,
						'parent' => $row->datanode_num_parent,
						'page' => $this->id,
						'type' => 'datanode'
				);
			}
		}
		return $datanodes;
	}
	
	protected function add_cadre($id_cadre, $name, $num_datanode=0, $visibility=0, $order=0, $cadre_type='') {
		return array(
				'id' => $id_cadre,
				'name' => $name,
				'parent' => $num_datanode,
				'page' => $this->id,
				'cadre_type' => $cadre_type,
				'visibility' => $visibility,
				'order' => $order,
				'type' => 'cadre'
		);
	}
	
	protected function get_cadres() {
		$query = 'SELECT *
				FROM frbr_cadres
				LEFT JOIN frbr_place ON frbr_place.place_num_cadre = frbr_cadres.id_cadre
				WHERE cadre_num_page = "'.$this->id.'"';
		$result = pmb_mysql_query($query);
		$datacadres = array();
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$datacadres[] = $this->add_cadre($row->id_cadre, $row->cadre_name, $row->cadre_num_datanode, ($row->place_visibility ? $row->place_visibility : 0), ($row->place_order ? $row->place_order : 0));
			}
		}
		return $datacadres;
	}
	
	protected function add_cadre_opac($cadre_type) {
		global $msg;
	
		$query = "select * from frbr_place
				where place_cadre_type = '".$cadre_type."' and place_num_page = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			return $this->add_cadre(0, $msg['frbr_page_'.$cadre_type], 0, $row->place_visibility, $row->place_order, $cadre_type);
		} else {
			return $this->add_cadre(0, $msg['frbr_page_'.$cadre_type], 0, $this->parameters->{$cadre_type}->value, 0, $cadre_type);
		}
	}
	
	protected function get_cadres_opac() {
		$cadres_opac = array();
		foreach($this->get_cadre_opac_types() as $cadre_type) {
			$cadres_opac[] = $this->add_cadre_opac($cadre_type);
		}
		return $cadres_opac;
	}
	
	public function get_dojo_tree(){
		$data_array = array('num_page'=> $this->id, 'rootNode'=> array('id'=> 0, 'root'=> true, 'name'=> $this->name, 'page' => $this->id, 'cadres_opac' => $this->get_cadres_opac()), 'treeDatanodes' => $this->get_datanodes(), 'treeCadres' => $this->get_cadres());
		return encoding_normalize::json_encode($data_array);
	}
	
	public function get_form_tree() {
		global $frbr_page_tree_tpl;
		$form = $frbr_page_tree_tpl;
		$form = str_replace('!!parameters!!', $this->get_dojo_tree(), $form);
		return $form;
	}
	
	public function get_form_build() {
		$form = "<div data-dojo-type='dijit/layout/TabContainer' id='frbrTabContainer' data-dojo-props='splitter:true,region:\"top\"' style='width:auto;height:50%'>";
		$form .= $this->get_form_tree();
		$frbr_place = new frbr_place($this->id);
		$form .= $frbr_place->get_form();
		$form .= "</div>";
		return $form;
	}
	
	public function get_backbone() {
		return $this->backbone;
	}
	
	public static function get_class_name_from_id($id_page) {
		$id_page = intval($id_page);
		$class_name = '';
		$query = '	SELECT page_entity
					FROM frbr_pages
					WHERE id_page = "'.$id_page.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row  =  pmb_mysql_fetch_object($result);
			$class_name = 'frbr_entity_'.$row->page_entity.'_page';
		}else {
			$class_name = 'frbr_entity_common_entity_page';
		}
		return $class_name;
	}
	
	public static function get_entity_type_from_id($id_page) {
		$entity_type = '';
		$query = '	SELECT page_entity
					FROM frbr_pages
					WHERE id_page = "'.$id_page.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row  =  pmb_mysql_fetch_object($result);
			$entity_type = $row->page_entity;
		}
		return $entity_type;
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