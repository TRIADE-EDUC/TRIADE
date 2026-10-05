<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_search_universes.class.php,v 1.1 2020/09/14 13:10:52 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/search_universes/search_segment.class.php');

class cms_module_common_condition_search_universes extends cms_module_common_condition{
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_search_universes",
		);
	}
	
	public function check_condition(){
	    global $lvl, $id;
	    
	    $selector = $this->get_selected_selector();
	    if(is_object($selector) ) {
	        //récupere les valeur sélectionnées en gestion
	        $values = $selector->get_value();
	        
	        // si page d'univers de recherche
	        if ($lvl=="search_universe") {
    	        //on regarde si le lecteur est autorisé à accéder aux informations de ce cadre...
    	        if(is_array($values)){
    	            foreach($values as $value){
    	                if($id == $value){
    	                    return true;
    	                }
    	            }
    	        }
	        }
	        
	        //si page de segment de recherche 
	        elseif ($lvl=="search_segment"){
	            $segment = search_segment::get_instance($id);
	            $num_universe = $segment->get_num_universe();
	            foreach($values as $value){
	                if($num_universe == $value){
	                    return true;
	                }
	            }
	        }
	    }
		return false;
	}
}