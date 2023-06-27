<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_controller.class.php,v 1.2 2018-06-08 08:10:27 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/pnb/pnb.class.php');

class pnb_controller {
	
	public function proceed() {
		global $lvl;
		global $sub;
		$empr_id = $_SESSION['id_empr_session']*1;
		if (!$empr_id) {
			die();
		}
		
		$pnb = new pnb();
		
		switch ($lvl) {
			case 'pnb_loan_list' :
				print $pnb->get_empr_loans_list($empr_id);
				break;
			case 'pnb_devices' :
				switch($sub){
					default:
						print $pnb->get_devices_list($empr_id);
						break;
					case 'save': 
						$pnb->save_devices_list($empr_id);
						print $pnb->get_devices_list($empr_id);
						break;
				}
				break;
			case 'pnb_parameters':
				switch($sub){
					default:
						print $pnb->get_parameters($empr_id);
						break;
					case 'save':
						$pnb->save_parameters($empr_id);
						print $pnb->get_parameters($empr_id);
						break;
					break;
				}
		}
	}
	
	public function proceed_ajax() {
		global $action;
		$pnb = new pnb();
		switch($action) {
			case 'loan' :
				$pnb->loan_book();
				break;
			case 'get_loan_form':
				$pnb->get_loan_form();
				break;
			case 'post_loan_info':
				$pnb->loan_book();
				break;
		}
	}
}// end class