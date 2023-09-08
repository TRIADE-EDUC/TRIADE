<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.inc.php,v 1.34 2007/06/08 16:55:57 jlesaint 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");	
	
// gestion du tri
require_once($include_path . "/templates/sort.tpl.php");
require_once($class_path . "/sort.class.php");

$sort = new sort('notices', 'session');

//Si vidage historique des tris demandé ?
if ($raz_sort) {
	if ((isset($_POST['cases_suppr'])) && !empty($_POST['cases_suppr'])) {
		$cases_a_suppr = $_POST['cases_suppr'];
		$sort->supprimer($cases_a_suppr);
	} 
}

$page_en_cours = '';
if (isset($_GET['page_en_cours'])) {
    $page_en_cours = htmlentities($_GET['page_en_cours'],ENT_QUOTES,$charset);
}

if (isset($params)) {
	$params_tab = unserialize(rawurldecode(stripslashes($params)));
	if (count($params_tab)) {
		foreach ($params_tab as $param_name => $param_value) {
			$page_en_cours.= '&' . $param_name . '=' . $param_value;
		}
	}
}
if (isset($_GET['modif_sort'])) {
	$temp = array();
	for ($i = 0;$i <= 4; $i++) {
		if ($_POST['liste_critere'.$i] != "") {
			$temp[$i] = $_POST['croit_decroit'.$i] . "_".$_POST['num_text'.$i] . "_" . $_POST['liste_critere'.$i];	
		}	
	}
	if (count($temp)!=0) {
		$affichage = $sort->sauvegarder('','',$temp);
		print $affichage;
		if (substr($affichage,0,8)=="<script>") {
			$tmpStr = $sort->show_tris_form();
	    	$tmpStr = str_replace("!!page_en_cours!!", urlencode($page_en_cours), $tmpStr);
	    	$tmpStr = str_replace("!!page_en_cours1!!", $page_en_cours, $tmpStr);
	    	$tmpStr = str_replace("!!action_suppr_tris!!", "document.cases_a_cocher.submit();", $tmpStr);
	    	echo $tmpStr;

			$tmpStr = $sort->show_sel_form();
    		$tmpStr = str_replace("!!page_en_cours!!",urlencode($page_en_cours), $tmpStr);
			$tmpStr = str_replace("!!page_en_cours1!!",$page_en_cours, $tmpStr);
			echo $tmpStr;
		} else {
			$temp_tri = $_SESSION["nb_sortnotices"]-1;
			print "<script> document.location='./index.php?" . $page_en_cours . "&get_last_query=" . htmlentities($_SESSION["last_query"],ENT_QUOTES,$charset) . "&sort=" . $temp_tri."';</script>";	
		}	
	} else {
	    print "<script> document.location='./index.php?" . $page_en_cours . "&get_last_query=" . htmlentities($_SESSION["last_query"],ENT_QUOTES,$charset) . "';</script>";	
	}
} else {
	$tmpStr = $sort->show_tris_form();
	$tmpStr = str_replace("!!page_en_cours!!", urlencode($page_en_cours), $tmpStr);
	$tmpStr = str_replace("!!page_en_cours1!!", $page_en_cours, $tmpStr);
	$tmpStr = str_replace("!!action_suppr_tris!!", "document.cases_a_cocher.submit();", $tmpStr);
	echo $tmpStr;

	$tmpStr = $sort->show_sel_form();
	$tmpStr = str_replace("!!page_en_cours!!", urlencode($page_en_cours), $tmpStr);
	$tmpStr = str_replace("!!page_en_cours1!!", $page_en_cours, $tmpStr);
	echo $tmpStr;
}

