<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: licence.inc.php,v 1.2 2017-07-19 08:38:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/explnum_licence/explnum_licence.class.php');

if (!isset($action)) {
	$action = 'list';
}

switch ($action) {
	case 'save' :
		$id+= 0;
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$explnum_licence = new explnum_licence($id);
		$explnum_licence->get_values_from_form();
		$explnum_licence->save();
		print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=list";
			   </script>';
		break;
	case 'edit' :
		$id+= 0;
		$explnum_licence = new explnum_licence($id);
		$explnum_licence->fetch_data();
		print $explnum_licence->get_form();
		break;
	case 'delete' :
		if (!isset($force)) {
			$force = 0;
		}
		print '<div class="row"><div class="msg-perio">'.$msg['suppression_en_cours'].'</div></div>';
		$id+= 0;
		$explnum_licence = new explnum_licence($id);
		$return = $explnum_licence->delete($force);
		if ($return) {
			print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=list";
			   </script>';
			break;
		}
		print '<script type ="text/javascript">
				if (confirm("'.addslashes($msg['explnum_licence_is_used_confirm_delete']).'")) {
					document.location = "./admin.php?categ=docnum&sub=licence&action=delete&id='.$id.'&force=1";
				} else {
					history.go(-1);
				}
		   </script>';
		break;
	case 'settings' :
		$id+= 0;
		if (!isset($what)) {
			$what = 'profiles';
		}
		$explnum_licence = new explnum_licence($id);
		print $explnum_licence->get_settings_menu();
		switch ($what) {
			case 'rights' :
				if (!isset($rightaction)) {
					$rightaction = 'list';
				}
				switch ($rightaction) {
					case 'list':
						print $explnum_licence->get_rights_list();
						print '<input class="bouton" value="'.$msg['explnum_licence_right_new'].'" onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=rights&rightaction=edit\'" type="button">';
						break;
					default :
						require_once($base_path.'/admin/upload/licence_rights.inc.php');
						break;
				}
				break;
			case 'profiles' :
			default :
				if (!isset($profileaction)) {
					$profileaction = 'list';
				}
				switch ($profileaction) {
					case 'list':
						print $explnum_licence->get_profiles_list();
						print '<input class="bouton" value="'.$msg['explnum_licence_profile_new'].'" onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=profiles&profileaction=edit\'" type="button">';
						break;
					default :
						require_once($base_path.'/admin/upload/licence_profiles.inc.php');
						break;
				}
				break;
		}
		break;
	case 'list':
	default :
		print explnum_licence::get_explnum_licence_list();
		print '<input class="bouton" value="'.$msg['explnum_licence_new'].'" onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=edit\'" type="button">';
		break;
}

?>