<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_external_search_result.class.php,v 1.13 2021/11/12 10:19:21 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path."/search_universes/search_segment_search_result.class.php";

class search_segment_external_search_result extends search_segment_search_result {
	private $undisplayed_search_index = [];
	private $json_search;
    
	protected function prepare_segment_search($is_sub_rmc){
	    global $refine_user_rmc;
	    global $refine_user_query;
	    global $search;
	    global $deleted_search_nb;
	    global $es;
	    global $new_search;

	    if(!is_object($es)){
	        $es = search::get_instance('search_fields_unimarc_gestion');
	    }

	    if (!is_array($search) || (!empty($new_search) && !$is_sub_rmc)) {
	        $search = array();
	    }

        search_universe::$start_search["external"] = true;
        
	    // Traitement special pour la Human query
	    if (search_universe::$start_search["type"] == "extended") {
	        $this->add_special_search_index(11, stripslashes(search_universe::$start_search["query"]), "");
	        search_universe::$start_search["human_query"] = $es->make_human_query();
	        $es->push();
	    }
	    
	    //search_universes_history::update_json_search_with_history();
	    if (!empty(search_universe::$start_search["segment_json_search"]) && empty($new_search) && !$is_sub_rmc) {
	        $es->json_decode_search(stripslashes(search_universe::$start_search["segment_json_search"]));
	    }
	    
	    if (!in_array('s_2', $search)) {
	        $es->json_decode_search($this->segment->get_set()->get_data_set());
	    	if (search_universe::$start_search["query"]) {
	    	    if (search_universe::$start_search["type"] == "extended") {
	    	        $es->unserialize_search(stripslashes(search_universe::$start_search["query"]), true);
	    	    }
	    	    if (search_universe::$start_search["type"] == "simple") {
	    	        $user_query_mc = combine_search::simple_search_to_mc(stripslashes(search_universe::$start_search["query"]), true, $this->get_type_from_segment());
	    	        $es->json_decode_search($user_query_mc);
	    	    }
	    	}
	    }
	    
	    //affinage
	    if (!empty($refine_user_rmc)) {
	        $es->unserialize_search(stripslashes($refine_user_rmc), true);
	    } elseif (!empty($refine_user_query)) {
	        $user_query_mc = combine_search::simple_search_to_mc(stripslashes($refine_user_query), true, $this->get_type_from_segment());
	        $es->json_decode_search($user_query_mc);
	    }
	    
	    if (isset($deleted_search_nb)) {
	    	$es->delete_search($deleted_search_nb);
	    	$this->json_search = $es->json_encode_search();
	    }
	    
	    $this->init_global_universe_id();
	}
	
	public function get_display_facets() {
	    global $es, $base_path;
	    global $search;
	    
	    $es->push();
	    $es->json_decode_search($this->segment->get_set()->get_data_set());
	    
	    if (search_universe::$start_search["type"] == "extended") {
	        $es->unserialize_search(stripslashes(search_universe::$start_search["query"]), true);
	    }
	    
	    $this->undisplayed_search_index = array_keys($search);
	    $es->pull();
	    
	    search_universes_history::$undisplayed_search_index = $this->undisplayed_search_index;
	    
	    $facettes_tpl = '';
	    $tab_result = $this->init_session_facets();
	    $segment_facets = search_segment_facets::get_instance('', $this->segment->get_id());
	    //$segment_facets->set_num_segment($this->segment->get_id());
	    $segment_facets->set_segment_search($es->json_encode_search());
	    //$segment_facets->set_segment_search($this->json_search);
	    //$es->json_decode_search($this->json_search);
	    $content = $es->make_segment_search_form($base_path.'/index.php?lvl=search_segment&id='.$this->segment->get_id().'&action=segment_results', 'form_values', "", true, $this->undisplayed_search_index);
	    $facettes_tpl .= $segment_facets->call_facets($content);
	    
	    return $facettes_tpl;
	}
	
	private function format_search($json_search) {
	    $format_search = "";
	    $segment_search = encoding_normalize::json_decode($json_search, true);
	    $tab_search = $segment_search["SEARCH"];
	    if (in_array("f_42", $segment_search["SEARCH"])) {
	        foreach ($tab_search as $i => $field) {
	            if ($i == 0) {
	                $segment_search[$i]["INTER"] = "and";
	            }
	            if ($field == "f_42") {
	                $temp_search = $segment_search[$i];
	                unset($segment_search[$i]);
	                array_unshift($segment_search, $temp_search);
	                unset($segment_search["SEARCH"][$i]);
	                array_unshift($segment_search["SEARCH"], "f_42");
	            }
	        }
	    }
	    $format_search = json_encode($segment_search);
	    return $format_search;
	}
	
	public function get_nb_results($ajax_mode = false, $is_sub_rmc = self::IS_NOT_SUB_RMC) {
	    global $search_type;
	    
	    $search_type="search_universes";
	    
	    $this->prepare_segment_search($is_sub_rmc);
	    //search_segment_facets::make_facette_search_env();
	    if (!$is_sub_rmc) {
            $this->checked_facette_search();
	        rec_history();
	    }
	    if ($ajax_mode) {
	        // Afin de paralléliser les recherches AJAX, on ferme la session PHP
	        session_write_close();
	    }
	    $this->get_searcher();
	    $nb_results = $this->searcher->get_nb_results();
	    search_universes_history::$segment_json_search = $this->json_search;
	    return $nb_results;
	}
}