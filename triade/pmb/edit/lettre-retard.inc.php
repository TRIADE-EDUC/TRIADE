<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-retard.inc.php,v 1.46 2021/12/10 09:36:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path;
global $pdflettreretard_1largeur_page, $pdflettreretard_1hauteur_page, $pdflettreretard_1format_page;
global $pdfdoc, $relance, $fpdf;

require_once($include_path."/sms.inc.php");

// popup d'impression PDF pour lettre retard de prêt
// reçoit : id_empr et éventuellement cb_doc
function get_texts($relance) {
	global $format_page,$marge_page_gauche, $marge_page_droite, $largeur_page;
	global $biblio_name, $biblio_phone, $biblio_email, $biblio_commentaire;
	
	// la marge gauche des pages
	$var = "pdflettreretard_".$relance."marge_page_gauche";
	global ${$var};
	$marge_page_gauche = ${$var};
	
	// la marge droite des pages
	$var = "pdflettreretard_".$relance."marge_page_droite";
	global ${$var};
	$marge_page_droite = ${$var};
	
	// la largeur des pages
	$var = "pdflettreretard_1largeur_page";
	global ${$var};
	$largeur_page = ${$var};
	
	// la hauteur des pages
	$var = "pdflettreretard_1hauteur_page";
	global ${$var};
	$hauteur_page = ${$var};
	
	// le format des pages
	$var = "pdflettreretard_1format_page";
	global ${$var};
	$format_page = ${$var};
} // fin function get_texts

function get_texts_group($relance) {
	global $format_page,$marge_page_gauche, $marge_page_droite, $largeur_page;
	global $biblio_name, $biblio_phone, $biblio_email, $biblio_commentaire;
	
	// la marge gauche des pages
	$var = "pdflettreretard_".$relance."marge_page_gauche";
	global ${$var};
	$marge_page_gauche = ${$var};
	
	// la marge droite des pages
	$var = "pdflettreretard_".$relance."marge_page_droite";
	global ${$var};
	$marge_page_droite = ${$var};
	
	// la largeur des pages
	$var = "pdflettreretard_1largeur_page";
	global ${$var};
	$largeur_page = ${$var};
	
	// la hauteur des pages
	$var = "pdflettreretard_1hauteur_page";
	global ${$var};
	$hauteur_page = ${$var};
	
	// le format des pages
	$var = "pdflettreretard_1format_page";
	global ${$var};
	$format_page = ${$var};
} // fin function get_texts_group

$largeur_page=$pdflettreretard_1largeur_page;
$hauteur_page=$pdflettreretard_1hauteur_page;

$taille_doc=array($largeur_page,$hauteur_page);

$format_page=$pdflettreretard_1format_page;

$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF->Open();

switch($pdfdoc) {
    case "lettre_mail_retard_groupe" :
        //TODO 
        break;
	case "lettre_retard_groupe" :
		get_texts_group($relance);
		
		$module_pdf = module_pdf::get_instance();
		$module_pdf->proceed_lettre_retard_groupe();
		break;
	case "lettre_retard" :
	default :
		get_texts($relance);
		$module_pdf = module_pdf::get_instance();
		$module_pdf->proceed_lettre_retard();
		break;
	}
$ourPDF->OutPut();
