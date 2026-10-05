<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_contributions_ui.class.php,v 1.15 2022/07/07 14:49:19 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_contributions_ui extends list_ui {
    
    private $store;
    
    public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
        $this->store = contribution_area_forms_controller::get_datastore();
        parent::__construct($filters, $pager, $applied_sort);
    }
    
    protected function fetch_data() {
        global $charset;
        
        $results = array ();
        $this->objects = array();

        $query = $this->_get_query();
        $this->store->query($query);
        //Parse initial des résultats de la requete sparql
        if ($this->store->num_rows()) {
            $rows = $this->store->get_result();
            foreach ($rows as $row) {
                if (empty($row->identifier)) {
                    if (!isset($results[$row->s])) {
                        $results[$row->s] = array ();
                    }
                    $results[$row->s][explode('#', $row->p)[1]] = htmlentities(strip_tags($row->o),ENT_QUOTES,$charset);
                    
                    if (empty($results[$row->s]["uri_id"])) {
                        $uri_id = onto_common_uri::get_id($row->s);
                        if (empty($uri_id)) {
                            $uri_id = onto_common_uri::set_new_uri($row->s);
                        }
                        $results[$row->s]["uri_id"] = $uri_id;
                    }
                    
                    if (!isset($results[$row->s]["contributor"])) {
                        $results[$row->s]["contributor"] = $row->contributor;
                    }
                    if (!isset($results[$row->s]["sub_form"])) {
                        $results[$row->s]["sub_form"] = 0;
                    } 
                }
            }
            $i = 0;
            foreach ($results as $result){
                if (!empty($result['is_draft'])){
                    array_splice($results, $i, 1);
                } else {
                    $i++;
                }
            }
            $this->pager['nb_results'] = count($results);
            $results = $this->slice_results($results);
            $results = contribution_area_forms_controller::edit_results_to_template($results);
            foreach ($results as $result){
                $this->add_object($result);
            }
        }
        
        $this->messages = "";
    }
    
    protected function _get_query() {
        $query = $this->_get_query_base();
        //$query .= $this->_get_query_filters();
        $query .= $this->_get_query_order();
        //$query .= $this->_get_query_pager();
        
        return $query;
    }
    
    protected function _get_query_base() {
        $query = "SELECT * WHERE {
                ?s <http://www.pmbservices.fr/ontology#has_contributor> ?contributor .
                ?s ?p ?o .
                ?s <http://www.pmbservices.fr/ontology#last_edit> ?last_edit .
                ?s <http://www.pmbservices.fr/ontology#displayLabel> ?displayLabel .
                optional {
                    ?s <http://www.pmbservices.fr/ontology#identifier> ?identifier
                }
                ".$this->_get_query_filters()."
            }";
        return $query;
    }
    
    protected function _get_query_order() {
        if ($this->applied_sort[0]['by']) {
            $order = '';
            $sort_by = $this->applied_sort[0]['by'];
            switch($sort_by) {
                case 'contributor_name':
                    $order .= '?contributor';
                    break;
                case 'last_edit':
                    $order .= '?last_edit';
                    break;
                case 'entity_type':
                    $order .= '?entity_type';
                    break;
                case 'displayLabel':
                    $order .= '?displayLabel';
                    break;
                default :
                    $order .= parent::_get_query_order();
                    break;
            }
            if ($order) {
                $this->applied_sort_type = 'SPARQL';
                return " ORDER BY ".$this->applied_sort[0]['asc_desc']."(".$order.")";
            } else {
                return "";
            }
        }
    }
    
    protected function init_default_applied_sort() {
        $this->add_applied_sort('last_edit', 'desc');
    }
    
    protected function add_object($row) {
        $object = $row;
        if (is_array($row)) {
            $object = new stdClass();
            foreach ($row as $property => $value) {
                $object->$property = $value;
            }
        }
        $this->objects[] = $object;
    }
    
    /**
     * Contenu d'une colonne
     * @param object $object
     * @param string $property
     */
    protected function get_cell_content($object, $property) {
        $content = '';
        switch($property) {
            default :
                if (is_object($object) && isset($object->{$property})) {
                    $content .= $object->{$property};
                } elseif(method_exists($object, 'get_'.$property)) {
                    $content .= call_user_func_array(array($object, "get_".$property), array());
                } elseif(isset($this->custom_fields_available_columns[$property])) {
                    $custom_instance = $this->get_custom_parameters_instance($this->custom_fields_available_columns[$property]['type']);
                    $property_id = $this->custom_fields_available_columns[$property]['property_id'];
                    if(method_exists($object, 'get_'.$property_id)) {
                        $custom_instance->get_values(call_user_func_array(array($object, "get_".$property_id), array()));
                    } else {
                        $custom_instance->get_values($object->{$property_id});
                    }
                    $field_id = $custom_instance->get_field_id_from_name($property);
                    if(isset($custom_instance->values[$field_id]) && count($custom_instance->values[$field_id])) {
                        $content .= $custom_instance->get_formatted_output($custom_instance->values[$field_id], $field_id);
                    }
                }
                break;
        }
        return $content;
    }
    
    /**
     * Affichage d'une colonne avec du HTML non calculé
     * @param string $value
     */
    protected function get_display_cell_html_value($object, $value) {
        $search = [
            "!!sub!!",
            "!!uri_id!!",
            "!!area_id!!",
            "!!form_uri!!",
            "!!form_id!!",
            "!!parent_scenario_uri!!",
            "!!contributor_id!!",
        ];
        $replace = [
            $object->sub ?? '',
            $object->uri_id ?? 0,
            $object->area["id"] ?? 0,
            $object->form_uri ?? "",
            $object->form_id ?? 0,
            $object->parent_scenario_uri ?? "",
            $object->contributor_id ?? 0,
        ];
        $value = str_replace($search, $replace, $value);
        $display = "<td class='center'>".$value."</td>";
        return $display;
    }
    
    /**
     * Initialisation des colonnes disponibles
     */
    protected function init_available_columns() {
        global $msg;
        $this->available_columns =
        array('main_fields' =>
            array(
                'displayLabel' => $msg['contribution_moderation_list_display_label'],
                'contributor_name' => $msg['contribution_moderation_list_contributor'],
                'entity_type' => $msg['contribution_moderation_list_entity_type'],
                'last_edit' => $msg['contribution_moderation_list_last_edit'],
            )
        );
    }
    
    /**
     * Initialisation des colonnes par défaut
     */
    protected function init_default_columns() {
        $this->add_column('displayLabel');
        $this->add_column('contributor_name');
        $this->add_column('entity_type');
        $this->add_column('last_edit');
        $this->add_column_sel_button();
    }
    
    protected function init_no_sortable_columns() {
        $this->no_sortable_columns = array(
            'contributor_name',
            'entity_type'
        );
    }
    
    protected function add_column_sel_button() {
        global $msg;
        $this->columns[] = array(
            'property' => '',
            'label' => "<div class='center'></div>",
            'html' => "	<a href='./catalog.php?categ=contribution_area&action=push&sub=!!sub!!&id=!!uri_id!!&form_id=!!form_id!!&action=push&from_gestion=1' onclick='if(!confirm(pmbDojo.messages.getMessage(\"contribution\", \"onto_contribution_push_confirm\"))){return false;}'>
							<input type='button' value='$msg[contribution_area_validate]' class='bouton'/>
						</a>
						<a href='./catalog.php?categ=contribution_area&action=edit&sub=!!sub!!&area_id=!!area_id!!&form_id=!!form_id!!&form_uri=!!form_uri!!&id=!!uri_id!!&scenario=!!parent_scenario_uri!!&contributor=!!contributor_id!!'>
							<input type='button' value='$msg[62]' class='bouton'/>
						</a>
						<a href='./catalog.php?categ=contribution_area&action=delete&sub=!!sub!!&id=!!uri_id!!&form_id=!!form_id!!&action=delete' onclick='if(!confirm(\"$msg[onto_contribution_delete_confirm]\")){return false;}'>
							<input type='button' value='$msg[63]' class='bouton'/>
						</a>",
            'exportable' => false
        );
    }
    
    public static function get_controller_url_base() {
        global $base_path, $action;
        return $base_path.'/catalog.php?categ=contribution_area&action=' . $action;
    }
    
    private function slice_results($results) {
        global $dest;
        $this->set_pager_from_form();
        switch($dest) {
            case 'HTML':
            case 'TABLEAUHTML':
            case 'TABLEAU':
                break;
            default:
                $results = array_slice($results, (($this->pager['page']-1)*$this->pager['nb_per_page']), $this->pager['nb_per_page']);
                break;
        }
        return $results;
    }
    
    
    
    /**
     * Initialisation des filtres disponibles
     */
    protected function init_available_filters() {
        $this->available_filters = ['main_fields' =>
            [
                'sub_form' => 'sub_form'
            ]
        ];
        $this->available_filters['custom_fields'] = [];
    }
    
    /**
     * Initialisation des filtres de recherche
     */
    public function init_filters($filters=array()) {
        
        $this->filters = array(
            'sub_form' => array(),
        );
        parent::init_filters($filters);
    }
    
    /**
     * Filtre SQL
     */
    protected function _get_query_filters() {
        $filter_query = '';
        
        $this->set_filters_from_form();
        $filters = array();
        if(count($this->filters['sub_form']) && $this->filters['sub_form'][0] == true) {
            $filters [] = 'optional {
                                ?s <http://www.pmbservices.fr/ontology#sub_form> ?sub_form.                      
                            }
                           FILTER ( !bound(?sub_form) ).
                         ';
        }        
        if(count($filters)) {
            $filter_query .= implode(' .', $filters);
        }
        return $filter_query;
    }
    
    /**
     * Filtres provenant du formulaire
     */
    public function set_filters_from_form() {
        $this->set_filter_from_form('sub_form');
        parent::set_filters_from_form();
    }
    
    protected function _get_query_human_sub_form() {
    	global $msg;
    	if($this->filters['sub_form']) {
    		return $msg['sub_form'];
    	}
    	return '';
    }
    
    protected function get_search_filter_sub_form() {
        global $msg;
        $selector = "<div>";
        $selector .= "<input type='radio' id='display_sub_contribution_on' name='".$this->objects_type."_sub_form[]' value='".true."' ".(((isset($this->filters['sub_form'][0]) && $this->filters['sub_form'][0] == true)) ? "checked='checked'" : "").">";
        $selector .= "<label for=0 >".$msg['40']."</label>";
        $selector .= "<input type='radio' id='display_sub_contribution_off' name='".$this->objects_type."_sub_form[]' value='".false."' ".(((isset($this->filters['sub_form'][0]) && $this->filters['sub_form'][0] == false) || !count($this->filters['sub_form']) ) ? "checked='checked'" : "").">";
        $selector .= "<label for=1 >".$msg['39']."</label>";
        $selector .= "</div>";
        return $selector;
    }
    
    protected function init_default_selected_filters() {
        $this->add_selected_filter('sub_form');
    }
    
    protected function _get_label_query_human($label, $value ='') {
        global $charset;
        if(is_array($value)) {
            return "<b>".htmlentities($label, ENT_QUOTES, $charset)."</b> <i>".implode(', ', $value)."</i>";
        } elseif ($value == '') {
            return "<b>".htmlentities($label, ENT_QUOTES, $charset)."</b>";
        } else {
            return "<b>".htmlentities($label, ENT_QUOTES, $charset)."</b> <i>".$value."</i>";
        }
    }
    
    protected function get_spreadsheet_title() {
        return "contributions.xls";
    }
    
    protected function get_display_spreadsheet_title() {
        global $msg;
        $this->spreadsheet->write_string(0,0,$msg['catalog_menu_contribution']);
    }
    
    public function get_export_icons() {
        global $msg;
        if (!(SESSrights & EDIT_AUTH)) {
            return "";
        }
        if($this->get_setting('display', 'search_form', 'export_icons')) {
            return "
				<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function start_export(type){
                        var action = document.forms['".$this->get_form_name()."'].action;
                        var action_edit = document.forms['".$this->get_form_name()."'].action.replace('catalog.php', 'edit.php');

                        document.forms['".$this->get_form_name()."'].action = action_edit;
						document.forms['".$this->get_form_name()."'].dest.value = type;
						document.forms['".$this->get_form_name()."'].target='_blank';
						document.forms['".$this->get_form_name()."'].submit();
						document.forms['".$this->get_form_name()."'].dest.value = '';
						document.forms['".$this->get_form_name()."'].target='';
                        document.forms['".$this->get_form_name()."'].action = action;
					}
				</script>
				<img  src='".get_url_icon('tableur.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."'/>&nbsp;&nbsp;
				<img  src='".get_url_icon('tableur_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."'/>
				<input type='hidden' name='dest' value='' />
			";
        } else {
            return "";
        }
    }
}