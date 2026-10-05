<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: licence.inc.php,v 1.6 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $id, $action, $rightaction, $profileaction, $force, $what;

require_once($class_path.'/explnum_licence/explnum_licence.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

$id = intval($id);
if (empty($action)) {
	$action = 'list';
}

switch ($action) {
	case 'delete' :
		$force = intval($force);
		print '<div class="row"><div class="msg-perio">'.$msg['suppression_en_cours'].'</div></div>';
		$explnum_licence = new explnum_licence($id);
		$return = $explnum_licence->delete($force);
		if ($return) {
			configuration_controller::redirect_display_list();
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
		//Assurons-nous de passer un identifiant de licence
		if (empty($id)) {
			configuration_controller::redirect_display_list();
			break;
		}
		if (empty($what)) {
			$what = 'profiles';
		}
		$explnum_licence = new explnum_licence($id);
		print $explnum_licence->get_settings_menu();
		switch ($what) {
			case 'rights' :
				if (empty($rightaction)) {
					$rightaction = 'list';
				}
				switch ($rightaction) {
					case 'list':
						print '
							<script type="text/javascript">
								document.title="'.$msg['explnum_licence_rights'].'";
							</script>';
						print $explnum_licence->get_rights_list();
						break;
					default :
						require_once($base_path.'/admin/upload/licence_rights.inc.php');
						break;
				}
				break;
			case 'profiles' :
			default :
				if (empty($profileaction)) {
					$profileaction = 'list';
				}
				switch ($profileaction) {
					case 'list':
						print '
							<script type="text/javascript">
								document.title="'.$msg['explnum_licence_profiles'].'";
							</script>';
						print $explnum_licence->get_profiles_list();
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
		configuration_controller::set_model_class_name('explnum_licence');
		configuration_controller::set_list_ui_class_name('list_configuration_explnum_licence_ui');
		configuration_controller::proceed($id);
		break;
}

?>