<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync_pnb_planning.class.php,v 1.4.10.1 2025/03/13 16:40:34 qvarin Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class sync_pnb_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param='') {

		$pnb_do_full_sync = 0;
		if(!empty($param['pnb_do_full_sync'])) {
			$pnb_do_full_sync = intval($param['pnb_do_full_sync']);
		}
		$pnb_check_only_ended_loans = 0;
		if(!empty($param['pnb_check_only_ended_loans'])) {
			$pnb_check_only_ended_loans = intval($param['pnb_check_only_ended_loans']);
		}
		$form_task = '
		<div class="row">
			<div class="colonne3">
				<label>'.$this->msg['pnb_sync_type'].'</label>
			</div>
			<div class="colonne_suite">
				<input type="radio" name="pnb_do_full_sync" id="pnb_sync_diff" value="0" '.(($pnb_do_full_sync)?'':'checked').' />
				<label for="pnb_sync_diff">'.$this->msg['pnb_sync_diff'].'</label>
				<input type="radio" name="pnb_do_full_sync" id="pnb_sync_full" value="1" '.(($pnb_do_full_sync)?'checked':'').'/>
				<label for="pnb_sync_full">'.$this->msg['pnb_sync_full'].'</label>
			</div>
		</div>
		<div class="row">
			<div class="colonne3">
				<label>'.$this->msg['pnb_check_only_ended_loans'].'</label>
			</div>
			<div class="colonne_suite">
				<input type="checkbox" name="pnb_check_only_ended_loans" id="pnb_check_only_ended_loans" value="1" '.(($pnb_check_only_ended_loans)?'checked':'').' />
			</div>
		</div>
		<div class="row">&nbsp;</div> ';

		return $form_task;

	}

	public function make_serialized_task_params() {

		global $pnb_do_full_sync, $pnb_check_only_ended_loans;

		if(empty($pnb_do_full_sync)) {
			$pnb_do_full_sync = 0;
		}
		$pnb_do_full_sync = intval($pnb_do_full_sync);

		if(empty($pnb_check_only_ended_loans)) {
			$pnb_check_only_ended_loans = 0;
		}
		$pnb_check_only_ended_loans = intval($pnb_check_only_ended_loans);

		$t = parent::make_serialized_task_params();
		$t['pnb_do_full_sync'] = $pnb_do_full_sync;
		$t['pnb_check_only_ended_loans'] = $pnb_check_only_ended_loans;
		return serialize($t);
	}
}