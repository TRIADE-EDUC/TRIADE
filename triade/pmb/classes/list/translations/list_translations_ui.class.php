<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_translations_ui.class.php,v 1.12.6.2 2024/12/18 07:27:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_translations_ui extends list_ui {
	
    protected $primary_keys = [];
    
    protected static $translations_languages = [];
    
    public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
        parent::__construct($filters, $pager, $applied_sort);
        $this->_init_translations_languages();
    }
    
    protected function _get_query_base() {
        $query = 'SELECT translation.*, IFNULL(trans_small_text, trans_text) AS translation_to FROM translation';
        return $query;
    }
	
    public static function get_translation_from($row) {
        switch (true) {
            case strpos($row->trans_field, 'segment') === 0:
                $trans_field = 'search_'.$row->trans_field;
                break;
            case strpos($row->trans_field, 'universe') === 0:
                $trans_field = 'search_'.$row->trans_field;
                break;
            default:
                $trans_field = $row->trans_field;
                break;
        }
        $query = "SELECT ".$trans_field." FROM ".$row->trans_table." WHERE ".$row->trans_primary_key." = ".$row->trans_num;
        $result = pmb_mysql_query($query);
        if ($result) {
            return pmb_mysql_result($result, 0);
        } else {
            return '';
        }
        
    }
    
    protected function add_object($row) {
        if (static::class == 'list_translations_ui') {
            $row->code = $row->trans_table."_".$row->trans_field;
            if (empty($this->primary_keys[$row->trans_table])) {
                $query = "SHOW KEYS FROM ".addslashes($row->trans_table)." WHERE Key_name = 'PRIMARY'";
                $result = pmb_mysql_query($query);
                $this->primary_keys[$row->trans_table] = pmb_mysql_result($result, 0, 'Column_name');
            }
            $row->trans_primary_key = $this->primary_keys[$row->trans_table];
            
            $row->translation_from = static::get_translation_from($row);
            
            if ($row->translation_from != $row->translation_to) {
                $this->objects[] = $row;
            }
        } else {
            parent::add_object($row);
        }
    }
	
    protected function fetch_data() {
        parent::fetch_data();
        $this->pager['nb_results'] = count($this->objects);
    }
    
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array(
				'translation_to' => 'translation_to',
				'is_translated' => 'is_translated',
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $lang;
		$this->filters = array(
				'root' => 'pmb',
				'module' => 'common',
				'translation_from' => $lang,
				'translation_to' => '',
				'is_translated' => -1,
				'classifications' => array(),
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('translation_to');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
				        'code' => '663',
				        'translation_from' => 'translation_from',
    				    'trans_lang' => 'langue_sort',
    				    'translation_to' => 'translation_to',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function get_button_import() {
	    global $msg;
	    
	    return $this->get_button('import', $msg['import']);
	}
	
	protected function init_default_columns() {
	    $this->add_column('code');
		$this->add_column('translation_from');
	    $this->add_column('translation_to');
	    $this->add_column('trans_lang');
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 100;
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('translation_from');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('root');
		$this->set_filter_from_form('module');
		$this->set_filter_from_form('translation_from');
		$this->set_filter_from_form('translation_to');
		$this->set_filter_from_form('is_translated', 'integer');
		$this->set_filter_from_form('classifications');
	}
	
	protected function _init_translations_languages() {
	    global $include_path;
	    global $opac_show_languages, $lang;
	    
	    static::$translations_languages = [];
	    
	    $langues = new XMLlist($include_path."/messages/languages.xml");
	    $langues->analyser();
	    $clang = $langues->table;
	    $languages = explode(',', explode(' ', trim($opac_show_languages))[1]);
	    if(count($languages)) {
	        foreach ($languages as $language) {
	            static::$translations_languages[$language] = $clang[$language];
	        }
	    } else {
	        static::$translations_languages[$lang] = $clang[$lang];
	    }
	}
	
	protected function get_search_filter_translation_from() {
	    return $this->get_search_filter_simple_selection('', 'translation_from', '', static::$translations_languages);
	}
	
	protected function get_search_filter_translation_to() {
	    global $msg;
	    
	    return $this->get_search_filter_simple_selection('', 'translation_to', $msg['all'], static::$translations_languages);
	}
	
	protected function get_search_filter_is_translated() {
		global $msg;
		return "
			<input type='radio' id='".$this->objects_type."_is_translated_all' name='".$this->objects_type."_is_translated' value='0' ".(!$this->filters['is_translated'] ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_is_translated_all'>".$msg['all']."</label>
			<input type='radio' id='".$this->objects_type."_is_translated_no' name='".$this->objects_type."_is_translated' value='1' ".($this->filters['is_translated'] == 1 ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_is_translated_no'>".$msg['39']."</label>
			<input type='radio' id='".$this->objects_type."_is_translated_yes' name='".$this->objects_type."_is_translated' value='2' ".($this->filters['is_translated'] == 2 ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_is_translated_yes'>".$msg['40']."</label>";
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('translation_to', 'trans_lang');
	}
	
	protected function _get_object_property_trans_lang($object) {
	    return static::$translations_languages[$object->trans_lang];
	}
	
	protected function _get_query_human_translation_from() {
	    if (!empty($this->filters['translation_from'])) {
	        return static::$translations_languages[$this->filters['translation_from']];
	    }
	    return '';
	}
	
	protected function _get_query_human_translation_to() {
	    if (!empty($this->filters['translation_to'])) {
	        return static::$translations_languages[$this->filters['translation_to']];
	    }
	    return '';
	}
	
	protected function _get_query_human_is_translated() {
		global $msg;
		
		if($this->filters['is_translated'] == 1) {
			return $msg['40'];
		} elseif($this->filters['is_translated'] == 0) {
			return $msg['39'];
		}
	}
	
	public function get_primary_keys() {
	    return $this->primary_keys;
	}
}