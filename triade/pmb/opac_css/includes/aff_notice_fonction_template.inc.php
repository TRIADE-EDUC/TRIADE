<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aff_notice_fonction_template.inc.php,v 1.1 2015/05/11 13:37:58 abacarisse Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

/**
 * 
 * Fonction par défaut d'affichage d'un template (DJANGO ou en base)
 */
function aff_notice_fonction_template($id,$cart,$gen_header,$use_cache,$mode_aff_notice,$depliable,$nodocnum,$enrichment,$recherche_ajax_mode,$show_map,$recordmodes){

	global $liens_opac;
	global $opac_notice_affichage_class;
	global $opac_recherche_ajax_mode;
	global $aff_notice_table_pos;
	global $memo_notice;
	
	$retour_aff='';
	$current_mode=$recordmodes->get_current_mode();
	$layout=$recordmodes->get_layout($current_mode);
	
	//Début du flux
	if ($id==-1) {
		if($layout['TYPE']=='table'){
			$retour_aff.="<table class='aff_notice_template'>";
			$aff_notice_table_pos=0;
		}else{
			$retour_aff.="<div class='row'>";
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
			$retour_aff.="<div class='aff_notice_template row'></div>";
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
			
			$retour_aff.="<td>";
		}else{
			$retour_aff.="<div class='aff_notice_template_notice'>";
		}
		$header_only=0;
		if($recherche_ajax_mode && $opac_recherche_ajax_mode){
			//Si ajax, on ne charge pas tout
			$header_only=1;
		}
		
		$current = new $opac_notice_affichage_class($id,$liens_opac,$cart,0,$header_only,!$gen_header, $show_map);
		
		$tpl = new notice_tpl_gen($recordmodes->get_template_id($current_mode),$recordmodes->get_template_code($current_mode));
		$notice_tpl_header=$tpl->build_notice($id);
		if($notice_tpl_header){
			
			$aff_notice='';
			
			$aff_notice.=$notice_tpl_header;
			//coins pour Zotero
			$coins_span=$current->gen_coins_span();
			$aff_notice.=$coins_span;
			$memo_notice[$id]["header_without_doclink"]=$aff_notice;
			$memo_notice[$id]["header_doclink"]="";
			$memo_notice[$id]["header"]=$aff_notice;
			$memo_notice[$id]["niveau_biblio"]	= $current->notice->niveau_biblio;
			
			$retour_aff.=$aff_notice;
			$aff_notice_table_pos++;
		}
		
		if($layout['TYPE']=='table'){
			$retour_aff.="</td>";
		}else{
			$retour_aff.="</div>";
		}
	}
	
	return $retour_aff;
	
}