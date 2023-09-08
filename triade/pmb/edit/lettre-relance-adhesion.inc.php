<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-relance-adhesion.inc.php,v 1.23 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/marc_table.class.php");
require_once("$class_path/mono_display.class.php");
require_once($class_path."/pdf/reader/lettre_reader_relance_adhesion_PDF.class.php");

// popup d'impression PDF pour lettre de relance d'abonnement
	

$lettre_reader_relance_adhesion_PDF = new lettre_reader_relance_adhesion_PDF();
$lettre_reader_relance_adhesion_PDF->doLettre($id_empr);
$ourPDF = $lettre_reader_relance_adhesion_PDF->PDF;
$ourPDF->OutPut();