<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_edition_ui.class.php,v 1.22.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/list/readers/list_readers_edition_ui.tpl.php");

class list_readers_edition_ui extends list_readers_ui {
	
	protected function init_available_filters() {
		parent::init_available_filters();
		//Contre productif par rapport aux contextes des menus (lecteurs en cours - proche fin d'abon. - abon. dépassé)
		unset($this->available_filters['main_fields']['date_expiration']);
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
	
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('location');
		}
		$this->add_selected_filter('status');
		$this->add_empty_selected_filter();
		if(!$pmb_lecteurs_localises) {
			$this->add_empty_selected_filter();
		}
		$this->add_selected_filter('categorie');
		$this->add_selected_filter('codestat_one');
	}
	
	protected function init_default_columns() {
		global $sub;
		
		if(count($this->get_selection_actions())) {
			$this->add_column_selection();
		}
		$this->add_column('cb');
		$this->add_column('empr_name');
		$this->add_column('adr1');
		$this->add_column('ville');
		$this->add_column('birth');
		$this->add_column('aff_date_expiration');
		$this->add_column('empr_statut_libelle');
		switch ($sub) {
			case "encours" :
				break;
			case "categ_change" :
				$this->add_column('categ_libelle');
				$this->add_column('categ_change');
				break;
			default :
				$this->add_column('relance', '');
				break;
		}
	}
		
	protected function get_sub_title() {
		global $sub, $msg;
		switch ($sub) {
			case "limite" :
				return $msg["edit_titre_empr_abo_limite"];
			case "depasse" :
				return $msg["edit_titre_empr_abo_depasse"];
			case "categ_change" :
				return $msg["edit_titre_empr_categ_change"];
			case "encours" :
			default :
				return $msg["1121"];
		}
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg["1120"].": ".$this->get_sub_title());
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg["1120"].": ".$this->get_sub_title()."</h1>";
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $sub;
		global $empr_relance_adhesion;
		global $empr_show_caddie;
		
		switch ($sub) {
			case 'categ_change':
				$link = array(
					'href' => static::get_controller_url_base()."&categ_action=change_categ_empr",
					'confirm' => $msg["empr_categ_confirm_change"]
				);
				$this->add_selection_action('change_categ', $msg["save_change_categ"], 'group_by_grey.png', $link);
				break;
			case 'limite':
			case 'depasse':
				$link = array(
					'href' => static::get_controller_url_base()."&action=print",
                    'confirm' => ($empr_relance_adhesion ? $msg["readers_relances_send_confirm"] : $msg["readers_relances_print_confirm"])
				);
				$this->add_selection_action('print_relances', ($empr_relance_adhesion ? $msg["readers_relances_send"] : $msg["readers_relances_print"]), 'doc.gif', $link);
				break;
		}
		if ($empr_show_caddie) {
			$link = array(
					'openPopUp' => "./cart.php?object_type=EMPR&action=add_empr_".$sub."&sub_action=add",
					'openPopUpTitle' => 'cart'
			);
			$this->add_selection_action('add_empr_cart', $msg['add_empr_cart'], 'basket_20x20.gif', $link);
		}
	}
	
	protected function get_selection_mode() {
		return "button";
	}
		
	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'relance':
			case 'categ_change':
				return array();
			default:
				return array(
						'onclick' => "document.location=\"".$this->get_edition_link($object)."\";"
				);
		}
	}
	
	protected function get_search_buttons_extension() {
	    global $msg, $charset;
	    global $empr_relance_adhesion;
	    
	    if(count($this->objects)) {
            $action = array(
    	            'name' => 'print_all_relances',
                    'link' => array(
                        'href' => static::get_controller_url_base()."&action=print_all",
                        'confirm' => ($empr_relance_adhesion ? $msg['readers_all_relances_send_confirm'] : $msg['readers_all_relances_print_confirm'])
                    )
            );
            if($empr_relance_adhesion) {
                $label = $msg['readers_all_relances_send'];
            } else {
                $label = $msg['readers_all_relances_print'];
            }
            return 
    			$this->get_interface_button($label, [], ['id' => $this->objects_type."_global_action_print_all_relances_link"])
    			.$this->add_event_on_global_action($action);
	    }
	    return "";
	}
	
	protected function get_inheritance_nodes_selected_objects_form($action=array()) {
		return "
			var categ_change = new Array();
			query('.".$this->objects_type."_categ_change').forEach(function(node) {
				categ_change.push(node);
			});
			categ_change.forEach(function(selector_node) {
				var empr_id = selector_node.getAttribute('data-empr-id');
				var selected_categs_hidden = domConstruct.create('input', {
					type : 'hidden',
					name : '".$this->objects_type."_categ_change['+empr_id+']',
					value : selector_node.value
				});
				domConstruct.place(selected_categs_hidden, selected_objects_form);
			});
		";
	}
	
	protected function get_display_others_actions() {
		global $msg;
		
		return "
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
		<span class='right list_ui_other_action_empr_change_status ".$this->objects_type."_other_action_empr_change_status'>
			<label for='".$this->objects_type."_selection_action_empr_change_status'>".$msg["empr_chang_statut"]."</label>&nbsp;
			".gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle",$this->objects_type."_selection_action_empr_change_status","","",0,(isset($msg['none']) ? $msg['none'] : ''),0,(isset($msg['none']) ? $msg['none'] : ''))."
			&nbsp;".$this->get_interface_button_small($msg['empr_chang_statut_button'], [], ['id' => $this->objects_type."_other_action_empr_change_status_link"])."
		</span>
		<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){
			on(dom.byId('".$this->objects_type."_other_action_empr_change_status_link'), 'click', function() {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					selection.push(node.value);
				});
				if(selection.length) {
					var confirm_msg = '".addslashes($msg['empr_chang_statut_confirm'])."';
					if(!confirm_msg || confirm(confirm_msg)) {
						var selected_objects_form = domConstruct.create('form', {
							action : '".static::get_controller_url_base()."&statut_action=modify',
							name : '".$this->objects_type."_selected_objects_form',
							id : '".$this->objects_type."_selected_objects_form',
							method : 'POST'
						});
						selection.forEach(function(selected_option) {
							var selected_objects_hidden = domConstruct.create('input', {
								type : 'hidden',
								name : '".$this->get_name_selected_objects()."[]',
								value : selected_option
							});
							domConstruct.place(selected_objects_hidden, selected_objects_form);
						});
						domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
						var change_status_hidden = domConstruct.create('input', {
							type : 'hidden',
							id : '".$this->objects_type."_empr_change_status',
							name : '".$this->objects_type."_empr_change_status',
							value : dom.byId('".$this->objects_type."_selection_action_empr_change_status').value
						});
						domConstruct.place(change_status_hidden, dom.byId('".$this->objects_type."_selected_objects_form'));
						dom.byId('".$this->objects_type."_selected_objects_form').submit();
					}
				} else {
					alert('".addslashes($msg['list_ui_no_selected'])."');
				}
			});
		});
		</script>";
	}
	
	public function run_change_status() {
		$change_status = $this->objects_type."_empr_change_status";
		global ${$change_status};
		if(!empty(${$change_status})) {
			$selected_objects = static::get_selected_objects();
			if(count($selected_objects)) {
				foreach ($this->objects as $object) {
					if(in_array($object->id, $selected_objects)) {
						$query = "UPDATE empr set empr_statut='".$$change_status."' where id_empr = ".$object->id;
						pmb_mysql_query($query);
						$object->set_empr_statut($$change_status);
					}
				}
			}
		}
	}
	
	protected function get_edition_link($object) {
		global $base_path;
		return $base_path.'/circ.php?categ=pret&form_cb='.$object->cb;
	}
}