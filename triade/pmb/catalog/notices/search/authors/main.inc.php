<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.16 2021/03/17 13:32:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $f_notice_id, $ex_query, $pmb_show_notice_id;
global $shcut;

// recherche notice (catalogage) : page de switch recherche auteurs/titres
require_once($class_path."/searcher.class.php");
require_once($class_path."/notice.class.php");

if(!isset($f_notice_id)) $f_notice_id = 0;
if(!isset($ex_query)) $ex_query = '';
if($pmb_show_notice_id && $f_notice_id){
	require_once("catalog/notices/search/authors/id_notice.inc.php");
} elseif ($ex_query){ 
	require_once("catalog/notices/search/authors/expl.inc.php");
} else {
	notice::init_globals_patterns_links();
	
	$sh=new searcher_title("./catalog.php?categ=search&mode=0",true);
	if (isset($shcut) && ($shcut=='B')) print "<script type='text/javascript'>document.forms['NOTICE_author_query'].elements['ex_query'].focus();</script>" ;
	
}