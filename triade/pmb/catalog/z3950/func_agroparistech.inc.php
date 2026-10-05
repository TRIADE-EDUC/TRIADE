<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_agroparistech.inc.php,v 1.4 2016/06/22 06:51:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//EQUIVALENT DE FUNC_CATEGORY_AUTO + inversion dans les monos du 200$i et du 200$a
require_once("func_category_auto.inc.php");

function z_recup_noticeunimarc_suite($notice) {
	global $base_path;
	require_once($base_path."/admin/import/func_agroparistech.inc.php");
	recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	global $base_path;
	require_once($base_path."/admin/import/func_agroparistech.inc.php");
	import_new_notice_suite();
} 

function traite_info_subst(&$obj){

	//pour les monographies, le 200$a et 200$i s'inverse...
	if($obj->bibliographic_level=="m"){
		if($obj->serie_200[0]['i'] != ''){
			$tmp_buffer = $obj->serie_200[0]['i'];
			$obj->serie = $obj->titles[0];
			$obj->titles[0] = $tmp_buffer;
		}
	} elseif($obj->bibliographic_level=="s"){ //Pour les périos, on bascule les infos de série en complément de titre
		$record = new iso2709_record ($obj->notice, AUTO_UPDATE,$obj->notice_type);
		$_200_e_complement=array();
		$_200_h=$record->get_subfield_array("200","h");
		if (count($_200_h)) {
			foreach ($_200_h as $value) {
				if (trim($value)) {
					$_200_e_complement[] = $value;
				}
			}
		}
		$_200_i=$record->get_subfield_array("200","i");
		if (count($_200_i)) {
			foreach ($_200_i as $value) {
				if (trim($value)) {
					$_200_e_complement[] = $value;
				}
			}
		}
		if (count($_200_e_complement)) {
			$obj->serie_200 = array();
			$obj->serie = "";
			$obj->nbr_in_serie = "";
			$obj->titles[3] .= implode('. ',$_200_e_complement);
		}
	}
}