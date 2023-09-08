<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_planning.class.php,v 1.2 2018-06-05 12:48:40 ngantier Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class pnb_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param='') {
		global $charset;
/*		
		$form_task.= '
		<div class="row">
			<div class="colonne3">
				<label for="pnb_import_notice_statut">'.$this->msg['pnb_import_notice_statut'].'</label>
			</div>
			<div class="colonne_suite">
				<select name="pnb_import_notice_statut" id="pnb_import_notice_statut">';
		$result = pmb_mysql_query('select id_notice_statut as id, gestion_libelle as label from notice_statut');
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$form_task.= '
					<option value="'.$row->id.'"'.(($row->id == $param['pnb_import_notice_statut']) ? ' selected="selected"' : '').'>'.htmlentities($row->label, ENT_QUOTES, $charset).'</option>';
			}
		}
		$form_task.= '
				</select>
			</div>
		</div>';		
		
		return $form_task;
*/
		return '';
	}

	function make_serialized_task_params() {
		//global $pnb_import_notice_statut;

		$t = parent::make_serialized_task_params();
		//$t['pnb_import_notice_statut'] = $pnb_import_notice_statut;		
		return serialize($t);
	}
}