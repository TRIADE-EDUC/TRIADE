<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_access_rights_planning.class.php,v 1.1 2020/04/22 08:49:33 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/acces.class.php");
		
class scheduler_access_rights_planning extends scheduler_planning {
	
	protected function get_checkbox_form($path, $property) {
		global $charset;
		
		$checked = false;
		if(isset($this->param['access_rights'][$path][$property]) && $this->param['access_rights'][$path][$property] == "1") {
			$checked = true;
		} elseif(!$this->id && ($property == 'initialization' || $property == 'keep_specific_rights')) {
			$checked = true;
		}
		return "<input type='checkbox' value='1' id='".$path."_".$property."' name='access_rights[".$path."][".$property."]' ".($checked ? "checked='checked'" : "")." />
				&nbsp;<label for='".$path."_initialization' >".htmlentities($this->msg['planificateur_access_rights_'.$property], ENT_QUOTES, $charset)."</label>";
	}
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $charset;
		
		$form_task = "";
		$ac = new acces();
		$t_cat= $ac->getCatalog();
		foreach($t_cat as $cat) {
			$form_task .= "
			<div class='row'>
				<div class='colonne3'>
					<label>".htmlentities($cat['comment'], ENT_QUOTES, $charset)."</label>
				</div>
				<div class='colonne3'>
					".$this->get_checkbox_form($cat['path'], 'delete_calculated_rights')."
					<br />
					".$this->get_checkbox_form($cat['path'], 'initialization')."
					<br />	
					".$this->get_checkbox_form($cat['path'], 'keep_specific_rights')."
				<div>
			</div></div>
			";
		}
		return $form_task;
	}
		    
	public function make_serialized_task_params() {
    	global $access_rights;

		$t = parent::make_serialized_task_params();
		$t["access_rights"] = $access_rights;
    	return serialize($t);
	}
}


