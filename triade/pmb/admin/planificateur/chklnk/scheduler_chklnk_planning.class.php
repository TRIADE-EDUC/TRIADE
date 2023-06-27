<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_chklnk_planning.class.php,v 1.1 2017-10-26 10:16:36 dgoron Exp $

require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/chklnk/chklnk.class.php");
		
class scheduler_chklnk_planning extends scheduler_planning {
		
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		$form_task = "
		<div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_chklnk_checking"]."</label>
			</div>
			<div class='colonne_suite'>";
		if(!isset($param['scheduler_chknk_filtering_parameters'])) {
			$param['scheduler_chknk_filtering_parameters'] = array();
		}
		chklnk::set_filtering_parameters($param['scheduler_chknk_filtering_parameters']);
		if(!isset($param['scheduler_chknk_parameters'])) {
			$param['scheduler_chknk_parameters'] = array();
		}
		chklnk::set_parameters($param['scheduler_chknk_parameters']);
		chklnk::set_curl_timeout($param['scheduler_chknk_curltimeout']);
		$chklnk = new chklnk();
		$form_task .= $chklnk->get_content_form();
		$form_task .= "
			</div>
		</div>
		<div class='row'>&nbsp;</div>";	
								
		return $form_task;
	}
	
	public function make_serialized_task_params() {
		global $filtering_parameters;
		global $parameters;
		global $chkcurltimeout;
		
		$t = parent::make_serialized_task_params();
		
		$t['scheduler_chknk_filtering_parameters'] = $filtering_parameters;
		$t['scheduler_chknk_parameters'] = $parameters;
		$t['scheduler_chknk_curltimeout'] = stripslashes($chkcurltimeout);

    	return serialize($t);
	}
}


