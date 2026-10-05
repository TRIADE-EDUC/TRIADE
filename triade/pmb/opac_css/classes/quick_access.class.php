<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: quick_access.class.php,v 1.10 2024/03/13 15:25:24 pmallambic Exp $

use Pmb\MFA\Controller\MFAServicesController;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class quick_access {
    
	public static function get_selector() {
		global $msg, $charset;
		global $allow_loan, $allow_loan_hist;
		global $allow_book, $opac_resa;
		global $opac_dsi_active, $allow_dsi, $allow_dsi_priv;
		global $opac_show_suggest, $allow_sugg;
		global $opac_shared_lists, $allow_liste_lecture;
		global $opac_demandes_active, $allow_dema;
		global $opac_serialcirc_active, $allow_serialcirc;
		global $opac_scan_request_activate, $allow_scan_request;
		global $opac_contribution_area_activate, $allow_contribution;
		global $opac_quick_access_logout;
		global $allow_pnb, $animations_active, $security_mfa_active;
		
	    $selector = "
		<label for='empr_quick_access' class='visually-hidden'>".htmlentities($msg["empr_quick_access"],ENT_QUOTES,$charset)."</label>
	    <select id='empr_quick_access' name='empr_quick_access' onchange='if (this.value) window.location.href=this.value'>
			<option value=''>".$msg["empr_quick_access"]."</option>
			<option value='empr.php'>".$msg["empr_my_account"]."</option>";
		if ($allow_loan || $allow_loan_hist) {
			$selector .= "<option value='empr.php?tab=loan_reza&lvl=all#empr-loan' class='empr_quick_access_loan'>".$msg["empr_my_loans"]."</option>";
		}
		if ($allow_book && $opac_resa) {
			$selector .= "<option value='empr.php?tab=loan_reza&lvl=all#empr-resa' class='empr_quick_access_resa'>".$msg["empr_my_resas"]."</option>";
		}
		if (($opac_dsi_active) && ($allow_dsi || $allow_dsi_priv)) {
			$selector .= "<option value='empr.php?tab=dsi&lvl=bannette' class='empr_quick_access_dsi'>".$msg["empr_menu_dsi"]."</option>";
		}
		if ($opac_show_suggest && $allow_sugg) {
			$selector .= "<option value='empr.php?tab=sugg&lvl=view_sugg' class='empr_quick_access_sugg'>".$msg["empr_menu_sugg"]."</option>";
		}
		if ($opac_shared_lists && $allow_liste_lecture) {
			$selector .= "<option value='empr.php?tab=lecture&lvl=private_list' class='empr_quick_access_private_list'>".$msg["empr_menu_lecture"]."</option>";
		}
		if ($opac_demandes_active && $allow_dema) {
			$selector .= "<option value='empr.php?tab=request&lvl=list_dmde' class='empr_quick_access_request'>".$msg["empr_my_dmde"]."</option>";
		}
		if ($opac_serialcirc_active && $allow_serialcirc){
			$selector .= "<option value='empr.php?tab=serialcirc&lvl=list_abo' class='empr_quick_access_serialcirc'>".$msg["empr_menu_serialcirc"]."</option>";
		}
		if ($opac_scan_request_activate && $allow_scan_request){
			$selector .= "<option value='empr.php?tab=scan_requests&lvl=scan_requests_list' class='empr_quick_access_scan_requests'>".$msg["empr_menu_scan_requests"]."</option>";
		}
		if ($opac_contribution_area_activate && $allow_contribution) {
			$selector .= "<option value='empr.php?tab=contribution_area&lvl=contribution_area_list' class='empr_quick_access_contribution_area'>" . $msg["empr_menu_contribution_area"] . "</option>";
		}
		if ($allow_pnb) {
			$selector .= "<option value='empr.php?tab=pnb&lvl=pnb_devices' class='empr_quick_access_pnb'>" . $msg['empr_menu_pnb']. "</option>";
		}
		if ($animations_active) {
	        $selector .= "<option value='empr.php?tab=animations&lvl=animations_list' class='empr_quick_access_animations'>" . $msg['empr_menu_animations'] . "</option>";
		}
		if ($security_mfa_active) {
		    $mfa_service = (new MFAServicesController())->getData("OPAC");
			if($mfa_service->application) {
				$selector .= "<option value='empr.php?tab=mfa&lvl=mfa_initialization' class='empr_quick_access_mfa'>" . $msg['empr_menu_mfa'] . "</option>";
			}
		}
		
		// En cour de Dev
	    // $selector .= "<option value='empr.php?tab=payments&lvl=payments_list' class='empr_quick_access_payment'>" . $msg['empr_menu_payment'] . "</option>";
	    
		if($opac_quick_access_logout){			
			$selector .= "<option value='index.php?logout=1'>".$msg["empr_logout"]."</option>";
		}
		$selector .= "</select>";
	    return $selector;
	}
}