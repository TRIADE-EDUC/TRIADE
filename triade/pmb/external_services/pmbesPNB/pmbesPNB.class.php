<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesPNB.class.php,v 1.5 2023/03/16 10:49:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/pnb/dilicom.class.php");

class pmbesPNB extends external_services_api_class {
	
	//Pour emprunter un ouvrage
    public function loanBook($emprId, $recordId, $userAgent,$pass ='', $hint_pass =''){
		$pnb = new pnb();
		$loan_data = $pnb->loan_book($emprId, $recordId, $userAgent, $pass, $hint_pass);
		return encoding_normalize::utf8_normalize($loan_data);
	}
	
	public function returnBook($emprId,$explId,$fromPortal = 0, $drm = ''){
	    $responseDilicom = array();
	    $pnb = new pnb();

	    //Si on provient du portail, on appel le retour anticipé Dilicom
	    if ($fromPortal && $drm =='LCP'){
            $responseDilicom = $pnb->return_book_to_dilicom($emprId,$explId);
    	    //S'il y à un pb, on arrete le traitement
    	    if ($responseDilicom['status'] == false){
    	        return encoding_normalize::utf8_normalize($responseDilicom);
    	    }
	    }
        
	    //Suppression côté PNB
	    $responsePmb = array();
	    $responsePmb = $pnb->return_book($emprId,$explId);

	    return encoding_normalize::utf8_normalize($responsePmb);
	}
	
	public function extendLoan($emprId,$explId, $fromPortal = 0, $drm = ''){
	    
	    $responseDilicom = array();
	    $pnb = new pnb();
	    
	    //Si on ne provient pas du portail, la DRM n'est pas transmise, on va la récuperer en base
	    if ($drm == ''){
	        $r = pmb_mysql_query("SELECT pnb_loan_drm FROM pnb_loans WHERE pnb_loan_num_expl = $explId");
	        if (pmb_mysql_num_rows($r)){
    	        $drm = pmb_mysql_fetch_array($r)['pnb_loan_drm'];
	        }
	    }
        	    
	    //Si on est sous DRM LCP, on appel le retour anticipé Dilicom
	    if ($drm =='LCP'){
            $responseDilicom = $pnb->extend_loan_to_dilicom($emprId,$explId);
            //S'il y à un pb, on arrete le traitement
    	    if ($responseDilicom['status'] == false){
                 return encoding_normalize::utf8_normalize($responseDilicom);
            }
	    }
	    
 	    //Prolongement côté PNB
	    $responsePmb = array();
	    $responsePmb = $pnb->extend_loan($emprId,$explId);
	    //Si on a une date de fin, on la retourne avec la réponse pour actualiser le JS
	    if (!empty($responseDilicom["infos"]["loanEndDate"])) {
	        $responsePmb["loanEndDate"] = $responseDilicom["infos"]["loanEndDate"];
	    }
	    return encoding_normalize::utf8_normalize($responsePmb);
	}
}