<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: section.inc.php,v 1.25 2019-06-07 12:42:43 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_section_ui.class.php");

// gestion des codes section exemplaires
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_libelle.value.length == 0)
	{
		alert("<?php echo $msg[98]; ?>");
		return false;
	}
	return true;
}

</script>
<?php
function show_section($dbh) {
	print list_configuration_docs_section_ui::get_instance()->get_display_list();
}

function section_form($libelle="", $sdoc_codage_import="", $sdoc_owner=0, $id=0, $section_pic="", $section_visible_opac=1, $num_locations=array()) {
	global $msg;
	global $admin_section_form;
	global $charset;
	
	$admin_section_form = str_replace('!!id!!', $id, $admin_section_form);

	if(!$id) $admin_section_form = str_replace('!!form_title!!', $msg[110], $admin_section_form);
		else $admin_section_form = str_replace('!!form_title!!', $msg[111], $admin_section_form);

	$admin_section_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_section_form);
	$admin_section_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_section_form);

	$admin_section_form = str_replace('!!section_pic!!', htmlentities($section_pic,ENT_QUOTES, $charset), $admin_section_form);

	if($section_visible_opac) $checkbox="checked"; else $checkbox="";
	$admin_section_form = str_replace('!!checkbox!!', $checkbox, $admin_section_form);
	
	$admin_section_form = str_replace('!!sdoc_codage_import!!', $sdoc_codage_import, $admin_section_form);
	$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_sdoc_owner", "", $sdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
	$admin_section_form = str_replace('!!lender!!', $combo_lender, $admin_section_form);
	
	$localisations="";
	$requete = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
	$res = pmb_mysql_query($requete) ; 
	
	if (!$num_locations) $num_locations=array();
	while ($obj=pmb_mysql_fetch_object($res)) {
		$as=array_search($obj->idlocation,$num_locations);
		if (($as!==null)&&($as!==false)) $localisations.="<input type='checkbox' name='num_locations[]' value='".$obj->idlocation."' checked class='checkbox' id='numloc".$obj->idlocation."' /><label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label><br />";
			else $localisations.="<input type='checkbox' name='num_locations[]' value='".$obj->idlocation."' class='checkbox' id='numloc".$obj->idlocation."' /><label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label><br />";
	}
	$admin_section_form = str_replace('!!num_locations!!', $localisations, $admin_section_form);
	
	print confirmation_delete("./admin.php?categ=docs&sub=section&action=del&id=");
	print $admin_section_form;
}

switch($action) {
	case 'update':
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM docs_section WHERE (section_libelle='$form_libelle' AND idsection!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg["docs_label_already_used"]);
		}else{
			// O.K.  if item already exists UPDATE else INSERT
			if ($id) {
				$requete = "UPDATE docs_section SET section_libelle='$form_libelle', sdoc_codage_import='$form_sdoc_codage_import', sdoc_owner='$form_sdoc_owner', section_pic='$form_section_pic', section_visible_opac='$form_section_visible_opac' WHERE idsection=$id ";
				$res = pmb_mysql_query($requete, $dbh);
			}else{
				$requete = "INSERT INTO docs_section (idsection,section_libelle,sdoc_codage_import,sdoc_owner,section_pic, section_visible_opac) VALUES ('', '$form_libelle','$form_sdoc_codage_import','$form_sdoc_owner', '$form_section_pic', '$form_section_visible_opac') ";
				$res = pmb_mysql_query($requete, $dbh);
				$id = pmb_mysql_insert_id();
			}
			if (!isset($num_locations)) $num_locations=array();
			$requete="SELECT num_location FROM docsloc_section WHERE num_section='".$id."'";
			$res=pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)){
				while ($ligne=pmb_mysql_fetch_object($res)) {
					if(array_search($ligne->num_location,$num_locations) !== false){
						//Si l'ancienne loc est toujours dans les nouvelles je n'y touche pas
						unset($num_locations[array_search($ligne->num_location,$num_locations)]);
					}else{
						//Si l'ancienne n'est pas dans les nouvelles loc je la supprime
						$requete = "delete from docsloc_section where num_section='$id' and num_location='".$ligne->num_location."' ";
						pmb_mysql_query($requete, $dbh);
					}
				}	
			}
			//Si il y a des nouvelles loc pour la section je les créer
			foreach ( $num_locations as $value ) {
      			$requete = "INSERT INTO docsloc_section (num_section,num_location) VALUES ('$id', '".$value."') ";
				pmb_mysql_query($requete, $dbh);
			}
			/*avant
			$requete = "delete from docsloc_section where num_section='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			for ($i=0 ; $i < count($num_locations); $i++) {
				$requete = "INSERT INTO docsloc_section (num_section,num_location) VALUES ('$id', '".$num_locations[$i]."') ";
				$res = pmb_mysql_query($requete, $dbh);
				}*/
		}
		show_section($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) section_form();
			else show_section($dbh);
		break;
	case 'modif':
		if($id){
			$requete = "SELECT section_libelle, sdoc_codage_import, sdoc_owner, section_pic, section_visible_opac FROM docs_section WHERE idsection=$id LIMIT 1 ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				$rqtloc = "select num_location from docsloc_section where num_section='$id' " ;
				$resloc = pmb_mysql_query($rqtloc, $dbh);
				$num_locations = array();
				while ($loc=pmb_mysql_fetch_object($resloc)) $num_locations[]=$loc->num_location ;
				section_form($row->section_libelle, $row->sdoc_codage_import, $row->sdoc_owner, $id, $row->section_pic, $row->section_visible_opac, $num_locations );
			} else {
				show_section($dbh);
			}
		} else {
			show_section($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total=0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_section ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$compt=pmb_mysql_num_rows(pmb_mysql_query("select userid from users where deflt_docs_section='$id'"));
				if ($compt==0) {
					$total = pmb_mysql_result(pmb_mysql_query("select count(1) from abts_abts where section_id ='".$id."' ", $dbh), 0, 0);
					if ($total==0) {		
						$requete = "DELETE FROM docs_section WHERE idsection=$id ";
						$res = pmb_mysql_query($requete, $dbh);
						$requete = "delete from docsloc_section where num_section='$id' ";
						$res = pmb_mysql_query($requete, $dbh);
						show_section($dbh);
					}else {
						$msg_suppr_err = $admin_liste_jscript;
						$msg_suppr_err .= $msg["section_used_abts"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_abts' item='".$id."' total='".$total."' alt=\"".$msg["admin_abts_list"]."\" title=\"".$msg["admin_abts_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
						error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=section&action=');
					}	
				} else {
					$msg_suppr_err = $admin_liste_jscript;
					$msg_suppr_err .= $msg["section_used_users"]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_users' item='".$id."' total='".$compt."' alt=\"".$msg["admin_users_list"]."\" title=\"".$msg["admin_users_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
					error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=section&action=');
				}
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg[1702]." <a href='#' onclick=\"showListItems(this);return(false);\" what='section_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=section&action=');
			}
		} else show_section($dbh);
		break;
	default:
		show_section($dbh);
		break;
	}
