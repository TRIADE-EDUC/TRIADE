<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_edition_ui.class.php,v 1.5 2019-04-26 15:49:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/readers/list_readers_ui.class.php");
require_once($include_path."/templates/list/readers/list_readers_edition_ui.tpl.php");

class list_readers_edition_ui extends list_readers_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_form_title() {
		return '';
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
		
	protected function get_display_spreadsheet_title() {
		global $titre_page;
		$this->spreadsheet->write_string(0,0,$titre_page);
	}
	
	protected function get_html_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_selection_actions() {
		global $msg;
		global $sub;
		global $current_module;
		global $empr_show_caddie;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			switch ($sub) {
				case 'categ_change':
					$link = array(
						'href' => static::get_controller_url_base()."&categ_action=change_categ_empr",
						'confirm' => $msg["empr_categ_confirm_change"]
					);
					$this->selection_actions[] = $this->get_selection_action('change_categ', $msg["save_change_categ"], 'group_by_grey.png', $link);
					break;
				case 'limite':
				case 'depasse':
					$link = array(
						'href' => static::get_controller_url_base()."&action=print_all"
					);
					$this->selection_actions[] = $this->get_selection_action('print_all_relances', $msg["print_all_relances"], 'doc.gif', $link);
					break;
			}
			if ($empr_show_caddie) {
				$link = array();
				$link['openPopUp'] = "./cart.php?object_type=EMPR&action=add_empr_".$sub."&sub_action=add";
				$link['openPopUpTitle'] = 'cart';
				$this->selection_actions[] = $this->get_selection_action('add_empr_cart', $msg['add_empr_cart'], 'basket_20x20.gif', $link);
			}
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return "button";
	}
		
	/**
	 * Affichage d'une colonne
	 * @param unknown $object
	 * @param string $property
	 */
	protected function get_display_cell($object, $property) {
		$display = '';
		switch ($property) {
			case 'relance':
			case 'categ_change':
				$display .= parent::get_display_cell($object, $property);
				break;
			default:
				$display .= "<td class='center' onmousedown=\"document.location='".$this->get_edition_link($object)."';\" style='cursor: pointer'>".$this->get_cell_content($object, $property)."</td>";
				break;
		}
		return $display;
	}
	
	protected function add_event_on_selection_action($action=array()) {
		global $msg, $charset;
	
		$display = "
			on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					selection.push(node.value);
				});
				var categ_change = new Array();
				query('.".$this->objects_type."_categ_change').forEach(function(node) {
					categ_change.push(node);
				});
				if(selection.length) {
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
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
							categ_change.forEach(function(selector_node) {
								var empr_id = selector_node.getAttribute('data-empr-id');
								var selected_categs_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->objects_type."_categ_change['+empr_id+']',
									value : selector_node.value
								});
								domConstruct.place(selected_categs_hidden, selected_objects_form);
							});	
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
						: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				} else {
					alert('".addslashes($msg['list_ui_no_selected'])."');
				}
			});
		";
		return $display;
	}
	
	protected function get_display_others_actions() {
		global $msg, $charset;
		
		return "
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
		<span class='right list_ui_other_action_empr_change_status ".$this->objects_type."_other_action_empr_change_status'>
			".$msg["empr_chang_statut"]."&nbsp;
			".gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle",$this->objects_type."_selection_action_empr_change_status","","",0,(isset($msg['none']) ? $msg['none'] : ''),0,(isset($msg['none']) ? $msg['none'] : ''))."
			&nbsp;<input type='button' id='".$this->objects_type."_other_action_empr_change_status_link' class='bouton_small' value='".$msg['empr_chang_statut_button']."' />
		</span>
		<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){
			on(dom.byId('".$this->objects_type."_other_action_empr_change_status_link'), 'click', function() {		
				var statut_action = domConstruct.create('input', {
					type : 'hidden',
					id : 'statut_action',
					name : 'statut_action',
					value : 'modify'
				});
				domConstruct.place(statut_action, dom.byId('".$this->objects_type."_search_form'));
						
				var change_status_hidden = domConstruct.create('input', {
					type : 'hidden',
					id : '".$this->objects_type."_empr_change_status',
					name : '".$this->objects_type."_empr_change_status',
					value : dom.byId('".$this->objects_type."_selection_action_empr_change_status').value
				});
				domConstruct.place(change_status_hidden, dom.byId('".$this->objects_type."_search_form'));
						
				dom.byId('".$this->objects_type."_search_form').submit();
			});
		});
		</script>";
	}
	
	public function run_action_add_caddie() {
		global $caddie;
		
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach($caddie as $id_caddie => $coche) {
				if($coche){
					$myCart = new empr_caddie($id_caddie);
					foreach ($selected_objects as $id) {
						$myCart->add_item($id);
					}
				}
			}
		}
	}
	
	public function run_change_status() {
		$change_status = $this->objects_type."_empr_change_status";
		global ${$change_status};
		if(!empty(${$change_status})) {
			foreach ($this->objects as $object) {
				$query = "UPDATE empr set empr_statut='".$$change_status."' where id_empr = ".$object->id;
				pmb_mysql_query($query);
				$object->set_empr_statut($$change_status);
			}
		}
	}
	
	protected function get_edition_link($object) {
		global $base_path;
		return $base_path.'/circ.php?categ=pret&form_cb='.$object->cb;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $sub;
	
		return $base_path.'/edit.php?categ=empr&sub='.$sub;
	}
}