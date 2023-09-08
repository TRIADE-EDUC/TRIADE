<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_lang.class.php,v 1.1 2017-01-23 16:09:56 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_lang extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		if(!$this->parameters){
			$this->parameters = array();
		}
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_lang_id_lang'])."</label>
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
		$this->parameters = $this->get_value_from_form('lang_id');
		return parent ::save_form();
	}
	
	protected function gen_select(){
		global $include_path, $opac_show_languages;
		
		$show_languages = substr($opac_show_languages,0,1) ;
		$languages = explode(",",substr($opac_show_languages,2)) ;
		$langues = new XMLlist("$include_path/messages/languages.xml");
		$langues->analyser();
		$clang = $langues->table;
		for ($i=0; $i<sizeof($languages); $i++) {
			$lang_combo[$languages[$i]] = $clang[$languages[$i]] ;
		}
		$select = "
				<select name='".$this->get_form_value_name("lang_id")."[]' multiple='yes'>";
		if(count($lang_combo)){
			foreach ($lang_combo as $cle => $value) {
				$select.="
					<option value='".$cle."' ".(in_array($cle,$this->parameters) ? "selected='selected'" : "").">".$this->format_text($value)."</option>";
			}
		}else{
			$select.= "
					<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_lang_no_lang'])."</option>";
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