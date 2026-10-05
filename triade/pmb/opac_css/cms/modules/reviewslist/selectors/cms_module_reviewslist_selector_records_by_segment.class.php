<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_selector_records_by_segment.class.php,v 1.2 2022/09/16 07:57:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_reviewslist_selector_records_by_segment extends cms_module_common_selector{
	
    public function __construct($id=0){
        parent::__construct($id);
    }
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_reviewslist_selector_records_by_segment_filter'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	protected function gen_select(){
        $select= "
		<select name='".$this->get_form_value_name("segment_filter")."'>
			<option value ='0'>".$this->format_text($this->msg['cms_module_reviewslist_selector_records_by_segment_no_segment'])."</option>
			<option value='1' ".(1 == $this->parameters['segment_filter'] ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_reviewslist_selector_records_by_segment_current_segment'])."</option>
       	<select>";
	    return $select;
	}
	
		
	
	public function save_form(){
		$this->parameters['segment_filter'] = $this->get_value_from_form("segment_filter");
		return parent ::save_form();
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
	    global $lvl, $id;
	    $id = intval($id);
		if(!isset($this->value)){
		    $this->value = "";
		    //segement courant
		    if ($this->parameters["segment_filter"] == 1) {
    		    if ($lvl == "search_segment") {
    		        $segment = search_segment::get_instance($id);
        	        if ($segment->get_type() == TYPE_NOTICE) {
        	            $this->value = $segment->get_search_result()->get_searcher()->get_result();
        	        }
    		    }
		    }
		}
		return $this->value;
	}
}