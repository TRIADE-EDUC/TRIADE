<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statuts.inc.php,v 1.3 2015-11-05 14:23:32 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// Page de gestion des statuts d'autorités

//dépendances
require_once($class_path.'/authorities_statuts.class.php');

switch($action) {
	case 'update':
		$statut = authorities_statuts::get_from_from();
		if(!authorities_statuts::save($statut)){
			error_message("",$msg['save_error'], 0);
		}
		authorities_statuts::show_list();
		break;
	case 'add':
		authorities_statuts::show_form(0);
		break;
	case 'edit':
		authorities_statuts::show_form($id);
		break;
	case 'del':
		if(!authorities_statuts::delete($id)){
			$used=authorities_statuts::check_used($id);			
			foreach($used as $auth){
				$list.=$auth['link'].'<br/>';
			}
			error_message("", $msg['authorities_statut_used'].'<br/>'.$list);
		}
		authorities_statuts::show_list();
		break;
	default:
		authorities_statuts::show_list();
		break;
}