<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_selectors_tabs.class.php,v 1.2 2020/04/03 07:31:13 dgoron Exp $

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
    	
    	if(count($this->objects_ids)) {
    	    
    	    $instance_elements_list_ui = $this->get_instance_elements_list_ui();
    		$elements = $instance_elements_list_ui->get_elements_list();
    		print $begin_result_liste;
    		search_authorities::get_caddie_link();    		
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
}
?>