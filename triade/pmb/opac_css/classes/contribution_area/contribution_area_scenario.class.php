<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_scenario.class.php,v 1.17 2022/03/22 14:04:05 gneveu Exp $
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
	 * Reponse de l'attachment
	 * @var string
	 */
	protected $response;

	/**
	 * Ordre de l'attachment
	 * @var string
	 */
	protected $orderResponse;
		
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
	
	/**
	 * Url ajax du scenario
	 * @var string
	 */
	protected $ajax_link;
	
	/**
	 * Equation de recherche du scenario
	 * @var string
	 */
	protected $equation;
	
	/**
	 * @var string
	 */
	protected $equation_query;
	
	public function __construct($id,$area_id = 0) {
		$this->id = $id;
		if ($area_id*1) {
			$this->area = new contribution_area($area_id);
		}
		
	}
	
	public function get_ajax_link()
	{
	    global $entity, $entity_type;
	    
	    $this->ajax_link = "";
	    
        $form_url = './ajax.php?module=ajax&categ=contribution&sub=scenario_child&id=0';
        if (!empty($this->area)) {
            $form_url .= '&area_id='.($this->area->get_id() ?? '0');
        }
        $form_url .= '&scenario='.($this->get_id() ?? '0');
        $form_url .= '&sub_tab=1';
        
        if ($entity) {
            $form_url .= '&action=edit_entity';
            $form_url .= '&entity_type='.$entity_type;
            $form_url .= '&create_entity=true';
        }
        
        $this->ajax_link = $form_url;
	    
        return $this->ajax_link;
	}
	
	public function render() {
		global $include_path;
		$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_area_scenario.tpl.html');
		return $h2o->render(array('scenario' => $this));
	}
	
	public function sub_render() {
		global $include_path;
		if (count($this->get_forms()) == 1) {
		    global $scenario, $sub_form;
		    // On redéfinit les globales
		    // Elles sont utilisées plus loin
		    $scenario = $this->get_id();
		    $sub_form = 1;
		    
		    $form = new contribution_area_form($this->forms[0]['entityType'], $this->forms[0]['formId'], $this->area->get_id(), $this->forms[0]['id']);
		    return $form->render();
		} else {
    		$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_area_sub_scenario.tpl.html');
    		return $h2o->render(array('scenario' => $this));
		}
	}
	
	public function get_uri() {
		if (!isset($this->uri)) {
			$this->get_infos();
		}
		return $this->uri;
	}
	
	public function get_forms ($edit_entity = false, $entity_id = 0) {
		if (isset($this->forms)) {
			return $this->forms;
		}
		$contribution_area_store  = new contribution_area_store();		
		$graph_store_datas = $contribution_area_store->get_attachment_detail($this->get_uri(), $this->get_area_uri(),'','form', 1);
		$this->forms = array();
		for ($i = 0 ; $i < count($graph_store_datas); $i++) {
			//if ($graph_store_datas[$i]['type'] == 'startScenario') {
		    $graph_store_datas[$i]['area_id'] = (!empty($this->area) ? $this->area->get_id() : '0');
		    
		    $graph_store_datas[$i]['url'] = './ajax.php?module=ajax&categ=contribution&sub='. $graph_store_datas[$i]['entityType'] .'&area_id='. $graph_store_datas[$i]['area_id'];
		    $graph_store_datas[$i]['url'] .= '&id='. $entity_id .'&sub_form=1&form_id='. $graph_store_datas[$i]['formId'] .'&form_uri='. $graph_store_datas[$i]['id'] .'&scenario='. $this->id;
		    if ($edit_entity) {
    		    $graph_store_datas[$i]['url'] .= '&action=edit_entity';
    		    $graph_store_datas[$i]['url'] .= '&create_entity=true';
		    }
			$this->forms[] = $graph_store_datas[$i];
		}
		
		if(count($this->forms) > 1){
			//usort($this->forms, array($this, 'sort_forms'));
		    usort($this->forms,  function($a,$b){
		        if ($a["orderResponse"] == $b["orderResponse"]) {
		            return 0;
		        }
		        return (intval($a["orderResponse"]) < intval($b["orderResponse"])) ? -1 : 1;
		    });
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
		$this->name = isset($infos['name']) ? $infos['name'] : '';
		$this->question = isset($infos['question']) ? $infos['question'] : '';
		$this->response = isset($infos['response']) ? $infos['response'] : '';
		$this->orderResponse = isset($infos['orderResponse']) ? $infos['orderResponse'] : '';
		$this->entity_type = isset($infos['entityType']) ? $infos['entityType'] : '' ;
		$this->equation = isset($infos['equation']) ? $infos['equation'] : '';
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
	
	public function get_response() {
	    if (!isset($this->response)) {
			$this->get_infos();
		}
		return $this->response;
	}

	public function get_orderResponse() {
	    if (!isset($this->orderResponse)) {
			$this->get_infos();
		}
		return $this->orderResponse;
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
	
	public function get_equation() {
	    if (!isset($this->equation)) {
			$this->get_infos();
		}
		return $this->equation;
	}
	
	public function get_equation_query() {
	    $this->equation_query = "";
	    
	    $query = "SELECT contribution_area_equation_query FROM contribution_area_equations 
                  WHERE contribution_area_equation_id='".$this->get_equation()."'";
	    
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        $row = pmb_mysql_fetch_object($result);
	        $this->equation_query = $row->contribution_area_equation_query;
	    }
	    
        return $this->equation_query;
	}
	
	public function get_area() {
		return $this->area;
	}
	
	public function sort_forms($a, $b){
		return strcasecmp($a['name'], $b['name']);
	}
}