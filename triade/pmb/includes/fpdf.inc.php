<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fpdf.inc.php,v 1.94 2019/08/02 10:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/amende.class.php");
require_once($class_path."/comptes.class.php");
require_once ("$include_path/notice_authors.inc.php");
require_once($class_path."/serie.class.php");
require_once ("$class_path/author.class.php");

require_once($class_path."/pdf/reader/loans/lettre_reader_loans_late_PDF.class.php");
require_once($class_path."/pdf/reader/loans/lettre_reader_loans_late_group_PDF.class.php");
require_once($class_path."/pdf/reader/resa/lettre_reader_resa_PDF.class.php");
require_once($class_path."/pdf/reader/resa/lettre_reader_resa_planning_PDF.class.php");

// Fonctions fpdf
function biblio_info($x, $y, $short=0) {

	global $ourPDF,$msg,$base_path;
	global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;
	global $txt_biblio_info ;
	global $pmb_pdf_font;

	if ($short==1) {
		$ourPDF->SetXY ($x,$y);
		$ourPDF->setFont($pmb_pdf_font, 'B', 16);
		$ourPDF->multiCell(120, 8, $biblio_name, 0, 'L', 0);
	} else {
		// afin de ne générer qu'une fois l'adr et compagnie
		if (!$txt_biblio_info) {
			
			$txt_biblio_info = trim($biblio_adr1);
			if ($biblio_adr2 != "") {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= $biblio_adr2;
			}
			if (($biblio_cp != "") || ($biblio_town != "")) {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= trim($biblio_cp." ".$biblio_town);
			}
			if (($biblio_state != "") || ($biblio_country != "")) {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= trim($biblio_state." ".$biblio_country);
			}
			if ($biblio_phone != "") {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= $msg['lettre_titre_tel'].$biblio_phone;
			}
			if ($biblio_email != "") {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= "@ : ".$biblio_email;
			}
			if ($biblio_website != "") {
				if(trim($txt_biblio_info)){
					$txt_biblio_info .= "\n";
				}
				$txt_biblio_info .= "Web : ".$biblio_website;
			}
		}

		if ($biblio_logo) {
			$ourPDF->Image($base_path."/images/".$biblio_logo, $x, $y );
			$ourPDF->SetXY ($x,$y+50);
		} else {
			$ourPDF->SetXY ($x,$y+10);
		}
		$ourPDF->setFont($pmb_pdf_font, '', 9);
		$ourPDF->multiCell(0, 5, $txt_biblio_info, 0, 'L', 0);

		$ourPDF->SetXY ($x+60,$y);
		$ourPDF->setFont($pmb_pdf_font, 'B', 16);
		$ourPDF->multiCell(90, 8, $biblio_name, 0, 'C', 0);
	}
} /* fin biblio_info */

function lettre_retard_par_lecteur($id_empr, $niveau_relance=1) {
	global $ourPDF;
	
	lettre_reader_loans_late_PDF::set_niveau_relance($niveau_relance);
	$lettre_reader_loans_late_PDF = lettre_reader_loans_late_PDF::get_instance('reader/loans');
	$lettre_reader_loans_late_PDF->doLettre($id_empr);
	$ourPDF = $lettre_reader_loans_late_PDF->PDF; 
} // fin lettre_retard_par_lecteur

// ******************** Imprime les lettres de retard pour un groupe ****************************
function lettre_retard_par_groupe($id_groupe, $lecteurs_ids=array(), $niveau_relance=1) {
	global $ourPDF;
	
	lettre_reader_loans_late_group_PDF::set_niveau_relance($niveau_relance);
	$lettre_reader_loans_late_group_PDF = lettre_reader_loans_late_group_PDF::get_instance('reader/loans');
	$lettre_reader_loans_late_group_PDF->set_lecteurs_ids($lecteurs_ids);
	$lettre_reader_loans_late_group_PDF->doLettre($id_groupe);
	$ourPDF = $lettre_reader_loans_late_group_PDF->PDF;
} // fin lettre_retard_par_groupe

// **************** Réservations *************************************

function lettre_resa_par_lecteur($id_empr) {
	global $ourPDF;
	
	$lettre_reader_resa_PDF = lettre_reader_resa_PDF::get_instance('reader/resa');
	$lettre_reader_resa_PDF->doLettre($id_empr);
	$ourPDF = $lettre_reader_resa_PDF->PDF;
} // fin lettre_resa_par_lecteur

function lettre_resa_planning_par_lecteur($id_empr) {
	global $ourPDF;
	
	$lettre_reader_resa_planning_PDF = lettre_reader_resa_planning_PDF::get_instance('reader/resa');
	$lettre_reader_resa_planning_PDF->doLettre($id_empr);
	$ourPDF = $lettre_reader_resa_planning_PDF->PDF;
} // fin lettre_resa_planning_par_lecteur