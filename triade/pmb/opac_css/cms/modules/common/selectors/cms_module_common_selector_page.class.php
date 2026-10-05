<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_page.class.php,v 1.9 2017/11/30 10:53:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_page extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		
		if(!is_array($this->parameters) && $this->parameters){
			$temp= array();
			if($this->parameters)$temp[0]=$this->parameters;
			$this->parameters=$temp;
		}
	}
	
	public function get_form(){
		//si on est sur une page de type Page en création de cadre, on propose la condition pré-remplie...
		if($this->cms_build_env['lvl'] == "cmspage"){
			if(!$this->id){
				$this->parameters[] = (isset($this->cms_build_env['get']['pageid']) ? $this->cms_build_env['get']['pageid'] : '');
			}
		}
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_page_id_page'])."</label>
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
		$this->parameters = $this->get_value_from_form("id_page");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$query= "select id_page, page_name from cms_pages order by page_name";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("id_page")."[]' multiple='yes'>";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
						<option value='".$row->id_page."' ".(in_array($row->id_page,$this->parameters) ? "selected='selected'" : "").">".$this->format_text($row->page_name)."</option>";
			}
		}else{
			$select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_page_no_page'])."</option>";
		}
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
	
	public function get_human_description_selector(){
		$description = "";
		if(is_array($this->parameters)) {
			$query= "select id_page, page_name from cms_pages order by page_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){			
				while($row = pmb_mysql_fetch_object($result)){
					if(in_array($row->id_page,$this->parameters)){
						if(array_search($row->id_page, $this->parameters) == 0){
							$description .= $this->format_text($row->page_name);
						}else{
							$description .= ", ".$this->format_text($row->page_name);
						}			
					}
				}
			}
		}
		return $description;		
	}
}