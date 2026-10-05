<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_custom_fields.class.php,v 1.4.12.1 2025/02/18 16:14:44 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_custom_fields extends frbr_entity_common_datasource {

	protected $custom_list;

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_sub_datasources(){
	    if(static::class != 'frbr_entity_common_datasource_custom_fields') {
			return array();
		} else {
			return array(
					"frbr_entity_records_datasource_custom_fields",
					"frbr_entity_authors_datasource_custom_fields",
					"frbr_entity_categories_datasource_custom_fields",
					"frbr_entity_concepts_datasource_custom_fields",
					"frbr_entity_publishers_datasource_custom_fields",
					"frbr_entity_collections_datasource_custom_fields",
					"frbr_entity_subcollections_datasource_custom_fields",
					"frbr_entity_series_datasource_custom_fields",
					"frbr_entity_works_datasource_custom_fields",
					"frbr_entity_indexint_datasource_custom_fields",
					"frbr_entity_authperso_datasource_custom_fields"
			);
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

	protected function get_prefixes() {
		return array(
                'notices',
				'author',
				'categ',
				'publisher',
				'collection',
				'subcollection',
				'serie',
				'indexint',
				'skos',
				'tu',
				'authperso',
	            'expl'
		);
	}

	protected function get_custom_list() {
		if(!isset($this->custom_list)) {
			$this->custom_list = array();
			foreach ($this->get_prefixes() as $prefix) {
				$query = "select idchamp, titre, options, datatype from ".$prefix."_custom where type='query_auth' order by name";
				$result = pmb_mysql_query($query);
				while($row = pmb_mysql_fetch_assoc($result)) {
					$options = _parser_text_no_function_($row['options']);
					if($this->get_entity_type_from_data_type($options['OPTIONS'][0]['DATA_TYPE'][0]['value']) == $this->entity_type) {
						$this->custom_list[$prefix][] = $row;
					}
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
		$selector = "<select name='datanode_datasource_custom_field'>";
		foreach ($custom_list as $prefix=>$customs) {
			foreach ($customs as $custom) {
			    $selector .= "<option value='".$prefix."|||".$custom['idchamp']."|||".$custom['datatype']."' ".($this->parameters->prefix.'|||'.$this->parameters->id.'|||'.$this->parameters->datatype == $prefix."|||".$custom['idchamp']."|||".$custom['datatype'] ? "selected='selected'" : "").">".htmlentities($custom['titre'], ENT_QUOTES, $charset)."</option>";
			}
		}
		$selector .= "</select>";
		return $selector;
	}

	public function get_form(){
		$form = parent::get_form();
		if(static::class != 'frbr_entity_common_datasource_custom_fields') {
			$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='datasource_custom_fields'>".$this->format_text($this->msg['frbr_entity_common_datasource_custom_fields_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_custom_list_selector()."
				</div>
			</div>";
		}
		return $form;
	}

	public function save_form(){
		global $datanode_datasource_custom_field;
		$custom_field = explode('|||', $datanode_datasource_custom_field);
		$this->parameters->prefix = $custom_field[0];
		$this->parameters->id = $custom_field[1];
		$this->parameters->datatype = $custom_field[2];
		return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $datatype = 'integer';
	    $qid = "select datatype from ".$this->parameters->prefix."_custom where idchamp=".$this->parameters->id;
	    $rid = pmb_mysql_query($qid);
	    if($rid) {
	       $datatype = pmb_mysql_result($rid,0,0);
	    }
		$query = "select ".$this->parameters->prefix."_custom_".$datatype." as id, ".$this->parameters->prefix."_custom_origine as parent from ".$this->parameters->prefix."_custom_values where ".$this->parameters->prefix."_custom_champ = ".$this->parameters->id." and ".$this->parameters->prefix."_custom_origine in (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}

}