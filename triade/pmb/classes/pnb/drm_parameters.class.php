<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: drm_parameters.class.php,v 1.3 2018-06-26 10:25:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/pnb/pnb_param.tpl.php");
require_once("$class_path/pnb/drm_parameters.class.php");


class drm_parameters {
	
	public function __construct(){
				
	}
	
	public function proceed() {
		global $action, $msg;
		
		switch ($action) {		
			case 'save':
				if($this->save()) {					
					print display_notification($msg['account_types_success_saved']);
				}
				print $this->get_form();
				break;
			case 'edit':
			default:
				print $this->get_form();
				break;
		}
	}

	private function get_form() {
		global $pnb_param_form_drm_parameters, $pmb_pnb_drm_parameters, $msg, $charset;
 		
		$drm = json_decode($pmb_pnb_drm_parameters, true);		
		$tpl = $pnb_param_form_drm_parameters;
		
		$loan_duration = 0;
		if (isset($drm['ACS']) && $drm['ACS']['loan_duration']) {
			$loan_duration = $drm['ACS']['loan_duration'];
		}
		$tpl = str_replace('!!loan_duration_ACS!!', $loan_duration, $tpl);		
		$prolongation = 0;
		if (isset($drm['ACS']) && $drm['ACS']['prolongation']) {
			$prolongation = $drm['ACS']['prolongation'];
		}		
		$tpl = str_replace('!!prolongation_ACS_checked!!', ($prolongation ? 'checked=checked' : ''), $tpl);

		$loan_duration = 0;
		if (isset($drm['LCP']) && $drm['LCP']['loan_duration']) {
			$loan_duration = $drm['LCP']['loan_duration'];
		}
		$tpl = str_replace('!!loan_duration_LCP!!', $loan_duration, $tpl);
		$prolongation = 0;
		if (isset($drm['LCP']) && $drm['LCP']['prolongation']) {
			$prolongation = $drm['LCP']['prolongation'];
		}
		$tpl = str_replace('!!prolongation_LCP_checked!!', ($prolongation ? 'checked=checked' : ''), $tpl);

		$loan_duration = 0;
		if (isset($drm['SONY']) && $drm['SONY']['loan_duration']) {
			$loan_duration = $drm['SONY']['loan_duration'];
		}
		$tpl = str_replace('!!loan_duration_SONY!!', $loan_duration, $tpl);
		$prolongation = 0;
		if (isset($drm['SONY']) && $drm['SONY']['prolongation']) {
			$prolongation = $drm['SONY']['prolongation'];
		}
		$tpl = str_replace('!!prolongation_SONY_checked!!', ($prolongation ? 'checked=checked' : ''), $tpl);
			
		return $tpl;		
	}
	
	private function save() {
		global $pmb_pnb_drm_parameters;
		global $loan_durations_ACS, $prolongation_ACS;
		global $loan_durations_LCP, $prolongation_LCP;
		global $loan_durations_SONY, $prolongation_SONY;

		$drm = array();

		if (!isset($prolongation_ACS)) {
			$prolongation_ACS = 0;
		}
		if (!isset($prolongation_LCP)) {
			$prolongation_LCP = 0;
		}
		if (!isset($prolongation_SONY)) {
			$prolongation_SONY = 0;
		}
		
		$drm['ACS']= array(
				'loan_duration' => $loan_durations_ACS,
				'prolongation' => $prolongation_ACS,				
		);
		$drm['LCP']= array(
				'loan_duration' => $loan_durations_LCP,
				'prolongation' => $prolongation_LCP,				
		);
		$drm['SONY']= array(
				'loan_duration' => $loan_durations_SONY,
				'prolongation' => $prolongation_SONY,				
		);
		
		
		$pmb_pnb_drm_parameters = json_encode($drm);
		$query = "UPDATE parametres set valeur_param = '" . $pmb_pnb_drm_parameters . "' WHERE type_param= 'pmb' and sstype_param='pnb_drm_parameters'";
		pmb_mysql_query($query);	
		return true;
	}
}