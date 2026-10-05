<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier_gestion.inc.php,v 1.9 2021/02/12 22:26:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $mode, $sub, $act, $msg, $charset;
global $prefix, $perso_word, $page;

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/fiche.class.php");

switch($mode){
	case 'champs':		
		print "<h1>".htmlentities($msg['fichier_gestion_champs_title'],ENT_QUOTES,$charset)."</h1>";
		$option_visibilite=array();
		$option_visibilite["multiple"]="block";
		$option_visibilite["obligatoire"]="block";
		$option_visibilite["search"]="block";
		$option_visibilite["export"]="none";
		$option_visibilite["filters"]="none";
		$option_visibilite["exclusion"]="none";
		$option_visibilite["opac_sort"]="none";
		$p_perso=new parametres_perso($prefix,"./fichier.php?categ=gerer&mode=champs",$option_visibilite);
		$p_perso->proceed();
		break;
	case 'reindex':
		$fiche = new fiche();
		switch($sub){
			case 'run':
				$fiche->reindex_all();
				break;
			default:
				$fiche->show_reindex_form();
			break;
		}
		break;
	case 'display':
		$fiche = new fiche();
		switch($sub){
			case 'position':
				break;
			case 'list':
				$fiche->show_search_list($act,"./fichier.php?categ=consult&mode=search&perso_word=$perso_word",$page);
				break;	
			default:
				break;
		}		
		break;
}