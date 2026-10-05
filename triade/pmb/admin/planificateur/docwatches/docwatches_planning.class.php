<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatches_planning.class.php,v 1.1 2017/07/10 15:50:01 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class docwatches_planning extends scheduler_planning {
	
	public function make_serialized_task_params() {		
		$t = parent::make_serialized_task_params();	
		return serialize($t);
	}
}
