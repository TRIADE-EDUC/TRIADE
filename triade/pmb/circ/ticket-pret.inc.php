<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ticket-pret.inc.php,v 1.25 2020/11/04 10:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$base_path/circ/pret_func.inc.php");
require_once($class_path."/pdf/reader/loans/lettre_reader_loans_ticket_PDF.class.php");

// liste des prêts et réservations
// prise en compte du param d'envoi de ticket de prêt électronique
// la liste n'est envoyée que si pas de cb_doc, si cb_doc, c'est que c'est un ticket unique d'un prêt et dans ce cas, le ticket électronique est envoyé par pret.inc.php 
if ($empr_electronic_loan_ticket && (!isset($cb_doc) || !$cb_doc) && $param_popup_ticket) {
	electronic_ticket($id_empr) ;
}

$lettre_reader_loans_ticket_PDF = lettre_reader_loans_ticket_PDF::get_instance('reader/loans');
$lettre_reader_loans_ticket_PDF->doLettre($id_empr);
$ourPDF = $lettre_reader_loans_ticket_PDF->PDF;
$ourPDF->OutPut();