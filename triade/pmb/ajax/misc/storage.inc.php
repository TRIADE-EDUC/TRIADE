<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.inc.php,v 1.5.14.1 2024/09/30 13:12:06 qvarin Exp $

global $class_path, $sub, $id, $type, $id_collection, $from, $action, $class_name;

require_once($class_path."/storages/storages.class.php");

switch($sub){
	case "upload" :
		$storage = storages::get_storage_class($id);
		if ($storage) {
			$filenames = $storage->upload_process();
		}
		if (!empty($filenames)) {
			switch ($type) {
				case 'collection' :
					require_once($class_path."/cms/cms_collections.class.php");
					$collection = new cms_collection($id_collection);
					for ($i = 0; $i < count($filenames); $i++) {
						print $collection->add_document($storage->get_uploaded_fileinfos($filenames[$i]),true,$from);
					}
					break;

				default:
					http_response_code(404);
					break;
			}
		} else {
			switch ($storage->codeError) {
				case storage::ERROR_INVALIDE_EXT:
					echo $msg['upload_file_not_allowed'];
					break;

				case storage::ERROR_INVALIDE_SIZE:
					echo $msg['upload_file_too_big'];
					break;

				default:
					break;
			}
		}
		break;

	default :
		switch($action) {

			case "get_params_form" :
				$storages = new storages();
				print $storages->get_params_form($class_name,$id);
				break;

			default:
				http_response_code(404);
				break;
		}
		break;
}