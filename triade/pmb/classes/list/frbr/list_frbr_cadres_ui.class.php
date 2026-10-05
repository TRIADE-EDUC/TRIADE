<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_frbr_cadres_ui.class.php,v 1.10 2023/12/07 14:46:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_frbr_cadres_ui extends list_ui {
    
    protected function _get_query_base() {
        $query = 'select id_cadre
				from frbr_cadres';
        return $query;
    }
    
    protected function get_object_instance($row) {
    	return new frbr_entity_common_entity_cadre($row->id_cadre);
    }
    
    protected function init_default_applied_sort() {
        $this->add_applied_sort('id_cadre', 'desc');
    }
    
    /**
     * Affichage d'une colonne avec du HTML non calculé
     * @param string $value
     */
    protected function get_display_cell_html_value($object, $value) {
        $search = [
            "!!id_cadre!!",
        ];
        $replace = [
            $object->get_id(),
        ];
        $value = str_replace($search, $replace, $value);
        $display = "<td class='center'>".$value."</td>";
        return $display;
    }
    
    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns() {
        $this->available_columns =
        array('main_fields' =>
            array(
                'id_cadre' => '1601',
                'name' => '67',
                'class_name' => 'class_name',
                'num_page' => 'list_ui_frbr_page_id',
                'page_name' => 'frbr_page_name',
            )
        );
    }
    
    /**
     * Initialisation des colonnes par défaut
     */
    protected function init_default_columns() {
        $this->add_column('id_cadre');
        $this->add_column('name');
        $this->add_column('class_name');
        $this->add_column('num_page');
        $this->add_column('page_name');
        $this->add_column_sel_button();
    }
    
    protected function init_default_settings() {
    	parent::init_default_settings();
    	$this->set_setting_column('id_cadre', 'datatype', 'integer');
    	$this->set_setting_column('num_page', 'datatype', 'integer');
    }
    
    protected function init_no_sortable_columns() {
        $this->no_sortable_columns = array(
            'class_name',
            'num_page',
            'page_name',
        );
    }
    
    protected function add_column_sel_button() {
        global $msg, $charset;
        $this->columns[] = array(
            'property' => '',
            'label' => "<div class='center'></div>",
            'html' => "	<a onclick=\"frbr_edit_entity('cadre','get_form',!!id_cadre!!);\" href='#' >
			                 <img class='icon' width='16' height='16' title='".htmlentities($msg["cms_build_edit_bt"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["cms_build_page_add_bt"], ENT_QUOTES, $charset)."' src='".get_url_icon('b_edit.png')."'  >
		                  </a>",
            'exportable' => false
        );
    }
    
    public static function get_controller_url_base() {
        global $base_path, $action;
        return $base_path.'/cms.php?categ=frbr_pages&sub=cadres&action=' . $action;
    }
    
    public static function get_ajax_controller_url_base() {
    	global $base_path, $current_module;
    	return $base_path.'/ajax.php?module='.$current_module.'&categ=frbr_pages';
    }
    
    /**
     * Initialisation des filtres de recherche
     */
    public function init_filters($filters=array()) {
        $this->filters = array(
            'id_cadre' => '',
            'num_page' => '',
            'template_content' => '',
        );
        parent::init_filters($filters);
    }
    
    /**
     * Initialisation des filtres disponibles
     */
    protected function init_available_filters() {
        $this->available_filters =
        array('main_fields' =>
            array(
                'id_cadre' => '1601',
                'num_page' => 'list_ui_frbr_page_id',
                'template_content' => 'template_content',
            )
        );
        $this->available_filters['custom_fields'] = array();
    }
    
    protected function init_default_selected_filters() {
        $this->add_selected_filter('id_cadre');
    }
    
    protected function get_search_filter_id_cadre() {
        global $charset;
        return "<input type='text' pattern='[0-9]*'  name='id_cadre' id='id_cadre' value='".htmlentities($this->filters['id_cadre'], ENT_QUOTES, $charset)."'/>";
    }
    
    protected function get_search_filter_num_page() {
        global $charset;
        return "<input type='text' pattern='[0-9]*'  name='num_page' id='num_page' value='".htmlentities($this->filters['num_page'], ENT_QUOTES, $charset)."'/>";
    }
    
    protected function get_search_filter_template_content() {
        global $charset;
        return "<textarea id='template_content' cols='90' rows='5' maxlength='2000' name='template_content' wrap='virtual'>".htmlentities($this->filters['template_content'], ENT_QUOTES, $charset)."</textarea>";
    }
    
    /**
     * Filtres provenant du formulaire
     */
    public function set_filters_from_form() {
        global $id_cadre, $num_page, $template_content;
        
        if(isset($id_cadre)) {
            $this->filters['id_cadre'] = intval($id_cadre);
        }
        if(isset($num_page)) {
            $this->filters['num_page'] = intval($num_page);
        }
        if(isset($template_content)) {
            $this->filters['template_content'] = stripslashes($template_content);
        }
        parent::set_filters_from_form();
    }
    
    protected function _add_query_filters() {
    	$this->_add_query_filter_simple_restriction('id_cadre', 'id_cadre', 'integer');
    	$this->_add_query_filter_simple_restriction('num_page', 'cadre_num_page', 'integer');
    	if($this->filters['template_content']) {
    		$this->query_filters [] = 'cadre_content_data LIKE "%'.$this->filters['template_content'].'%"';
    	}
    }
    
    /**
     * Jointure externes SQL pour les besoins des filtres
     */
    protected function _get_query_join_filters() {
        $filter_join_query = '';
        if($this->filters['template_content']) {
            $filter_join_query .= " LEFT JOIN frbr_cadres_content ON (id_cadre = cadre_content_num_cadre) ";
        }
        return $filter_join_query;
    }
    
    protected function _get_object_property_id_cadre($object) {
    	return $object->get_id();
    }
    
    protected function _get_object_property_num_page($object) {
    	return $object->get_page()->get_id();
    }
    
    protected function _get_object_property_page_name($object) {
    	return $object->get_page()->get_name();
    }
    
    protected function get_display_others_actions() {
        global $msg;
        return "<script type='text/javascript'>
            require(['dojo/ready', 
                    'dojo/topic', 
                    'dojo/dom', 
                    'dijit/registry',
                    'dojo/request/xhr',  
                    'apps/pmb/PMBDojoxDialogSimple'], 
                function(ready, topic, dom, registry, xhr, Dialog) {
    				ready(function() {
    					frbr_edit_entity = function(type, action, id){
                            var myDijit = registry.byId('frbr_edit_dialog');
                            if(!myDijit){
                                myDijit = new Dialog({
                                    title: '".$msg["cms_build_modules"]."',
                                    executeScripts:true,
                                    id:'frbr_edit_dialog'
                                });
                            }
                            myDijit.set('title','".$msg["cms_build_modules"]."');

                            xhr.post('./ajax.php?module=cms&categ=frbr_entities&type='+type+'&action='+action+'&id='+id, {
                				data: {
                					no_deletion : 1,
                				}
                			}).then(function(data) {
                                myDijit.set('content',data);
                                myDijit.startup();
                                myDijit.show();
                			})
    
                            topic.subscribe('EntityForm', function(evtType,evtArgs){
                                switch(evtType) {
                				    case 'saved':
                                       submitForm() 
                					   myDijit.hide();
                					   break;
                				    case 'canceled':
                					   myDijit.hide();
                					   break;
                                }
    					   });
                        },
                        submitForm = function() {
                            var myForm = dom.byId('".$this->get_form_name()."');
                            if (myForm) {
                                myForm.submit();
                            }
                        }
    				});
                });
       </script>";
    }
}