<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_sections_ui.class.php,v 1.3 2023/12/08 15:25:27 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_sections_ui extends list_opac_ui {
    
    protected function _get_query_base() {
        $query = 'select idsection FROM docs_section';
        return $query;
    }
    
    protected function get_object_instance($row) {
        return new docs_section($row->idsection);
    }
    
    /**
     * Champ(s) du tri SQL
     */
    protected function _get_query_field_order($sort_by) {
        switch($sort_by) {
            case 'libelle':
                return 'section_libelle_opac, section_libelle';
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
    
    protected function _get_query_order() {
        return " group by idsection ".parent::_get_query_order();
    }
    
    /**
     * Initialisation des filtres de recherche
     */
    public function init_filters($filters=array()) {
        $this->filters = array(
                'location' => 0,
        );
        parent::init_filters($filters);
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
    
    /**
     * Jointure externes SQL pour les besoins des filtres
     */
    protected function _get_query_join_filters() {
        $filter_join_query = '';
        if($this->filters['location']) {
            $filter_join_query .= " JOIN exemplaires ON expl_section=idsection";
        }
        return $filter_join_query;
    }
    
    protected function _add_query_filters() {
        $this->query_filters [] = 'section_visible_opac=1';
        if($this->filters['location']) {
            $this->query_filters [] = 'expl_location='.$this->filters['location'];
        }
//         group by idsection
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
    
    protected function _get_object_property_libelle($object) {
        if ($object->libelle_opac) {
            return $object->get_translated_libelle_opac();
        } else {
            return $object->get_translated_libelle();
        }
    }
    
    /**
	 * Affichage de la liste des objets
	 * @return string
	 */
	public function get_display_objects_list() {
        global $opac_nb_sections_per_line;
        
        if (!$opac_nb_sections_per_line) $opac_nb_sections_per_line=6;
        
        $display = "<table class='center' style='width:100%' role='presentation'>";
        $display .= $this->get_display_caption_list();
        $npl=0;
        foreach ($this->objects as $object) {
            if ($npl==0) {
                $display .= "<tr>";
            }
            $display .= "<td class='center' style='width:120px'>";
            $display .= $this->get_cell_content($object, 'libelle');
            $display .= "</td>";
            $npl++;
            if ($npl==$opac_nb_sections_per_line) {
                $display .= "</tr>";
                $npl=0;
            }
        }
        $display .= "</table>";
        return $display;
    }
    
    protected function get_cell_content($object, $property) {
        global $charset, $base_path;
        global $back_surloc, $url_loc, $back_section_see;
        
        $content = '';
        switch($property) {
            case 'libelle':
                $section_label = $this->_get_object_property_libelle($object);
                if ($object->pic) {
                    $image_src = $object->pic;
                } else {
                    $image_src = get_url_icon("rayonnage-small.png") ;
                }
                if (isset($back_section_see) && $back_section_see) {
                    $param_section_see = "&back_section_see=index.php";
                }
                else {
                    $param_section_see = "";
                }
                if (isset($back_surloc) && $back_surloc) {
                    $url = $base_path."/index.php?lvl=section_see&location=".$this->filters['location']."&id=".$object->id."&back_surloc=".rawurlencode($back_surloc)."&back_loc=".rawurlencode($url_loc).$param_section_see;
                } else {
                    $url = $base_path."/index.php?lvl=section_see&location=".$this->filters['location']."&id=".$object->id;
                }
                $content .= "
                <a href='".$url."'>
                    <img src='$image_src' style='border:0px' alt='".htmlentities($section_label, ENT_QUOTES, $charset)."' title='".htmlentities($section_label, ENT_QUOTES, $charset)."'/>
                </a>
                <br />
                <a href='".$url."'>
                    <b>".$section_label."</b>
                </a>";
                break;
            default :
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }
}