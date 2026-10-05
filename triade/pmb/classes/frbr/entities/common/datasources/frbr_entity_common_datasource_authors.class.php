<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_authors.class.php,v 1.3 2021/03/08 15:39:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_authors extends frbr_entity_common_datasource {
    
	public function __construct($id=0){
		$this->entity_type = 'authors';
		parent::__construct($id); 
	}
	
	public function get_author_function_selector($selected = array()) {
	    global $charset, $msg;
	    
	    $authors_function = marc_list_collection::get_instance('function');
	    $selector = "<select name='datanode_author_function[]' id='datanode_author_function' multiple='yes'>";
	    $options = '';
	    foreach($authors_function->table as $code => $libelle){
	        if ((is_array($selected) && in_array($code, $selected)) || ($code == $selected)) {
	            $options .= "<option value='".$code."' selected='selected'>".$libelle."</option>";
	        } else {
	            $options .= "<option value='".$code."'>".$libelle."</option>";
	        }
	    }
	    $selector.= $options;
	    $selector.= '</select>';
	    return $selector;
	}
	
	public function save_form() {
	    global $datanode_author_function;
	    if(isset($datanode_author_function)){
	        $this->parameters->author_function = $datanode_author_function;
	    } else {
	        unset($this->parameters->author_function);
	    }
	    return parent::save_form();
	}
	
	protected function get_label_from_group($group) {
	    $authors_function= marc_list_collection::get_instance('function');
        if (isset($authors_function->table[$group])) {
            return $authors_function->table[$group];
        }
	    return $this->msg['frbr_entity_common_datasource_authors_without_function'];
	}
}