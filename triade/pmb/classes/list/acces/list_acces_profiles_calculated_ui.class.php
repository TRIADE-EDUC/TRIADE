<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_calculated_ui.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_acces_profiles_calculated_ui extends list_acces_profiles_ui {
	
	protected $t_reused;
	
	protected function get_array_calc() {
		return array();
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$t_calc = $this->get_array_calc();
		$this->t_reused=array();
		if (count($t_calc)) {
			foreach($t_calc as $k=>$v) {
				//printr($v);
				if ($v['old']) $this->t_reused[]=$v['old'];
				$object = new stdClass();
				$object->prf_id = $k;
				$object->prf_name = $v['name'];
				$object->prf_rule = $v['rule'];
				$object->prf_hrule = $v['hrule'];
				$object->prf_used = $v['old'];
				$object->dom_num = static::$domain;
				$object->prf_old = $v['old'];
				$this->add_object($object);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
	
	protected function init_default_columns() {
		$this->add_column('prf_name');
		$this->add_column('prf_rule');
	}
	
	public function get_display_search_form() {
		return '';
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('prf_name', 'display_mode', 'normal');
	}
	
	protected function init_default_selection_actions() {
		$this->selection_actions = array();
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'prf_name':
				$content.= htmlentities($object->prf_name,ENT_QUOTES, $charset);
				$content.= "<input type='hidden' id='prf_lib[".$object->prf_id."]' name='prf_lib[".$object->prf_id."]' value='".htmlentities($object->prf_name, ENT_QUOTES, $charset)."' />";
				break;
			case 'prf_rule':
				$content.= nl2br(htmlentities($object->prf_hrule,ENT_QUOTES, $charset));
				$content.= "<input type=hidden id='prf_hrule[".$object->prf_id."]' name='prf_hrule[".$object->prf_id."]' value='".$object->prf_hrule."' />";
				$content.= "<input type='hidden' id='prf_id[".$object->prf_id."]' name='prf_id[".$object->prf_id."]' value='".$object->prf_old."' />";
				$content.= "<input type='hidden' id='prf_rule[".$object->prf_id."]' name='prf_rule[".$object->prf_id."]' value='".$object->prf_rule."' />";
				$content.= "<input type='hidden' id='prf_used[".$object->prf_id."]' name='prf_used[".$object->prf_id."]' value='".$object->prf_old."' />";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_delete() {
		return "";
	}
}