<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier_consult.inc.php,v 1.5 2017-04-20 16:25:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idfiche)) $idfiche = 0;
if(!isset($act)) $act = '';
if(!isset($perso_word)) $perso_word = '';
if(!isset($page)) $page = 0;

require_once($class_path."/search.class.php");
require_once($class_path."/parametres_perso.class.php");

require_once($class_path."/fiche.class.php");

$sc = new search(false,"search_fields",$include_path."/fichier/");
$sc->isfichier=true;

switch($mode){
	case 'search':		
		$fiche = new fiche($idfiche);
		$sc->limited_search = true;
		switch($sub){
			case 'view':
				print $fiche->show_fiche_form();
			break;
			case 'edit':
				print $fiche->show_edit_form();
			break;
			case 'update':
				$fiche->save();		
				print $fiche->show_fiche_form();
			break;		
			case 'del':
				$fiche->delete();		
				$fiche->show_search_list($act,"./fichier.php?categ=consult&mode=search&perso_word=".rawurlencode(stripslashes($perso_word)),$page);				
			break;
			default:
				$fiche->show_search_list($act,"./fichier.php?categ=consult&mode=search&perso_word=".rawurlencode(stripslashes($perso_word)),$page);
			break;
		}	
		break;
		
	case 'search_multi':
		switch($sub){
			case 'launch':
				$sc->show_results_fichier("./fichier.php?categ=consult&mode=search_multi&sub=launch","./fichier.php?categ=consult&mode=search_multi", true, '', true );
				break;
			default:
				print $sc->show_form("./fichier.php?categ=consult&mode=search_multi","./fichier.php?categ=consult&mode=search_multi&sub=launch");
				break;
		}		
		break;
}