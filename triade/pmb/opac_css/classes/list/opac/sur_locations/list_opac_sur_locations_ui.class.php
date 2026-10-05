<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_sur_locations_ui.class.php,v 1.1 2023/12/13 09:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_sur_locations_ui extends list_opac_ui {
    
    protected function _get_query_base() {
        $query = 'SELECT surloc_id FROM sur_location';
        return $query;
    }
    
    protected function get_object_instance($row) {
        return new sur_location($row->surloc_id);
    }
    
    /**
     * Champ(s) du tri SQL
     */
    protected function _get_query_field_order($sort_by) {
        switch($sort_by) {
            case 'libelle':
                return 'surloc_libelle';
            default :
                return parent::_get_query_field_order($sort_by);
        }
    }
    
    /**
     * Initialisation du tri par défaut appliqué
     */
    protected function init_default_applied_sort() {
        $this->add_applied_sort('libelle');
    }
    
    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns() {
        $this->available_columns =
        array('main_fields' =>
            array(
                'libelle' => '',
            )
        );
        $this->available_columns['custom_fields'] = array();
    }
    
    protected function init_default_columns() {
        $this->add_column('libelle');
    }
    
    protected function init_default_settings() {
        parent::init_default_settings();
        $this->set_setting_display('search_form', 'visible', false);
        $this->set_setting_display('search_form', 'export_icons', false);
        $this->set_setting_display('query', 'human', false);
        $this->set_setting_display('pager', 'visible', false);
        $this->set_setting_column('libelle', 'align', 'center');
    }
    
    protected function _get_query_join_filters() {
        global $opac_view_filter_class;
        
        if($opac_view_filter_class){
            if(!empty($opac_view_filter_class->params['nav_sections'])) {
                return "JOIN docs_location ON surloc_id=surloc_num";
            }
        }
        return '';
    }
    
    protected function _add_query_filters() {
        global $opac_view_filter_class;
        
        $this->query_filters [] = 'surloc_visible_opac=1';
        if($opac_view_filter_class){
            if(!empty($opac_view_filter_class->params['nav_sections'])) {
                $this->query_filters [] = 'idlocation IN('. implode(",",$opac_view_filter_class->params['nav_sections']).')';
            }
        }
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
        global $opac_nb_localisations_per_line;
        
        if (!$opac_nb_localisations_per_line) $opac_nb_localisations_per_line=6;
        
        $display = "<table class='center' style='width:100%' role='presentation'>";
        $display .= $this->get_display_caption_list();
        $npl=0;
        foreach ($this->objects as $object) {
            if ($npl==0) {
                $display .= "<tr>";
            }
            $display .= "<td class='center'>";
            $display .= $this->get_cell_content($object, 'libelle');
            $display .= "</td>";
            $npl++;
            if ($npl==$opac_nb_localisations_per_line) {
                $display .= "</tr>";
                $npl=0;
            }
        }
        return $display;
    }
    
    protected function get_cell_content($object, $property) {
        global $charset, $base_path;
        
        $content = '';
        switch($property) {
            case 'libelle':
                if ($object->pic) {
                    $image_src = $object->pic;
                } else {
                    $image_src = get_url_icon("bibli-small.png");
                }
                if($object->css_style) {
                    $url_extra = "&opac_css=".$object->css_style;
                } else {
                    $url_extra = "";
                }
                $content .= "
                <a href='".$base_path."/index.php?lvl=section_see&surloc=".$object->id.$url_extra."'>
                    <img src='$image_src' style='border:0px' alt='".htmlentities($object->libelle, ENT_QUOTES, $charset)."' title='".htmlentities($object->libelle, ENT_QUOTES, $charset)."'/>
                </a>
				<br />
                <a href='".$base_path."/index.php?lvl=section_see&surloc=".$object->id.$url_extra."'><b>".$object->libelle."</b></a>";
                break;
            default :
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }
}