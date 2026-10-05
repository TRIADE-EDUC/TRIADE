<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_label_no_script.inc.php,v 1.22 2023/11/30 11:10:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

/*
$label_con['s0']['content_type'][1] 	= "image";
$label_con['s0']['comment'][1] 			= htmlentities($msg[image], ENT_QUOTES, $charset);
$label_con['s0'][source][1]			= "pmb.png";
$label_con['s0'][width][1]			= "8";
$label_con['s0'][height][1] 			= "5";
$label_con['s0'][from_top][1] 		= "2";
$label_con['s0']['from_left'][1] 		= "2";
$label_con['s0']['rotation'][1]			= "0";
*/

$label_fmt['s1']['label_name'] 				= "Standard - 38.1x21.2mm - Avery J8651 3*7 étiquettes";
$label_fmt['s1']['page_format'] 				= "A4";
$label_fmt['s1']['page_orientation']			= "P";
$label_fmt['s1']['unit'] 						= "mm";
$label_fmt['s1']['label_grid_nb_per_row'] 		= "3";
$label_fmt['s1']['label_grid_nb_per_col']		= "7";
$label_fmt['s1']['label_width'] 				= "65";
$label_fmt['s1']['label_height'] 				= "42";
$label_fmt['s1']['label_grid_from_top'] 		= "0.8";
$label_fmt['s1']['label_grid_from_left'] 		= "0.5";
$label_fmt['s1']['label_grid_h_spacing']		= "65";
$label_fmt['s1']['label_grid_v_spacing'] 		= "42";

$label_con['s1']['content_type'][0] 	= "cote";
$label_con['s1']['comment'][0] 			= $msg["serial_simple_circ_edit_perio"];
$label_con['s1']['width'][0]			= "65";
$label_con['s1']['height'][0] 			= "20";
$label_con['s1']['from_top'][0] 		= "5";
$label_con['s1']['from_left'][0] 		= "10";

$label_con['s1']['font'][0] 			= "Courier";
$label_con['s1']['font_size'][0]	 	= "14";
$label_con['s1']['font_style'][0]	 	= "B";
$label_con['s1']['font_color'][0]	 	= "000000";
$label_con['s1']['align'][0] 			= "C";
$label_con['s1']['rotation'][0]			= "0";
$label_con['s1']['border_size'][0]		= "0";
$label_con['s1']['character_line_break'][0]	= "";

$label_con['s1']['content_type'][1] 	= "abts";
$label_con['s1']['comment'][1] 			= $msg["serial_simple_circ_edit_abt"];
$label_con['s1']['width'][1]			= "65";
$label_con['s1']['height'][1] 			= "20";
$label_con['s1']['from_top'][1] 		= "15";
$label_con['s1']['from_left'][1] 		= "10";

$label_con['s1']['font'][1] 			= "Courier";
$label_con['s1']['font_size'][1]	 	= "12";
$label_con['s1']['font_style'][1]	 	= "";
$label_con['s1']['font_color'][1]	 	= "000000";
$label_con['s1']['align'][1] 			= "C";
$label_con['s1']['rotation'][1]			= "0";


$label_con['s1']['content_type'][2] 	= "diff";
$label_con['s1']['comment'][2] 			= $msg["serial_simple_circ_edit_diff"];
$label_con['s1']['width'][2]			= "65";
$label_con['s1']['height'][2] 			= "20";
$label_con['s1']['from_top'][2] 		= "18";
$label_con['s1']['from_left'][2] 		= "10";

$label_con['s1']['font'][2] 			= "Courier";
$label_con['s1']['font_size'][2]	 	= "12";
$label_con['s1']['font_style'][2]	 	= "";
$label_con['s1']['font_color'][2]	 	= "000000";
$label_con['s1']['align'][2] 			= "C";
$label_con['s1']['rotation'][2]			= "0";


$label_fmt['s0']['label_name'] 				= "Standard - 38.1x21.2mm - Avery J8651 2*7 étiquettes";
$label_fmt['s0']['page_format'] 				= "A4";
$label_fmt['s0']['page_orientation']			= "P";
$label_fmt['s0']['unit'] 						= "mm";
$label_fmt['s0']['label_grid_nb_per_row'] 		= "2";
$label_fmt['s0']['label_grid_nb_per_col']		= "7";
$label_fmt['s0']['label_width'] 				= "104";
$label_fmt['s0']['label_height'] 				= "42";
$label_fmt['s0']['label_grid_from_top'] 		= "0.8";
$label_fmt['s0']['label_grid_from_left'] 		= "0.5";
$label_fmt['s0']['label_grid_h_spacing']		= "100";
$label_fmt['s0']['label_grid_v_spacing'] 		= "42";

$label_con['s0']['content_type'][0] 	= "cote";
$label_con['s0']['comment'][0] 			= $msg["serial_simple_circ_edit_perio"];
$label_con['s0']['width'][0]			= "100";
$label_con['s0']['height'][0] 			= "20";
$label_con['s0']['from_top'][0] 		= "5";
$label_con['s0']['from_left'][0] 		= "10";

$label_con['s0']['font'][0] 			= "Courier";
$label_con['s0']['font_size'][0]	 	= "14";
$label_con['s0']['font_style'][0]	 	= "B";
$label_con['s0']['font_color'][0]	 	= "000000";
$label_con['s0']['align'][0] 			= "C";
$label_con['s0']['rotation'][0]			= "0";
$label_con['s0']['border_size'][0]		= "0";
$label_con['s0']['character_line_break'][0]	= "";

$label_con['s0']['content_type'][1] 	= "abts";
$label_con['s0']['comment'][1] 			= $msg["serial_simple_circ_edit_abt"];
$label_con['s0']['width'][1]			= "100";
$label_con['s0']['height'][1] 			= "20";
$label_con['s0']['from_top'][1] 		= "15";
$label_con['s0']['from_left'][1] 		= "10";

$label_con['s0']['font'][1] 			= "Courier";
$label_con['s0']['font_size'][1]	 	= "12";
$label_con['s0']['font_style'][1]	 	= "";
$label_con['s0']['font_color'][1]	 	= "000000";
$label_con['s0']['align'][1] 			= "C";
$label_con['s0']['rotation'][1]			= "0";


$label_con['s0']['content_type'][2] 	= "diff";
$label_con['s0']['comment'][2] 			= $msg["serial_simple_circ_edit_diff"];
$label_con['s0']['width'][2]			= "100";
$label_con['s0']['height'][2] 			= "20";
$label_con['s0']['from_top'][2] 		= "18";
$label_con['s0']['from_left'][2] 		= "10";

$label_con['s0']['font'][2] 			= "Courier";
$label_con['s0']['font_size'][2]	 	= "12";
$label_con['s0']['font_style'][2]	 	= "";
$label_con['s0']['font_color'][2]	 	= "000000";
$label_con['s0']['align'][2] 			= "C";
$label_con['s0']['rotation'][2]			= "0";


/*
$label_fmt['s1'][label_name] 					= "Standard - 38.1x21.2mm - Rotation90° - Avery J8651";
$label_fmt['s1']['page_format'] 				= "A4";
$label_fmt['s1']['page_orientation']			= "P";
$label_fmt['s1']['unit'] 						= "mm";
$label_fmt['s1'][label_grid_nb_per_row] 		= "5";
$label_fmt['s1'][label_grid_nb_per_col]		= "13";
$label_fmt['s1'][label_width] 				= "38.1";
$label_fmt['s1'][label_height] 				= "21.2";
$label_fmt['s1'][label_grid_from_top] 		= "11.3";
$label_fmt['s1'][label_grid_from_left] 		= "5.5";
$label_fmt['s1'][label_grid_h_spacing]		= "40.75";
$label_fmt['s1'][label_grid_v_spacing] 		= "21.2";

$label_con['s1']['content_type'][0] 	= "cote";
$label_con['s1']['comment'][0] 			= htmlentities($msg[296], ENT_QUOTES, $charset);
$label_con['s1'][width][0]			= "18";
$label_con['s1'][height][0] 			= "21";
$label_con['s1'][from_top][0] 		= "1.5";
$label_con['s1']['from_left'][0] 		= "37";
$label_con['s1']['font'][0] 			= "Courier";
$label_con['s1']['font_size'][0]	 	= "14";
$label_con['s1']['font_style'][0]	 	= "B";
$label_con['s1']['font_color'][0]	 	= "000000";
$label_con['s1']['align'][0] 			= "C";
$label_con['s1']['rotation'][0]			= "90";
$label_con['s1']['border_size'][0]		= "0";
$label_con['s1']['character_line_break'][0]	= "";

$label_con['s1']['content_type'][1] 	= "image";
$label_con['s1']['comment'][1] 			= htmlentities($msg[image], ENT_QUOTES, $charset);
$label_con['s1'][source][1]			= "pmb.png";
$label_con['s1'][width][1]			= "8";
$label_con['s1'][height][1] 			= "5";
$label_con['s1'][from_top][1] 		= "7.1";
$label_con['s1']['from_left'][1] 		= "7";
$label_con['s1']['rotation'][1]			= "90";

$label_fmt[s2][label_name] 				= "Standard - 38.1x21.2mm - Avery L7651";
$label_fmt[s2]['page_format'] 				= "A4";
$label_fmt[s2]['page_orientation']			= "P";
$label_fmt[s2]['unit'] 						= "mm";
$label_fmt[s2][label_grid_nb_per_row] 		= "5";
$label_fmt[s2][label_grid_nb_per_col]		= "13";
$label_fmt[s2][label_width] 				= "38.1";
$label_fmt[s2][label_height] 				= "21.2";
$label_fmt[s2][label_grid_from_top] 		= "17.1";
$label_fmt[s2][label_grid_from_left] 		= "1";
$label_fmt[s2][label_grid_h_spacing]		= "40.75";
$label_fmt[s2][label_grid_v_spacing] 		= "21.2";

$label_con[s2]['content_type'][0] 	= "cote";
$label_con[s2]['comment'][0] 			= htmlentities($msg[296], ENT_QUOTES, $charset);
$label_con[s2][width][0]			= "22.1";
$label_con[s2][height][0] 			= "18";
$label_con[s2][from_top][0] 		= "1.6";
$label_con[s2]['from_left'][0] 		= "10";
$label_con[s2]['font'][0] 			= "Courier";
$label_con[s2]['font_size'][0]	 	= "14";
$label_con[s2]['font_style'][0]	 	= "B";
$label_con[s2]['font_color'][0]	 	= "000000";
$label_con[s2]['align'][0] 			= "C";
$label_con[s2]['rotation'][0]			= "0";
$label_con[s2]['border_size'][0]		= "0";
$label_con['s2']['character_line_break'][0]	= "";

$label_con[s2]['content_type'][1] 	= "image";
$label_con[s2]['comment'][1] 			= htmlentities($msg[image], ENT_QUOTES, $charset);
$label_con[s2][source][1]			= "";
$label_con[s2][width][1]			= "8";
$label_con[s2][height][1] 			= "5";
$label_con[s2][from_top][1] 		= "2";
$label_con[s2]['from_left'][1] 		= "2";
$label_con[s2]['rotation'][1]			= "0";
*/
function getLabelFormatList() {
	global $label_fmt;	
	return $label_fmt;

}


function displayLabelFormat($label_id) {	
	global $label_fmt, $msg, $charset;
		
	$page_size=array("A3","A4","A5","Letter","Legal");
	
	$r ="<div class='row'>
			<div class='left'>".htmlentities($msg['page_format'], ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<select id='page_format' name='page_format' >";

	foreach ($page_size as $size) {
		$r.="<option value='".$size."' ";
		if ($label_fmt[$label_id]['page_format']==$size) {
			$r.="selected='selected' ";
		}
		$r.=">".htmlentities($size, ENT_QUOTES, $charset)."</option>";							
	}
	$r.="		</select>
			</div>
		</div>";
	
		$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['page_orientation'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='page_orientation' name='page_orientation' >";
	if ($label_fmt[$label_id]['page_orientation']=='P') {
		$a=array('P', $msg['edit_cbgen_mep_portrait'],'L', $msg['edit_cbgen_mep_paysage']);
	} else {
		$a=array('L', $msg['edit_cbgen_mep_paysage'],'P', $msg['edit_cbgen_mep_portrait']);
	}
	$r.="			<option value='".$a[0]."' selected='selected' >".htmlentities($a[1], ENT_QUOTES, $charset)."</option>
					<option value='".$a[2]."'>".htmlentities($a[3], ENT_QUOTES, $charset)."</option>
				</select>
			</div>
		</div>";

	$r.="<input type='hidden' id='unit' name='unit' value='".$label_fmt[$label_id]['unit']."' />";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_nb_per_row'], ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_nb_per_row' name='label_grid_nb_per_row' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_nb_per_row']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_nb_per_col'], ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_nb_per_col' name='label_grid_nb_per_col' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_nb_per_col']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_width']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_width' name='label_width' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_width']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_height']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_height' name='label_height' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_height']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_from_top']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_from_top' name='label_grid_from_top' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_from_top']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_from_left']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_from_left' name='label_grid_from_left' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_from_left']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_h_spacing']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_h_spacing' name='label_grid_h_spacing' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_h_spacing']."' />
			</div>
		</div>";

	$r.="<div class='row'>
			<div class='left'>".htmlentities($msg['label_grid_v_spacing']." (".$label_fmt[$label_id]['unit'].")", ENT_QUOTES, $charset)."</div>
			<div class='right' >
				<input type='text' id='label_grid_v_spacing' name='label_grid_v_spacing' class='saisie-5em' style='text-align:right;' value='".$label_fmt[$label_id]['label_grid_v_spacing']."' />
			</div>
		</div>";

	return $r;
}

function verifLabelFormat($label_id){
	global $label_fmt, $msg, $charset;	
	
	$r = "
		var first_col = document.getElementById('first_col').value;
		var max_col = document.getElementById('label_grid_nb_per_row').value;
		if ( (first_col=='') || (max_col=='') || (isNaN(first_col)) || (isNaN(max_col)) ) {
			alert(\"".$msg['param_err_impr']."\");
			return false;
		}
		first_col = parseInt(first_col);
		max_col = parseInt(max_col);
		if ( (first_col < 1) || (max_col < 1) || (first_col > max_col) ) {
			alert(\"".$msg['param_err_impr']."\");
			return false;
		}";

	$r.= "
		var first_row = document.getElementById('first_row').value;
		var max_row = document.getElementById('label_grid_nb_per_col').value;
		if ( (first_row=='') || (max_row=='') || (isNaN(first_row)) || (isNaN(max_row)) ) {
			alert(\"".$msg['param_err_impr']."\");
			return false;
		}
		first_row = parseInt(first_row);
		max_row = parseInt(max_row);
		if ( (first_row < 1) || (max_row < 1) || (first_row > max_row) ) {
			alert(\"".$msg['param_err_impr']."\");
			return false;
		}";

	$r.= "
		var label_width = document.getElementById('label_width').value;
		if ( (label_width=='') || (isNaN(label_width)) || (parseFloat(label_width) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
			return false;
		}";

	$r.= "
		var label_height = document.getElementById('label_height').value;
		if ( (label_height=='') || (isNaN(label_height)) || (parseFloat(label_height) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var label_grid_from_top = document.getElementById('label_grid_from_top').value;
		if ( (label_grid_from_top=='') || (isNaN(label_grid_from_top)) || (parseFloat(label_grid_from_top) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var label_grid_from_left = document.getElementById('label_grid_from_left').value;
		if ( (label_grid_from_left=='') || (isNaN(label_grid_from_left)) || (parseFloat(label_grid_from_left) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var label_grid_h_spacing = document.getElementById('label_grid_h_spacing').value;
		if ( (label_grid_h_spacing=='') || (isNaN(label_grid_h_spacing)) || (parseFloat(label_grid_h_spacing) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var label_grid_v_spacing = document.getElementById('label_grid_v_spacing').value;
		if ( (label_grid_v_spacing=='') || (isNaN(label_grid_v_spacing)) || (parseFloat(label_grid_v_spacing) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	return $r;	
}

function getLabelContent($label_id) {	
	global $label_con;
	return $label_con[$label_id];	
}

function displayLabelContent($label_id){
	global $label_con, $msg, $charset;
	
	$r="";
	foreach($label_con[$label_id]['content_type'] as $step=>$content_type) {

		eval('$r.=display_'.$content_type.'_content($label_id, $step);' );
	}
	return $r;
}

function display_cote_content($label_id, $step) {	
	global $label_fmt, $label_con, $msg, $charset;

	$r = "<div class='row'>
			<input type='hidden' id='content_type[".$step."]' name='content_type[".$step."]' value='".$label_con[$label_id]['content_type'][$step]."' />
			<label class='etiquette'>".htmlentities($label_con[$label_id]['comment'][$step], ENT_QUOTES, $charset)."</label>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_largeur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][width]' name='content_value[".$step."][width]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['width'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_hauteur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][height]' name='content_value[".$step."][height]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['height'][$step]."' />
			</div>
		</div>";	
	
	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_top'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_top]' name='content_value[".$step."][from_top]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_top'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_left'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_left]' name='content_value[".$step."][from_left]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_left'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][font]' name='content_value[".$step."][font]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['font'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_size'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][font_size]' name='content_value[".$step."][font_size]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['font_size'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_style'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][font_style]' name='content_value[".$step."][font_style]' >";		
	if ($label_con[$label_id]['font_style'][$step] == '') {
		$r.="		<option value='' selected='selected' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";	
	} else {
		$r.="		<option value='' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' selected='selected' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";	
	}	
	$r.= "		</select>
			</div>
		</div>";

	$r.= "<input type='hidden' id='content_value[".$step."][font_color]' name='content_value[".$step."][font_color]' value='".$label_con[$label_id]['font_color'][$step]."' />";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['align'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][align]' name='content_value[".$step."][align]' >";
	switch ($label_con[$label_id]['align'][$step]) {
		case 'L' :	
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' selected='selected' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'R' :	
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' selected='selected' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'J' :	
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' selected='selected' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
		case 'C':
		default :
			$r.="	<option value='C' selected='selected' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
	}
	$r.= "		</select>
			</div>
		</div>";

	if(!isset($label_con[$label_id]['rotation'][$step])) $label_con[$label_id]['rotation'][$step] = '0';
	if(!isset($label_con[$label_id]['border_size'][$step])) $label_con[$label_id]['border_size'][$step] = '0';
	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][rotation]' name='content_value[".$step."][rotation]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['rotation'][$step]."' />
			</div>
		</div>
        <div class='row'>
			<div class='left'>".htmlentities($msg['cote_border_size'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][border_size]' name='content_value[".$step."][border_size]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['border_size'][$step]."' />
			</div>
		</div>
        <div class='row'>
			<div class='left'>".htmlentities($msg['cote_character_line_break'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][character_line_break]' name='content_value[".$step."][character_line_break]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['character_line_break'][$step]."' />
			</div>
		</div>";

	return $r;
}
	
function display_abts_content($label_id, $step) {
	global $label_fmt, $label_con, $msg, $charset;

	$r = "<div class='row'>
			<input type='hidden' id='content_type[".$step."]' name='content_type[".$step."]' value='".$label_con[$label_id]['content_type'][$step]."' />
			<label class='etiquette'>".htmlentities($label_con[$label_id]['comment'][$step], ENT_QUOTES, $charset)."</label>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_largeur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][width]' name='content_value[".$step."][width]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['width'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_hauteur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][height]' name='content_value[".$step."][height]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['height'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_top'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_top]' name='content_value[".$step."][from_top]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_top'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_left'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_left]' name='content_value[".$step."][from_left]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_left'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='hidden' id='content_value[".$step."][font]' name='content_value[".$step."][font]' value='".$label_con[$label_id]['font'][$step]."' />
				".htmlentities($label_con[$label_id]['font'][$step], ENT_QUOTES, $charset)."
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_size'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][font_size]' name='content_value[".$step."][font_size]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['font_size'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_style'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][font_style]' name='content_value[".$step."][font_style]' >";
	if ($label_con[$label_id]['font_style'][$step] == '') {
		$r.="		<option value='' selected='selected' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
	} else {
		$r.="		<option value='' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' selected='selected' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
	}
	$r.= "		</select>
			</div>
		</div>";

	$r.= "<input type='hidden' id='content_value[".$step."][font_color]' name='content_value[".$step."][font_color]' value='".$label_con[$label_id]['font_color'][$step]."' />";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['align'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][align]' name='content_value[".$step."][align]' >";
	switch ($label_con[$label_id]['align'][$step]) {
		case 'L' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' selected='selected' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'R' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' selected='selected' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'J' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' selected='selected' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
		case 'C':
		default :
			$r.="	<option value='C' selected='selected' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
	}
	$r.= "		</select>
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][rotation]' name='content_value[".$step."][rotation]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['rotation'][$step]."' />
			</div>
		</div>";

	return $r;
}

function display_diff_content($label_id, $step) {
	global $label_fmt, $label_con, $msg, $charset;

	$r = "<div class='row'>
			<input type='hidden' id='content_type[".$step."]' name='content_type[".$step."]' value='".$label_con[$label_id]['content_type'][$step]."' />
			<label class='etiquette'>".htmlentities($label_con[$label_id]['comment'][$step], ENT_QUOTES, $charset)."</label>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_largeur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][width]' name='content_value[".$step."][width]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['width'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['serial_simple_circ_edit_hauteur_zone'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][height]' name='content_value[".$step."][height]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['height'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_top'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_top]' name='content_value[".$step."][from_top]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_top'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['cote_from_left'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_left]' name='content_value[".$step."][from_left]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_left'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='hidden' id='content_value[".$step."][font]' name='content_value[".$step."][font]' value='".$label_con[$label_id]['font'][$step]."' />
				".htmlentities($label_con[$label_id]['font'][$step], ENT_QUOTES, $charset)."
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_size'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][font_size]' name='content_value[".$step."][font_size]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['font_size'][$step]."' />
			</div>
		</div>";


	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['font_style'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][font_style]' name='content_value[".$step."][font_style]' >";
	if ($label_con[$label_id]['font_style'][$step] == '') {
		$r.="		<option value='' selected='selected' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
	} else {
		$r.="		<option value='' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' selected='selected' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
	}
	$r.= "		</select>
			</div>
		</div>";

	$r.= "<input type='hidden' id='content_value[".$step."][font_color]' name='content_value[".$step."][font_color]' value='".$label_con[$label_id]['font_color'][$step]."' />";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['align'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<select id='content_value[".$step."][align]' name='content_value[".$step."][align]' >";
	switch ($label_con[$label_id]['align'][$step]) {
		case 'L' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' selected='selected' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'R' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' selected='selected' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
		case 'J' :
			$r.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' selected='selected' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
		case 'C':
		default :
			$r.="	<option value='C' selected='selected' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
			break;
	}
	$r.= "		</select>
			</div>
		</div>";


	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][rotation]' name='content_value[".$step."][rotation]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['rotation'][$step]."' />
			</div>
		</div>";

	return $r;
}

function display_image_content($label_id, $step) {
	
	global $label_fmt, $label_con, $msg, $charset;

	$r = "<div class='row'>
			<input type='hidden' id='content_type[".$step."]' name='content_type[".$step."]' value='".$label_con[$label_id]['content_type'][$step]."' />
			<label class='etiquette'>".htmlentities($label_con[$label_id]['comment'][$step], ENT_QUOTES, $charset)."</label>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['image_source'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][source]' name='content_value[".$step."][source]' class='saisie-10em' value='".$label_con[$label_id]['source'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['image_width'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][width]' name='content_value[".$step."][width]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['width'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['image_height'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][height]' name='content_value[".$step."][height]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['height'][$step]."' />
			</div>
		</div>";	
	
	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['image_from_top'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_top]' name='content_value[".$step."][from_top]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_top'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['image_from_left'].' ('.$label_fmt[$label_id]['unit'].')', ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][from_left]' name='content_value[".$step."][from_left]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['from_left'][$step]."' />
			</div>
		</div>";

	$r.= "<div class='row'>
			<div class='left'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='right'>
				<input type='text' id='content_value[".$step."][rotation]' name='content_value[".$step."][rotation]' class='saisie-5em' style='text-align:right;' value='".$label_con[$label_id]['rotation'][$step]."' />
			</div>
		</div>";

	return $r;
}

function verifLabelContent($label_id){
	global $label_con, $msg, $charset;
	
	$r="";
	foreach($label_con[$label_id]['content_type'] as $step=>$content_type) {

		eval('$r.=verif_'.$content_type.'_content($label_id, $step);' );
	}
	return $r;
}

function  verif_abts_content($label_id, $step) {
}

function  verif_diff_content($label_id, $step) {
}

function  verif_cote_content($label_id, $step) {
	global $label_fmt, $label_con, $msg, $charset;

	$r = "
		var width = document.getElementById('content_value[".$step."][width]').value;	
		if ( (width=='') || (isNaN(width)) || (parseFloat(width) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";	

	$r.= "
		var height = document.getElementById('content_value[".$step."][height]').value;	
		if ( (height=='') || (isNaN(height)) || (parseFloat(height) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var from_top = document.getElementById('content_value[".$step."][from_top]').value;	
		if ( (from_top=='') || (isNaN(from_top)) || (parseFloat(from_top) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var from_left = document.getElementById('content_value[".$step."][from_left]').value;	
		if ( (from_left=='') || (isNaN(from_left)) || (parseFloat(from_left) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var font_size = document.getElementById('content_value[".$step."][font_size]').value;	
		if ( (font_size=='') || (isNaN(font_size)) || (parseInt(font_size) < 1) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var rotation = document.getElementById('content_value[".$step."][rotation]').value;	
		if ( (rotation=='') || (isNaN(rotation)) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";
	
	$r.= "
		var border_size = document.getElementById('content_value[".$step."][border_size]').value;
		if ( (border_size=='') || (isNaN(border_size)) || (parseFloat(border_size) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	return $r;	
}

function  verif_image_content($label_id, $step) {

	global $label_fmt, $label_con, $msg, $charset;

	$r = "
		var width = document.getElementById('content_value[".$step."][width]').value;	
		if ( (width=='') || (isNaN(width)) || (parseFloat(width) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";	

	$r.= "
		var height = document.getElementById('content_value[".$step."][height]').value;	
		if ( (height=='') || (isNaN(height)) || (parseFloat(height) <= 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var from_top = document.getElementById('content_value[".$step."][from_top]').value;	
		if ( (from_top=='') || (isNaN(from_top)) || (parseFloat(from_top) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var from_left = document.getElementById('content_value[".$step."][from_left]').value;	
		if ( (from_left=='') || (isNaN(from_left)) || (parseFloat(from_left) < 0) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	$r.= "
		var rotation = document.getElementById('content_value[".$step."][rotation]').value;	
		if ( (rotation=='') || (isNaN(rotation)) ) {
			alert(\"".$msg['param_err_impr']."\");
		return false;
	}";

	return $r;	
}

function print_abts(&$target, $content_value, $data = array()) {	

	//if(count($data["diff"])>1)return;
	$target->SetFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
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
	
	$display_diff_list="";
	$group_name = '';
	foreach($data["diff"] as $diff){
		if($diff["is_group"]){
			$group_name=$diff["group_name"];
			$group=explode(" - ", $group_name);			
			$contens_group="";
			if(count($diff["group"]))
			foreach($diff["group"] as $elt_group){
				if(count($diff["group"])>1)$gen_codebarres=1;
				if($elt_group["is_responsable"]) {
					$contens_group.=$elt_group['nom'];
					break;
				}
			}
			$display_diff_list.=$group_name;
			if($contens_group){
				$display_diff_list.=": ".$contens_group;
			}
			break; // le premier trouvé est imprimé, si autres il y aura un cb
		}else{
			$gen_codebarres=1;
			//if($display_diff_list)$display_diff_list.=", ";
			//$display_diff_list.=$diff["nom"];
		}
		if($gen_codebarres) break;
			
	}
	
	$target->SetXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'],$target->GetStickY()+$content_value['from_top'] ) ;
	if(!empty($group) && count($group)==2){
		$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $group[0], 0,  $content_value['align']);

		$target->SetXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']+5);
		$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $group[1], 0,  $content_value['align']);
	}else{
		$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $group_name, 0,  $content_value['align']);
	}
	$target->Rotate(0);
	//$target->Rect($target->GetStickX(), $target->GetStickY(), 38.1, 21.2 ); //Affiche un cadre autour de l'etiquette 
}

function print_date(&$target, $content_value, $data = array()) {

	$target->SetFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
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

	$tab_jours = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
	$tabDate = explode('-', $data["date_parution"]);
	$timestamp = mktime(0, 0, 0, $tabDate[1], $tabDate[2], $tabDate[0]);
	$jour = date('w', $timestamp);
	
	$target->SetTextColor($r, $g, $b);
	$target->SetXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'],$target->GetStickY()+$content_value['from_top'] ) ;
	$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $tab_jours[$jour], 0,  $content_value['align']);
	$target->Rotate(0);	
}

function get_border_print_cote(&$target, $content_value) {
    $border = 0;
    if(!empty($content_value['border_size'])) {
        $target->SetLineWidth($content_value['border_size']);
        $border = 1;
    }
    return $border;
}

function print_cote(&$target, $content_value, $data = array()) {
	//printr($data);
    $str_display = $data["tit1"];
    if(!empty($content_value['character_line_break'])) {
        $tab_display = explode($content_value['character_line_break'], rtrim(ltrim($str_display)));
        $str_display = implode("\n", $tab_display);
    }
	$target->SetFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
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
	$target->SetXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'],$target->GetStickY()+$content_value['from_top'] ) ;
	$target->MultiCell($content_value['width'], ($content_value['font_size']*25.4/72), $str_display, 0,  $content_value['align']);
	$target->Rotate(0);
	$border = get_border_print_cote($target, $content_value);
	if($border) {
	    $target->Rect($target->GetStickX(), $target->GetStickY(), $content_value['width'], $content_value['height']); //Affiche un cadre autour de l'etiquette
	    $target->SetLineWidth(0.2);
	}
}


function print_circ_list($abt_cb,$data){
	global $msg,$charset;

	$data=($charset != "uft-8" ? encoding_normalize::utf8_normalize($data) : $data);
	
	$serialcirc_circ_pdf_page="	
		<page backtop='2mm' backbottom='2mm' backleft='2mm' backright='5mm' >	
			<table style='border-spacing: 5px'>
		    	<tr>
					<td style='width:540'>!!tableau_left!!</td>
					<td style='width:540'>!!tableau_rigth!!</td>
		    	</tr>
			</table>
		</page>
			";
	$serialcirc_circ_pdf_diffusion="			
			<table class='listcirc' style='width:100%; border: solid 1px black; background: #E7E7E7; text-align: center; font-size: 8pt; border-spacing: 0px'>
	    		<tr>
	    			<td colspan='4'style='height:30'><strong>!!periodique!!</strong></td>
	    		</tr>
	    		<tr>
	    			<td class='align_left' style='height:30'>DATE/N <br/> <br/></td>
	    			<td colspan='3' class='align_left'>DEPART DOC <br/> <br/></td>
	    		</tr>
	    		<tr>
	    			<td>CIRCUIT DE CONSULTATION</td>
	    			<td>DATE DE RECEPTION</td>
	    			<td>A FAIRE PASSE LE</td>
	    			<td style='width: 120'>VISA</td>
	    		</tr>
	    		!!destinataires!!
	    		<tr>
	    			<td colspan='4' style='height:20'>MERCI DE BIEN VOULOIR RESPECTER LES DELAIS DE CONSULTATION</td>
	    		</tr>
			</table>		
		";
	$serialcirc_circ_pdf_diff="
					<tr>
		    			<td style='width:70'>!!destinataire!!</td>
		    			<td style='width:70'></td>
		    			<td style='width:70'></td>
		    			<td style='width:70'></td>
		    		</tr>";

	$all_tpl="";
	$pair=0;
	
	$abt_cb_list=array();
	foreach ($abt_cb as $circ_cb){
	    $abt_cb_list[] = (int) $circ_cb;
	}
	$count_values=array_count_values($abt_cb_list);
	foreach ($data as $abt){
		for ($count=0;$count<$count_values[$abt["abt_id"]];$count++){
			
			$tpl=$serialcirc_circ_pdf_diffusion;
			
			$tpl=str_replace("!!periodique!!",strtoupper($abt["tit1"]), $tpl);
			$all_tpl_diff="";
			foreach($abt["diff"] as $diff){
				$tpl_diff=$serialcirc_circ_pdf_diff;
				if($diff["is_group"]){
					$group=explode(" - ", $diff["group_name"]);
					if(count($group)==2){
						$destinataire=strtoupper($group[0])."<br/>( ".strtoupper($group[1])." )";
					}else $destinataire=strtoupper($diff["group_name"]);
					if(count($diff["group"]))
					foreach($diff["group"] as $elt_group){
						if($elt_group["is_responsable"]) {
							$destinataire.="<br/>".strtoupper($elt_group['nom']);
						}
					}
				}else{
					$destinataire=strtoupper($diff["nom"]." ".$diff["prenom"]);
				}
				$tpl_diff=str_replace("!!destinataire!!",$destinataire, $tpl_diff);
				$all_tpl_diff.=$tpl_diff;
			}
			for($i=count($abt["diff"]);$i<7;$i++){
				$tpl_diff=$serialcirc_circ_pdf_diff;
				$tpl_diff=str_replace("!!destinataire!!","<br/> <br/> <br/>", $tpl_diff);
				$all_tpl_diff.=$tpl_diff;
			}
			$tpl=str_replace("!!destinataires!!",$all_tpl_diff, $tpl);
			if(!$pair){
				$pair=1;
				$tpl_page=$serialcirc_circ_pdf_page;
				$tpl_page=str_replace("!!tableau_left!!",$tpl, $tpl_page);
			}else{
				$pair=0;
				$tpl_page=str_replace("!!tableau_rigth!!",$tpl, $tpl_page);
				$all_tpl.=$tpl_page;
			}
			
		}
	}
	$tpl_page=str_replace("!!tableau_rigth!!","", $tpl_page);
	if($pair){
		$all_tpl.=$tpl_page;
	}
	$style="		
		<style type='text/css'>
		table.listcirc {
			border-width: 5px;
			border-spacing: 0px;
			border-style: outset;
			border-color: gray;
			border-collapse: separate;
			background-color: rgb(255, 250, 250);
		}
		table.listcirc th {
			border-width: 1px;
			padding: 4px;
			margin:0px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			-moz-border-radius: ;
		}
		table.listcirc td {
			border-width: 1px;
			padding: 4px;
			margin:0px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			-moz-border-radius: ;
		}
		</style>
	";
//	print $style.$all_tpl;exit;
	
	$html2pdf = new Html2Pdf('L','A4','fr');
	$html2pdf->writeHTML($style.$all_tpl);
	$html2pdf->output('diffusion.pdf','D');
}

function print_diff(&$target, $content_value, $data = array()) {
//	printr($data);
	global $label_grid_nb_per_row;
	$display_diff_list = "";
	if(count($data["diff"])==1)return;
/*	
	foreach($data["diff"] as $diff){
		if($diff["is_group"]){
			$group_name=$diff["group_name"];
			$contens_group="";
			foreach($diff["group"] as $elt_group){
				//if($contens_group)$contens_group.=", ";
				if($elt_group["is_responsable"]) $contens_group.=$elt_group['nom'];
				if(count($diff["group"])>1)$gen_codebarres=1;
			}
			$display_diff_list.=$group_name;
			if($contens_group){
				$display_diff_list.=": ".$contens_group;
			}
		}else{
			$gen_codebarres=1;
			if($display_diff_list)$display_diff_list.=", ";
			$display_diff_list.=$diff["nom"];
		}			
	}
*/	

	// gen du code-barre
	$code_barres = "".$data["abt_id"];
	while(strlen($code_barres) < 12)
		$code_barres = "0".$code_barres;

	$target->SetFont($content_value['font'],$content_value['font_style'] ,$content_value['font_size']);
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
	$target->SetXY($target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top']);
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'],$target->GetStickY()+$content_value['from_top'] ) ;
	if($code_barres){
		if($label_grid_nb_per_row==3) $offset=8;
		else $offset=24; 
		$target->DrawBarcode($code_barres, $target->GetStickX()+$content_value['from_left']+$offset, 
			$target->GetStickY()+$content_value['from_top']+8, 50, 8, 'c39');		
	}	
	//else $target->MultiCell($content_value[width], ($content_value['font_size']*25.4/72), $display_diff_list, 0,  $content_value['align']);
	$target->Rotate(0);
}

function print_image(&$target, $content_value, $content_src='') {	
	if($content_value['source'] == '') return;		
	$target->Rotate($content_value['rotation'], $target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top'] );
	$target->Image("../../../images/".$content_value['source'], $target->GetStickX()+$content_value['from_left'], $target->GetStickY()+$content_value['from_top'], $content_value['width'], $content_value['height']);
	$target->Rotate(0);	
}

function print_sel_diffusion($list){
	global $fpdf;
	global $print_action,$index_start;
	if(!$print_action){
		print $this->gen_print_option($list);
		return;
	}
	if(!$index_start)$index_start=1;

	$this->count=0;
	$ourPDF = new $fpdf('P', 'mm', 'A4');
	$ourPDF->Open();
	$ourPDF->SetAutoPageBreak(0,0);

	for($i=0;$i<$index_start-1;$i++){
			
		$this->build($this->count++,$ourPDF,"");
	}
	foreach($list as $circ){
		$expl_id=$circ['expl_id'];
		$start_diff_id=$circ['start_diff_id'];

		$tpl.=$this->build_print_diffusion($expl_id,$start_diff_id,$ourPDF);
	}
	//print 		$tpl;exit;
	@$ourPDF->OutPut("/home/insee/www/pmb/temp/pdf_impression_perio_circ/".date("Ymd_His").".pdf","F");
	header("Content-Type: application/pdf");
	$ourPDF->OutPut();
}

function build($num,$ourPDF,$data){
	global $pmb_pdf_font;

	if(!(($num)%14)) {
		$ourPDF->addPage();
		$ourPDF->SetLeftMargin(0);
		$ourPDF->SetTopMargin(0);

	}
	if(!$data) return;

	$num=$num-((int)($num/14)*14);

	$hauteur=42;
	if($num%2){
		$x=110;
	}else{
		$x=6;
	}
	$y=($hauteur * (int)(($num)/2))+10;

	//$info.=" num=$num; x=$x, y=$y " ;
	// titre perio
	$ourPDF->SetXY ($x,$y);
	$ourPDF->setFont($pmb_pdf_font, 'BI', 10);
	$ourPDF->multiCell(110, 3, $info.substr($data['titre'],0,52) , 0, 'L', 0);

	if($data['cb']){
		// Code barre
		$ourPDF->SetXY ($x,$y+4);
		$ourPDF->setFont($pmb_pdf_font, '', 10);
		$ourPDF->multiCell(110, 3, "Ex ".substr($data['cb'],0,20)."  Cote : ".substr($data['cote'],0,20) , 0, 'L', 0);
	}
	// Numéro & Date
	$ourPDF->SetXY ($x,$y+7);
	$ourPDF->setFont($pmb_pdf_font, '', 8);
	$ourPDF->multiCell(110, 3, "".substr($data['numero_libelle'],0,20)."  Date n° : ".substr($data['date_libelle'],0,30) , 0, 'L', 0);

	// Date reception
	$ourPDF->SetXY ($x,$y+10);
	$ourPDF->setFont($pmb_pdf_font, '', 8);
	$ourPDF->multiCell(110, 3, "Reçu le : ".substr($data['date_reception'],0,20)."  ".substr($data['abt_name'],0,30) , 0, 'L', 0);
	$i=0;
	// empr list
	if(is_array($data['empr'])){
		foreach($data['empr']as $empr){
			if($i==4){
				$x=$x+30;
				$i=0;
			}
			$ourPDF->SetXY ($x,$y+15+($i*3));
			$ourPDF->setFont($pmb_pdf_font, '', 8);
			$field=substr($empr['name'],0,5);
			if($empr['pperso'])$field.="-".substr($empr['pperso'],0,5);
			$ourPDF->multiCell(110, 3, $field , 0, 'L', 0);
			$i++;
		}
	}
	if((!is_array($data['empr']) || !count($data['empr'])) && $data['abt_name1']){
			
		// aff abt
		$ourPDF->SetXY ($x,$y+15);
		$ourPDF->setFont($pmb_pdf_font, '', 10);
		$ourPDF->multiCell(110, 3, substr($data['abt_name1'],0,30) , 0, 'L', 0);
		$i=0;
	}
}

?>