<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_facettes_root_ui.class.php,v 1.7.4.1 2025/03/25 14:54:03 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_opac_facettes_root_ui extends list_configuration_opac_ui {
	
	protected static $facettes_model;
	
	protected $fields;
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg["title_tab_facette"], ENT_QUOTES, $charset);
	}
	
	public static function set_facettes_model($facettes_model) {
		static::$facettes_model = $facettes_model;
	}
	
	protected function get_fields() {
		if(!isset($this->fields)) {
			$this->fields = static::$facettes_model->fields_sort();
		}
		return $this->fields;
	}
	
	public function init_filters($filters=array()) {
		$this->filters = array(
				'type' => '',
                'num_facettes_set' => 0
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('facette_order');
	    $this->add_applied_sort('facette_name');
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('facette_order', 'datatype', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'facette_order' => 'facette_order',
				'facette_name' => 'intitule_vue_facette'
		);
		if ($this->filters['type'] == 'authperso') {
			$main_fields['facette_authperso'] = 'admin_authperso_form_name';
		}
		$main_fields['facette_critere'] = 'critP_vue_facette';
		$main_fields['facette_ss_critere'] = 'ssCrit_vue_facette';
		$main_fields['facette_nb_result'] = 'nbRslt_vue_facette';
		$main_fields['facette_type_sort'] = 'sort_view_facette';
		$main_fields['facette_visible_gestion'] = 'facettes_admin_visible_gestion';
		$main_fields['facette_visible'] = 'visible_facette';
		return $main_fields;
	}
	
	protected function _add_query_filters() {
		$this->query_filters [] = 'facette_type LIKE "'.$this->filters['type'].'%"';
		$this->query_filters [] = 'num_facettes_set = "0"';
	}
	
	public function get_dataset_title() {
	    global $msg, $charset;
	    
	    return htmlentities($msg['list_configuration_facettes'], ENT_QUOTES, $charset);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'facette_order':
				$content .= "
					<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=down&id=".$object->id_facette."'\" style='cursor:pointer;'/>
					<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=up&id=".$object->id_facette."'\" style='cursor:pointer;'/>
				";
				break;
			case 'facette_authperso':
				$authperso =  explode("_",$object->facette_type);
				$authperso_id = 0;
				if (!empty($authperso[1]) && intval($authperso[1])) {
					$authperso_id = $authperso[1];
				}
				$authperso_query = "select authperso_name from authperso where id_authperso =".$authperso_id;
				$authperso_result = pmb_mysql_query($authperso_query);
				if (pmb_mysql_num_rows($authperso_result)) {
					$authperso_row = pmb_mysql_fetch_object($authperso_result);
					$content .= $authperso_row->authperso_name;
				}
				break;
			case 'facette_critere':
				$facette_critere = $object->facette_critere;
				if ($facette_critere > static::$facettes_model->get_authperso_start() && $this->filters['type'] != "authperso") {
					$authperso_query = "select authperso_name from authperso where id_authperso =".(intval($facette_critere) - intval(static::$facettes_model->get_authperso_start()));
					$authperso_result = pmb_mysql_query($authperso_query);
					if (pmb_mysql_num_rows($authperso_result)) {
						$authperso_row = pmb_mysql_fetch_object($authperso_result);
						$content .= $authperso_row->authperso_name;
					}
				} elseif ($this->filters['type'] == "authperso")  {
					$facette_critere = substr($facette_critere, 0, 3) . "0" . substr($facette_critere, -3);
					$content .= (count($this->get_fields()) > 1 ? htmlentities($this->get_fields()[$facette_critere], ENT_QUOTES, $charset) : $msg["admin_opac_facette_ss_critere"]);
				} else {
					$content .= $this->get_fields()[$facette_critere];
				}
				break;
			case 'facette_ss_critere':
				$array_subfields = static::$facettes_model->array_subfields($object->facette_critere);
				$content .= (count($array_subfields) > 1 ? htmlentities($array_subfields[$object->facette_ss_critere], ENT_QUOTES, $charset) : $msg["admin_opac_facette_ss_critere"]);
				break;
			case 'facette_nb_result':
				if ($object->facette_nb_result) {
					$content .= $object->facette_nb_result;
				} else {
					$content .= $msg["admin_opac_facette_illimite"];
				}
				break;
			case 'facette_type_sort':
				if ($object->facette_type_sort) {
					$content .= $msg['intit_gest_tri2'];
				} else {
					$content .= $msg['intit_gest_tri1'];
				}
				$content .= " ";
				if ($object->facette_order_sort) {
					$content .= $msg['intit_gest_tri4'];
				} else {
					$content .= $msg['intit_gest_tri3'];
				}
				break;
			case 'facette_visible_gestion':
			case 'facette_visible':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_order() {
		global $msg;
		
		return $this->get_button('order', $msg['facette_order_bt']);
	}
	
	protected function get_display_left_actions() {
		$display = parent::get_display_left_actions();
		$display .= $this->get_button_order();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'facette_order':
				return array();
			default :
				return array(
						'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
				);
		}
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['lib_nelle_facette_form'];
	}
}