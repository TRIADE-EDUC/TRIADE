<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: launch_search.inc.php,v 1.13.10.2 2025/03/04 11:05:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $reinit_facettes_external, $param_delete_facette, $check_facette, $source, $flag_found, $msg, $op_0_s_2, $field_0_s_2, $notice_id, $notice_id_info;

if (!isset($reinit_facettes_external)) $reinit_facettes_external = 0;
if($reinit_facettes_external) {
	facettes_external::destroy_global_env();
}
if((isset($param_delete_facette)) || (isset($check_facette) && is_array($check_facette))) {
	facettes_external::checked_facette_search();
}

if ($_SESSION["ext_type"]=="simple") {
	//Deblocage des sources si demande
	if (is_array($source)) {
		for ($i=0; $i<count($source); $i++) {
			$debloque="debloque_source_".$source[$i];
			if (!empty(${$debloque})) pmb_mysql_query("delete from source_sync where source_id=".$source[$i]);
		}
	}
	
	//Recherche du champ source, s'il n'est pas present, on decale tout et on l'ajoute
	$flag_found=false;
	for ($i=0; $i<count($search); $i++) {
		if ($search[$i]=="s_2") { $flag_found=true; break; }
	}
	if (!$flag_found) {
		//Pas trouve, on verifie qu'il y a au moins une source
		if (!is_countable($source) || !count($source)) {
			print "<script type='text/javascript' >alert(\"".$msg["connecteurs_no_source"]."\"); history.go(-1);</script>";
			exit();
		}
		//Pas trouve, on décale tout !!
		for ($i=count($search)-1; $i>=0; $i--) {
			$search[$i+1]=$search[$i];
			decale("field_".$i."_".$search[$i],"field_".($i+1)."_".$search[$i]);
			decale("op_".$i."_".$search[$i],"op_".($i+1)."_".$search[$i]);
			decale("inter_".$i."_".$search[$i],"inter_".($i+1)."_".$search[$i]);
			decale("fieldvar_".$i."_".$search[$i],"fieldvar_".($i+1)."_".$search[$i]);
		}
		
		$search[0]="s_2";
		$op_0_s_2="EQ";
		$field_0_s_2=$source;
		$inter="inter_1_".$search[1];
		global ${$inter};
		${$inter}="and";
		$_SESSION["checked_sources"] = $source;
	}
} elseif ($_SESSION["ext_type"]=="multi") {
	if (is_array($field_0_s_2) && count($field_0_s_2)){
		$_SESSION["checked_sources"] = $field_0_s_2;
	}
}

if (isset($notice_id)) {
	$notice_id_info = "&notice_id=".$notice_id;
} else {
	$notice_id_info = "";
}

if ($sc->rec_history) {
    if (!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
    if ((string)$page=="" || $page==0) {
        $_SESSION["CURRENT"]=count($_SESSION["session_history"]);
        $_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["serialized_search"]=$sc->serialize_search();
        $_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
        $_POST["page"]=1;
        $page=1;
    }
}
//Effectue la recherche et l'affiche
$sc->show_results_unimarc("./catalog.php?categ=search&mode=7&sub=launch".$notice_id_info,"./catalog.php?categ=search&mode=7".$notice_id_info,true);

if ($sc->rec_history) {
    if (!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
    if ($_SESSION["CURRENT"]!==false) {
        $_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["serialized_search"]=$sc->serialize_search();
        $_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["GET"]=$_GET;
    }
}
?>
