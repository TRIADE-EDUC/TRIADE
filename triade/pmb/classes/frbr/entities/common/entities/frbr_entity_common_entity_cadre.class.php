<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_entity_cadre.class.php,v 1.29.4.1 2025/04/24 12:37:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/frbr/frbr_entities.class.php");
require_once($class_path."/frbr/frbr_entity_graph.class.php");

class frbr_entity_common_entity_cadre extends frbr_entity_common_entity {

	/**
	 * Identifiant du cadre
	 */
	protected $id;

	/**
	 * Libellé
	 * @var string
	 */
	protected $name;

	/**
	 * Description
	 */
	protected $comment;

	/**
	 * CSS sur le cadre
	 * @var string
	 */
	protected $css_class;

	/**
	 * Instance du jeu de données associé
	 * @var frbr_entity_common_entity_datanode
	 */
	protected $datanode;

	protected $type = 'cadre';

	protected $view = array();

	protected $parent_datanodes;

	/**
	 * cadre visible dans le graph
	 * @var boolean
	 */
	protected $visible_in_graph;

	/**
	 * chemin des jeux de données parent
	 * @var string
	 */
	protected $datanodes_path;

	/**
	 * affiche le template même sans données
	 * @var string
	 */
	protected $display_empty_template;

	/**
	 * nom de la classe
	 * @var string
	 */
	protected $class_name;

	public $sorting;

	protected $object;

	public function __construct($id=0) {
		parent::__construct($id);
	}

	protected function fetch_data() {
		$this->name = '';
		$this->comment = '';
		$this->css_class = '';
		if($this->id) {
			$query = 'select * from frbr_cadres where id_cadre ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->cadre_name;
			$this->comment = $row->cadre_comment;
			$this->css_class = $row->cadre_css_class;
			$this->visible_in_graph = $row->cadre_visible_in_graph;
			$this->datanodes_path = $row->cadre_datanodes_path;
			$this->display_empty_template = $row->cadre_display_empty_template;
			$this->class_name = $row->cadre_object;
			$this->set_page_from_num($row->cadre_num_page);
			$this->set_datanode_from_num($row->cadre_num_datanode);

			$query = "select id_cadre_content,cadre_content_object,cadre_content_type from frbr_cadres_content where cadre_content_num_cadre = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)){
				while ($ligne=pmb_mysql_fetch_object($result)) {
					switch ($ligne->cadre_content_type) {
						case "view":
							$this->view = array(
							    'id' => (int) $ligne->id_cadre_content,
								'name' => $ligne->cadre_content_object
							);
							break;
					}
				}
			}
		}
	}

	public function get_comment(){
	    return $this->comment;
	}

	protected function get_linked_form() {
		$num_datanode = 0;
		if (isset($this->datanode) && is_object($this->datanode)) {
			$num_datanode = $this->datanode->get_id();
		}
		$form = "
				<div class='colonne3'>
					<label class='etiquette' for='".$this->type."_num_parent'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_parent'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form .= $this->get_parent_name_from_page($num_datanode);
		if ($this->id) {
			$form .= "<input type='hidden' name='".$this->type."_num_parent' id='".$this->type."_num_parent' value='".$num_datanode."'/>";
		}
// 					<input type='text' class='saisie-50em' name='".$this->type."_datanode_name' id='".$this->type."_datanode_name' value='".($this->datanode->get_name() ? $this->datanode->get_name() : "")."' disabled/>
// 					<input type='hidden' name='".$this->type."_num_datanode' id='".$this->type."_num_datanode' value='".$num_datanode."'/>
		$form .="</div>";
		return $form;
	}

	protected function get_views_list_form(){
		//si aucun datanode n'est lié au cadre, on prend la vue par défaut de la page
		if (!$this->datanode && $this->page) {
			$entity_type = frbr_entity_common_entity_page::get_entity_type_from_id($this->page->get_id());
			$this->elements_used['view'] = array('frbr_entity_'.$entity_type.'_view');
		}
		if(count($this->elements_used['view'])>1){
			$form= "
				<div class='colonne3'>
					<label for='cadre_view_choice'>".$this->format_text($this->msg['frbr_entity_common_entity_cadre_view_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cadre_view_choice' id='cadre_view_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadElemForm\", \"parameters\":{\"id\":\"0\", \"domId\":\"view_form\", \"numPage\":\"".$this->page->get_id()."\"}}'>
						<option value=''>".$this->format_text($this->msg['frbr_entity_common_entity_cadre_view_choice'])."</option>";
			foreach($this->elements_used['view'] as $view){
				$form.= "
						<option value='".$view."'".(isset($this->view['name']) && $view == $this->view['name'] ? " selected='selected'" : "").">".$this->format_text($this->msg[$view])."</option>";
			}
			$form.="
					</select>
				</div>";
		}else{
			$form = "
					<input type='hidden' name='cadre_view_choice' id='cadre_view_choice' value='".$this->elements_used['view'][0]."'/>";
		}

		return $form;
	}

	protected function get_sorting_list_form(){
		$form = "";
		return $form;
	}

	public function get_parameters_form() {
		global $charset;
		$parameters_form = "
			<div class='row'>
				<div class='colonne3'>
					<label class='etiquette' for='".$this->type."_visible_in_graph'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_visible_in_graph'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='".$this->type."_visible_in_graph' id='".$this->type."_visible_in_graph' ".($this->visible_in_graph ? "checked" : "")."/>
				</div>
			</div>
			<br/>
			<div class='row'>
				<div class='colonne3'>
					<label class='etiquette' for='".$this->type."_css_class'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_css_class'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' class='saisie-50em' name='".$this->type."_css_class' id='".$this->type."_css_class' value='".htmlentities($this->css_class, ENT_QUOTES, $charset)."' />
				</div>
			</div>
			<br/>
			<div class='row'>
				<div class='colonne3'>
					<label class='etiquette' for='".$this->type."_display_empty_template'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_display_empty_template'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='".$this->type."_display_empty_template' id='".$this->type."_display_empty_template' ".($this->display_empty_template ? "checked" : "")."/>
				</div>
			</div>
			<div class='row'>";
		$parameters_form .= $this->get_views_list_form();
		$parameters_form .= "</div>";
		if((isset($this->view['id']) && $this->view['id']) || count($this->elements_used['view'])==1 ){
			if(isset($this->view['id']) && $this->view['id']){
				$view_name = $this->view['name'];
				$view_id = $this->view['id'];
			}else if(count($this->elements_used['view'])==1){
				$view_name = $this->elements_used['view'][0];
				$view_id = 0;
			}
			$parameters_form.="
				<script type='text/javascript'>
					require(['dojo/topic'],
					function(topic){
						topic.publish('ParametersFormsReady', 'frbrEntityLoadElemForm', {elem:'".$view_name."',id:'".$view_id."',domId:'view_form',numPage:'".$this->page->get_id()."'});
					});
				</script>";
		}
		$parameters_form .= "
			<div id='view_form' data-dojo-type='dijit/layout/ContentPane'>
			</div>
			<div class='row'>";
		$parameters_form .= $this->get_sorting_list_form();
		$parameters_form .= "</div>";
		$parameters_form .=	"
			<div id='sorting_form' data-dojo-type='dijit/layout/ContentPane'>
			</div>
			<div class='row'>
			</div>";
		return $parameters_form;
	}

	/**
	 * Données provenant d'un formulaire
	 */
	public function set_properties_from_form() {
		global $cadre_name;
		global $cadre_comment;
		global $cadre_css_class;
		global $cadre_visible_in_graph;
		global $cadre_display_empty_template;
		global $cadre_num_parent;
		global $num_page;

		$this->name = stripslashes($cadre_name);
		$this->comment = stripslashes($cadre_comment);
		$this->css_class = stripslashes($cadre_css_class);
		$this->visible_in_graph = ($cadre_visible_in_graph ? 1 : 0);
		$this->display_empty_template = ($cadre_display_empty_template ? 1 : 0);
		$this->set_datanode_from_num($cadre_num_parent);
		$this->set_page_from_num($num_page);
	}

	/**
	 * Formatage pour la sauvegarde dans la table '_content'
	 */
	public function save_content($type='') {
		$cadre_type = 'cadre_'.$type.'_choice';
		global ${$cadre_type};
		if(isset(${$cadre_type}) && isset($this->{$type}['name']) && ${$cadre_type} == $this->{$type}['name']){
			$type_id = $this->{$type}['id'];
		}else{
			$type_id = 0;
		}
		if(${$cadre_type}) {
			$type_instance = new ${$cadre_type}($type_id);
			$type_instance->set_num_cadre($this->id);
			$result = $type_instance->save_form();
			if($result) {
				$this->{$type} = array(
						'id' => $type_instance->id,
						'name' => ${$cadre_type}
				);
				return true;
			} else {
				return false;
			}
		} else {
			if($type != 'view') {
				if(!isset($this->{$type}['id'])) $this->{$type}['id'] = 0;
				if($this->{$type}['id']){
					$query = "delete from frbr_cadres_content
						where id_cadre_content = '".($this->{$type}['id']*1)."'
						and cadre_content_type='".$type."'
						and cadre_content_num_cadre='".$this->id."'";
					pmb_mysql_query($query);
				}
			}
		}
		return false;
	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		if($this->id) {
			$query = 'update frbr_cadres set ';
			$where = 'where id_cadre= '.$this->id;
		} else {
			$query = 'insert into frbr_cadres set ';
			$where = '';
		}
		$query .= '
				cadre_name = "'.addslashes($this->name).'",
				cadre_comment = "'.addslashes($this->comment).'",
				cadre_css_class = "'.addslashes($this->css_class).'",
				cadre_object = "'.addslashes($this->class_name).'",
				cadre_num_datanode = "'.(isset($this->datanode) ? $this->datanode->get_id() : 0).'",
				cadre_num_page = "'.(isset($this->page) ? $this->page->get_id() : 0).'",
				cadre_visible_in_graph = "'.addslashes($this->visible_in_graph).'",
				cadre_datanodes_path = "'.(isset($this->datanode) ? $this->datanode->get_path() : 0).'",
				cadre_display_empty_template = "'.addslashes($this->display_empty_template).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			//vue
			$saved = $this->save_content('view');
			if($saved) {
				//le tri
				$saved = $this->save_content('sorting');
			}
			return $this->id;
		}
		return false;
	}

	/**
	 * Suppression
	 */
	public static function delete($id=0){
		$id = intval($id);
		if($id) {
			//suppression du placement du cadre
			frbr_place::delete($id);
			$query = "delete from frbr_cadres_content where cadre_content_num_cadre = ".$id;
			pmb_mysql_query($query);
			$query = "delete from frbr_cadres where id_cadre = ".$id;
			pmb_mysql_query($query);
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

	public function get_object() {
		return $this->object;
	}

	public function get_type() {
		return $this->type;
	}

	public function set_datanode_from_num($num_datanode) {
		$num_datanode = intval($num_datanode);
		if($num_datanode) {
			$query = 'select datanode_object from frbr_datanodes where id_datanode = '.$num_datanode;
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->datanode = new $row->datanode_object($num_datanode);
			}
		}
	}

	public static function get_class_name_from_id($id_cadre) {
		$id_cadre = intval($id_cadre);
		$class_name = '';
		$query = '	SELECT cadre_object
					FROM frbr_cadres
					WHERE id_cadre = "'.$id_cadre.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row  =  pmb_mysql_fetch_object($result);
			$class_name = $row->cadre_object;
		}else {
			$class_name = 'frbr_entity_common_entity_cadre';
		}
		return $class_name;
	}

	public function get_object_id() {
		global $id;
		return $id;
	}

	protected function recursive_parent_datanodes($datanode_instance) {
		$parent_datanode_id = $datanode_instance->get_parent()->get_id();
		$this->parent_datanodes = array();
		$parent_datanode_class_name = get_class($datanode_instance->get_parent());
		while ($parent_datanode_id) {
			$datanode_parent_instance = new $parent_datanode_class_name($parent_datanode_id);
			array_unshift($this->parent_datanodes, $datanode_parent_instance);
			$parent_datanode_id = ($datanode_parent_instance->get_parent() ? $datanode_parent_instance->get_parent()->get_id() : 0);
			if($parent_datanode_id) {
				$parent_datanode_class_name = get_class($datanode_parent_instance->get_parent());
			}
		}
	}

	protected function get_content_cadre($data, $grouped_data = []) {
		$content = "";
		if (count($data) || $this->display_empty_template) {
			$view = new $this->view['name']($this->view['id']);
			$content = $view->render($data, $grouped_data);
		}
		return "<div id='".$this->get_dom_id()."'".($this->css_class != '' ? " class='".$this->css_class."'" : "").">".$content."</div>";
	}

	public function show_cadre($datanodes_data = array()) {
		if(isset($this->datanode) && is_object($this->datanode)) {
			if ($this->view['id'] != 0) {
			    if (!empty($datanodes_data[$this->datanode->get_id()]) && count($datanodes_data[$this->datanode->get_id()][0])) {
					$datanode_datasource_class_name = $this->datanode->get_datasource()['name'];
					$datasource = new $datanode_datasource_class_name($this->datanode->get_datasource()['id']);
					//Gestion de la limite d'éléments à renvoyer
					$limit = $datasource->get_parameters()->nb_max_elements;
					$data = $datanodes_data[$this->datanode->get_id()][0];
					if($limit > 0){
					    $data = array_slice($data, 0, $limit);
					}
					$grouped_data = $datanodes_data[$this->datanode->get_id()]["group"] ?? [];
					return $this->get_content_cadre($data, $grouped_data);
				} else {
					return $this->get_content_cadre(array());
				}
			}
		} elseif($this->view['id'] != 0){
			//$view = new $this->view['name']($this->view['id']);
			$data = array($this->get_object_id());
			return $this->get_content_cadre($data);
		}
		return "<div id='".$this->get_dom_id()."'".($this->css_class != '' ? " class='".$this->css_class."'" : "")."></div>";
	}

	public function get_dom_id(){
		return $this->class_name."_".$this->id;
	}

	public function get_class_name() {
	    if (isset($this->class_name)) {
	        return $this->class_name;
	    }
	    return "";
	}
}