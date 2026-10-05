<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: licence_profiles.inc.php,v 1.4 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id, $profileid, $profileaction;

require_once($class_path.'/explnum_licence/explnum_licence_profile.class.php');

$profileid = intval($profileid);
switch ($profileaction) {
	case 'save' :
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$explnum_licence_profile = new explnum_licence_profile($profileid);
		$explnum_licence_profile->set_explnum_licence_num($id);
		$explnum_licence_profile->set_properties_from_form();
		$explnum_licence_profile->save();
		print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=profiles";
			   </script>';
		break;
	case 'edit' :
		$explnum_licence_profile = new explnum_licence_profile($profileid);
		$explnum_licence_profile->set_explnum_licence_num($id);
		$explnum_licence_profile->fetch_data();
		print $explnum_licence_profile->get_form();
		break;
	case 'delete' :
		if (!isset($force)) {
			$force = 0;
		}
		print '<div class="row"><div class="msg-perio">'.$msg['suppression_en_cours'].'</div></div>';
		$explnum_licence_profile = new explnum_licence_profile($profileid);
		$return = $explnum_licence_profile->delete($force);
		if ($return) {
			print '<script type ="text/javascript">
					document.location = "./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=profiles";
			   </script>';
			break;
		}
		print '<script type ="text/javascript">
				if (confirm("'.addslashes($msg['explnum_licence_profile_is_used_confirm_delete']).'")) {
					document.location = "./admin.php?categ=docnum&sub=licence&action=settings&id='.$id.'&what=profiles&profileaction=delete&profileid='.$profileid.'&force=1";
				} else {
					history.go(-1);
				}
		   </script>';
		break;
}

?>