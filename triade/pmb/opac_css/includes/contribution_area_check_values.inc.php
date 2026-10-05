<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_check_values.inc.php,v 1.7 2024/02/28 11:14:09 tsamson Exp $

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
		$permalinks = array();
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
						while($row = pmb_mysql_fetch_object($result)) {
							$rights = record_display::get_record_rights($row->explnum_notice, $row->explnum_bulletin);
							if ($rights['visible']) {
								$permalinks[] = record_display::get_display_isbd_with_link($row->explnum_notice, $row->explnum_bulletin);
							}
						}
						$return['doublon'] = 1;
						$return['records'] = $permalinks;
						break;
					}
					
					$list_results = array();
					$query = "
                        SELECT * WHERE {
                                ?sujet ?predicat <http://www.pmbservices.fr/ontology#docnum> .
                                ?sujet <http://www.pmbservices.fr/ontology#upload_directory> ?upload_directory .
                                ?sujet <http://www.pmbservices.fr/ontology#docnum_file> ?docnum_file .
                                ?sujet <http://www.pmbservices.fr/ontology#has_record> ?has_record .
                                ?has_record pmb:displayLabel ?displayLabel .
                                ?has_record pmb:has_contributor ?has_contributor .
                                ?has_record pmb:parent_scenario_uri ?parent_scenario_uri .
                                ?has_record pmb:form_id ?form_id .
                                ?has_record pmb:form_uri ?form_uri .
                                ?has_record pmb:area ?area .
                            OPTIONAL {
                                ?sujet <http://www.pmbservices.fr/ontology#identifier> ?identifier .
                                ?sujet <http://www.pmbservices.fr/ontology#is_draft> ?is_draft .
                            } FILTER (!bound(?identifier)) . 
                              FILTER (!bound(?is_draft))
                        }
                    ";
					$store = new contribution_area_store();
					$datastore = $store->get_datastore();
					$datastore->query($query);
					if ($datastore->get_result()) {
					    $list_results = $datastore->get_result();
					}
					if (count($list_results)) {
					    foreach ($list_results as $triple){
					        $directory = new upload_folder($triple->upload_directory);
                            $path = $directory->repertoire_path.$triple->docnum_file;
                            if (!is_file($path)) {
                                continue;					                    
                            }
                            $sign = md5_file($path);
                            if (!$sign) {
                                continue;
                            }
                            if ($sign != $explnum_signature) {
                                continue;
                            }
                            $empr_data = new emprunteur_datas(intval($triple->has_contributor));
                            $permalinks = $triple->displayLabel . " / " . $empr_data->empr_prenom . " " . $empr_data->empr_nom;
                            if ($triple->has_contributor == $_SESSION['id_empr_session']) {
                                // on créer le lien
                                $id = onto_common_uri::get_id($triple->has_record);
                                if (!empty($id)) {
                                    $permalinks = "<a href='".$opac_url_base."index.php?lvl=contribution_area&sub=record&area_id=".$triple->area."&form_id=".$triple->form_id."&form_uri=".$triple->form_uri."&id=".$id."&scenario=".$triple->parent_scenario_uri."'>".$permalinks."</a><br />";
                                }
                            }
                            $return['records'] = $permalinks;
	                        $return['doublon'] = 1;
	                        break;
					    }
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
	$val = intval($val);
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