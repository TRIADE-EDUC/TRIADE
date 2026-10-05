<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_datasource_works.class.php,v 1.5 2019/08/07 15:25:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authors_datasource_works extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
		if (!isset($this->parameters->author_type)) {
		    $this->parameters->author_type = -1;
		}
	}
	
	/**
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "SELECT DISTINCT responsability_tu_num AS id, responsability_tu_author_num AS parent FROM responsability_tu
				WHERE responsability_tu_author_num IN (".implode(',', $datas).")";
		if (!empty($this->parameters->author_function)) {
			$query .= " AND responsability_tu_fonction IN ('".implode("','", $this->parameters->author_function)."')";
		}
		if($this->parameters->author_type != -1){
		    $query .= " AND responsability_tu_type = ".$this->parameters->author_type;
		}
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function get_form() {
		if (!isset($this->parameters->author_function)) {
			$this->parameters->author_function = '';
		}
		$form = parent::get_form();
		$form.= "
            <div class='row'>
				<div class='colonne3'>
					<label for='datanode_author_function'>".$this->format_text($this->msg['frbr_entity_authors_datasource_authors_function'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_author_function_selector($this->parameters->author_function)."
				</div>
				<div class='colonne3'>
					<label for='datanode_author_type'>".$this->format_text($this->msg['frbr_entity_authors_datasource_authors_type'])."</label>
				</div>
				<div class='colonne-suite'>
                    <select name='datanode_author_type'>
                        <option value='-1' ".(-1 == $this->parameters->author_type ? "selected='selected'" :"").">".$this->format_text($this->msg['frbr_entity_authors_datasource_authors_type_all'])."</option>
                        <option value='0' ".(0 == $this->parameters->author_type ? "selected='selected'" :"")." >".$this->format_text($this->msg['frbr_entity_authors_datasource_authors_type_author'])."</option>
                        <option value='1' ".(1 == $this->parameters->author_type ? "selected='selected'" :"").">".$this->format_text($this->msg['frbr_entity_authors_datasource_authors_type_interpreter'])."</option>
                    </select>

				</div>
			</div>";
		return $form;
	}
	
	public function get_author_function_selector() {
		global $charset, $msg;
		
		$authors_function = marc_list_collection::get_instance('function');
		$selector = "<select name='datanode_author_function[]' id='datanode_author_function' multiple='yes'>";
		$options = '';
		foreach($authors_function->table as $code => $libelle){
			if ((is_array($this->parameters->author_function) && in_array($code, $this->parameters->author_function)) || ($code == $this->parameters->author_function)) {
				$options .= "<option value='".$code."' selected='selected'>".$libelle."</option>";
			} else {
				$options .= "<option value='".$code."'>".$libelle."</option>";
			}
		}
		$selector.= $options;
		$selector.= '</select>';
		return $selector;
	}
	
	public function save_form() {
		global $datanode_author_function;
		global $datanode_author_type;
		
		if(isset($datanode_author_function)){
			$this->parameters->author_function = $datanode_author_function;
		} else {
			unset($this->parameters->author_function);
		}
	    $this->parameters->author_type = $datanode_author_type;
		return parent::save_form();
	}
}