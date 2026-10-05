<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pdf.php,v 1.57 2022/09/27 06:16:37 dgoron Exp $

// définition du minimum nécéssaire
use Pmb\Animations\Controller\AnimationsController;
$base_path=".";                            
$base_auth = "CATALOGAGE_AUTH|CIRCULATION_AUTH|EDIT_AUTH|ACQUISITION_AUTH|ANIMATION_AUTH";  
$base_title = "PDF";
$base_noheader=1;
$base_nosession = 0 ; // pas d'envoi de cookie avant l'entête PDF
require_once ("$base_path/includes/init.inc.php");  

global $class_path, $include_path, $charset;
global $pmb_pdf_font, $pmb_pdf_fontfixed, $pmb_printer_ticket_script;

require_once($class_path."/modules/module_pdf.class.php");

//Appliquons un eventuel fichier de substitution de paramètres en fonction de la localisation de l'utilisateur courant
require_once("$class_path/parameters_subst.class.php");
if (file_exists($include_path.'/parameters_subst/per_localisations_subst.xml')){
	$subst_filename = $include_path.'/parameters_subst/per_localisations_subst.xml';
} else {
	$subst_filename = $include_path.'/parameters_subst/per_localisations.xml';
}
$parameter_subst = new parameters_subst($subst_filename, $deflt2docs_location);
$parameter_subst->extract();

// modules propres à pdf.php ou à ses sous-modules
require_once("$include_path/fpdf.inc.php");

require_once("$include_path/misc.inc.php");
require_once("$class_path/author.class.php");
require_once("$include_path/notice_authors.inc.php");
require_once("$include_path/notice_categories.inc.php");
require_once("$base_path/circ/pret_func.inc.php");

// pour les champs perso
require_once("$include_path/fields_empr.inc.php");
require_once("$include_path/datatype.inc.php");
require_once("$include_path/parser.inc.php");

// inclusion de la classe de gestion des impressions PDF
// Definition de la police si pas définie dans les paramètres
if (!$pmb_pdf_font) $pmb_pdf_font = 'pmb'; 
if (!$pmb_pdf_fontfixed) $pmb_pdf_fontfixed = 'pmbmono'; 
if(!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH',"$class_path/font/");
require_once("$class_path/fpdf.class.php");
require_once("$class_path/ufpdf.class.php");

require_once($class_path."/sticks_sheet/sticks_sheet_output.class.php");
require_once($class_path."/event/events/event_pdf.class.php");

global $pdf_params, $pdfdoc;
global $empr_electronic_loan_ticket, $param_popup_ticket, $id_empr;

switch ($pdfdoc) {
	case 'ticket_pret':
		if($pmb_printer_ticket_script) $script_perso_file=$pmb_printer_ticket_script;
		else $script_perso_file	= "./circ/ticket-pret.inc.php";
		if(SESSrights & CIRCULATION_AUTH) include($script_perso_file);
			else echo "<script> self.close(); </script>" ;
		break;
	case 'liste_pret':
		if(SESSrights & CIRCULATION_AUTH) {
			require_once("$base_path/circ/pret_func.inc.php");
			// prise en compte du param d'envoi de ticket de prêt électronique si l'utilisateur le veut !
			if ($empr_electronic_loan_ticket && $param_popup_ticket) {
				electronic_ticket($id_empr) ;
			}
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_liste_pret();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'mail_liste_pret':
		if(SESSrights & CIRCULATION_AUTH) {
			header ("Content-Type: text/html; charset=$charset");
			include("./circ/ticket-pret-electro.inc.php");
		} else {
			echo "<script> self.close(); </script>" ;
		}
		break;
	case 'lettre_retard':
		if(!isset($niveau)) $niveau = '';
		if ($niveau) $relance=$niveau; else $relance=1;
		if((SESSrights & EDIT_AUTH) || (SESSrights & CIRCULATION_AUTH))  include("./edit/lettre-retard.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;
	case 'lettre_resa':
		if(SESSrights & CIRCULATION_AUTH) {
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_lettre_resa();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'lettre_resa_planning':
		if(SESSrights & CIRCULATION_AUTH) {
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_lettre_resa_planning();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'lettre_retard_groupe':
		$relance=1;
		if(SESSrights & EDIT_AUTH) include("./edit/lettre-retard.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;
	case 'liste_pret_groupe':
		if((SESSrights & EDIT_AUTH) || (SESSrights & CIRCULATION_AUTH)) {
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_liste_pret_groupe();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'lettre_relance_adhesion':
		if(SESSrights & EDIT_AUTH) {
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_lettre_relance_adhesion();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'fiche_catalographique':
		if((SESSrights & CATALOGAGE_AUTH) || (SESSrights & CIRCULATION_AUTH) ) include("./edit/fiche_catalographique.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;
	case 'carte-lecteur':
		if(SESSrights & CIRCULATION_AUTH) {
			require("$class_path/fpdf_carte_lecteur.class.php");
			$module_pdf = module_pdf::get_instance();
			$module_pdf->proceed_carte_lecteur();
		} else echo "<script> self.close(); </script>" ;
		break;
	case 'cmde':
		if(SESSrights & ACQUISITION_AUTH) {
			include("./acquisition/achats/commandes/lettre_commande.inc.php");
			} else echo "<script> self.close(); </script>" ;	
		break;		
	case 'devi':
		if(SESSrights & ACQUISITION_AUTH) {
			include("./acquisition/achats/devis/lettre-devis.inc.php");
			} else echo "<script> self.close(); </script>" ;	
		break;		
	case 'livr':
		if(SESSrights & ACQUISITION_AUTH) {
			include("./acquisition/achats/livraisons/lettre-livraison.inc.php");
			}	
		break;		
	case 'fact':
		if(SESSrights & ACQUISITION_AUTH) {
			include("./acquisition/achats/factures/lettre-facture.inc.php");
			}	
		break;		
	case 'listsug':
		if(SESSrights & ACQUISITION_AUTH) {
			include("./acquisition/suggestions/liste-suggestions.inc.php");
			}	
		break;		
	case 'liste_bulletinage':
		if(SESSrights & CIRCULATION_AUTH) include("./edit/liste_bulletinage.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;	
	case 'abts_depasse':
		if(SESSrights & CIRCULATION_AUTH) include("./edit/abts_depasse.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;
	case 'listrecept':
		if(SESSrights  & ACQUISITION_AUTH) include("./acquisition/achats/receptions/liste_relances.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;
	case 'rapport_tache':
		if(SESSrights & ADMINISTRATION_AUTH) include("./admin/planificateur/rapport_tache.inc.php");
			else echo "<script> self.close(); </script>" ;
		break;			
	case 'account_command':
		if(SESSrights & ACQUISITION_AUTH) include("./acquisition/rent/account_command.inc.php");
		else echo "<script> self.close(); </script>" ;		
		break;		
	case 'account_invoice':
		if(SESSrights & ACQUISITION_AUTH) include("./acquisition/rent/account_invoice.inc.php");
		else echo "<script> self.close(); </script>" ;		
		break;
	case 'sticks_sheet':
		$sticks_sheet_output = new sticks_sheet_output($id, $display_class);
		$data = explode(",", $data);
		$sticks_sheet_output->output("PDF", $data, $x_stick_selected, $y_stick_selected);
		break;
	case 'barcodes_sheet':
		require_once($class_path."/pdf/barcodes/lettre_barcodes_PDF.class.php");
		
		global $idcaddie, $elt_flag, $elt_no_flag;
		if($idcaddie) {
			$myCart = new caddie($idcaddie);
			if ($elt_flag && $elt_no_flag)
				$liste = $myCart->get_cart("ALL");
			if ($elt_flag && !$elt_no_flag)
				$liste = $myCart->get_cart("FLAG");
			if ($elt_no_flag && !$elt_flag)
				$liste = $myCart->get_cart("NOFLAG");
				
			$barcodes = array();
			if(!empty($liste)) {
				foreach ($liste as $object_id) {
					$barcodes[] = exemplaire::get_expl_cb_from_id($object_id);
				}
			}
			$lettre_barcodes_PDF = lettre_barcodes_PDF::get_instance('barcodes');
			$lettre_barcodes_PDF->set_barcodes($barcodes);
			$lettre_barcodes_PDF->doLettre();
			$lettre_barcodes_PDF->getLettre();
		}
		break;
	case 'mail_liste_pret_groupe':
		if(SESSrights & CIRCULATION_AUTH) {
			header ("Content-Type: text/html; charset=$charset");
			include("./circ/ticket-pret-electro.inc.php");
		} else {
			echo "<script> self.close(); </script>" ;
		}
		break;
	case 'animations':
	    global $action, $id;
	    
	    $data = new stdClass();
	    $data->id = $id;
	    
	    if(SESSrights & ANIMATION_AUTH) {
	        $AnimationsController = new AnimationsController($data);
	        $AnimationsController->proceed($action);
	    }
		echo "<script> self.close(); </script>" ;
		break;
	default:
	    $evth = events_handler::get_instance();
	    $listeners = $evth->get_listener();
	    if (isset($listeners["pdf"]) && isset($listeners["pdf"][$pdfdoc])) {
	        $evt = new event_pdf("pdf", $pdfdoc);
	        if (!empty($pdf_params)) {
	            $evt->set_params($pdf_params);
	        }
	        $evth->send($evt);
	    } else {
			echo "<script> self.close(); </script>" ;
	    }
		break;
}

pmb_mysql_close();
