<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_indexation_ui.class.php,v 1.2.2.6.2.1 2025/02/20 09:18:48 dgoron Exp $

use Pmb\Common\Helper\HelperEntities;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_indexation_ui extends list_ui {
	
	protected $indexation;
	
	protected $reference_table;
	
	protected $fields;
	
	protected $sub_fields;
	
	protected static $indexation_name = 'notices';
	
	protected $is_displayed_add_filters_block = false;
	
	protected $entities = array();
	
	protected $table_fields = array();
	
	protected function _get_query_primary_key() {
	    switch ($this->filters['entity_type']) {
	        case TYPE_NOTICE:
	            return 'id_notice';
	        default:
	            return 'id_authority';
	    }
	}
	
	protected function _get_query_table_name() {
	    switch ($this->filters['entity_type']) {
	        case TYPE_NOTICE:
	            switch ($this->filters['type']) {
	                case 'fields':
	                    return 'notices_fields_global_index';
	                case 'words':
	                    return 'notices_mots_global_index';
	            }
	            break;
	        default:
	            switch ($this->filters['type']) {
	                case 'fields':
	                    return 'authorities_fields_global_index';
	                case 'words':
	                    return 'authorities_words_global_index';
	            }
	            break;
	    }
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id' :
	            return 'id';
	        case 'i_value' :
	            return 'value';
	        case 'pond' :
	            return 'pond';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity_type' => 'indexation_entity_type',
						'field' => 'indexation_field',
						'sub_field' => 'indexation_sub_field',
						'id' => 'indexation_id',
						'i_value' => 'indexation_i_value',
				        'state' => 'indexation_state'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'type' => 'fields',
				'entity_type' => TYPE_NOTICE,
				'field' => '',
				'sub_field' => '',
				'id' => '',
				'i_value' => ''
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('id');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 50;
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'state', 'actions'
	    );
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('entity_type', 'integer');
		$this->set_filter_from_form('field', 'integer');
		$this->set_filter_from_form('sub_field', 'integer');
		$this->set_filter_from_form('id', 'integer');
		$this->set_filter_from_form('i_value');
		$this->set_filter_from_form('state');
		parent::set_filters_from_form();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('id', 'datatype', 'integer');
		$this->set_setting_column('pond', 'datatype', 'integer');
	}
	
	protected function get_search_filter_entity_type() {
		$options = HelperEntities::get_entities_labels();
		return $this->get_search_filter_simple_selection('', 'entity_type', '', $options);
	}
	
	protected function get_search_filter_field() {
		global $msg;
		
		$options = array();
		$fields = $this->get_fields();
		foreach ($fields as $code=>$field) {
			$options[$code] = $field['label'];
		}
		asort($options);
		return $this->get_search_filter_simple_selection('', 'field', $msg['all'], $options);
	}
	
	protected function get_search_filter_sub_field() {
		$options = array();
		$fields = $this->get_fields();
		if(!empty($fields[$this->filters['field']]['sub_fields'])) {
			foreach ($fields[$this->filters['field']]['sub_fields'] as $code=>$label) {
				$options[$code] = $label;
			}
		}
		asort($options);
		return $this->get_search_filter_simple_selection('', 'sub_field', '', $options);
	}
	
	protected function get_search_filter_id() {
		return $this->get_search_filter_simple_text('id');
	}
	
	protected function get_search_filter_i_value() {
		return $this->get_search_filter_simple_text('i_value');
	}
	
	protected function _add_query_filters() {
		if($this->filters['entity_type'] != TYPE_NOTICE) {
			$this->query_filters[] = 'type = '.authority::$type_table[$this->filters['entity_type']];
		}
		$this->_add_query_filter_simple_restriction('field', 'code_champ');
		$this->_add_query_filter_simple_restriction('sub_field', 'code_ss_champ');
		switch ($this->filters['entity_type']) {
			case TYPE_NOTICE:
				$this->_add_query_filter_simple_restriction('id', 'id_notice');
				break;
			default:
				$this->_add_query_filter_simple_restriction('id', 'id_authority');
				break;
		}
		$this->_add_query_filter_simple_restriction('i_value', 'value');
	}
	
	protected function _get_object_property_field($object) {
	    $fields = $this->get_fields();
	    return $fields[$object->id]['label'];
	}
	
	protected function _get_object_property_sub_field($object) {
		$fields = $this->get_fields();
		if(isset($fields[$object->code_champ]['sub_fields'][$object->code_ss_champ])) {
		    return $fields[$object->code_champ]['sub_fields'][$object->code_ss_champ];
		}
		return '';
	}
	
	protected function _get_object_property_i_value($object) {
		return $object->value;
	}
	
	protected function get_numbers_from_query($query) {
	    $all_results = [];
	    if ($query) {
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            while($row = pmb_mysql_fetch_array($result)){
	                $all_results[$row[1]][$row[2]] = $row[0];
	            }
	        }
	    }
	    return $all_results;
	}
	
	protected function _init_table_fields() {
	    if (empty($this->table_fields)) {
	        switch (static::$indexation_name) {
	            case 'notices':
	                $query = 'SELECT COUNT(DISTINCT(id_notice)) AS nb, code_champ, code_ss_champ FROM notices_mots_global_index GROUP BY code_champ, code_ss_champ';
	                break;
	            default:
	                $query = 'SELECT COUNT(DISTINCT(id_authority)) AS nb, code_champ, code_ss_champ FROM authorities_fields_global_index WHERE type = '.authority::$type_table[$this->filters['entity_type']].' GROUP BY code_champ, code_ss_champ';
	                break;
	        }
	        $this->table_fields = $this->get_numbers_from_query($query);
	    }
	}
	
	protected function _get_object_property_state($object) {
	    return false;
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg;
	    global $sphinx_active;
	    
	    $content = '';
	    switch($property) {
	        case 'state':
	            $state = $this->_get_object_property_state($object);
	            if ($state !== false) {
	                $origin_fields_number = $this->get_origin_fields_number($object);
	                if ($origin_fields_number !== false) {
	                    if($state == $origin_fields_number) {
	                        $content .= "<img src='images/tick.gif' style='height:16px;'/>";
	                        $content .= $state;
	                    } else {
	                        $content .= "<img src='images/info.gif' style='height:16px;'/>";
	                        $content .= $state." / ".$origin_fields_number;
	                    }
	                }
	            }
	            break;
	        case 'actions':
	            if (static::class == 'list_indexation_entities_ui') {
	                $location = static::get_controller_url_base()."&id=".$object->id;
	            } else {
	                $location = static::get_controller_url_base()."&field=".$object->id;
	            }
	            //Voir le détails
	            $content .= $this->get_interface_button_small($msg['see'], ['location' => $location]);
	            //Indexer
	            $content .= $this->get_interface_button_small($msg['index'], ['location' => $location."&action=reindex"]);
	            //Indexer dans Sphinx
	            if ($sphinx_active) {
	                $content .= $this->get_interface_button_small('[Sphinx] '.$msg["index"], ['location' => $location."&action=reindex_sphinx"]);
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	protected function _get_query_human_entity_type() {
		if(!empty($this->filters['entity_type'])) {
		    $labels = HelperEntities::get_entities_labels();
		    return $labels[$this->filters['entity_type']];
		}
		return '';
	}
	
	protected function _get_query_human_field() {
	    if (!empty($this->filters['field'])) {
	        $fields = $this->get_fields();
	        return $fields[$this->filters['field']]['label'];
	    }
	    return '';
	}
	
	protected function get_xml_file() {
	    global $include_path;
	    
	    $file = '';
	    switch (static::$indexation_name) {
	        case 'authors':
	        case 'categories':
	        case 'publishers':
	        case 'collections':
	        case 'subcollections':
	        case 'series':
	        case 'titres_uniformes':
	        case 'indexint':
	        case 'authperso':
	            $file = $include_path."/indexation/authorities/".static::$indexation_name."/champs_base_subst.xml";
	            if(!file_exists($file)){
	                $file = $include_path."/indexation/authorities/".static::$indexation_name."/champs_base.xml";
	            }
	            break;
	        default:
	            $file = $include_path."/indexation/".static::$indexation_name."/champs_base_subst.xml";
	            if(!file_exists($file)){
	                $file = $include_path."/indexation/".static::$indexation_name."/champs_base.xml";
	            }
	            break;
	    }
	    return $file;
	}
	
	protected function add_field($id, $label, $field=[]) {
	    if (empty($this->filters['field']) || $id == $this->filters['field']) {
    	    $this->fields[$id] = array(
    	        'id' => $id,
    	        'label' => $label,
    	        'pond' => $field['POND'] ?? '',
    	        'field' => $field
    	    );
	    }
	}
	
	protected function get_field($field) {
	    global $msg;
	    
	    $prev_tmp = '';
	    if(isset($field['TABLE'][0]['NAME'])){
	        $prev_tmp = (isset($msg[$field['TABLE'][0]['NAME']]) ? $msg[$field['TABLE'][0]['NAME']] : $field['TABLE'][0]['NAME']);
	    }
	    if(isset($msg[$field['NAME']]) && $tmp = $msg[$field['NAME']]){
	        $lib = $tmp;
	    }else{
	        $lib = $field['NAME'];
	    }
	    $field_id = intval($field['ID']);
	    
	    if ($this->has_subfields($field_id)) {
	        $subfields = $this->get_subfields($field_id);
	        if (!empty($subfields)) {
    	        foreach ($subfields as $subfield_key=>$subfield) {
    	            $subfield_id = ($field_id + $subfield_key);
    	            $this->add_field($subfield_id, $lib.($subfield['label'] ? ' - '.$subfield['label'] : ''), $subfield);
    	        }
	        }
	    } else {
	        $this->add_field($field_id, $lib.($prev_tmp ? ' - '.$prev_tmp : ''), $field);
	    }
	}
	
	protected function get_fields() {
		if(!isset($this->fields)) {
			$this->fields = array();
			
			$file = $this->get_xml_file();
			if (!file_exists($file)) {
			    return;
			}
			$fp=fopen($file,"r");
			if ($fp) {
			    $xml=fread($fp,filesize($file));
			}
			fclose($fp);
			$fields = _parser_text_no_function_($xml,"INDEXATION",$file);
			$this->reference_table = $fields['REFERENCE'][0]['value'];
			if (isset($fields['FIELD'])) {
			    for($i=0;$i<count($fields['FIELD']);$i++){
			        $this->get_field($fields['FIELD'][$i]);
			    }
			}
		}
		return $this->fields;
	}
	
	public function has_subfields($id){
	    $fields_ids = array( 
	        $this->get_custom_fields_id(),
	        $this->get_custom_expl_fields_id(),
	        $this->get_custom_explnum_fields_id()
	    );
	    if(in_array($id, $fields_ids)) {
	        return true;
	    }
	    if(($id > $this->get_authperso_start()) && ($id < ($this->get_authperso_start()+100))) {//on garde une plage de cent authperso différentes
	        return true;
	    }
	    return false;
	}
	
	public function get_subfields($id){
	    global $msg;
	    
	    $array_subfields = array();
	    
	    if($id == $this->get_custom_fields_id()) {
	        $result = pmb_mysql_query("select idchamp, titre from ".$this->get_custom_fields_table()."_custom where search = 1 order by titre asc");
	        while($row=pmb_mysql_fetch_object($result)){
	            $array_subfields[$row->idchamp] = array(
	                'type' => 'custom',
	                'table' => $this->get_custom_fields_table(),
	                'id' => $id,
	                'label' => $row->titre
	            );
	        }
	    } elseif($id == $this->get_custom_expl_fields_id()) {
	        $result = pmb_mysql_query("select idchamp, titre from expl_custom where search = 1 order by titre asc");
	        while($row=pmb_mysql_fetch_object($result)){
	            $array_subfields[$row->idchamp] = array(
	                'type' => 'custom',
	                'table' => 'expl',
	                'id' => $id,
	                'label' => $row->titre
	            );
	        }
	    } elseif($id == $this->get_custom_explnum_fields_id()) {
	        $result = pmb_mysql_query("select idchamp, titre from explnum_custom where search = 1 order by titre asc");
	        while($row=pmb_mysql_fetch_object($result)){
	            $array_subfields[$row->idchamp] = array(
	                'type' => 'custom',
	                'table' => 'explnum',
	                'id' => $id,
	                'label' => $row->titre
	            );
	        }
	    } elseif(($id > $this->get_authperso_start()) && ($id < ($this->get_authperso_start()+100))) {//on garde une plage de cent authperso différentes
	        $array_subfields[0] = $msg['facette_isbd'];
	        $result = pmb_mysql_query("select idchamp,titre from authperso_custom where num_type='".($id-$this->get_authperso_start())."' and search = 1 order by titre asc");
	        while($row=pmb_mysql_fetch_object($result)){
	            $array_subfields[$row->idchamp] = array(
	                'type' => 'custom',
	                'table' => 'authperso',
	                'id' => $id,
	                'label' => $row->titre
	            );
	        }
	    }
	    return $array_subfields;
	}
	
	protected function get_prefix_id() {
	    switch (static::$indexation_name) {
	        case 'notices':
	            return 0;
	        case 'authors':
	            return 1;
	        case 'categories':
	            return 2;
	        case 'publishers':
	            return 3;
	        case 'collections':
	            return 4;
	        case 'subcollections':
	            return 5;
	        case 'series':
	            return 6;
	        case 'titres_uniformes':
	            return 7;
	        case 'indexint':
	            return 8;
	        case 'authperso':
	            break;
	    }
	}
	
	protected function get_custom_fields_id() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'100';
	    } else {
	        return 100;
	    }
	}
	
	protected function get_custom_expl_fields_id() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'200';
	    } else {
	        return 200;
	    }
	}
	
	protected function get_custom_explnum_fields_id() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'300';
	    } else {
	        return 300;
	    }
	}
	
	protected function get_authperso_start() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'500';
	    } else {
	        return 1000;
	    }
	}
	
	protected function get_custom_fields_table() {
	    switch (static::$indexation_name) {
	        case 'notices':
	            return 'notices';
	        case 'authors':
	            return 'author';
	        case 'categories':
	            return 'categ';
	        case 'publishers':
	            return 'publisher';
	        case 'collections':
	            return 'collection';
	        case 'subcollections':
	            return 'subcollection';
	        case 'series':
	            return 'serie';
	        case 'titres_uniformes':
	            return 'tu';
	        case 'indexint':
	            return 'indexint';
	        case 'authperso':
	            return 'authperso';
	    }
	}
	
	public function get_indexation() {
		global $include_path;
		
		if(!isset($this->indexation)) {
			switch (static::$indexation_name) {
				case 'notices':
					$this->indexation = new indexation_record($include_path."/indexation/notices/champs_base.xml", 'notices');;
					break;
				default:
				    $this->indexation = indexations_collection::get_indexation(authority::get_const_type_object(static::$indexation_name));
					break;
			}
			$this->indexation->initialization();
		}
		return $this->indexation;
	}
	
	public static function set_indexation_name($indexation_name) {
	    static::$indexation_name = $indexation_name;
	}
	
	public function get_entity_number() {
	    global $id_authperso;
	    
	    $id_authperso = intval($id_authperso);
	    if(!isset($this->entities[static::$indexation_name]['number'])) {
	        if (static::$indexation_name == 'authperso' && !empty($id_authperso)) {
	            $query = entities::get_query_count(static::$indexation_name, $id_authperso);
	        } else {
	            $query = entities::get_query_count(static::$indexation_name);
	        }
	        $this->entities[static::$indexation_name]['number'] = pmb_mysql_result(pmb_mysql_query($query), 0);
	    }
	    return $this->entities[static::$indexation_name]['number'];
	}
	
	public static function get_controller_url_base() {
	    global $field;
	    
	    $field = intval($field);
	    return parent::get_controller_url_base()."&name=".static::$indexation_name.(!empty($field) ? "&field=".$field : "");
	}
	
}