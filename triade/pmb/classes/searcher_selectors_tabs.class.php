<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_selectors_tabs.class.php,v 1.8 2021/02/09 11:09:52 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/searcher_tabs.class.php");
require_once($class_path."/elements_list/elements_authorities_selectors_list_ui.class.php");
require_once($class_path."/elements_list/elements_records_selectors_list_ui.class.php");

class searcher_selectors_tabs extends searcher_tabs {
    
    public function show_result() {
    	global $begin_result_liste;
    	global $end_result_liste;
    	
    	print $this->make_hidden_form();
    	print $this->make_human_query();
    	if(is_array($this->objects_ids) && count($this->objects_ids)) {
    		$instance_elements_list_ui = $this->get_instance_elements_list_ui();
    		$elements = $instance_elements_list_ui->get_elements_list();
    		print $begin_result_liste;
    		search_authorities::get_caddie_link();
    		$tab=$this->get_current_tab();
    		search_authorities::get_sort_link(count($this->objects_ids), $tab['OBJECTS_TYPE'], true);
    		print $elements;
    		print $end_result_liste;
    		$this->pager();
    	}
    }
    
    public function proceed_search() {
    	$tab=$this->get_current_tab();
    	if($this->is_multi_search_criteria()){
    		$sc=$this->get_instance_search();
    		$sc->add_context_parameter('in_selector', true);
    		$sc->set_elements_list_ui_class_name('elements_'.$this->xml_file.'_selectors_list_ui');
    		$sc->reduct_search();
    		$this->set_session_history($sc->make_human_query(), $tab, "QUERY");
    		print $sc->show_results($this->url_target."&mode=".$tab['MODE']."&action=search", $this->url_target."&mode=".$tab['MODE'], true, '', true );
    		$this->set_session_history($sc->make_human_query(), $tab, $this->get_type());
    	} else {
    		$this->search();
    		$this->set_session_history($this->make_human_query(true), $tab, "QUERY");
    		print $this->show_result();
    		$this->set_session_history($this->make_human_query(true), $tab, $this->get_type(), "simple");
    	}
    }
    
    public function get_instance_elements_list_ui() {
    	switch ($this->xml_file) {
    		case 'authorities':
    			return new elements_authorities_selectors_list_ui($this->objects_ids, $this->search_nb_results, 1);
    			break;
    		case 'records':
    			return new elements_records_selectors_list_ui($this->objects_ids, $this->search_nb_results, 1);
    			break;
    	}
    } 
        
    public function get_default_selector_concept_mode($concept_schemes){
        if(!is_array($concept_schemes)){
            $concept_schemes = [$concept_schemes];
        }
        $first_found = 0;
        foreach($this->tabs as $mode => $tab){
            if(in_array($tab['SHOW_IN_SELECTOR'],['yes','only'])){
                if($tab['OBJECTS_TYPE'] == 'concepts'){  
                    if($first_found === 0){
                        $first_found = $mode;
                    }
                    if($tab['VARVIS']){
                        for($i=0 ; $i<count($tab['VARVIS']) ; $i++){
                            if($tab['VARVIS'][$i]['NAME'] == 'concept_scheme'){
                                if(count($concept_schemes) == 0){
                                    if($mode == $this->get_default_selector_mode()){
                                        return $mode;
                                    }
                                }
                                foreach($tab['VARVIS'][$i]['VALUE'] as $id => $visibility){
                                    if(in_array($id,$concept_schemes) && $visibility){
                                        return $mode;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($first_found === 0){
            foreach($this->tabs as $mode => $tab){
                if(in_array($tab['SHOW_IN_SELECTOR'],['yes','only'])){
                    $first_found = $mode;
                    break;
                }
            }
        }
        return $first_found;
    }
}
?>