<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_items_treat_ui.class.php,v 1.1.4.3 2025/05/02 12:27:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_items_treat_ui extends list_items_ui {
	
    protected $is_displayed_add_filters_block = false;
    
    protected function get_title() {
        global $msg;
        
        return "<h3>".$msg['expl_todo_liste']."</h3>";
    }
    
    protected function init_default_selected_filters() {
        global $pmb_lecteurs_localises;
        
        //Uniquement possible si le droit "PREF_AUTH" est autorisé
        if($pmb_lecteurs_localises && defined('SESSrights') && SESSrights & PREF_AUTH) {
            $this->add_selected_filter('expl_retloc');
        }
    }
    
    protected function init_available_columns() {
        parent::init_available_columns();
        $this->available_columns['main_fields']['item_header'] = 'ISBD';
    }
    
    protected function init_default_columns() {
        $this->add_column('item_header');
    }
    
	protected function init_default_settings() {
	    global $pmb_lecteurs_localises;
		parent::init_default_settings();
		if($pmb_lecteurs_localises && defined('SESSrights') && (SESSrights & PREF_AUTH)) {
		    $this->set_setting_display('search_form', 'unfoldable_filters', false);
		    $this->set_setting_display('search_form', 'sorts', false);
		    $this->set_setting_display('search_form', 'export_icons', true);
		} else {
		    $this->set_setting_display('search_form', 'visible', false);
		}
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_div';
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function _get_object_property_item_header($object) {
	    if($stuff = get_expl_info($object->expl_id)) {
	        $stuff = check_pret($stuff);
	        return print_info($stuff,2,0,0);
	    }
	    return '';
	}
	
	protected function get_cell_content($object, $property) {
	    $content = '';
	    switch($property) {
	        case 'item_header':
	            if($expl = get_expl_info($object->expl_id)) {
	                $expl = check_pret($expl);
	                $content .=  print_info($expl,0,0,0);
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	protected function get_display_html_cell($object, $property) {
	    $display = '';
	    switch($property) {
	        case 'item_header':
	            $content = '';
	            if($expl = get_expl_info($object->expl_id)) {
	                $expl = check_pret($expl);
	                $content .=  print_info($expl,3,0,0);
	            }
	            $display .= "<td class='center'>".strip_tags($content)."</td>";
                break;
	        default:
                $display .= parent::get_display_html_cell($object, $property)();
	            break;
	    }
	    return $display;
	}
	
	protected function get_display_spreadsheet_cell($object, $property, $row, $col) {
	    global $msg;
	    switch($property) {
	        case 'item_header':
	            $content = '';
	            if($expl = get_expl_info($object->expl_id)) {
	                $expl = check_pret($expl);
	                $content .=  print_info($expl,3,0,0);
	            }
	            $this->spreadsheet->write_string($row,$col, strip_tags($content));
	            break;
	        default:
	            parent::get_display_spreadsheet_cell($object, $property, $row, $col);
	            break;
	    }
	    
	}
	
	public function get_error_message_empty_list() {
	    global $msg;
	    
	    return $msg['resa_liste_docranger_nodoc'];
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ;
		
		return $base_path.'/circ.php?categ='.$categ;
	}
}