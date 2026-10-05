<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: licence_rights.inc.php,v 1.3 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id, $rightid, $rightaction;

require_once($class_path.'/explnum_licence/explnum_licence_right.class.php');

$rightid = intval($rightid);
switch ($rightaction) {
	case 'save' :
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$explnum_licence_right = new explnum_licence_right($rightid);
		$explnum_licence_right->set_explnum_licence_num($id);
		$explnum_licence_right->set_properties_from_form();
		$explnum_licence_right->save();
		print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=rights";
			   </script>';
		break;
	case 'edit' :
		$explnum_licence_right = new explnum_licence_right($rightid);
		$explnum_licence_right->set_explnum_licence_num($id);
		$explnum_licence_right->fetch_data();
		print $explnum_licence_right->get_form();
		break;
	case 'delete' :
		print '<div class="row"><div class="msg-perio">'.$msg['suppression_en_cours'].'</div></div>';
		$explnum_licence_right = new explnum_licence_right($rightid);
		$explnum_licence_right->delete();
		print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=rights";
			   </script>';
		break;
}

?>