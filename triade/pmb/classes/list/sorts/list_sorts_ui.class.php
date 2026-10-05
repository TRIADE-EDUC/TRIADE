<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_sorts_ui.class.php,v 1.3.4.1 2025/03/07 16:12:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_sorts_ui extends list_ui {
    
    protected static $sort;
    
    protected static $sort_type;
    
    protected static $popup = false;
    
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
    
    protected function add_column_actions() {
        global $msg, $charset;
        global $categ;
        
        $html = '';
        if($categ != "search_universes") {
            $html .= "
            <a href='#' onClick='agitTri(\"modif\",!!id!!);'>
    		  <img src='".get_url_icon('b_edit.png')."' alt='" . htmlentities($msg['modif_tri'], ENT_QUOTES, $charset) . "' title='" . htmlentities($msg['modif_tri'], ENT_QUOTES, $charset) . "' style='border:0px' />
            </a>
            ";
        }
        $html .= "
        <a href='#' onClick='suppr(!!id!!);'>
            <img src='".get_url_icon('cross.png')."' alt='" . htmlentities($msg['suppr_tri'], ENT_QUOTES, $charset) . "' title='" . htmlentities($msg['suppr_tri'], ENT_QUOTES, $charset) . "' />
        </a>";
        
        $this->columns[] = array(
            'property' => 'edit',
            'label' => '',
            'html' => $html,
            'exportable' => false
        );
    }
    
    protected function init_default_columns() {
        $this->add_column('nom_tri');
        $this->add_column_actions();
    }
    
    protected function init_default_settings() {
        parent::init_default_settings();
        $this->set_setting_display('search_form', 'visible', false);
        $this->set_setting_display('search_form', 'export_icons', false);
        $this->set_setting_display('query', 'human', false);
        $this->set_setting_display('pager', 'visible', false);
        $this->set_setting_column('default', 'align', 'left');
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
        $display = "<table id='".$this->get_uid_objects_list()."' class='".$this->get_class_objects_list()."' role='presentation'>";
        if(count($this->objects)) {
            $display .= $this->get_display_content_list();
        }
        $display .= "</table>";
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
    
    protected function get_default_attributes_format_cell($object, $property) {
        global $msg, $charset, $categ, $current_module;
        
        $attributes = array();
        switch ($property) {
            case 'nom_tri':
                $attributes['style'] = "width:80%;";
                $attributes['alt'] = $msg['appliq_tri'];
                $attributes['title'] = $msg['appliq_tri'];
                switch (static::$sort->caller) {
                    case 'etagere':
                    case 'rss_flux':
                        $attributes['onclick'] = "parent.document.getElementById(\"history\").style.display=\"none\";parent.window.getSort(\"".$object->id."\",\"".htmlentities(static::$sort->descriptionTriParId($object->id),ENT_QUOTES,$charset)."\"); return false;";
                        break;
                    case 'external':
                        $attributes['onclick'] = "parent.document.getElementById(\"history\").style.display=\"none\";parent.location.href=\"./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=".static::$sort_type."&tri=".$object->id."&external=1&reference=".static::$sort->params['REFERENCE']."&type_tri=".static::$sort->dSort->sortName."&module=$current_module\"; return false;";
                        break;
                    default:
                        if($categ == 'search_universes'){
                            if (static::$popup) {
                                $attributes['class'] = "sort";
                            }
                            $attributes['onclick'] = "defineSort(\"modif\", \"".static::$sort->dSort->posParcours."\")";
                        } else {
                            if (static::$popup) {
                                $attributes['class'] = "sort";
                                $attributes['style'] .= "cursor: pointer;";
                                $attributes['data-sort_link'] = "./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=".static::$sort_type."&tri=".$object->id."&reference=".static::$sort->params['REFERENCE']."&type_tri=".static::$sort->dSort->sortName."&ajax=1&module=$current_module";
                            } else {
                                $attributes['onclick'] = "parent.document.getElementById(\"history\").style.display=\"none\";parent.location.href=\"./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=".static::$sort_type."&tri=".$object->id."&reference=".static::$sort->params['REFERENCE']."&type_tri=".static::$sort->dSort->sortName."&module=$current_module\"; return false;";
                            }
                        }
                        break;
                }
            default:
                break;
        }
        return $attributes;
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
    
    public static function set_sort_type($sort_type) {
        static::$sort_type = $sort_type;
    }
    
    public static function set_popup($popup) {
        static::$popup = $popup;
    }
}