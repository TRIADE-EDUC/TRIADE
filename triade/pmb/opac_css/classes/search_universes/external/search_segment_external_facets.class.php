<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_external_facets.class.php,v 1.3 2023/06/30 14:18:50 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//require_once($class_path."/search_universes/search_segment_facets.class.php");

class search_segment_external_facets extends search_segment_facets {
	
	
	protected function get_query() {
		return "SELECT * FROM facettes_external
					JOIN search_segments_facets ON search_segments_facets.num_facet = facettes_external.id_facette
					WHERE num_search_segment = ".$this->num_segment."
					ORDER BY search_segment_facet_order";
	}
	
	public static function make_facette_search_env() {
	    global $search;
	    global $check_facette;
	        
	    //facettes_external::make_facette_search_env();
	    //creation des globales => parametres de recherche
	    $n = count($search);
	    if (is_array($check_facette)) { 
	        $fields = [];
	        foreach($check_facette as $facet){
	            if(!isset($fields[$facet[2]][$facet[3]])){
	                $facet[1] = array($facet[1]);
                    $fields[$facet[2]][$facet[3]] = $facet;
	            }else{
	                $fields[$facet[2]][$facet[3]][1][] = $facet[1];
	            }
	        }
	        $i = 0;
	        foreach($fields as $field => $subfields){
	            foreach($subfields as $subfield){
	                $search[] = "s_5";
	                $fieldname = "field_".($i+$n)."_s_5";
	                global ${$fieldname};
	                ${$fieldname} = array($subfield);
	                $op = "op_".($i+$n)."_s_5";
	                $op_ = "EQ";
	                global ${$op};
	                ${$op}=$op_;
	                
	                $inter = "inter_".($i+$n)."_s_5";
	                $inter_ = "and";
	                global ${$inter};
	                ${$inter} = $inter_;
	                $i++;
	            }
	        }
	    }
	}
	
	protected function create_search_environment() {
	    $search_class = new search("search_fields_unimarc_gestion");
	    $search_class->json_decode_search($this->get_segment_search());
	}
	
	public function get_clicked() {
	    if(!isset($this->clicked)) {
	       global $search;
	       $this->clicked = array();
    	    //on reconstruit la session des facettes pour que l'affichage fonctionne comme avant
	       if (is_array($search) && count($search)) {
	           foreach ($search as $i => $value) {
	               if ($value == 's_5') {
	                   $field = "field_".$i."_s_5";
	                   global ${$field};
	                   if (!empty(${$field})) {
	                       $this->clicked[] = ${$field};
	                   }
	               }
	           }
	       }
	    }
        return $this->clicked;
	}
	
	protected function get_query_by_facette($id_critere, $id_ss_critere, $type = "notices_externes") {
	    	    
	    $sub_queries = facettes_external::get_sub_queries($id_critere, $id_ss_critere);
	    $sources = facettes_external::get_selected_sources();
	    $selected_sources = facettes_external::set_selected_sources($sources);
	    $queries = array();
	    foreach ($selected_sources as $source) {
	        $queries [] = "SELECT value, recid FROM entrepot_source_".$source." WHERE recid IN (".$this->objects_ids.") AND ((".implode(') OR (', $sub_queries)."))";
	    }
	    
	    $query = "select value ,count(distinct recid) as nb_result from (".implode(' UNION ', $queries).") as sub GROUP BY value ORDER BY";

        return $query;
	}
}