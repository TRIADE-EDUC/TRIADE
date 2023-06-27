<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_pret.inc.php,v 1.33 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$base_path/circ/pret_func.inc.php");
require_once($class_path."/pdf/reader/loans/lettre_reader_loans_PDF.class.php");

// liste des prêts et réservations
// prise en compte du param d'envoi de ticket de prêt électronique si l'utilisateur le veut !
if ($empr_electronic_loan_ticket && $param_popup_ticket) {
	electronic_ticket($id_empr) ;
}

// popup d'impression PDF pour fiche lecteur

header("Content-Type: application/pdf");
$lettre_reader_loans_PDF = new lettre_reader_loans_PDF();
$lettre_reader_loans_PDF->doLettre($id_empr);
$ourPDF = $lettre_reader_loans_PDF->PDF; 
$ourPDF->OutPut();