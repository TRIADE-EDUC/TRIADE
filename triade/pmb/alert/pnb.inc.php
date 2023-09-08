<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.inc.php,v 1.3 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg;

$temp_aff = alert_pnb();
if ($temp_aff) $aff_alerte.= "<ul>".$msg["alert_pnb"].$temp_aff."</ul>";

function alert_pnb() {
	global $msg;
	global $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers;
	
	$alert = "";
	
	$pmb_pnb_alert_end_offers+=0;
	$query = "SELECT pnb_order_line_id FROM pnb_orders WHERE 
			DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
	$result = pmb_mysql_query($query);
	if(pmb_mysql_num_rows($result)){
		$alert.= "<li><a href='./edit.php?categ=pnb&sub=orders&alert_end_offers=1' target='_parent'>" . $msg['alert_pnb_end'] . "</a></li>";
	}

	$pmb_pnb_alert_staturation_offers+=0;
/*	    $query = "select * from pnb_orders    
            join pnb_loans on pnb_loan_order_line_id = pnb_order_line_id 
            join pret on pnb_loan_num_expl = pret_idexpl 
            group by pnb_order_line_id having count(id_pnb_loan) >= pnb_order_nb_simultaneous_loans - " . $pmb_pnb_alert_staturation_offers . " limit 1";
            */	   
    $query = "select * from pnb_orders
        join pnb_loans on pnb_loan_order_line_id = pnb_order_line_id
        group by pnb_order_line_id having count(id_pnb_loan) >= pnb_order_nb_simultaneous_loans - " . $pmb_pnb_alert_staturation_offers . " limit 1";
    
    $result = pmb_mysql_query($query);
    if(pmb_mysql_num_rows($result)){
        $alert.= "<li><a href='./edit.php?categ=pnb&sub=orders&alert_staturation_offers=1' target='_parent'>" . $msg['alert_pnb_saturation'] . "</a></li>";
    }	
	return $alert;
}

