<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste-suggestions.inc.php,v 1.25 2019/08/02 10:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $acquisition_pdfsug_print, $class_path, $acquisition_pdfsug_text_size, $acquisition_pdfsug_format_page, $acquisition_pdfsug_orient_page, $acquisition_pdfsug_marges_page;
global $acquisition_pdfsug_pos_titre, $acquisition_pdfsug_pos_date, $acquisition_pdfsug_tab_sug, $acquisition_pdfsug_pos_footer, $fpdf, $statut, $user_input, $num_categ, $sugg_location_id;
global $filtre_src, $origine_id, $type_origine, $date_inf, $date_sup, $pmb_pdf_font;

// popup d'impression PDF pour liste de suggestions
// reçoit : user_input, statut

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

if ($acquisition_pdfsug_print) {
	require_once($acquisition_pdfsug_print);
} else {
    require_once($class_path.'/pdf/suggestions/lettre_suggestions_PDF.class.php');
	$lettre_suggestions_PDF = lettre_suggestions_PDF::get_instance('suggestions');
	$lettre_suggestions_PDF->doLettre();
}
?>