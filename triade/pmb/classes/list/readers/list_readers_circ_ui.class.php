<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_circ_ui.class.php,v 1.15.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");

class list_readers_circ_ui extends list_readers_ui {
	
    protected function get_title() {
        global $msg, $charset;
        return "<h3>".htmlentities($msg["search_search_emprunteur"], ENT_QUOTES, $charset)."</h3>";
    }
    
    protected function get_search_buttons_extension() {
    	global $empr_show_caddie;
    	global $msg;
    	
    	if ($empr_show_caddie) {
    		$action = array(
    				'name' => 'add_caddie',
    				'link' => array(
    						'href' => './cart.php?object_type=EMPR&action=add_result&list_ui_objects_type='.$this->objects_type,
    						'openPopUp' => './cart.php?object_type=EMPR&action=add_result&list_ui_objects_type='.$this->objects_type,
    						'openPopUpTitle' => 'cart'
    				)
    		);
    		return $this->get_interface_button($msg["add_empr_cart"], [], ['id' => $this->objects_type."_global_action_add_caddie_link"])
				.$this->add_event_on_global_action($action);
    	}
    	return "";
    }
    
    protected function get_default_attributes_format_cell($object, $property) {
    	global $id_notice, $id_bulletin, $type_resa, $groupID;
    	// si on est en résa on a un id de notice ou de bulletin
    	if ($id_notice || $id_bulletin) {
    		//type_resa : on est en prévision
    		if ($type_resa) {
    			$onclick = "document.location=\"./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=".$object->id."&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
    		} else {
    			$onclick = "document.location=\"./circ.php?categ=resa&id_empr=".$object->id."&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
    		}
    	} else {
    		$onclick = "if(event.ctrlKey || event.metaKey) { window.open(\"./circ.php?categ=pret&form_cb=".$object->cb."\",\"_blank\"); } else { document.location=\"./circ.php?categ=pret&form_cb=".$object->cb."\"; }";
    	}
    	return array(
    			'onclick' => $onclick
    	);
    }
	
	protected function init_default_columns() {
	    global $empr_show_caddie;
	    
	    $this->add_column_selection();
	    if(!empty(static::$used_filter_list_mode)) {
	        $displaycolumns=explode(",",static::$filter_list->displaycolumns);
	        //parcours des champs
	        foreach ($displaycolumns as $displaycolumn) {
	            if(substr($displaycolumn,0,2) == "#e") {
	                $parametres_perso = $this->get_custom_parameters_instance('empr');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($displaycolumn,2));
	                $label = $this->get_label_available_column($custom_name, 'custom_fields');
	                $this->add_column($custom_name, $label);
	            } else {
	                $this->add_column(static::$correspondence_columns_fields['main_fields'][$displaycolumn]);
	            }
	        }
	    } else {
	        $this->add_column('cb');
	        $this->add_column('empr_name');
	        $this->add_column('groups');
	        $this->add_column('adr1');
	        $this->add_column('ville');
	        $this->add_column('birth');
	        $this->add_column('nb_loans');
	        if($empr_show_caddie) {
	            $this->add_column('add_empr_cart');
	        }
	    }
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function _get_query_order() {
	    $this->applied_sort_type = 'SQL';
	    return " group by id_empr ".parent::_get_query_order();
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
	    global $charset;
	    global $human_requete;
	    
	    $search_filters_form = parent::get_search_filters_form();
	    $search_filters_form .= "<input type='hidden' name='human_requete' value='".htmlentities($human_requete ?? "", ENT_QUOTES, $charset)."' />";
	    return $search_filters_form;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $id, $id_notice, $id_bulletin, $type_resa, $groupID, $form_cb, $empr_location_id;
		global $categ;

		// si on est en résa on a un id de notice ou de bulletin
		if ($id_notice || $id_bulletin) {
		    //type_resa : on est en prévision
		    if ($type_resa) {
		        return $base_path.'/circ.php?categ='.$categ.'&form_cb='.rawurlencode($form_cb).'&resa_action=add_resa&id_empr='.$id.'&groupID='.$groupID.'&id_notice='.$id_notice.'&id_bulletin='.$id_bulletin.'&type_resa=1'.($empr_location_id ? '&empr_location_id='.$empr_location_id : '');
		    } else {
		        return $base_path.'/circ.php?categ='.$categ.'&form_cb='.rawurlencode($form_cb).'&id_empr='.$id.'&groupID='.$groupID.'&id_notice='.$id_notice.'&id_bulletin='.$id_bulletin.($empr_location_id ? '&empr_location_id='.$empr_location_id : '');
		    }
		} else {
		    switch ($categ) {
		        case 'search':
		            return $base_path.'/circ.php?categ=pret';
		        default:
		        	return $base_path.'/circ.php?categ='.$categ.'&form_cb='.rawurlencode($form_cb).($id_notice ? '&id_notice='.$id_notice : '').($id_bulletin ? '&id_bulletin='.$id_bulletin : '').($empr_location_id ? '&empr_location_id='.$empr_location_id : '');
		    }
		}
	}
}