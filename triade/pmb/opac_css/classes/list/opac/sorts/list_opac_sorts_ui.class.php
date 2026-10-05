<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_sorts_ui.class.php,v 1.6.4.2 2025/02/18 10:11:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_sorts_ui extends list_opac_ui {
    
    protected static $sort;
    
    protected function fetch_data() {
        $this->objects = array();
        //affichage des enregistrements de tris possibles
        while ($row = static::$sort->dSort->parcoursTriSuivant()) {
            $row['id'] = $row['id_tri'];
            $this->add_object((object) $row);
        }
        $this->messages = "";
    }
    
    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns() {
        $this->available_columns =
        array('main_fields' =>
            array(
                'nom_tri' => '',
            )
        );
        $this->available_columns['custom_fields'] = array();
    }
    
    protected function at_least_one_action() {
        return true;
    }
    
    protected function init_default_columns() {
        $this->add_column_selection();
        $this->add_column('nom_tri');
    }
    
    protected function init_default_settings() {
        parent::init_default_settings();
        $this->set_setting_display('search_form', 'visible', false);
        $this->set_setting_display('search_form', 'export_icons', false);
        $this->set_setting_display('query', 'human', false);
        $this->set_setting_display('pager', 'visible', false);
    }
    
    /**
     * Initialisation de la pagination par défaut
     */
    protected function init_default_pager() {
        parent::init_default_pager();
        $this->pager['all_on_page'] = true;
    }
    
    public function get_display_search_form() {
        return '';
    }
    
    /**
     * Affichage de la liste des objets
     * @return string
     */
    public function get_display_objects_list() {
        global $msg, $charset;

        $display = "
            <span id='legend_".$this->get_uid_objects_list()."' class='visually-hidden'>".htmlentities($msg['rgaa_sort_ui_legend'], ENT_QUOTES, $charset)."</span>
            <table id='".$this->get_uid_objects_list()."' class='".$this->get_class_objects_list()."' role='presentation'>
                <tbody role='group' aria-labelledby='legend_".$this->get_uid_objects_list()."'>";
        if(count($this->objects)) {
            $display .= $this->get_display_content_list();
        }
        $display .= "</tbody></table>";
        if(count($this->objects)) {
            $display .= $this->add_events_on_objects_list();
        }
        return $display;
    }
    
    /**
     * Header de la liste
     */
    public function get_display_header_list() {
        return '';
    }
    
    protected function get_name_selection_objects() {
        return 'cases_suppr';
    }
    
    protected function get_cell_content($object, $property) {
        global $msg, $charset;
        
        $content = '';
        switch($property) {
            case 'nom_tri':
                //rétro-compatibilité : !!page_en_cours1!! doit être remplacé plus tard 
                $content .= "
                <a href='./index.php?!!page_en_cours1!!&get_last_query=".(isset($_SESSION["last_query"]) ? $_SESSION["last_query"] : '')."&sort=".$object->id_tri."' title='".htmlentities($msg['appliq_tri'], ENT_QUOTES, $charset)."' aria-label='".htmlentities($msg['common_filter_redirection_aria_label'], ENT_QUOTES, $charset) . htmlentities($object->nom_tri, ENT_QUOTES, $charset)."'>
                    <span id='label_sort_".$object->id_tri."'>".htmlentities($object->nom_tri, ENT_QUOTES, $charset)."</span>
                </a>";   
                break;
            default :
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }
    
    protected function _cell_is_sortable($name) {
        return false;
    }
    
    protected function get_js_sort_script_sort() {
        return '';
    }
    
    public static function set_sort($sort) {
        static::$sort = $sort;
    }

    /**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		$ancre = "";
		if(!empty($this->object_id) && method_exists($object, 'get_id') && $this->object_id==$object->get_id()) {
			if(empty($this->ancre)) {
				$this->ancre = $this->objects_type."_object_list_ancre";
			}
			$ancre = " id='".$this->ancre."' ";
		}
		$highlight = "";
		if($this->is_highlight_activated()) {
		    $highlight = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$this->get_class_odd_even($indice)."'\"";
		}
		$onclick = "";
		if(!empty($this->is_editable_object_list) && method_exists($this, 'get_edition_link')) {
			$onclick = "onclick=\"document.location='".$this->get_edition_link($object)."';\" style='cursor: pointer'";
		}
		$display = "
					<tr ".$ancre." class='".$this->get_class_odd_even($indice)." list_ui_content_object_list ".$this->objects_type."_content_object_list' ".$highlight." ".$onclick.">";
		foreach ($this->columns as $column) {
			if($column['html']) {
				$display .= $this->get_display_cell_html_value($object, $column['html']);
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		return $display;
	}
}