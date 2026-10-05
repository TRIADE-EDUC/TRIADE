<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_import_ui.class.php,v 1.1.2.4 2024/12/19 14:48:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_import_ui extends list_ui {
	
	protected $instance_list_ui;
	
	protected $uploaded_file = false;
	
	protected $file_data;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
	    if(empty($this->objects_type)) {
	        $this->objects_type = str_replace('list_', '', get_class($this));
	    }
	    $this->init_uploaded_file();
	    parent::__construct($filters, $pager, $applied_sort);
	}
	
	/**
	 * Remplissage du tableau en fonction du contenu du fichier
	 */
	protected function init_file_data() {
	    $temp_file_path = "temp/" . $this->objects_type.".json";
	    if (file_exists($temp_file_path)) {
	        $this->uploaded_file = true;
	        $content = file_get_contents($temp_file_path);
	        $this->file_data = json_decode($content, true, 512, JSON_HEX_APOS|JSON_HEX_QUOT);
	    }
	}
	
	protected function init_uploaded_file() {
	    global $action;
	    
	    switch ($action) {
	        case 'list_import':
	            $this->init_file_data();
	            break;
	        default:
	            if (isset($_FILES['file_upload'])) {
	                $file_tmp = $_FILES['file_upload']['tmp_name'];
	                $file_name = $_FILES['file_upload']['name'];
	                // Vérifier l'extension du fichier (doit être .json)
	                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
	                // Vérifier le type MIME et l'extension
	                if (in_array(mime_content_type($file_tmp), ['application/json']) && pmb_strtolower($file_extension) === 'json') {
	                    $temp_file_path = "temp/" . $this->objects_type.".".$file_extension;
	                    // Copier le fichier dans le dossier "temp"
	                    if (copy($file_tmp, $temp_file_path)) {
	                        $this->init_file_data();
	                    }
	                }
	            } else {
	                $applied_action = $this->objects_type.'_applied_action';
	                global ${$applied_action};
	                //Récupérer le fichier si l'on vient d'appliquer le formulaire de recherche
	                if(!empty(${$applied_action}) && ${$applied_action} == 'apply') {
	                    $this->init_file_data();
	                }
	            }
	            break;
	    }
	}
	
	protected function fetch_data() {
	    $this->set_filters_from_form();
		$this->objects = array();
		if (!empty($this->file_data)) {
    		foreach ($this->file_data as $data) {
    		    if ($data['type'] == 'table') {
    		        if (!empty($data['data'])) {
    		            foreach ($data['data'] as $object) {
    		                $this->add_object((object) $object);
    		            }
    		        }
    		    }
    		}
		}
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    $this->available_filters =
	    array('main_fields' =>
	        array(
	            'states' => '1130'
	        )
	    );
	    $this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	    
	    $this->filters = array(
	        'states' => array()
	    );
	    parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('states');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    $this->available_columns = array();
	    if (!empty($this->file_data)) {
	        $this->available_columns['main_fields']['deduplication_state'] = 'Etat';
	        foreach ($this->file_data as $data) {
	            if ($data['type'] == 'table') {
	                if (!empty($data['data'])) {
	                    foreach ($data['data'][0] as $property=>$value) {
	                        $this->available_columns['main_fields'][$property] = $property;
	                    }
	                }
	            }
	        }
	    }
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
	    $this->pager['nb_per_page'] = 500;
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function get_search_filter_states() {
	    global $msg;
	    
	    $options = [
	        'new' => 'New',
	        'same' => 'Same',
			'replace' => 'Replace',
	        'unknown' => 'Unknown'
	    ];
	    return $this->get_search_filter_multiple_selection('', 'states', $msg['all'], $options);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    $this->set_filter_from_form('states');
	    parent::set_filters_from_form();
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'export_icons', false);
	    $this->set_setting_column('default', 'align', 'left');
	}
	
	/**
	 * Initialisation des colonnes par défaut
	 */
	protected function init_default_columns() {
		$this->columns = array();
		$this->add_column_selection();
		if (!empty($this->available_columns['main_fields'])) {
		    foreach ($this->available_columns['main_fields'] as $property=>$label) {
		        $this->add_column($property, $label);
		    }
		}
	}
	
	public function get_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    
	    $options = [
	        'json' => 'JSON'
	    ];
	    $interface_content_form->add_element('file_format', 'file_format')
	    ->add_select_node($options);
	    $interface_content_form->add_element('file_upload', 'choix_fi')
	    ->add_input_node('file');
	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
	    $interface_form = new interface_import_form('list_import_form');
	    $interface_form->set_enctype('multipart/form-data');
	    $interface_form->set_content_form($this->get_content_form());
	    return $interface_form->get_display();
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
	    if ($this->uploaded_file == false) {
	        return $this->get_form();
	    } else {
	        return parent::get_display_list();
	    }
	}
	
	protected function _get_object_property_deduplication_state($object) {
	    return '';
	}
	
	protected function get_cell_content_import_actions($object, $mode='insertion') {
	    global $msg, $charset;
	    
	    $actions = [];
	    switch ($mode) {
	        case 'insertion':
	            $actions[] = "<input type='radio' id='".$this->objects_type."_".$object->id."' name='".$this->objects_type."_action[".$object->id."]' value='insertion' checked='checked' />
	               <label for='".$this->objects_type."_".$object->id."'>".htmlentities($msg['insert'], ENT_QUOTES, $charset)."</label>";
                break;
	        case 'replacement':
	            $actions[] = "<input type='radio' id='".$this->objects_type."_".$object->id."' name='".$this->objects_type."_action[".$object->id."]' value='replacement' checked='checked' />
	               <label for='".$this->objects_type."_".$object->id."'>".htmlentities($msg['158'], ENT_QUOTES, $charset)."</label>";
	            break;
	    }
	    $actions[] = "<input type='radio' id='".$this->objects_type."_".$object->id."' name='".$this->objects_type."_action[".$object->id."]' value='ignore' />
	    <label for='".$this->objects_type."_".$object->id."'>".htmlentities($msg['ignore'], ENT_QUOTES, $charset)."</label>";
	    return "<span style='display:inline-block'>".implode(' ', $actions)."</span>";
	}
	
	/**
	 * Contenu d'une colonne
	 * @param object $object
	 * @param string $property
	 */
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
		    case 'deduplication_state':
		        $deduplication_state = $this->_get_object_property_deduplication_state($object);
		        switch ($deduplication_state) {
		            case 'same':
		                $content .= "<span style='color:#808080'><strong>Same</strong>";
		                break;
		            case 'replace':
		                $content .= "<span style='color:#FFA500'><strong>Replace</strong>";
		                break;
		            case 'unknown':
		                $content .= "<span style='color:#0080FF'><strong>Unknown</strong>";
		                break;
		            case 'new':
		            default:
		                $content .= "<span style='color:#689D71'><strong>New</strong>";
		                break;
		        }
		        break;
		    case 'import_actions':
		        $content .= $this->get_cell_content_import_actions($object);
		        break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_html_content_selection() {
	    return "<div class='center'><input type='checkbox' id='".$this->objects_type."_selection_!!id!!' name='".$this->objects_type."_selection[!!id!!]' class='".$this->objects_type."_selection' value='!!id!!' style='display:!!importable!!'></div>";
	}
	
	protected function get_display_cell_html_value($object, $value) {
	    if (in_array($this->_get_object_property_deduplication_state($object), array('same', 'unknown'))) {
	        $value = str_replace('!!importable!!', 'none', $value);
	    } else {
	        $value = str_replace('!!importable!!', 'block', $value);
	    }
	    return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function init_default_selection_actions() {
	    global $msg;
	    
	    parent::init_default_selection_actions();
	    $import_link = array(
	        'href' => static::get_controller_url_base()."&action=list_import",
	        'confirm' => $msg['list_import_ui_action_import_confirm']
	    );
	    $this->add_selection_action('import', $msg['import'], '', $import_link);
	}
	
	/**
	 * Insertion/Remplacement d'un objet sélectionne
	 * @param object $object
	 */
	protected function import_object($object) {
	}
	
	/**
	 * Insertion/Remplacement des objets sélectionnes
	 */
	public function import_objects() {
	    $selected_objects = static::get_selected_objects();
	    if(is_array($selected_objects) && count($selected_objects)) {
            foreach ($this->objects as $object) {
                if ($this->is_selected_object($object, $selected_objects)) {
                    $this->import_object($object);
                }
            }
	    }
	}
	
	public function set_instance_list_ui($instance_list_ui) {
	    $this->instance_list_ui = $instance_list_ui;
	    return $this;
	}
	
	public static function get_controller_url_base() {
	    global $action;
	    return parent::get_controller_url_base().($action ? '&action='.$action : '');
	}
}