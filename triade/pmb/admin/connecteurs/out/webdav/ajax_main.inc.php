<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2019/06/14 10:25:46 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $sub, $connector_id, $source_id, $group_name;

require_once($base_path.'/admin/connecteurs/out/webdav/webdav.class.php');
require_once($class_path.'/encoding_normalize.class.php');

switch($sub){
	case 'config_form':
		$webdav = new webdav($connector_id);
		$webdav_source = $webdav->instantiate_source_class($source_id);
		$groups_collections = $webdav_source->get_groups_collections();
		if (file_exists($base_path.'/admin/connecteurs/out/webdav/groups/'.$groups_collections[$group_name]['class'].'.class.php')) {
			require_once($base_path.'/admin/connecteurs/out/webdav/groups/'.$groups_collections[$group_name]['class'].'.class.php');
			$webdav_group = new $groups_collections[$group_name]['class']($webdav_source->config, $webdav_source->get_group_collections($group_name), $webdav->msg);
			print encoding_normalize::json_encode(array(
					'form' => $webdav_group->get_config_form(),
					'script' => $webdav_group->get_config_form_script()
			));
		} else {
			print '';
		}
		break;
}