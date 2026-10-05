<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: location.inc.php,v 1.44.8.1 2024/07/12 06:44:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg, $id, $form_actif;

require_once($class_path."/docs_location.class.php");
require_once("$class_path/sur_location.class.php");
require_once($class_path."/map/map_edition_controler.class.php");

function show_location() {
	global $msg,$pmb_location_reservation,$current_module;
	
	if($pmb_location_reservation) print "<h1>".$msg["admin_location_list_title"]."</h1>";

	print list_configuration_docs_location_ui::get_instance()->get_display_list();

	if($pmb_location_reservation) {
		$form_res_location= 
		"<br /><br /><h1>".$msg["admin_location_resa_title"]."</h1>
		<form class='form-$current_module' id='userform' name='userform' method='post' action='./admin.php?categ=docs&sub=location&action=resa_loc'>
		";	
		$form_res_location.=
		"<table>
			<tr>
				<th>&#8595; ".$msg["admin_location_resa_empr_loc"]." &#8594;</th>";
		$requete="select * from resa_loc";
		$res = pmb_mysql_query($requete);
		$resa_liste = array();
		if(pmb_mysql_num_rows($res)) {
			while(($row=pmb_mysql_fetch_object($res))) {
				$resa_liste[$row->resa_loc][$row->resa_emprloc]=1;				
			}
		}
		$ligne="";
		$memo_location = array();
		$requete = "SELECT idlocation,location_libelle, locdoc_owner, locdoc_codage_import, lender_libelle, location_visible_opac, css_style FROM docs_location left join lenders on locdoc_owner=idlender ORDER BY location_libelle";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)) {
			while($row = pmb_mysql_fetch_object($res)) {
				$memo_location[] = $row;
			}
		}
		$parity = 0;
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

$id = intval($id);
switch($action) {
	case 'update':
		global $form_libelle;

		// vérification validité des données fournies.
		if($form_actif) {
			$requete = " SELECT count(1) FROM docs_location WHERE (location_libelle='$form_libelle' AND idlocation!='$id' )  LIMIT 1 ";
			$res = pmb_mysql_query($requete);
			$nbr = pmb_mysql_result($res, 0, 0);
			if ($nbr > 0) {
				error_form_message($form_libelle.$msg["docs_label_already_used"]);
			} else {
				$docs_location = new docs_location($id);
				$docs_location->set_properties_from_form();
				$docs_location->save();
			}
		}	
		show_location();
		break;
	case 'add':
		$docs_location = new docs_location();
		print $docs_location->get_form();
		break;
	case 'resa_loc':
		global $matrice_loc;
		if($form_actif) {
			$requete = "truncate table resa_loc";
			pmb_mysql_query($requete);
			if(is_array($matrice_loc))foreach($matrice_loc as $loc_bibli=>$val) {
				foreach($val as $loc_empr=>$val1) {
					$requete = "INSERT INTO resa_loc SET resa_loc='$loc_bibli', resa_emprloc='$loc_empr'";
					pmb_mysql_query($requete);
				}
			}
		}	
		show_location();
		break;		
	case 'modif':
		$docs_location = new docs_location($id);
		if(pmb_error::get_instance('docs_location')->has_error()) {
			pmb_error::get_instance('docs_location')->display(1, 'admin.php?categ=docs&sub=location&action=');
		} else {
			print $docs_location->get_form();
		}
		break;
	case 'del':
		$deleted = docs_location::delete($id);
		if($deleted) {
			show_location();
		} else {
			pmb_error::get_instance('docs_location')->display(1, 'admin.php?categ=docs&sub=location&action=');
		}
		break;
	default:
		show_location();
		break;
	}
