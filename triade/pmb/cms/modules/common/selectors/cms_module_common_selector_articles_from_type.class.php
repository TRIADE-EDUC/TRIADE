<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_articles_from_type.class.php,v 1.2 2017/10/17 10:22:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_articles_from_type extends cms_module_common_selector {
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_articles_from_type'])."</label>
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
		$this->parameters = $this->get_value_from_form("articles_type");
		return parent::save_form();
	}
	
	protected function gen_select(){
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters = 0;
		}
		
		$select = "
					<select name='".$this->get_form_value_name("articles_type")."'>";
			$select.="
						<option value='0' ".(($this->parameters == 0) ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_selector_articles_from_type_all_articles'])."</option>";
		
		$query = "select id_editorial_type, editorial_type_label from cms_editorial_types where editorial_type_element = 'article' order by editorial_type_label";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				$select.="
						<option value='".$row['id_editorial_type']."' ".(($this->parameters == $row['id_editorial_type']) ? "selected='selected'" : "").">".$this->format_text($row['editorial_type_label'])."</option>";
			}
		}
		$select.= "
			</select>";
		return $select;
	}
	
	public function get_value(){
		if(!$this->value){
			if (empty($this->parameters)) {
				$this->parameters = 0;
			}
			$query = 'select id_article from cms_articles';
			if ($this->parameters) {
				$query.= ' where article_num_type = '.$this->parameters;
			}
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}