<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_prets.inc.php,v 1.10 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/pdf/reader/loans/lettre_reader_loans_group_PDF.class.php");

// popup d'impression PDF pour lettres de retard par groupe
// reçoit : liste des groupes cochés $coch_groupe

header("Content-Type: application/pdf");
$lettre_reader_loans_group_PDF = new lettre_reader_loans_group_PDF();
$lettre_reader_loans_group_PDF->doLettre($id_groupe);
$ourPDF = $lettre_reader_loans_group_PDF->PDF;
$ourPDF->OutPut();

?>