<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-facture.inc.php,v 1.19 2019/07/05 13:25:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_fac, $acquisition_pdffac_print, $class_path;

// popup d'impression PDF pour facture
// reçoit : id_cde

if (!$id_fac) {print "<script> self.close(); </script>" ; die;}

//Footer personalisé
class PDF extends FPDF
{
	public function Footer() {
		
		global $msg;
		global $y_footer, $fs_footer;
		
	    $this->SetY(-$y_footer);
	    //Numéro de page centré
	    $this->Cell(0,$fs_footer,$msg['acquisition_act_page'].$this->PageNo().' / '.$this->AliasNbPages,0,0,'C');
	}
}

if (strpos($acquisition_pdffac_print, '.php')) {
	require_once($acquisition_pdffac_print);
} else {
	require_once($class_path."/pdf/accounting/lettre_invoice_PDF.class.php");
	$lettre_invoice_PDF = lettre_invoice_factory::make();
	$lettre_invoice_PDF->doLettre(0, $id_fac);
	$lettre_invoice_PDF->getLettre();
}
?>