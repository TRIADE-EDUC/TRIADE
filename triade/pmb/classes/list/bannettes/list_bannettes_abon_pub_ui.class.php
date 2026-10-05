<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_abon_pub_ui.class.php,v 1.2 2022/03/22 11:02:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_bannettes_abon_pub_ui extends list_bannettes_abon_ui {
	
	protected function _get_query() {
	    $query = $this->_get_query_base();
        $query .= " join bannette_abon on num_bannette=id_bannette ";
	    $query .= $this->_get_query_filters();
        $query .= " union ".$this->_get_query_base()." where ((id_bannette IN (".implode(',',$this->get_access_liste_id()).")) or (bannette_opac_accueil = 1)) and proprio_bannette=0 ";
	    $query .= $this->_get_query_order();
	    if($this->applied_sort_type == "SQL"){
	        $this->pager['nb_results'] = pmb_mysql_num_rows(pmb_mysql_query($query));
	        $query .= $this->_get_query_pager();
	    }
	    return $query;
	}
	
	protected function get_title() {
		global $msg;
		
		return "<h3><span>".$msg['dsi_bannette_gerer_pub']."</span></h3>\n";
	}
	
	protected function add_event_on_selection_action($action=array()) {
		if($action['name'] == 'save') {
			$display = "
				on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
					var selection = new Array();
					query('.".$this->objects_type."_selection:checked').forEach(function(node) {
						selection.push(node.value);
					});
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
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							domConstruct.destroy(dom.byId('".$this->objects_type."_selected_objects_form'));
							"
								: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				});
			";
		} else {
			$display = parent::add_event_on_selection_action($action);
		}
		return $display;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$save_link = array(
				'onClick' => "save_bannette_abon"
		);
		$this->add_selection_action('save', $msg['77'], 'sauv.gif', $save_link);
	}
}