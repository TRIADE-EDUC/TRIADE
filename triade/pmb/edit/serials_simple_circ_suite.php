<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_simple_circ_suite.php,v 1.8 2021/03/16 09:48:36 dgoron Exp $

$base_path = "..";   
$class_path = "$base_path/classes";
$base_noheader = 1;
require_once ("$base_path/includes/init.inc.php");

require_once ("$class_path/fpdf.class.php");
require_once ("$class_path/ufpdf.class.php");
require_once ("$class_path/fpdf_etiquette.class.php");
require_once ("$class_path/encoding_normalize.class.php");
require_once("$class_path/simple_circ.class.php");

if(!isset($start_date)) $start_date = '';
if(!isset($end_date)) $end_date = '';
if(!isset($circ_cb)) $circ_cb = '';

switch($action){
	case "add_circ_cb":
		$simple_circ= new simple_circ($start_date,$end_date,$circ_cb);
		$data=$simple_circ->get_data();
		print(encoding_normalize::json_encode($data));
		exit;
		break;
	case "print_list":
		$simple_circ= new simple_circ($start_date,$end_date,$abt_cb);
		print_circ_list($abt_cb,$simple_circ->get_data());
		exit;
		break;
	default:
		$simple_circ= new simple_circ($start_date,$end_date,$circ_cb);
		break;
		
}


$data=$simple_circ->get_data();

// Démarrage et configuration du pdf
$nom_classe = $fpdf . "_Etiquette";
$pdf = new $nom_classe ($label_grid_nb_per_row, $label_grid_nb_per_col, $page_orientation, $unit , $page_format );
$pdf->Open();
$pdf->SetPageMargins($label_grid_from_top, '0', $label_grid_from_left, '0');
$pdf->SetSticksMargins(0, 0, 0, 0);
$pdf->SetSticksPadding($label_grid_h_spacing,$label_grid_v_spacing );

//Saut Etiquettes
$pos = (($first_row-1)*$label_grid_nb_per_row) + ($first_col);
for ($i=1;$i<$pos;$i++) {
	$pdf->AddStick();
}
$date_parution="";
//Impression etiquettes
for ($i=0;$i<count($data) ;$i++) {
	$content_src = $data[$i];
	if($date_parution!=$data[$i]["date_parution"]){
		$pdf->AddStick();
		$font_family = $content_value[0]['font'];
		if(!empty($font_family)) {
			if(strtolower($font_family) == 'arial') $font_family='Helvetica';
			if (empty($pdf->fonts[$font_family]) && array_key_exists(strtolower($font_family),$pdf->CoreFonts)===false && in_array($font_family,$pdf->CoreFonts)===false) {
		        $pdf->AddFont($font_family);
		        $pdf->AddFont($font_family, 'BI');
		        $pdf->AddFont($font_family, 'B');
		        $pdf->AddFont($font_family, 'I');
		    }
		}
		print_date($pdf, $content_value[0], $content_src); 
		$date_parution=$data[$i]["date_parution"];
	}
	$pdf->AddStick();
	foreach($content_type as $step=>$value) {
	    $font_family = $content_value[$step]['font'];
	    if(!empty($font_family)) {
	    	if(strtolower($font_family) == 'arial') $font_family='Helvetica';
	    	if (empty($pdf->fonts[$font_family]) && array_key_exists(strtolower($font_family),$pdf->CoreFonts)===false && in_array($font_family,$pdf->CoreFonts)===false) {
	            $pdf->AddFont($font_family);
	            $pdf->AddFont($font_family, 'BI');
	            $pdf->AddFont($font_family, 'B');
	            $pdf->AddFont($font_family, 'I');
	        }
	    }
		eval('print_'.$content_type[$step].'($pdf, $content_value[$step], $content_src); ');
	}	
}
	
$pdf->Output('planche_etiquette.pdf', true);
