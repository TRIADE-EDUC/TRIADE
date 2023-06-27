<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: status.inc.php,v 1.1 2018-01-29 14:52:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/abts_status.class.php');

switch($action) {
	case 'update':
		$statut = abts_status::get_from_from();
		if(!abts_status::save($statut)){
			error_message("",$msg['save_error'], 0);
		}
		abts_status::show_list();
		break;
	case 'add':
		abts_status::show_form(0);
		break;
	case 'edit':
		abts_status::show_form($id);
		break;
	case 'del':
		if(!abts_status::delete($id)){
			$used=abts_status::check_used($id);			
			foreach($used as $abt){
				$list.=$abt['link'].'<br/>';
			}
			error_message("", $msg['abts_status_used'].'<br/>'.$list);
		}
		abts_status::show_list();
		break;
	default:
		abts_status::show_list();
		break;
}