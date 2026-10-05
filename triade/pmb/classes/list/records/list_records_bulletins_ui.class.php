<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_records_bulletins_ui.class.php,v 1.12 2023/12/18 15:55:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serials.class.php");

class list_records_bulletins_ui extends list_records_ui {
	
	protected function _get_query_base() {
		$aq_members = $this->get_aq_members();
		$query = 'SELECT bulletin_id,'.$aq_members["select"].' as pert FROM bulletins 
				JOIN notices ON bulletin_notice=notice_id ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new bulletinage($row->bulletin_id);
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'pert':
	            return 'pert, index_sew, date_date, bulletin_id';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'empty' => 'search_empty_field',
						'caddie' => 'caddie_de_BULL',
						'bulletin_numero' => '4025',
						'mention_date' => 'bulletin_mention_periode',
						'aff_date_date' => '4026',
						'bulletin_titre' => 'bulletin_mention_titre',
						'expl' => 'bulletin_nb_exemplaires',
						'record_header' => 'titre_perio_query',
				        'record_isbd' => 'serial_isbd'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function _get_object_property_expl($object) {
		global $msg;
		
		if (!empty($object->expl)) {
			return count($object->expl)." ".$msg['bulletin_nb_exemplaires'];
		}
		return '';
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch ($property) {
			case 'caddie':
				// gestion des paniers de bulletins
				$cart_click_bull = "onClick=\"openPopUp('./cart.php?object_type=BULL&item=$object->bulletin_id', 'cart')\"";
				$content .= "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' $cart_click_bull>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		switch($property) {
			case 'bulletin_numero':
				$attributes['href'] = bulletinage::get_permalink($object->bulletin_id);
			default:
				break;
		}
		return $attributes;
	}
}