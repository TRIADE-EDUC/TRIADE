<?php
use Pmb\Common\Library\Navbar\Navbar;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_filters.class.php,v 1.1.4.3 2025/02/28 11:38:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_filters {
	
    protected $type;
    
    protected $search_mode;
    
    protected $elements;
    
    protected $elements_list_ui_class_name;
    
    protected $page = 1;
    
    protected $elements_list_nb_per_page = 20;
    
    protected $hidden_form_name = 'search_form';
	
    public function __construct($elements) {
        $this->elements = $elements;
    }
    
    protected function _init_elements_list_ui_class_name() {
        switch ($this->type) {
            case 'notices':
                $this->elements_list_ui_class_name = 'elements_records_list_ui';
                break;
            default:
                $this->elements_list_ui_class_name = 'elements_authorities_list_ui';
                break;
        }
    }
    
    public function filter_elements() {
        global $mode;
        
        if (empty($_SESSION['facette'])) {
            return;
        }
        //A retirer si le paramètre est ajouté côté gestion
        $pmb_facettes_operator = 'and';
        
        $prefix = '';
        switch($this->type){
            case 'notices':
                $plural_prefix = 'notices';
                $prefix = 'notice';
                $tempo_key_name = 'notice_id';
                break;
            default:
                $plural_prefix = 'authorities';
                $prefix = 'authority';
                $tempo_key_name = 'id_authority';
                break;
        }
        $filter_array = [];
        foreach ($_SESSION['facette'] as $facette) {
            $filter_array[] = $facette[0];
        }
        $t_ids=array();
        $ids = implode(',', $this->elements);
        if(is_array($filter_array)) {
            foreach ($filter_array as $v) {
                $filter_value = $v[1];
                $filter_field = $v[2];
                $filter_subfield = $v[3];
                
                switch ($mode) {
                    case 7:
                        $qs = facettes_external::get_filter_query_by_facette($filter_field, $filter_subfield, $filter_value);
                        if($ids) {
                            $qs .= ' where recid IN ('.$ids.')';
                        }
                        break;
                    default:
                        $qs = 'SELECT DISTINCT id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index WHERE code_champ = '.(intval($filter_field)).' AND code_ss_champ = '.(intval($filter_subfield)).' AND (';
                        foreach ($filter_value as $k2=>$v2) {
                            if ($k2) {
                                $qs .= ' OR ';
                            }
                            $qs .= 'value ="'.addslashes($v2).'"';
                        }
                        $qs .= ')';
                        if($ids) {
                            $qs .= ' and id_'.$prefix.' in ('.$ids.')';
                        }
                        break;
                }
                $rs = pmb_mysql_query($qs);
                
                //Opérateur "AND", on repart d'un tableau vide
                if($pmb_facettes_operator == 'and') {
                    $t_ids=array();
                    if(!pmb_mysql_num_rows($rs)) {
                        break;
                    }
                    while ($o=pmb_mysql_fetch_object($rs)) {
                        $t_ids[]= $o->{'id_'.$prefix};
                    }
                    $ids = implode(',',$t_ids);
                } else {
                    while ($o=pmb_mysql_fetch_object($rs)) {
                        $t_ids[]= $o->{'id_'.$prefix};
                    }
                }
            }
        }
        session::set_value('filtered_search', [$this->type => [$this->search_mode => $ids]]);
        $this->elements = explode(',',$ids);
    }
    
    public function get_elements_list_ui() {
        $start = ($this->page-1)*$this->elements_list_nb_per_page;
        $elements = array_slice($this->elements, $start, $this->elements_list_nb_per_page);
        $this->_init_elements_list_ui_class_name();
        $elements_list_ui = new $this->elements_list_ui_class_name($elements, count($elements), false, [], $this->elements_list_nb_per_page);
        $elements_list_ui->add_context_parameter('in_search', '1');
        return $elements_list_ui->get_elements_list();
    }
    
    public function get_pager() {
        $navbar = new Navbar($this->page, count($this->elements), $this->elements_list_nb_per_page);
        $navbar->setOnclick('facettes_valid_facette("", !!page!!);');
        $navbar->setHiddenFormName($this->hidden_form_name);
        return $navbar->render();
    }
    
    public function set_type($type) {
        $this->type = $type;
    }
    
    public function set_search_mode($search_mode) {
        $this->search_mode = $search_mode;
    }
    
    public function set_elements_list_ui_class_name($elements_list_ui_class_name) {
        $this->elements_list_ui_class_name = $elements_list_ui_class_name;
    }
    
    public function set_page($page) {
        $this->page = intval($page);
    }
    
    public function set_elements_list_nb_per_page($elements_list_nb_per_page) {
        $this->elements_list_nb_per_page = intval($elements_list_nb_per_page);
    }
    
    public function set_hidden_form_name($hidden_form_name) {
        $this->hidden_form_name = $hidden_form_name;
    }
}

