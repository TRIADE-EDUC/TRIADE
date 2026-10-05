<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_section.class.php,v 1.7 2016/09/20 14:33:53 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_section extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_section_id_section'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("id_section");
		return parent ::save_form();
	}
	
	protected function _recurse_parent_select($parent=0,$lvl=0){
		$opts = "";
		$rqt = "select id_section, section_title from cms_sections where section_num_parent = '".($parent*1)."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$opts.="
				<option value='".$row->id_section."' ".($this->parameters == $row->id_section ? "selected='selected'" : "").">".str_repeat("&nbsp;&nbsp;",$lvl).$this->format_text($row->section_title)."</option>";
				$opts.=$this->_recurse_parent_select($row->id_section,$lvl+1);
			}
		} else {
			if($lvl ==0) {
				$opts.= "
				<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_section_no_section'])."</option>";
			}
		}
		return $opts;
	}
	
	protected function gen_select(){
		$select = "
					<select name='".$this->get_form_value_name("id_section")."'>";
		$select.= $this->_recurse_parent_select();
		$select.= "
					</select>";
		return $select;
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}