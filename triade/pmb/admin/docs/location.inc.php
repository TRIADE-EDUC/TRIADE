<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: location.inc.php,v 1.36 2019-06-07 12:30:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des codes localisation exemplaires
?>
<script type="text/javascript">
function test_form(form) {
	if(form.form_libelle.value.length == 0) {
		alert("<?php echo $msg[98]; ?>");
		return false;
	}
	return true;
}

</script>
<?php

require_once($class_path."/list/configuration/docs/list_configuration_docs_location_ui.class.php");
require_once("$class_path/sur_location.class.php");
require_once($class_path."/map/map_edition_controler.class.php");

function show_location($dbh) {
	global $msg,$pmb_location_reservation,$current_module;
	
	if($pmb_location_reservation) print "<h1>".$msg["admin_location_list_title"]."</h1>";

	print list_configuration_docs_location_ui::get_instance()->get_display_list();

	if($pmb_location_reservation) {
		$form_res_location= 
		"<h1>".$msg["admin_location_resa_title"]."</h1>
		<form class='form-$current_module' id='userform' name='userform' method='post' action='./admin.php?categ=docs&sub=location&action=resa_loc'>
		";	
		$form_res_location.=
		"<table>
			<tr>
				<th>".$msg["admin_location_resa_empr_loc"]."</th>";
		$requete="select * from resa_loc";
		$res = pmb_mysql_query($requete, $dbh);	
		if(pmb_mysql_num_rows($res)) {
			while(($row=pmb_mysql_fetch_object($res))) {
				$resa_liste[$row->resa_loc][$row->resa_emprloc]=1;				
			}
		}
		$ligne="";		
		foreach($memo_location as $row) {
			$form_res_location.="<th>".$row->location_libelle."</th>";		
			if ($parity++ % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$ligne.="</tr><tr class='$pair_impair'><td>".$row->location_libelle."</td>";		
			foreach($memo_location as $row1) {
				if(isset($resa_liste[$row->idlocation][$row1->idlocation])) $check=" checked='checked' ";
				else $check="";
				$ligne.="<td><input value='1' name='matrice_loc[".$row->idlocation."][".$row1->idlocation."]' type='checkbox' $check ></td>";		
			}	
		}		
		$form_res_location.=$ligne."
			</tr>		
			</table>
			<input class='bouton' type='submit' value=' ".$msg["admin_location_resa_memo"]." ' />
			<input type='hidden' name='form_actif' value='1'>
			</form>";		
		print $form_res_location;
	}	
}

function location_form($libelle="", $locdoc_codage_import="", $locdoc_owner=0, $id=0, $location_pic="", $location_visible_opac=1, $name = "", $adr1 = "", $adr2 = "", $cp = "", $town = "", $state = "", $country = "", $phone = "", $email = "", $website = "", $logo = "", $commentaire="", $num_infopage=0, $css_style="",$surloc_used=0 ) {
	global $msg;
	global $admin_location_form;
	global $charset, $sur_loc_selector;
	global $pmb_map_activate;
	
	$admin_location_form = str_replace('!!id!!', $id, $admin_location_form);

	if(!$id) $admin_location_form = str_replace('!!form_title!!', $msg[106], $admin_location_form);
	else $admin_location_form = str_replace('!!form_title!!', $msg[107], $admin_location_form);

	$admin_location_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_location_form);
	$admin_location_form = str_replace('!!libelle_suppr!!', htmlentities(addslashes($libelle),ENT_QUOTES, $charset), $admin_location_form);

	$admin_location_form = str_replace('!!location_pic!!', htmlentities($location_pic,ENT_QUOTES, $charset), $admin_location_form);

	if($location_visible_opac) $checkbox="checked"; else $checkbox="";
	$admin_location_form = str_replace('!!checkbox!!', $checkbox, $admin_location_form);

	$admin_location_form = str_replace('!!locdoc_codage_import!!', $locdoc_codage_import, $admin_location_form);
	$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_locdoc_owner", "", $locdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
	$admin_location_form = str_replace('!!lender!!', $combo_lender, $admin_location_form);	
	
	$admin_location_form = str_replace('!!sur_loc_selector!!', $sur_loc_selector, $admin_location_form);
	if($surloc_used) $checkbox="checked"; else $checkbox="";
	$admin_location_form = str_replace('!!checkbox_use_surloc!!', $checkbox, $admin_location_form);
	
	// map
	if($pmb_map_activate){
		$map_edition=new map_edition_controler(TYPE_LOCATION,$id);
		$map_form=$map_edition->get_form();
		$admin_location_form = str_replace('!!location_map!!', $map_form, $admin_location_form);
		
	} else {
		$admin_location_form = str_replace('!!location_map!!', "", $admin_location_form);
	}
	
	$admin_location_form = str_replace('!!loc_name!!', 	htmlentities($name,ENT_QUOTES, $charset)     , $admin_location_form);
	$admin_location_form = str_replace('!!loc_adr1!!', 	htmlentities($adr1,ENT_QUOTES, $charset)     , $admin_location_form);
	$admin_location_form = str_replace('!!loc_adr2!!', 	htmlentities($adr2,ENT_QUOTES, $charset)     , $admin_location_form);
	$admin_location_form = str_replace('!!loc_cp!!', 	$cp       , $admin_location_form);
	$admin_location_form = str_replace('!!loc_town!!', 	htmlentities($town,ENT_QUOTES, $charset)     , $admin_location_form);
	$admin_location_form = str_replace('!!loc_state!!', 	htmlentities($state,ENT_QUOTES, $charset)    , $admin_location_form);
	$admin_location_form = str_replace('!!loc_country!!', 	htmlentities($country,ENT_QUOTES, $charset)  , $admin_location_form);
	$admin_location_form = str_replace('!!loc_phone!!', 	$phone    , $admin_location_form);
	$admin_location_form = str_replace('!!loc_email!!', 	$email    , $admin_location_form);
	$admin_location_form = str_replace('!!loc_website!!', 	$website  , $admin_location_form);
	$admin_location_form = str_replace('!!loc_logo!!', 	$logo     , $admin_location_form);
	$admin_location_form = str_replace('!!loc_commentaire!!', htmlentities($commentaire,ENT_QUOTES, $charset), $admin_location_form);

	$requete = "SELECT id_infopage, title_infopage FROM infopages where valid_infopage=1 ORDER BY title_infopage ";
	$infopages = gen_liste ($requete, "id_infopage", "title_infopage", "form_num_infopage", "", $num_infopage, 0, $msg["location_no_infopage"], 0,$msg["location_no_infopage"], 0) ;
	$admin_location_form = str_replace('!!loc_infopage!!', $infopages, $admin_location_form);
	
	$admin_location_form = str_replace('!!css_style!!', $css_style, $admin_location_form);
	
	print confirmation_delete("./admin.php?categ=docs&sub=location&action=del&id=");
	print $admin_location_form;
}

if($pmb_sur_location_activate){	
	$sur_loc= sur_location::get_info_surloc_from_location($id);
	$sur_loc_selector=($sur_loc->get_list("form_sur_localisation",$sur_loc->id,1));
}

switch($action) {
	case 'update':
		// vérification validité des données fournies.
		if($form_actif) {
			$requete = " SELECT count(1) FROM docs_location WHERE (location_libelle='$form_libelle' AND idlocation!='$id' )  LIMIT 1 ";
			$res = pmb_mysql_query($requete, $dbh);
			$nbr = pmb_mysql_result($res, 0, 0);
			if ($nbr > 0) {
				error_form_message($form_libelle.$msg["docs_label_already_used"]);
			} else {
				if(empty($form_sur_localisation)) {
					$form_sur_localisation = '';
				}
				if(empty($form_location_use_surloc)) {
				    $form_location_use_surloc=0;
				}
				// O.K.,  now if item already exists UPDATE else INSERT
				$set_values = "SET location_libelle='$form_libelle', locdoc_codage_import='$form_locdoc_codage_import', locdoc_owner='$form_locdoc_owner', location_pic='$form_location_pic', location_visible_opac='$form_location_visible_opac', name= '$form_locdoc_name', adr1= '$form_locdoc_adr1', adr2= '$form_locdoc_adr2', cp= '$form_locdoc_cp', town= '$form_locdoc_town', state= '$form_locdoc_state', country= '$form_locdoc_country', phone= '$form_locdoc_phone', email= '$form_locdoc_email', website= '$form_locdoc_website', logo= '$form_locdoc_logo', commentaire='$form_locdoc_commentaire', num_infopage='$form_num_infopage', css_style='$form_css_style', surloc_num='$form_sur_localisation', surloc_used='$form_location_use_surloc' " ;
				if($id) {
					$requete = "UPDATE docs_location $set_values WHERE idlocation='$id' ";
					$res = pmb_mysql_query($requete, $dbh);
					
				} else {
					$requete = "INSERT INTO docs_location $set_values ";
					$res = pmb_mysql_query($requete, $dbh);
					$id = pmb_mysql_insert_id($dbh);
				}
				// map
				if($pmb_map_activate){
					$map_edition=new map_edition_controler(TYPE_LOCATION,$id);
					$map_form=$map_edition->save_form();					
				}
			}
		}	
		show_location($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) location_form();
			else show_location($dbh);
		break;
	case 'resa_loc':
		if($form_actif) {
			$requete = "truncate table resa_loc";
			pmb_mysql_query($requete, $dbh);
			if(is_array($matrice_loc))foreach($matrice_loc as $loc_bibli=>$val) {
				foreach($val as $loc_empr=>$val1) {
					$requete = "INSERT INTO resa_loc SET resa_loc='$loc_bibli', resa_emprloc='$loc_empr'";
					pmb_mysql_query($requete, $dbh);
				}
			}
		}	
		show_location($dbh);
		break;		
	case 'modif':
		if($id){
			$requete = "SELECT location_libelle, locdoc_codage_import, locdoc_owner, location_pic, location_visible_opac, location_visible_opac, name, adr1, adr2, cp, town, state, country, phone, email, website, logo, commentaire, num_infopage, css_style, surloc_used FROM docs_location WHERE idlocation='$id' ";
			$res = pmb_mysql_query($requete, $dbh) or die(pmb_mysql_error()."<br />$requete");
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				location_form($row->location_libelle, $row->locdoc_codage_import, $row->locdoc_owner, $id, $row->location_pic, $row->location_visible_opac, $row->name, $row->adr1, $row->adr2, $row->cp, $row->town, $row->state, $row->country, $row->phone, $row->email, $row->website, $row->logo, $row->commentaire, $row->num_infopage, $row->css_style,$row->surloc_used);
			} else {
				show_location($dbh);
			}
		} else {
			show_location($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total1 = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_location='".$id."' ", $dbh), 0, 0);
			$total2 = pmb_mysql_result(pmb_mysql_query("select count(1) from users where deflt2docs_location='".$id."' or deflt_docs_location='".$id."'", $dbh), 0, 0);
			$total3 = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_location='".$id."' ", $dbh), 0, 0);
			$total4 = pmb_mysql_result(pmb_mysql_query("select count(1) from abts_abts where location_id ='".$id."' ", $dbh), 0, 0);
			$total5 = pmb_mysql_result(pmb_mysql_query("select count(1) from collections_state where location_id ='".$id."' ", $dbh), 0, 0);
			if (($total1+$total2+$total3+$total4+$total5)==0) {
				$requete = "DELETE FROM docs_location WHERE idlocation=$id ";
				$res = pmb_mysql_query($requete, $dbh);
				show_location($dbh);
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg["location_used"] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg["location_used_docs"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_docs' item='".$id."' total='".$total1."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg["location_used_users"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_users' item='".$id."' total='".$total2."' alt=\"".$msg["admin_users_list"]."\" title=\"".$msg["admin_users_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total3) $msg_suppr_err .= "<br />- ".$msg["location_used_empr"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_empr' item='".$id."' total='".$total3."' alt=\"".$msg["admin_empr_list"]."\" title=\"".$msg["admin_empr_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total4) $msg_suppr_err .= "<br />- ".$msg["location_used_abts"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_abts' item='".$id."' total='".$total4."' alt=\"".$msg["admin_abts_list"]."\" title=\"".$msg["admin_abts_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				if ($total5) $msg_suppr_err .= "<br />- ".$msg["location_used_collections_state"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='location_collections_state' item='".$id."' total='".$total5."' alt=\"".$msg["admin_collections_state_list"]."\" title=\"".$msg["admin_collections_state_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=location&action=');
			}
		} else show_location($dbh);
		break;
	default:
		show_location($dbh);
		break;
	}
