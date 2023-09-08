<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_form.class.php,v 1.11 2019-06-04 10:09:48 ngantier Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

// require_once ($include_path . '/templates/contribution_area/contribution_area.tpl.php');
require_once ($class_path . '/contribution_area/contribution_area.class.php');
require_once ($class_path . '/contribution_area/contribution_area_store.class.php');
require_once ($class_path . '/rdf/ontology.class.php');
require_once ($class_path . '/onto/onto_parametres_perso.class.php');
require_once ($class_path . '/onto/onto_store_arc2_extended.class.php');

/**
 * class contribution_area_form
 * Représente un formulaire
 */
class contribution_area_form {
	protected $id=0;
	protected $type = "";
	protected $uri = "" ;
	protected $availableProperties = array();
	protected $name="";
	protected $comment="";
	protected $parameters;
	protected $unserialized_parameters;
	protected $classname = ""; 
	protected $active_properties;
	protected $area_id;
	protected $form_uri;
	
	/**
	 * Formulaires liés à celui-ci
	 * @var array
	 */	
	protected $linked_forms;
	
	static protected $contribution_area_form = array();
	
	public function __construct($type, $id=0, $area_id = 0, $form_uri = '')	{
		$this->id = $id*1;
		$this->type = $type;
		$this->area_id = $area_id*1;
		if ($form_uri) {
			$this->form_uri = $form_uri;
		}
		$this->fetch_data();
	}
	
	public static function get_contribution_area_form($type, $id=0, $area_id = 0, $form_uri = '') {
		if (!isset(self::$contribution_area_form[$type])) {
			self::$contribution_area_form[$type] = array();
		}
		$key = '';
		if ($id*1) {
			$key = $id;
			$key.= ($area_id ? '_'.$area_id : '');
		}
		if (!$key) {
			return new contribution_area_form($type, $id, $area_id, $form_uri);
		}
		if (!isset(self::$contribution_area_form[$type][$key])) {
			self::$contribution_area_form[$type][$key] = new contribution_area_form($type, $id, $area_id, $form_uri);
		}
		return self::$contribution_area_form[$type][$key];
	}
	
	protected function fetch_data()	{
		if($this->id){
			$query = 'select * from contribution_area_forms where id_form = "'.$this->id.'"';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$params = pmb_mysql_fetch_object($result);
				$this->parameters = $params->form_parameters;
				$this->unserialized_parameters = json_decode($this->parameters);
				$this->name = $params->form_title;
				$this->comment = $params->form_comment;
				$this->type = $params->form_type;
			}
		}
		
		$contribution_area_store = new contribution_area_store();
		$onto = $contribution_area_store->get_ontology();
		$classes = $onto->get_classes();
		$class_uri = "";
		foreach($classes as $class){
			if($class->pmb_name == $this->type){
				$this->uri = $class->uri;
				$properties = $onto->get_class_properties($this->uri);
				for($i=0 ; $i<count($properties) ; $i++){
					$property = $onto->get_property($this->uri, $properties[$i]);
					$this->availableProperties[$property->pmb_name] = $property;
				}
				if (is_array($class->sub_class_of)) {
					foreach($class->sub_class_of as $parent_uri) {
						$properties = $onto->get_class_properties($parent_uri);
						for($i=0 ; $i<count($properties) ; $i++){
							$property = $onto->get_property($parent_uri, $properties[$i]);
							$this->availableProperties[$property->pmb_name] = $property;
						}
					}
				}
				break;
			}
		}
		
		$ontology = $contribution_area_store->get_ontology();
		$classes_array = $ontology->get_classes_uri();
		$this->classname = $classes_array[$this->uri]->name;
	}
	
	protected function get_saved_property($property)
	{
		if(isset($this->unserialized_parameters->$property)){
			return $this->unserialized_parameters->$property; 	
		}
		return '';
	}
	
	public function get_active_properties() {	
		if (isset($this->active_properties)) {
			return $this->active_properties;
		}
		$this->active_properties = array();
		if ($this->unserialized_parameters) {			
			foreach($this->unserialized_parameters as $key => $param){
				$uri = $this->availableProperties[$key]->uri;
				$this->active_properties[$uri] = new stdClass();
				$this->active_properties[$uri] = $this->unserialized_parameters->$key;			

				$tab_default_value = $this->unserialized_parameters->$key->default_value;				
				//on uniformise toutes les valeurs sous forme de tableau
				$this->active_properties[$uri]->default_value = array();
				if (is_array($tab_default_value)) {
					for ($j = 0; $j < count($tab_default_value); $j++) {
					    if (isset($tab_default_value[$j]->value) && !is_array($tab_default_value[$j]->value)) {
							$tab_default_value[$j]->value = array($tab_default_value[$j]->value);
						}
					}	
					$this->active_properties[$uri]->default_value = $tab_default_value;
				}
			}
		}
		return $this->active_properties;
	}
	
	
	public function render() {
		global $base_path, $class_path;
		
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'onto_contribution_form_' . $this->id,
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0,
				'params' => $this->get_active_properties()
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'contribution_area_datastore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		
		$tab_namespaces = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		
		$params = new onto_param(array(
				'base_resource' => 'index.php',
				'lvl' => 'contribution_area',
				'sub' => $this->type,
				'action' => 'edit',
				'page' => '1',
				'nb_per_page' => $nb_per_page_gestion,
				'id' => '0',
				'area_id' => $this->area_id,
				'parent_id' => '',
				'form_id' => $this->id,
				'form_uri' => $this->form_uri,
				'item_uri' => '',
		));
		
		$onto_store = new onto_store_arc2_extended($onto_store_config);
		$onto_store->set_namespaces($tab_namespaces);
			
		//chargement de l'ontologie dans son store
		$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
		onto_parametres_perso::load_in_store($onto_store, $reset);
		
		$onto_ui = new onto_ui("", $onto_store, array(), "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2000/01/rdf-schema#label',$params);
		$this->get_linked_forms();
		return $onto_ui->proceed();
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_comment() {
	    return $this->comment;
	}
	
	public function get_type() {
		return $this->type;
	}

	public function get_linked_forms () {
		if (isset($this->linked_forms)) {
			return $this->linked_forms;
		}
		$contribution_area_store  = new contribution_area_store();	
		$complete_form_uri = $contribution_area_store->get_uri_from_id($this->form_uri); 	
		$graph_store_datas = $contribution_area_store->get_attachment_detail($complete_form_uri, 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
		
		$this->linked_forms = array();
		for ($i = 0 ; $i < count($graph_store_datas); $i++) {
			if ($graph_store_datas[$i]['type'] == "form") {
				$graph_store_datas[$i]['area_id'] = $this->area_id;
				$this->linked_forms[] = $graph_store_datas[$i];
			} else {
				$data_form = $contribution_area_store->get_attachment_detail($graph_store_datas[$i]['uri'], 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
				for ($j = 0 ; $j < count($data_form); $j++) {
					if ($data_form[$j]['type'] == "form") {
						$data_form[$j]['area_id'] = $this->area_id;
						$data_form[$j]['propertyPmbName'] = $graph_store_datas[$i]['propertyPmbName'];
						if ($graph_store_datas[$i]['type'] == "scenario") {
							$data_form[$j]['scenarioUri'] = $graph_store_datas[$i]['uri'];
						}
						$this->linked_forms[] = $data_form[$j];
					}
				}
			}
		}
		return $this->linked_forms;
	}
}