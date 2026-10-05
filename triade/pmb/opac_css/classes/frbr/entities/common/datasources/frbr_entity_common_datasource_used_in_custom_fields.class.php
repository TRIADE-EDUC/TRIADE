<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_used_in_custom_fields.class.php,v 1.3 2021/01/20 11:01:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_used_in_custom_fields extends frbr_entity_common_datasource {
	
	protected $custom_list;
	
	protected $origin_entity;
	
	protected $prefix;
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_sub_datasources(){
	    if(static::class != 'frbr_entity_common_datasource_used_in_custom_fields') {
			return array();
		}
	}
	
	protected function get_entity_type_from_data_type($data_type) {
		switch ($data_type) {
			case 1:
				return 'authors';
				break;
			case 2:
				return 'categories';
				break;
			case 3:
				return 'publishers';
				break;
			case 4:
				return 'collections';
				break;
			case 5:
				return 'subcollections';
				break;
			case 6:
				return 'series';
				break;
			case 7:
				return 'indexint';
				break;
			case 8:
				return 'works';
				break;
			case 9:
				return 'concepts';
				break;
			default:
				return 'authperso';
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
		global $datanode_datasource_used_in_custom_field;
		$custom_field = explode('|||', $datanode_datasource_used_in_custom_field);
		$this->parameters->prefix = $custom_field[0];
		$this->parameters->id = $custom_field[1];
		$this->parameters->datatype = $custom_field[2];
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
		
	//cas particulier pour cette methode
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