<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_unused_ui.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_acces_profiles_unused_ui extends list_acces_profiles_ui {
	
	protected static $t_reused;
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('prf_name', 'display_mode', 'normal');
	}
	
	protected function get_array_calc() {
		return array();
	}
	
	protected function get_selector_profiles_use() {
		global $charset;
		
		//generation selecteur
		$selector = "<select name='!!sel_name!!' id='!!sel_name!!'>";
		$selector.= "<option value=\"0\" >".htmlentities($this->get_dom()->getComment($this->profile_type.'_prf_def_lib'), ENT_QUOTES, $charset)."</option>";
		$t_calc = $this->get_array_calc();
		foreach($t_calc as $v) {
			$selector.= "<option value=\"".$v['old']."\" >";
			$selector.= htmlentities($v['name'], ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		$selector .= "<script type=\"text/javascript\">!!sel_script!!</script>";
		return $selector;
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'prf_rule':
				$content.= nl2br(htmlentities($object->prf_hrule,ENT_QUOTES, $charset));
				$content.= "<input type='hidden' id='unused_prf_id[".$object->prf_id."]' name='unused_prf_id[".$object->prf_id."]' value='".$object->prf_id."' />";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function init_default_selection_actions() {
		$this->selection_actions = array();
	}
	
	protected function get_button_delete() {
		return "";
	}
	
	public static function get_t_reused() {
		return static::$t_reused;
	}
			
	public static function set_t_reused($t_reused) {
		static::$t_reused = $t_reused;
	}
}