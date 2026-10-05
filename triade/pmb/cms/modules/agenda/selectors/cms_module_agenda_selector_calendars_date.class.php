<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_selector_calendars_date.class.php,v 1.1 2012/10/12 14:03:49 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_agenda_selector_calendars_date extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	protected function get_sub_selectors(){
		return array(
			'cms_module_agenda_selector_calendars',
			'cms_module_common_selector_env_var'
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$calendars = new cms_module_agenda_selector_calendars($this->get_sub_selector_id('cms_module_agenda_selector_calendars'));
			$date = new cms_module_common_selector_env_var($this->get_sub_selector_id('cms_module_common_selector_env_var'));
			$this->value = array(
				'calendars' => $calendars->get_value(),
				'date' => $date->get_value()
			);
		}
		return $this->value;
	}
}