<?php
// +-------------------------------------------------+
// © 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_contributions_list_ui.class.php,v 1.4 2023/05/02 12:54:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class elements_contributions_list_ui extends elements_concepts_list_ui {

    protected function generate_elements_list()
    {
        $elements_list = '';
        $recherche_ajax_mode = 0;
        $nb = 0;
        if (is_array($this->contents)) {
            foreach ($this->contents as $element_id) {
                if (!in_array($element_id, $this->parent_path)) {
                    $this->parent_path[] = $element_id;
                    if (!$recherche_ajax_mode && ($nb++ > 5)) {
                        $recherche_ajax_mode = 1;
                    }
                    $elements_list .= $this->generate_element($element_id, $recherche_ajax_mode);
                    array_pop($this->parent_path);
                }
            }
        }
        return $elements_list;
    }
    
    protected function generate_element($element_id, $recherche_ajax_mode = 0)
    {
        global $iframe, $caller;
        $results = [];
        if (!isset($this->searcher_instance)) {
            $this->searcher_instance = new searcher_contributions('');
        }
        
        $query = "select ?displayLabel where {
			<$element_id> pmb:isbd ?displayLabel
		}";
        if (!empty($this->searcher_instance->get_datastore()->query($query))) {
            if (!empty($this->searcher_instance->get_datastore()->get_result())) {
                $results[$element_id] = $this->searcher_instance->get_datastore()->get_result();
            }
        }
        
        if (empty($results)) {
            $query = "select ?displayLabel where {
    			<$element_id> pmb:displayLabel ?displayLabel
    		}";
            if (!empty($this->searcher_instance->get_datastore()->query($query))) {
                if (!empty($this->searcher_instance->get_datastore()->get_result())) {
                    $results[$element_id] = $this->searcher_instance->get_datastore()->get_result();
                }
            }
        }
        foreach ($results as $uri => $result) {
            return "<div class='notice-parent'>
                        <a href='#' data-element-id='$uri' data-element-type='' onclick=\"set_parent('".(($iframe && $caller) ? $caller : '')."', '$uri', '".$result[0]->displayLabel."', '')\" class='contribution_result'>
    	                       ".$result[0]->displayLabel."
                        </a>
                    </div>";
        }
        return '';
    }
}