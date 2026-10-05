<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aff_notice_fonction_django.inc.php,v 1.2 2023/05/12 14:31:20 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function aff_notice_fonction_django($id,$cart,$gen_header,$use_cache,$mode_aff_notice,$depliable,$nodocnum,$enrichment,$recherche_ajax_mode,$show_map,$recordmodes){
	global $aff_notice_table_pos;
	global $include_path;
	global $record_css_already_included;
	
	$retour_aff = "";
	$current_mode = $recordmodes->get_current_mode();
	$layout = $recordmodes->get_layout($current_mode);
	$template_mode = $recordmodes->get_template_directory($current_mode);
		
	if (!$template_mode) $template_mode = "common";
	if (!$record_css_already_included) {
		if (file_exists($include_path."/templates/record/".$template_mode."/styles/style.css")) {
			$retour_aff .= "<link type='text/css' href='./includes/templates/record/".$template_mode."/styles/style.css' rel='stylesheet'></link>";
		}
		$record_css_already_included = true;
	}
	
	//Début du flux
	if ($id==-1) {
		if($layout['TYPE']=='table'){
			$retour_aff.="<table class='aff_notice_django'>";
			$aff_notice_table_pos=0;
		}else{
			$retour_aff.="<div class='row' id='aff_notice_django_container'>";
		}
	}
	
	if ($id==-2) {
		if($layout['TYPE']=='table'){
			for ($i=$aff_notice_table_pos; $i<$layout['COLUMS']; $i++) {
				$retour_aff.="<td>&nbsp;</td>";
			}
			if ($aff_notice_table_pos<$layout['COLUMS']){
				$retour_aff.="</tr>";
			}
				
			$retour_aff.="</table>";
		}else{
			$retour_aff.="<div class='aff_notice_django row'></div>";
			$retour_aff.="</div>";
		}
	}
	
	if ($id>=0) {
		if($layout['TYPE']=='table'){
			if ($aff_notice_table_pos>=$layout['COLUMS']) {
				$aff_notice_table_pos=0;
				$retour_aff.="</tr>";
			}
			if (!$aff_notice_table_pos){
				$retour_aff.="<tr>";
			}
			$aff_notice_table_pos++;
				
			$retour_aff.="<td>";
		}else{
			$retour_aff.="<div class='aff_notice_django_notice'>";
		}
		switch ($lvl) {
			case 'notice_display' :
			case 'bulletin_display' :
			case 'resa' :
				$retour_aff .= record_display::get_display_extended($id, $template_mode);
				break;
			case 'more_result' :
			default :
				if($search_type_asked=='perio_a2z'){
					$retour_aff .= record_display::get_display_extended($id, $template_mode);
				} else {
					$retour_aff .= record_display::get_display_in_result($id, $template_mode);
				}
				break;
		}
		if($layout['TYPE']=='table'){
			$retour_aff.="</td>";
		}else{
			$retour_aff.="</div>";
		}
	}
	return $retour_aff;
}