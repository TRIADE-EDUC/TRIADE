<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_check_values.inc.php,v 1.4 2018-01-26 14:55:16 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	die();
}

require_once($class_path.'/encoding_normalize.class.php');

$return = array();

switch ($what) {
	case 'docnum_file' :
		require_once($class_path.'/record_display.class.php');
		
		$field_elements = explode('[', $field_name);
		
		$return = array(
				'doublon' => 0,
				'max_size' => 0
		);
		// Quand la taille du POST dépasse la taille autorisé, $_FILES est vide, seul $_SERVER['CONTENT_LENGTH'] peut nous donner une indication
		if (empty($_FILES) && (($_SERVER['CONTENT_LENGTH'] > return_bytes(ini_get('upload_max_filesize'))) || ($_SERVER['CONTENT_LENGTH'] > return_bytes(ini_get('post_max_size'))))) {
			$return['max_size'] = 1;
		}
		if (isset($_FILES[$field_elements[0]])) {
			$explnum_size = $_FILES[$field_elements[0]]['size'];
			for ($i = 1; $i < count($field_elements); $i++) {
				$explnum_size = $explnum_size[rtrim($field_elements[$i], "]")];
			}
			
			if (($explnum_size > return_bytes(ini_get('upload_max_filesize'))) || ($explnum_size > return_bytes(ini_get('post_max_size')))) {
				$return['max_size'] = 1;
			}
			
			if (!$return['max_size'] && $pmb_explnum_controle_doublons) {
				$explnum_tmp_name = $_FILES[$field_elements[0]]['tmp_name'];
				for ($i = 1; $i < count($field_elements); $i++) {
					$explnum_tmp_name = $explnum_tmp_name[rtrim($field_elements[$i], "]")];
				}
				$explnum_signature = md5_file($explnum_tmp_name);
			
				if ($explnum_signature) {
					$result = pmb_mysql_query('select explnum_notice, explnum_bulletin from explnum where explnum_signature = "'.$explnum_signature.'"');
					if (pmb_mysql_num_rows($result)) {
						$permalinks = array();
						while($row = pmb_mysql_fetch_object($result)) {
							$rights = record_display::get_record_rights($row->explnum_notice, $row->explnum_bulletin);
							if ($rights['visible']) {
								$permalinks[] = record_display::get_display_isbd_with_link($row->explnum_notice, $row->explnum_bulletin);
							}
						}
						$return['doublon'] = 1;
						$return['records'] = $permalinks;
					}
				}
			}
		}
		break;
}

print '<textarea>';
print encoding_normalize::json_encode($return);
print '</textarea>';

function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}