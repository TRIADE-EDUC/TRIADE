<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: supagro.inc.php,v 1.11.4.1 2025/03/25 07:32:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function search_other_function_filters() {
	global $supagro_loc_sel,$supagro_rev_res;

	$supagro_loc_sel = intval($supagro_loc_sel);
	$supagro_rev_res = intval($supagro_rev_res);
	
	$r ="&nbsp;<select name='supagro_loc_sel'>";
	$r.="<option value=''>tous les sites</option>";
	$requete="select location_libelle,idlocation from docs_location where location_visible_opac=1";
	$result = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)){
		while (($loc = pmb_mysql_fetch_object($result))) {
			$selected="";
			if ($supagro_loc_sel==$loc->idlocation) {$selected="selected=\"selected\"";}
			$r.= "<option value='$loc->idlocation' $selected>$loc->location_libelle</option>";
		}
	}
	$r.="</select>";	
	$r.="&nbsp;<div style='display:none;'><input type='checkbox' id='supagro_rev_res' name='supagro_rev_res' value='1' ";
	if($supagro_rev_res) $r.="checked='checked' ";
	$r.="/><label for='supagro_rev_res' >Cocher pour localiser une revue</label></div>";
	$r.= "
	<script>
		function test_tp() {
			if(sel_tp.value=='v') {
				aff_chk_rev.parentNode.style.display='block';
			} else {
				aff_chk_rev.checked='';
				aff_chk_rev.parentNode.style.display='none';
			}
		}
		var sel_tp=document.forms['search_input'].elements['typdoc'];
		var aff_chk_rev=document.forms['search_input'].elements['supagro_rev_res'];
		test_tp();
		sel_tp.onchange=test_tp;
	</script>
	";
	return $r;
}

function search_other_function_clause() {
	global $supagro_loc_sel, $supagro_rev_res;

	$supagro_loc_sel = intval($supagro_loc_sel);
	$supagro_rev_res = intval($supagro_rev_res);
	$r="";
	//restriction type de notice si revue
	if ($supagro_rev_res) {
		//restriction localisation etats de collection pour les revues
		$r = "select distinct notice_id from notices where niveau_biblio='s' and notices.niveau_hierar='1'";		
		if ($supagro_loc_sel) {
			$r.=" and (notices.notice_id in (SELECT distinct id_serial from collections_state where collections_state.location_id='".$supagro_loc_sel."')) ";
		} else {
			$r.=" and (notices.notice_id in (SELECT distinct id_serial from collections_state )) ";
		}
	} else {	
		//restriction localisation exemplaires
		if ($supagro_loc_sel) {
			$r = "select distinct notice_id from notices where notice_id in (";
			$r.= "select distinct(expl_notice) from exemplaires where expl_location='".$supagro_loc_sel."' ";
			$r.= "union "; 
			$r.= "select distinct(bulletin_notice) from exemplaires, bulletins where expl_location='".$supagro_loc_sel."' and expl_bulletin=bulletin_id )";
		}
	}	
	return $r;
}

function search_other_function_has_values() {
	global $supagro_loc_sel,$supagro_rev_res;
	
	$supagro_loc_sel = intval($supagro_loc_sel);
	$supagro_rev_res = intval($supagro_rev_res);
	if (($supagro_loc_sel)||($supagro_rev_res)) return true; else return false;
}

function search_other_function_get_values(){
	global $supagro_loc_sel,$supagro_rev_res;
	return $supagro_loc_sel."---".$supagro_rev_res;
}

function search_other_function_rec_history($n) {
	global $supagro_loc_sel,$supagro_rev_res;
	$_SESSION["supagro_loc_sel".$n]=$supagro_loc_sel;
	$_SESSION["supagro_rev_res".$n]=$supagro_rev_res;
}

function search_other_function_get_history($n) {
	global $supagro_loc_sel,$supagro_rev_res;
	$supagro_loc_sel=$_SESSION["supagro_loc_sel".$n];
	$supagro_rev_res=$_SESSION["supagro_rev_res".$n];
}

function search_other_function_human_query($n) {
	global $supagro_loc_sel,$supagro_rev_res;
	$r="";
	$supagro_loc_sel=intval($_SESSION["supagro_loc_sel".$n]);
	$supagro_rev_res=intval($_SESSION["supagro_rev_res".$n]);
	if ($supagro_loc_sel) {
		$r="bibliotheque : ";
		$requete="select location_libelle from docs_location where idlocation='".$supagro_loc_sel."' limit 1";
		$res=pmb_mysql_query($requete);
		$r.=pmb_mysql_result($res,0,0);
		$r.=" ";
	}
	if ($supagro_rev_res) {
		$r.= "localisation revue ";
	}
	return $r;
}

function search_other_function_post_values() {
	global $supagro_loc_sel,$supagro_rev_res, $charset;
	return "<input type=\"hidden\" name=\"supagro_loc_sel\" value=\"".htmlentities($supagro_loc_sel, ENT_QUOTES, $charset)."\"><input type=\"hidden\" name=\"supagro_rev_res\" value=\"".htmlentities($supagro_rev_res, ENT_QUOTES, $charset)."\">\n";
}
