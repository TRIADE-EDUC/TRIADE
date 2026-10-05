<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-livraison.inc.php,v 1.19 2019/07/05 13:25:14 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_liv, $acquisition_pdfliv_print, $class_path;

// popup d'impression PDF pour bon de livraison
// reçoit : id_liv

if (!$id_liv) {print "<script> self.close(); </script>" ; die;}

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

if (strpos($acquisition_pdfliv_print, '.php')) {
	require_once($acquisition_pdfliv_print);
} else {
	require_once($class_path."/pdf/accounting/lettre_delivery_PDF.class.php");
	$lettre_delivery_PDF = lettre_delivery_factory::make();
	$lettre_delivery_PDF->doLettre(0, $id_liv);
	$lettre_delivery_PDF->getLettre();
}
?>