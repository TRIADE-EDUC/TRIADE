<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_used_in_custom_fields.class.php,v 1.5.10.1 2025/04/24 12:37:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_used_in_custom_fields extends frbr_entity_common_datasource {

	protected $custom_list;

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_sub_datasources(){
	    if(static::class != 'frbr_entity_common_datasource_used_in_custom_fields') {
			return array();
		}
	}

	protected function get_custom_list() {
		if(!isset($this->custom_list)) {
			$this->custom_list = array();
			$query = "SELECT idchamp, titre, options, datatype FROM ".$this->prefix."_custom WHERE type='query_auth'";
			if (!empty($this->parameters->authperso_id) && $this->prefix == "authperso") {
			    $query .= " AND num_type = ".$this->parameters->authperso_id;
			}
			$query .= " ORDER BY name";
			$result = pmb_mysql_query($query);
			while($row = pmb_mysql_fetch_assoc($result)) {
			    $options = _parser_text_no_function_($row['options']);
			    if($this->get_aut_type_from_entity_type($this->origin_entity) == $options['OPTIONS'][0]['DATA_TYPE'][0]['value']) {
					$this->custom_list[] = $row;
				}
			}
		}
		return $this->custom_list;
	}

	protected function get_custom_list_selector() {
		global $charset;

		if(!isset($this->parameters->prefix)) $this->parameters->prefix = '';
		if(!isset($this->parameters->id)) $this->parameters->id = 0;
		if(!isset($this->parameters->datatype)) $this->parameters->datatype = 'integer';

		$custom_list = $this->get_custom_list();
		$selector = "<select name='datanode_datasource_used_in_custom_field'>";
		foreach ($custom_list as $custom) {
		    $selector .= "<option value='".$this->prefix."|||".$custom['idchamp']."|||".$custom['datatype']."' ".($this->prefix.'|||'.$this->parameters->id.'|||'.$this->parameters->datatype == $this->prefix."|||".$custom['idchamp']."|||".$custom['datatype'] ? "selected='selected'" : "").">".htmlentities($custom['titre'], ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	public function get_form(){
		$form = parent::get_form();
		if(static::class != 'frbr_entity_common_datasource_used_in_custom_fields' && !empty($this->prefix)) {
			$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='datanode_datasource_used_in_custom_field'>".$this->format_text($this->msg['frbr_entity_common_datasource_used_in_custom_fields_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_custom_list_selector()."
				</div>
			</div>";
		}
		return $form;
	}

	public function save_form(){
		return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $query = "SELECT ".$this->parameters->prefix."_custom_origine AS id, ".$this->parameters->prefix."_custom_".$this->parameters->datatype." AS parent FROM ".$this->parameters->prefix."_custom_values WHERE ".$this->parameters->prefix."_custom_champ = ".$this->parameters->id." AND ".$this->parameters->prefix."_custom_".$this->parameters->datatype." IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}

	protected function get_aut_type_from_entity_type($type) {
	    global $authperso_num;
	    switch ($type) {
	        case 'authperso':
	            if (!empty($authperso_num)) {
	                return 1000 + intval($authperso_num);
	            }
	        default:
	            return parent::get_aut_type_from_entity_type($type);
	    }
    }
}