<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: infopages.inc.php,v 1.13 2017-11-21 12:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($sub2)) $sub2 = '';
if(!isset($form_title_infopage)) $form_title_infopage = '';
if(!isset($form_content_infopage)) $form_content_infopage = '';
if(!isset($form_valid_infopage)) $form_valid_infopage = '';
if(!isset($form_restrict_infopage)) $form_restrict_infopage = '';

require_once("$class_path/classementGen.class.php");

// gestion des pages d'information

function show_infopages($dbh) {
	global $msg, $charset, $opac_url_base,$PMBuserid;
	global $deflt_catalog_expanded_caddies;

	print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
	print "<div class='hmenu'>
					<span><a href='admin.php?categ=infopages&sub2=classementGen'>".$msg["classementGen_list_libelle"]."</a></span>
				</div><hr>";
	print "<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' border='0'></a>
			<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' border='0'></a></div>";

	$requete = "select id_infopage, title_infopage, content_infopage, valid_infopage, infopage_classement from infopages order by valid_infopage DESC, title_infopage ";
	$res = pmb_mysql_query($requete, $dbh);

	$nbr = pmb_mysql_num_rows($res);

	$parity=1;
	$arrayRows=array();
	for($i=0;$i<$nbr;$i++) {
		$row=pmb_mysql_fetch_object($res);
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		$baselink = "./admin.php?categ=infopages";
		$classementRow = $row->infopage_classement;
		if(!trim($classementRow)){
			$classementRow=classementGen::getDefaultLibelle();
		}
		$tr_javascript="class='$pair_impair' style='cursor: pointer' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
		$td_javascript="onmousedown=\"document.location='./admin.php?categ=infopages&sub=infopages&action=modif&id=$row->id_infopage';\" ";
		$rowPrint="<tr $tr_javascript>";
		$rowPrint.="<td $td_javascript class='align_right'><b>".$row->id_infopage."</b></td>";
		if ($row->valid_infopage) $visible="X" ; 
		else $visible="&nbsp;" ;
		$rowPrint.="<td $td_javascript class='erreur center'>$visible</td>" ;
		$rowPrint.="<td $td_javascript>".htmlentities($row->title_infopage, ENT_QUOTES, $charset)."</td>";
		$rowPrint.="<td><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$row->id_infopage."\" target=_blank>".htmlentities($opac_url_base."index.php?lvl=infopages&pagesid=".$row->id_infopage, ENT_QUOTES, $charset)."</a></td>" ;
		$classementGen = new classementGen('infopages', $row->id_infopage);
		$rowPrint.="<td>".$classementGen->show_selector($baselink,$PMBuserid)."</td>";
        $rowPrint.="</tr>";
        
        $arrayRows[$classementRow]["title"]=stripslashes($classementRow);
        if(!isset($arrayRows[$classementRow]["infopage_list"])) {
        	$arrayRows[$classementRow]["infopage_list"] = '';
        }
        $arrayRows[$classementRow]["infopage_list"].=$rowPrint;
	}
	//on trie
	ksort($arrayRows);
	//on remplace les clés à cause des accents
	$arrayRows=array_values($arrayRows);
	foreach($arrayRows as $key => $type) {
		print gen_plus($key,$type["title"],"<table><tr><th width='3%'>".$msg['infopages_id_infopage']."</th><th width='3%'>".$msg['infopage_valid_infopage']."</th><th>".$msg['infopage_title_infopage']."</th><th>".$msg['infopage_lien_direct']."</th><th width='3%'>&nbsp;</th></tr>".$type["infopage_list"]."</table>",$deflt_catalog_expanded_caddies);
	}
	
	print "	<input class='bouton' type='button' value=\" ".$msg['infopages_bt_ajout']." \" onClick=\"document.location='./admin.php?categ=infopages&sub=infopages&action=add'\" />";
}

function infopage_form($id=0, $title_infopage="", $content_infopage="", $valid_infopage=1, $restrict_infopage=0) {
	global $msg, $pmb_javascript_office_editor,$base_path;
	global $admin_infopages_form;
	global $charset,$PMBuserid;
	
	if ($pmb_javascript_office_editor){
		print $pmb_javascript_office_editor ;
		print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
	}


	$admin_infopages_form = str_replace('!!id!!', $id, $admin_infopages_form);

	if (!$id) $admin_infopages_form = str_replace('!!form_title!!', $msg['infopages_creer'], $admin_infopages_form);
	else $admin_infopages_form = str_replace('!!form_title!!', $msg['infopages_modifier'], $admin_infopages_form);

	$admin_infopages_form = str_replace('!!title_infopage!!', htmlentities($title_infopage,ENT_QUOTES, $charset), $admin_infopages_form);
	$admin_infopages_form = str_replace('!!libelle_suppr!!', htmlentities(addslashes($title_infopage),ENT_QUOTES, $charset), $admin_infopages_form);

	$admin_infopages_form = str_replace('!!content_infopage!!', htmlentities($content_infopage,ENT_QUOTES, $charset), $admin_infopages_form);

	if ($valid_infopage) 
		$checkbox="checked"; 
	else 
		$checkbox="";
	$admin_infopages_form = str_replace('!!checkbox!!', $checkbox, $admin_infopages_form);

	if ($restrict_infopage) 
		$restrict_checkbox="checked"; 
	else 
		$restrict_checkbox="";
	$admin_infopages_form = str_replace('!!restrict_checkbox!!', $restrict_checkbox, $admin_infopages_form);
	
	$classementGen = new classementGen('infopages', $id);
	$admin_infopages_form = str_replace("!!object_type!!",$classementGen->object_type,$admin_infopages_form);
	$admin_infopages_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$admin_infopages_form);

	if ($id) {
		$admin_infopages_form = str_replace("!!duplicate!!","<input class='bouton' type='button' value=' ".$msg["infopages_duplicate_bouton"]." ' onClick=\"document.location='./admin.php?categ=infopages&sub=infopages&action=duplicate&id=".$id."'\" />",$admin_infopages_form);
	} else {
		$admin_infopages_form = str_replace("!!duplicate!!","",$admin_infopages_form);
	}
	
	print confirmation_delete("./admin.php?categ=infopages&sub=infopages&action=del&id=");
	print "<script type=\"text/javascript\">
		function test_form(form) {
		if(form.form_title_infopage.value.length == 0) {
			alert(\"".$msg[98]."\");
			return false;
		}
		return true;
		}
		</script>";
	print $admin_infopages_form;
}

$admin_layout = str_replace('!!menu_sous_rub!!', $msg['infopages_admin_menu'], $admin_layout);
print $admin_layout;

if ($sub2) {
	switch($sub2){
		case 'classementGen' :
			$baseLink="./admin.php?categ=infopages&sub2=classementGen";
			$classementGen = new classementGen($categ,0);
			$classementGen->proceed($action);
			break;
	}
} else {
	switch($action) {
		case 'update':
			$set_values = "SET title_infopage='$form_title_infopage', content_infopage='$form_content_infopage', valid_infopage='$form_valid_infopage', restrict_infopage='$form_restrict_infopage', infopage_classement='".addslashes($classementGen_infopages)."' " ;
			if($id) {
				$requete = "UPDATE infopages $set_values WHERE id_infopage='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "INSERT INTO infopages $set_values ";
				$res = pmb_mysql_query($requete, $dbh);
			}
			show_infopages($dbh);
			break;
		case 'add':
			if (empty($form_title_infopage)) infopage_form(0, $form_title_infopage, $form_content_infopage, $form_valid_infopage,$form_restrict_infopage);
			else show_infopages($dbh);
			break;
		case 'modif':
			if($id){
				$requete = "select id_infopage, title_infopage, content_infopage, valid_infopage, restrict_infopage from infopages WHERE id_infopage='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				if(pmb_mysql_num_rows($res)) {
					$row=pmb_mysql_fetch_object($res);
					infopage_form($row->id_infopage, $row->title_infopage, $row->content_infopage, $row->valid_infopage, $row->restrict_infopage);
				} else {
					show_infopages($dbh);
				}
			} else {
				show_infopages($dbh);
			}
			break;
		case 'del':
			if($id) {
				$requete = "DELETE from infopages WHERE id_infopage='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				show_infopages($dbh);
			} else show_infopages($dbh);
			break;
		case 'duplicate':
			if($id){
				$requete = "select id_infopage, title_infopage, content_infopage, valid_infopage, restrict_infopage from infopages WHERE id_infopage='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				if(pmb_mysql_num_rows($res)) {
					$row=pmb_mysql_fetch_object($res);
					infopage_form(0, $row->title_infopage, $row->content_infopage, $row->valid_infopage, $row->restrict_infopage);
				} else {
					show_infopages($dbh);
				}
			} else {
				show_infopages($dbh);
			}
			break;
		default:
			show_infopages($dbh);
			break;
		}
}