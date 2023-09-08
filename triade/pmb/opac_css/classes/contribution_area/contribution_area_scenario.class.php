<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_scenario.class.php,v 1.6 2018-08-23 15:09:39 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');

class contribution_area_scenario {
	
	/**
	 * URI du scénario
	 * @var string
	 */
	protected $uri;
	
	/**
	 * formulaires liés au scénario
	 * @var unknown
	 */
	protected $forms; 
	
	/**
	 * Espace de contribution
	 * @var contribution_area
	 */
	protected $area;
	
	/**
	 * Nom du scénario
	 * @var string
	 */
	protected $name;

	/**
	 * Question du scénario
	 * @var string
	 */
	protected $question;
		
	/**
	 * Id du scenario
	 * @var float
	 */
	protected $id;
	
	/**
	 * Type d'entité du scenario
	 * @var string
	 */
	protected $entity_type;
	
	public function __construct($id,$area_id = 0) {
		$this->id = $id;
		if ($area_id*1) {
			$this->area = new contribution_area($area_id);
		}
		
	}
	
	public function render() {
		global $include_path;
		$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_area_scenario.tpl.html');
		return $h2o->render(array('scenario' => $this));
	}
	
	public function get_uri() {
		if (!isset($this->uri)) {
			$this->get_infos();
		}
		return $this->uri;
	}
	
	public function get_forms () {
		if (isset($this->forms)) {
			return $this->forms;
		}
		$contribution_area_store  = new contribution_area_store();		
		$graph_store_datas = $contribution_area_store->get_attachment_detail($this->get_uri(), $this->get_area_uri(),'','form', 1);
		$this->forms = array();
		for ($i = 0 ; $i < count($graph_store_datas); $i++) {
			//if ($graph_store_datas[$i]['type'] == 'startScenario') {
			$graph_store_datas[$i]['area_id'] = $this->area->get_id();
			$this->forms[] = $graph_store_datas[$i];
		}
		
		if(count($this->forms) > 1){
			usort($this->forms, array($this, 'sort_forms'));
		}
		
		return $this->forms;
	}
	
	public function get_area_uri() {
		if (isset($this->area)) {
			return $this->area->get_area_uri();
		}
		return '';
	}
	
	protected function get_infos() {
		$contribution_area_store  = new contribution_area_store();
		$this->uri = $contribution_area_store->get_uri_from_id($this->id);
		$infos = $contribution_area_store->get_infos($this->uri);
		$this->name = $infos['name'];
		$this->question = $infos['question'];
		$this->entity_type = $infos['entityType'];
	}
	
	public function get_name() {
		if (!isset($this->name)) {
			$this->get_infos();
		}
		return $this->name;
	}

	public function get_question() {
		if (!isset($this->question)) {
			$this->get_infos();
		}
		return $this->question;
	}
		
	public function get_id() {
		if (!isset($this->id)) {
			$this->get_infos();
		}
		return $this->id;
	}
	
	public function get_entity_type() {
		if (!isset($this->entity_type)) {
			$this->get_infos();
		}
		return $this->entity_type;
	}
	
	public function get_area() {
		return $this->area;
	}
	
	public function sort_forms($a, $b){
		return strcasecmp($a['name'], $b['name']);
	}
}