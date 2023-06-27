<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesPNB.class.php,v 1.1 2018-06-08 08:10:27 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");



class pmbesPNB extends external_services_api_class {
	
	//Pour emprunter un ouvrage
	public function loanBook($emprId, $recordId, $userAgent){
		$pnb = new pnb();
		$loan_data = $pnb->loan_book($emprId, $recordId, $userAgent);
		return encoding_normalize::utf8_normalize($loan_data);
	}
}

?>