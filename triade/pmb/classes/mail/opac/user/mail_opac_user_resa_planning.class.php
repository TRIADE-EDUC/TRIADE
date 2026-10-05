<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user_resa_planning.class.php,v 1.1 2022/08/01 09:50:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_user_resa_planning extends mail_opac_user_resa {
	
	protected function get_mail_object() {
		global $msg;
		
		if ($this->annul==1) {
			$mail_object = $msg["mail_obj_resa_planning_canceled"] ;
		} elseif ($this->annul==2) {
			$mail_object = $msg["mail_obj_resa_planning_reaffected"] ;
		} else {
				$mail_object = $msg["mail_obj_resa_planning_added"] ;
		}
		return $mail_object." ".$this->recipient->aff_quand;
	}
}