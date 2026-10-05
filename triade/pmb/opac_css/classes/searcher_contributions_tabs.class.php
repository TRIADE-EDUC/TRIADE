<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_contributions_tabs.class.php,v 1.3 2021/01/14 13:58:49 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/elements_list/elements_contributions_list_ui.class.php";

class searcher_contributions_tabs extends searcher_tabs {
      
    protected function search() {
        global $page, $nb_per_page, $id_empr;
        
        $values = $this->get_values_from_form();
        
        $searcher_contributions = new searcher_contributions($values);
        $searcher_contributions->set_empr_id($id_empr);
        $authperso_num = 0;
        if ($values['SEARCHFIELDS'][0]['type'] == 'authperso'){
            $authperso_num = $values['SEARCHFIELDS'][0]['mode']-1000;
        }
        $searcher_contributions->set_rdf_type(contribution_area_forms_controller::get_rdf_type_from_type($values['SEARCHFIELDS'][0]['type'], $authperso_num));

        $this->objects_ids = $searcher_contributions->get_sorted_result('default', intval($page) * intval($nb_per_page), $nb_per_page);
        $this->search_nb_results = $searcher_contributions->get_nb_results();
    }
    
    private function get_values_from_form() {
        
        $data = array();
        $tab=$this->get_current_tab();
        foreach ($tab['SEARCHFIELDS'] as $search_field) {
            $t = array();
            $t['id'] = $search_field['ID'];
            $t['values'] = $this->get_values_from_field($this->get_field_name($search_field, 'search'));
            $t['class'] = (isset($search_field['CLASS']) ? $search_field['CLASS'] : '');
            $t['type'] = $search_field['TYPE'];
            $t['mode'] = (isset($search_field['MODE']) ? $search_field['MODE'] : (isset($tab['MODE']) ? $tab['MODE'] : ''));
            $t['query'] = (isset($search_field['QUERY']) ? $search_field['QUERY'] : '');
            if (isset($search_field['FIELDRESTRICT']) && is_array($search_field['FIELDRESTRICT'])) {
                $t['fieldrestrict'] = $search_field['FIELDRESTRICT'];
            }
            if (isset($search_field['QUERYID'])) {
                $t['queryid'] = $search_field['QUERYID'];
            }
            if (isset($search_field['QUERYFILTER'])) {
                $t['queryfilter'] = $search_field['QUERYFILTER'];
            }
            if (isset($search_field['FIELDCONTRIBUTION'])) {
                $t['fieldcontribution'] = $search_field['FIELDCONTRIBUTION'];
            }
            $data['SEARCHFIELDS'][]= $t;
        }
        foreach ($tab['FILTERFIELDS'] as $filter_field) {
            $t = array();
            $t['id'] = $filter_field['ID'];
            if ($filter_field['HIDDEN']) {
                $t['values'] = explode(',', $filter_field['VALUE'][0]['value']);
            } else {
                $t['values'] = $this->get_values_from_field($this->get_field_name($filter_field, 'filter'));
            }
            $t['globalvar'] = $filter_field['GLOBALVAR'][0]['value'];
            $t['multiple'] = (isset($filter_field['INPUT_OPTIONS']['MULTIPLE']) ? $filter_field['INPUT_OPTIONS']['MULTIPLE'] : '');
            if (isset($search_field['FIELDCONTRIBUTION'])) {
                $t['fieldcontribution'] = $search_field['FIELDCONTRIBUTION'];
            }
            $data['FILTERFIELDS'][]= $t;
        }
        return $data;
    }
    
    public function show_result() {
        global $begin_result_liste;
        global $end_result_liste;
        print $this->make_hidden_form('store_search_contribution');
        print $this->make_human_query();
        if (is_array($this->objects_ids) && count($this->objects_ids)) {
            $instance_elements_list_ui = new elements_contributions_list_ui($this->objects_ids, $this->search_nb_results, 1);
            $instance_elements_list_ui->add_context_parameter('in_search', '1');
            $elements = $instance_elements_list_ui->get_elements_list();
            print $begin_result_liste;
            print $elements;
            print $end_result_liste;
            $this->pager('store_search_contribution');
        }
    }
}
