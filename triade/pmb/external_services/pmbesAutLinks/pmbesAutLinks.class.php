<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesAutLinks.class.php,v 1.5 2023/08/28 14:01:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesAutLinks extends external_services_api_class {
	
	public function getLinks($autTable, $id) {
		$autTable = intval($autTable);
		$id = intval($id);
		if ($autTable <= 0 || $autTable > 8)
			return false;
		$autlink = new aut_link($autTable, $id);
		$results = array();
		$aut_list = $autlink->get_aut_list();
		if(!empty($aut_list)) {
			foreach($aut_list as $alink) {
				$aresult = array(
					'autlink_to' => encoding_normalize::utf8_normalize($alink['to']),
					'autlink_to_id' => encoding_normalize::utf8_normalize($alink['to_num']),
					'autlink_to_libelle' => encoding_normalize::utf8_normalize($alink['libelle']),
					'autlink_type' => encoding_normalize::utf8_normalize($alink['type']),
					'autlink_reciproc' => encoding_normalize::utf8_normalize($alink['reciproc']),
					'autlink_comment' => encoding_normalize::utf8_normalize($alink['comment']),
				);
				$results[] = $aresult;
			}
		}
		return $results;
	}
	
}




?>