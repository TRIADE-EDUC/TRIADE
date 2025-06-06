<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2019-06-07 08:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $no_rec_history, $class_path, $link, $link_expl, $link_explnum, $link_serial, $link_analysis, $link_bulletin, $link_explnum_serial;
global $link_explnum_analysis, $link_explnum_bulletin, $browser_url, $rec_history, $page, $msg, $tu;

if(!isset($no_rec_history)) $no_rec_history = '';

// page de switch recherche titres uniformes

require_once($class_path."/searcher.class.php");

$link = './catalog.php?categ=isbd&id=!!id!!';
$link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!'; 
$link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
	
$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
$link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
$link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";

$browser_url = "./catalog/notices/search/titres_uniformes/titre_uniforme_browser.php";

$rec_history=true;

if(!isset($page))  $page = '';

if (($no_rec_history)&&((string)$page=="")) {
	$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["NOLINK"]=true;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$msg["histo_free_browse"];
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["titre_uniforme_search"];
	$_POST["page"]=0;
	$page=0;
}

$tu=new searcher_titre_uniforme("./catalog.php?categ=search&mode=9",$rec_history);

