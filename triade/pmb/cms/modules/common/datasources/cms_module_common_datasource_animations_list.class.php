<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_animations_list.class.php,v 1.2 2021/04/01 09:18:24 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_animations_list extends cms_module_common_datasource_list {
	
	public function __construct($id = 0) {
		parent::__construct($id);
		$this->limitable = true;
		$this->sortable = true;
	}
	
	/*
	 * On définit les critères de tri utilisables pour cette source de données
	 */
	protected function get_sort_criterias() {
		return array (
			'id_animation',
		    'name',
		    'start_date',
		    'end_date'
		);
	}
	
	protected function sort_animations($animations) {
	    if (empty($animations)) {
	        return false;
	    }
	    
	    foreach ($animations as $key => $animation_id) {
	        $animations[$key] = (int) $animation_id;
		}
		
		$query = "SELECT id_animation FROM anim_animations 
                  JOIN anim_events AS event ON num_event = id_event 
                  WHERE id_animation IN ('" . implode("','", $animations) . "') 
                  ORDER BY " . $this->parameters['sort_by'] . " " . $this->parameters['sort_order'];
		
		if (!empty($this->parameters['nb_max_elements'])) {
			$query .= " LIMIT " . (int) $this->parameters['nb_max_elements'];
		}
		
		$result = pmb_mysql_query($query);
		$return = array();
		if (pmb_mysql_num_rows($result)) {
			$return['title'] = "Liste d'animations";
			while ($row = pmb_mysql_fetch_object($result)) {
				$return['animations'][] = $row->id_animation;
			}
		}
		
		return $return;
	}
}