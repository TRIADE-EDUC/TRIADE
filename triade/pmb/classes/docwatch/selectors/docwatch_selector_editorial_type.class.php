<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_editorial_type.class.php,v 1.2.14.1 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class docwatch_selector_editorial_type extends docwatch_selector{

	public $docwatch_selector_editorial_type;

	public function get_form(){
		$form=parent::get_form();

		if(!$this->id){
			$this->parameters = array(
					'type_editorial' => '',
					'type_editorial_field' => '',
					'type_editorial_field_value' => ''
			);
		}

		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['docwatch_selector_editorial_type_label'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>
			<div id='type_editorial_fields'>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['docwatch_selector_editorial_type_fields_label'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='".$this->get_form_value_name("select_field")."' >";
		$fields = new cms_editorial_parametres_perso($this->parameters["type_editorial"]);
		$form.= $fields->get_selector_options($this->parameters["type_editorial_field"]);
		$form.= "
						</select>
					</div>
				</div>";
		if($this->parameters["type_editorial"]) {
			$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['docwatch_selector_editorial_type_field_value'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='".$this->get_form_value_name("field_value")."' name='".$this->get_form_value_name("field_value")."' value='".$this->format_text($this->parameters["type_editorial_field_value"])."' />
				</div>
			</div>";
		}
		$form.="</div>";
		return $form;

	}

	protected function gen_select(){
		global $base_path;

		$select = "<select name='".$this->get_form_value_name($this->docwatch_selector_editorial_type)."'
			onchange=\"cms_type_fields(this.value);\" >
		";

		$types = new cms_editorial_types($this->docwatch_selector_editorial_type);
		$select.= $types->get_selector_options($this->parameters["type_editorial"]);
		$select.= "</select>
		<script type='text/javascript'>
			function cms_type_fields(id_type){
				dojo.xhrGet({
					url : '".$base_path."/ajax.php?module=dsi&categ=docwatch&sub=sources&action=get_sub_selector_form&class=".static::class."&id_type='+id_type,
					handelAs : 'text/html',
					load : function(data){
						dojo.byId('type_editorial_fields').innerHTML = data;
					}
				});
			}
		</script>";

		return $select;
	}

	public function get_ajax_form(){
		global $id_type;

		$fields = new cms_editorial_parametres_perso($id_type);
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['docwatch_selector_editorial_type_fields_label'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='".$this->get_form_value_name("select_field")."' >";
		$form.= $fields->get_selector_options($this->parameters["type_editorial_field"]);
		$form.= "
				</select>
			</div>
		</div>";
		if($id_type) {
			$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['docwatch_selector_editorial_type_field_value'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='".$this->get_form_value_name("field_value")."' name='".$this->get_form_value_name("field_value")."' value='' />
				</div>
			</div>";
		}
		return $form;
	}

	public function set_from_form(){
		$this->parameters["type_editorial"] = $this->get_value_from_form($this->docwatch_selector_editorial_type);
		$this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
		$this->parameters["type_editorial_field_value"] = $this->get_value_from_form("field_value");
	}

	public function get_value(){
		if(!$this->value){
			$this->value = array();
			$fields = new cms_editorial_parametres_perso($this->parameters["type_editorial"]);
			if(!empty($this->parameters["type_editorial_field"])) {
				$datatype = $fields::$st_fields["cms_editorial_".$this->parameters["type_editorial"]][$this->parameters["type_editorial_field"]]['DATATYPE'];
				if(empty($datatype)) {
					$generic_type = $fields->get_generic_type($this->docwatch_selector_editorial_type);
					$fields = new cms_editorial_parametres_perso($generic_type);
					$datatype = $fields::$st_fields["cms_editorial_".$generic_type][$this->parameters["type_editorial_field"]]['DATATYPE'];
				}
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
		return $this->value;
	}
}