<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2015/08/14 12:33:05 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// recherche notice (resa_planning) : page de switch recherche auteurs/titres
require_once($class_path.'/searcher.class.php');
if($pmb_show_notice_id && $f_notice_id){
	require_once('circ/resa_planning/authors/id_notice.inc.php');
} elseif ($ex_query) {
	require_once('circ/resa_planning/authors/expl.inc.php');
} else {
	$link = "./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id_empr&groupID=$groupID&id_notice=!!id!!";
	$link_serial = "./circ.php?categ=resa_planning&resa_action=search_resa&id_empr=$id_empr&groupID=$groupID&mode=view_serial&serial_id=!!id!!";
	$link_analysis = '';
	$link_bulletin = "./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!";
	$link_notice_bulletin = "./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!";

	$sh=new searcher_title("./circ.php?categ=resa_planning&resa_action=search_resa&id_empr=$id_empr&groupID=$groupID");
}
