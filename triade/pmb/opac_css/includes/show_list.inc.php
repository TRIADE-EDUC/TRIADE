<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_list.inc.php,v 1.10 2017-07-05 10:10:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/liste_lecture.class.php");
require_once ($base_path."/includes/templates/liste_lecture.tpl.php");

if(isset($id_liste)) $id_liste += 0; else $id_liste = 0;
if($_SESSION['user_code'] && liste_lecture::check_rights($id_liste, $sub)){
	
	if(!isset($act)) $act = '';
	$listes = new liste_lecture($id_liste, $act);
	
	switch($sub){
		case 'transform_caddie' :
			$notices = $_SESSION['cart'];
			$listes->affichage_saveform($notices);			
			break;
		case 'transform_check':		
			$notices = $notice;
			$listes->affichage_saveform($notices);
			break;
		case 'view':			
			$listes->affichage_saveform();
			break;	
		case 'consultation':
			$listes->consulter_liste();
			break;
		default:
			$listes->generate_mylist();
			break;
	}
} else {
	print "<script>document.location='empr.php';</script>";
}
