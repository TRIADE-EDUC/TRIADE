<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.20.10.1 2025/01/24 16:34:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_thes, $no_rec_history, $class_path, $page, $id_thes;
global $browser_url, $rec_history, $msg, $sh;

if(!isset($id_thes)) $id_thes = 0;
if(!isset($no_rec_history)) $no_rec_history = '';

// page de switch recherche sujets
require_once($class_path."/searcher.class.php");
require_once("$class_path/thesaurus.class.php");
require_once($class_path."/notice.class.php");

if (!isset($page)) $page = 0;
//recuperation du thesaurus session 
if(!$id_thes) {
$id_thes = thesaurus::getSessionThesaurusId();
} else {
	thesaurus::setSessionThesaurusId($id_thes);
}

notice::init_globals_patterns_links();

$browser_url = "./catalog/notices/search/subjects/categ_browser.php?id_thes=".$id_thes;


$rec_history=true;
if (($no_rec_history)&&((string)$page=="" || $page==0)) {
	$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["NOLINK"]=true;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$msg["histo_free_browse"];
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["355"];
	$_POST["page"]=1;
	$page=1;
}

$sh=new searcher_subject("./catalog.php?categ=search&mode=1",$rec_history);
