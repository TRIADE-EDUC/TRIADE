<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_datasource_authorities.class.php,v 1.3 2017/10/17 10:22:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice.class.php');
require_once($class_path.'/parametres_perso.class.php');

class cms_module_timeline_datasource_authorities extends cms_module_timeline_datasource_generic{
	
	protected $entity_type = 'authorities';
	
	protected function get_full_values($ids){
		$events = array();
		foreach($ids as $id){
			$authority = new authority($id);
			$event = [];

			if(!empty($this->parameters['timeline_fields'])){
				foreach($this->parameters['timeline_fields'] as $field_name => $field_value){
					if(strpos($field_value, 'c_perso') !== false){
						$field_value = explode('c_perso_', $field_value)[1];
						$event[$field_name] = $this->get_cp_value($field_value, $authority->get_num_object());
					}else{
						$event[$field_name] = $authority->{$field_value};
					}
				}
			}
			$events[] = $event;
		}
		return $events;
		
	}
	
}