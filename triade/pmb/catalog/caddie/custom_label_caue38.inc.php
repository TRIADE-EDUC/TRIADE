<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_label_caue38.inc.php,v 1.11 2019-06-10 15:14:33 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $label_fmt, $label_con, $msg, $charset;

require_once ("$base_path/catalog/caddie/custom_label_no_script.inc.php");

$label_fmt['p0']['label_name'] 					= "Personnalisé CAUE38 - 38.1x21.2mm - Avery J8651";
$label_fmt['p0']['page_format'] 				= "A4";
$label_fmt['p0']['page_orientation']			= "P";
$label_fmt['p0']['unit'] 						= "mm";
$label_fmt['p0']['label_grid_nb_per_row'] 		= "5";
$label_fmt['p0']['label_grid_nb_per_col']	 	= "13";
$label_fmt['p0']['label_width'] 				= "38.1";
$label_fmt['p0']['label_height'] 				= "21.2";
$label_fmt['p0']['label_grid_from_top'] 		= "10";
$label_fmt['p0']['label_grid_from_left'] 		= "10";
$label_fmt['p0']['label_grid_h_spacing']		= "38.1";
$label_fmt['p0']['label_grid_v_spacing'] 		= "21.2";

$label_con['p0']['content_type'][0] 	= "cote_caue";
$label_con['p0']['comment'][0] 			= htmlentities($msg[296], ENT_QUOTES, $charset);
$label_con['p0']['width'][0]			= "25";
$label_con['p0']['height'][0] 			= "13";
$label_con['p0']['from_top'][0] 		= "8";
$label_con['p0']['from_left'][0] 		= "13";
$label_con['p0']['font'][0] 			= "Arial";
$label_con['p0']['font_size'][0]	 	= "15";
$label_con['p0']['font_style'][0]	 	= "B";
$label_con['p0']['font_color'][0]	 	= "01A5EC";
$label_con['p0']['align'][0] 			= "L";
$label_con['p0']['rotation'][0]			= "0";

$label_con['p0']['content_type'][1] 	= "image";
$label_con['p0']['comment'][1] 			= htmlentities($msg['image'], ENT_QUOTES, $charset);
$label_con['p0']['source'][1] 			= "logo_caue.jpg";
$label_con['p0']['width'][1]			= "11";
$label_con['p0']['height'][1] 			= "11";
$label_con['p0']['from_top'][1] 		= "5";
$label_con['p0']['from_left'][1] 		= "13";
$label_con['p0']['rotation'][1]			= "90";


$label_fmt['p1']['label_name'] 					= "Personnalisé CAUE38 - 38.1x21.2mm - Rotation90 - Avery J8651";
$label_fmt['p1']['page_format'] 				= "A4";
$label_fmt['p1']['page_orientation']			= "P";
$label_fmt['p1']['unit'] 						= "mm";
$label_fmt['p1']['label_grid_nb_per_row'] 		= "5";
$label_fmt['p1']['label_grid_nb_per_col']	 	= "13";
$label_fmt['p1']['label_width'] 				= "38.1";
$label_fmt['p1']['label_height'] 				= "21.2";
$label_fmt['p1']['label_grid_from_top'] 		= "11.3";
$label_fmt['p1']['label_grid_from_left'] 		= "5.5";
$label_fmt['p1']['label_grid_h_spacing']		= "40.75";
$label_fmt['p1']['label_grid_v_spacing'] 		= "21.2";

$label_con['p1']['content_type'][0] 	= "cote_caue";
$label_con['p1']['comment'][0] 			= htmlentities($msg[296], ENT_QUOTES, $charset);
$label_con['p1']['width'][0]			= "18";
$label_con['p1']['height'][0] 			= "21";
$label_con['p1']['from_top'][0] 		= "1.5";
$label_con['p1']['from_left'][0] 		= "37";
$label_con['p1']['font'][0] 			= "Arial";
$label_con['p1']['font_size'][0]	 	= "16";
$label_con['p1']['font_style'][0]	 	= "B";
$label_con['p1']['font_color'][0]	 	= "01A5EC";
$label_con['p1']['align'][0] 			= "C";
$label_con['p1']['rotation'][0]			= "90";

$label_con['p1']['content_type'][1] 	= "image";
$label_con['p1']['comment'][1] 			= htmlentities($msg['image'], ENT_QUOTES, $charset);
$label_con['p1']['source'][1] 			= "logo_caue.jpg";
$label_con['p1']['width'][1]			= "10";
$label_con['p1']['height'][1] 			= "10";
$label_con['p1']['from_top'][1] 		= "5.5";
$label_con['p1']['from_left'][1] 		= "15";
$label_con['p1']['rotation'][1]			= "90";


function display_cote_caue_content($label_id, $step) {
	return display_cote_content($label_id, $step);
}

function verif_cote_caue_content($label_id, $step) {
	return verif_cote_content($label_id, $step);
}

function print_cote_caue(&$target, $content_value, $content_src='') {
		
	$q = "select expl_cote from exemplaires where expl_id = '".$content_src."' ";
	$r = pmb_mysql_query($q);
	$cote = "";
	if (pmb_mysql_num_rows($r)) {
		$row_cote = pmb_mysql_fetch_row($r);
		//$tab_cote = explode("/", trim($row_cote[0]) );
		$str_cote = trim($row_cote[0]);
	} 
	$target->setFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
	$r = 0; $g=-1; $b=-1;
	switch (strlen($content_value['font_color'])) {
		case '6':
			$r = hexdec(substr($content_value['font_color'],0,2));
			$g = hexdec(substr($content_value['font_color'],2,2));
			$b = hexdec(substr($content_value['font_color'],4,2));
			break;
		case '2':
		    $r = hexdec(substr($content_value['font_color'],0,2));
			break;
		default:
			break;
	}
	$target->SetTextColor($r, $g, $b);
	$target->setXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);

	$target->setFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
	$target->setXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'],$target->GetStickY()+$content_value['from_top']);
	$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $str_cote, 0,  $content_value['align']);
	$target->Rotate(0);
	//$target->Rect($target->GetStickX(), $target->GetStickY(), 38.1, 21.2 );
}

?>
