<?php
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $no_rec_history, $class_path;
global $rec_history, $page, $msg, $sh;

if(!isset($no_rec_history)) $no_rec_history = '';

// page de switch recherche titre de série

require_once($class_path."/searcher.class.php");
require_once($class_path."/notice.class.php");

notice::init_globals_patterns_links();

//$browser_url = "./catalog/notices/search/publishers/publisher_browser.php";

$rec_history=true;

if (($no_rec_history)&&((string)$page=="" || $page==0)) {
	$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["NOLINK"]=true;
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$msg["histo_free_browse"];
	$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["356"];
	$_POST["page"]=1;
	$page=1;
}

$sh=new searcher_serie("./catalog.php?categ=search&mode=10",$rec_history);
