<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watch_selector_watch.class.php,v 1.3 2022/03/10 08:22:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watch_selector_watch extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_watch_selector_watch_id_watch'>".$this->format_text($this->msg['cms_module_watch_selector_watch_id_watch'])."</label>
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
		$this->parameters = $this->get_value_from_form("id_watch");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$query= "select id_watch, watch_title from docwatch_watches order by watch_title";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("id_watch")."'>";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
						<option value='".$row->id_watch."' ".($row->id_watch == $this->parameters ? "selected='selected'" : "").">".$this->format_text($row->watch_title)."</option>";
			}
		}else{
			$select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_watch_selector_watch_no_watch'])."</option>";
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
}