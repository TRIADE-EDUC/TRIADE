<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: impr_cote_suite.php,v 1.9.8.2 2025/05/13 09:32:02 dgoron Exp $

global $base_noheader, $pmb_label_construct_script, $idcaddie, $elt_flag, $elt_no_flag, $fpdf, $label_grid_nb_per_row, $label_grid_nb_per_col;
global $page_orientation, $unit, $page_format, $label_grid_from_top, $label_grid_from_left, $label_grid_h_spacing, $label_grid_v_spacing;
global $first_row, $first_col, $content_type;

$base_path = "../../..";
$class_path = "$base_path/classes";
$base_noheader = 1;
require_once ("$base_path/includes/init.inc.php");

require_once ("$class_path/fpdf.class.php");
require_once ("$class_path/ufpdf.class.php");
require_once ("$class_path/fpdf_etiquette.class.php");
require_once ("$class_path/caddie.class.php");

if ($pmb_label_construct_script) {
	require_once ("../$pmb_label_construct_script");
} else {
	require_once ("../custom_label_no_script.inc.php");
}

$myCart = new caddie($idcaddie);
if ($elt_flag && $elt_no_flag)
	$liste = $myCart->get_cart("ALL");
if ($elt_flag && !$elt_no_flag)
	$liste = $myCart->get_cart("FLAG");
if ($elt_no_flag && !$elt_flag)
	$liste = $myCart->get_cart("NOFLAG");

//Tri des exemplaires par cote alphabétique
$ordered_list = array();
if (!empty($liste) && is_countable($liste)) {
    for ($i=0;$i<count($liste) ;$i++) {
    	$res = pmb_mysql_query('select expl_cote from exemplaires where expl_id = '.$liste[$i]);
    	$row = pmb_mysql_fetch_assoc($res);
    
    	$ordered_list[] = array('id'=>$liste[$i], 'cote'=>$row['expl_cote']);
    }
}
$cotes = array();
foreach ($ordered_list as $key => $val) {
	$cotes[$key]  = $val['cote'];
}
array_multisort($cotes, SORT_ASC, $ordered_list);

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

//Impression etiquettes
for ($i=0;$i<count($ordered_list) ;$i++) {

	$pdf->AddStick();
	$content_src = $ordered_list[$i]['id'];
	foreach($content_type as $step=>$value) {
		$font_family = '';
		if( !empty($content_value[$step]['font']) ) {
			$font_family = $content_value[$step]['font'];
			if(strtolower($font_family) == 'arial') $font_family='Helvetica';
		}
		if(!empty($font_family)) {
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
