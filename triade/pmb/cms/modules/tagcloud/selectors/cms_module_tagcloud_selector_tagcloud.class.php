<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_selector_tagcloud.class.php,v 1.2.4.2 2025/04/30 12:52:10 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_tagcloud_selector_tagcloud extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_tagcloud_selector_tagcloud'])."</label>
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
		$this->parameters = $this->get_value_from_form("type_selector");
		return parent ::save_form();
	}

	protected function gen_select(){

		$select = "
		<select name='".$this->get_form_value_name("type_selector")."' onchange='load_type_selector_val_".$this->get_form_value_name("type_selector")."(this.value)'>
			<option value ='0'".(empty($this->parameters['type_selector']) ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_tagcloud_selector_tagcloud_no'])."</option>
			<option value ='1'".(isset($this->parameters['type_selector']) && $this->parameters['type_selector']==1 ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_tagcloud_selector_tagcloud_facette'])."</option>
			<option value ='2'".(isset($this->parameters['type_selector']) && $this->parameters['type_selector']==2 ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_tagcloud_selector_tagcloud_rmc'])."</option>
		</select>";

		$select.="
		<script type='text/javascript'>
			function load_type_selector_val_".$this->get_form_value_name("type_selector")."(selector_val){
				dojo.xhrGet({
					url : '".$this->get_ajax_link(array($this->class_name."_hash[]" => $this->hash))."&selector_val='+selector_val,
					handelAs : 'text/html',
					load : function(data){
						dojo.byId('".$this->get_form_value_name("type_selector")."_values').innerHTML = data;
					}
				});
			}
		</script>
		<div id='".$this->get_form_value_name("type_selector")."_values'></div>";

		if (isset($this->parameters['type_selector'])) {
			$select.="
			<script type='text/javascript'>
				load_type_selector_val_".$this->get_form_value_name("type_selector")."(".$this->parameters['type_selector'].");
			</script>";
		}
		return $select;
	}

	public function execute_ajax(){
		global $selector_val;
		$selector_val = intval($selector_val);

		$response = [
			'content' => '',
			'content-type' => 'text/html'
		];

		if ($selector_val==1){
			//Liste des facettes
			$response['content'] ="
			<div class='colonne3'>
				<label>".$this->format_text($this->msg['cms_module_tagcloud_selector_record_facette_label'])."</label>
			</div>
			<div class='colonne_suite'>";
		}elseif($selector_val==2){
			//Liste des Multicritères
			$response['content'] ="
			<div class='colonne3'>
				<label>".$this->format_text($this->msg['cms_module_tagcloud_selector_record_rmc_label'])."</label>
			</div>
			<div class='colonne_suite'>";
		} else {
			$response['content'] = "";
		}
		return $response;
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