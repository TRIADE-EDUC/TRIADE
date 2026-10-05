<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_loans_groups_ui.class.php,v 1.3 2023/08/31 08:31:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_loans_groups_ui extends list_opac_loans_ui {
    
    protected function _get_query_base() {
        $query = 'select pret_idempr, pret_idexpl, group_concat(libelle_groupe) as groups
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
                JOIN empr_groupe ON empr_groupe.empr_id = empr.id_empr 
                JOIN groupe ON groupe.id_groupe = empr_groupe.groupe_id
				JOIN docs_type ON expl_typdoc = idtyp_doc
				';
        return $query;
    }

    /**
     * Initialisation des filtres disponibles
     */
    protected function init_available_filters() {
		global $empr_groupes_localises;
        
        parent::init_available_filters();
        if($empr_groupes_localises) {
            $this->available_filters['main_fields']['empr_resp_group_location'] = 'empr_resp_group_location';
        }
       $this->available_filters['custom_fields'] = array();
    }
    
    protected function init_default_selected_filters() {
        global $empr_groupes_localises;
        $this->selected_filters = array();
        if($empr_groupes_localises) {
            $this->add_selected_filter('empr_resp_group_location');
        }
    }
    
    protected function init_default_applied_sort() {
        $this->add_applied_sort('empr_location_libelle');
        $this->add_applied_sort('empr');
        $this->add_applied_sort('pret_retour');
    }
    
    protected function init_default_applied_group() {
    	$this->applied_group = array(0 => 'expl_location_libelle');
    }
    
    protected function get_cell_group_label($group_label, $indice=0) {
    	global $msg;
    	
    	$content = '';
    	switch($this->applied_group[$indice]) {
    		case 'expl_location_libelle':
    			$content .= $msg["expl_header_location_libelle"]." : ".$group_label;
    			break;
    		default :
    			$content .= parent::get_cell_group_label($group_label, $indice);
    			break;
    	}
    	return $content;
    }
    
    protected function get_display_group_header_list($group_label, $level=1, $uid='') {
    	$display = "
		<tr id='".$uid."_group_header' class='tb_pret_location_row'>
			<td class='list_ui_content_list_group list_ui_content_list_group_level_".$level." ".$this->objects_type."_content_list_group ".$this->objects_type."_content_list_group_level_".$level."' colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</td>
		</tr>";
    	return $display;
    }
    
    protected function init_default_columns() {
    	global $opac_pret_groupe_prolongation;
    	global $lvl;
    	
    	if($opac_pret_groupe_prolongation) {
    		$this->add_column_selection();
    	}
    	$this->add_column('empr');
    	$this->add_column('record');
    	$this->add_column('typdoc');
    	$this->add_column('pret_date');
    	$this->add_column('pret_retour');
    	if ($lvl!="late") {
    		$this->add_column('late');
    	}
    	if($opac_pret_groupe_prolongation) {
    		$this->add_column_date_prolongation();
    	}
    }
    
    protected function add_column_date_prolongation() {
    	global $msg;
    	
    	$this->columns[] = array(
    			'property' => 'date_prolongation',
    			'label' => $msg['group_prolonge_pret_date_title'],
    			'html' => "",
    			'exportable' => false
    	);
    }
    
    protected function init_no_sortable_columns() {
    	parent::init_no_sortable_columns();
    	$this->no_sortable_columns[] = 'date_prolongation';
    }
    
    protected function _get_query_order() {
        return ' GROUP BY pret_idempr, pret_idexpl '.parent::_get_query_order();
    }
    
    protected function init_default_selection_actions() {
    	global $msg;
    	global $opac_pret_groupe_prolongation;
    	
    	parent::init_default_selection_actions();
    	if($opac_pret_groupe_prolongation) {
    		$prolonge_link = array(
					'href' => static::get_controller_url_base().'&action=group_prolonge_pret',
    				'confirm' => $msg['group_prolonge_pret_confirm']
			);
			$this->add_selection_action('group_prolonge_pret', $msg['group_prolonge_pret'], '', $prolonge_link);
    	}
    }
    
    protected function get_inheritance_nodes_selected_objects_form($action=array()) {
        if($action['name'] == 'group_prolonge_pret') {
            return "
                var selected_date_prolongation_number = 0;
    			selection.forEach(function(selected_option) {
                    if(dom.byId('group_prolonge_pret_date_'+selected_option) && dom.byId('group_prolonge_pret_date_'+selected_option).value) {
        				var date_prolongation_hidden = domConstruct.create('input', {
        					type : 'hidden',
        					name : 'group_prolonge_pret_date['+selected_option+']',
        					value : dom.byId('group_prolonge_pret_date_'+selected_option).value
        
        				});
        				domConstruct.place(date_prolongation_hidden, selected_objects_form);
                        selected_date_prolongation_number++;
                    }
    			});
                if(!selected_date_prolongation_number) {
                    alert(pmbDojo.messages.getMessage('empr', 'group_prolonge_pret_no_date'));
    				return false;
                }
    		";
        }
    }
    
    protected function get_cell_content($object, $property) {
        global $charset;
        
    	$content = '';
    	switch($property) {
    		case 'date_prolongation':
    		    if($object->is_extendable()) {
//     		        $content .= "<input type='date' name='group_prolonge_pret_date[".$object->id_expl."]' id='group_prolonge_pret_date_".$object->id_expl."' value='".$object->date_prolongation."' title='" . $msg['group_prolonge_pret_date_title'] . "' />";
    		        
    		        //La date de prolongation n'est plus modifiable
    		        $content .= "<span>".formatdate($object->date_prolongation)."</span>";
    		        $content .= "<input type='hidden' name='group_prolonge_pret_date[".$object->id_expl."]' id='group_prolonge_pret_date_".$object->id_expl."' value='".$object->date_prolongation."' />";
    		    } else {
    		        $content .= "<img src='".get_url_icon("no_prolongation.png")."' style='border:0px' title='".htmlentities($object->no_prolong_explanation,ENT_QUOTES,$charset)."' alt=''/>";
    		    }
    			break;
    		default:
    			$content .= parent::get_cell_content($object, $property);
    			break;
    	}
    	return $content;
    }
    
    public static function get_controller_url_base() {
    	global $base_path;
    	
    	return $base_path.'/empr.php?tab=loan_reza&lvl=all';
    	
	}
}