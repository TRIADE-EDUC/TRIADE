<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_parent_sections.class.php,v 1.3 2015-12-16 11:50:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class docwatch_selector_parent_sections
 * 
 */
class docwatch_selector_parent_sections extends docwatch_selector {
	
	public function get_value(){
		global $dbh;
		if(!count($this->value) && count($this->parameters['sections'])){
			$this->value = array();
			$query = "select distinct id_section from cms_sections where section_num_parent in(".implode(",",$this->parameters['sections']).")"; 
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->value[] = $row->id_section;
				}
			}
		}
		return $this->value;
	}
	
	public function get_form(){
		global $msg,$charset;
		$form ="
		<div class='row'>
			<div class='colonne3'>
				<label>".htmlentities($msg['dsi_docwatch_selector_parent_sections_select'],ENT_QUOTES,$charset)."</label>
			</div> 
			<div class='colonne_suite'>".$this->gen_select()."
			</div>
		</div>		
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_parent_sections_select;
		$this->parameters['sections'] = $docwatch_selector_parent_sections_select;
	}
	
	protected function _recurse_parent_select($parent=0,$lvl=0){
		global $charset;
		$opts = "";
		$rqt = "select id_section, section_title from cms_sections where section_num_parent = '".$parent."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$opts.="
				<option value='".$row->id_section."' ".(in_array($row->id_section,$this->parameters['sections']) ? "selected='selected'" : "").">".str_repeat("&nbsp;&nbsp;",$lvl).htmlentities($row->section_title,ENT_QUOTES,$charset)."</option>";
				$opts.=$this->_recurse_parent_select($row->id_section,$lvl+1);
			}
		}
		return $opts;
	}
	
	protected function gen_select(){
		if(!$this->id){
			$this->parameters = array();
			$this->parameters['sections'] = array();
		}
		$select = "
				<select name='docwatch_selector_parent_sections_select[]' multiple='yes'>";
		$select.= $this->_recurse_parent_select();
		$select.= "
				</select>";
		return $select;
	}
	
	
} // end of docwatch_selector_parent_sections
