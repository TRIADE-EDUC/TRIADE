<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_articles_type_article_generic.class.php,v 1.1 2019/03/19 14:38:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class docwatch_selector_articles_type_article_generic extends docwatch_selector {
	
	public function get_form(){
		global $msg,$charset;
		$form ="
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['docwatch_selector_articles_type_article_generic_select'])."</label>
			</div>
			<div class='colonne-suite'> 
				".$this->gen_select()."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['docwatch_selector_articles_type_article_generic_field_value'])."</label>
			</div>
			<div class='colonne-suite'> 
				<input type='text' id='".$this->get_form_value_name("field_value")."' name='".$this->get_form_value_name("field_value")."' value='".$this->format_text($this->parameters["type_editorial_field_value"])."' />
			</div>
		</div>
		";
		return $form;
	}
	
	public function set_from_form(){
		$this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
		$this->parameters["type_editorial_field_value"] = $this->get_value_from_form("field_value");
	}
	
	protected function gen_select(){
		$select="<select name='".$this->get_form_value_name("select_field")."' >";		
		$query = "select * from cms_editorial_types, cms_editorial_custom where editorial_type_element = 'article_generic' and id_editorial_type=num_type order by titre ";
		$result = pmb_mysql_query($query);
		$select.= "
				<option value='0'>".$this->format_text($this->msg['docwatch_selector_articles_type_article_generic_select_invit'])."</option>";		
		if(pmb_mysql_num_rows($result)){
			while($r = pmb_mysql_fetch_object($result)){
				$select.="
				<option value='".$r->idchamp."'".($r->idchamp==$this->parameters["type_editorial_field"] ? "selected='selected'" : "").">".$this->format_text($r->titre)."</option>";
			}
		}else{
			$select.= "
				<option value ='0'>".$this->format_text($this->msg['docwatch_selector_articles_type_article_generic_no'])."</option>";
		}		
		$select.= "</select>";
		
		return $select;
	}
	
	public function get_value(){
		// recup id de l'article dans le sous selecteur
		if(!$this->value){
			$this->value = array();
			$query = "select id_editorial_type from cms_editorial_types where editorial_type_element = 'article_generic'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$fields = new cms_editorial_parametres_perso(pmb_mysql_result($result,0,0));
				if(!empty($this->parameters["type_editorial_field"])) {
					$datatype = $fields::$st_fields["cms_editorial_".$this->parameters["type_editorial"]][$this->parameters["type_editorial_field"]]['DATATYPE'];
					$query = "select distinct cms_editorial_custom_origine as id from cms_editorial_custom_values
					where cms_editorial_custom_champ = '".$this->parameters["type_editorial_field"]."'
					and cms_editorial_custom_".$datatype." = '".addslashes($this->parameters["type_editorial_field_value"])."'";
					$result = pmb_mysql_query($query);
					if($result && pmb_mysql_num_rows($result)) {
						while($row = pmb_mysql_fetch_object($result)) {
							$this->value[] = $row->id;
						}
					}
				}	
			}	
		}
		return $this->value;
	}
}
