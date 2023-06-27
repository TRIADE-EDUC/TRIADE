<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.9 2017-06-02 10:05:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($f_notice_id)) $f_notice_id = 0; else $f_notice_id += 0;
if(!isset($id_empr)) $id_empr = 0; else $id_empr += 0;
if(!isset($groupID)) $groupID = 0; else $groupID += 0;
if(!isset($ex_query)) $ex_query = '';

// recherche notice (resa) : page de switch recherche auteurs/titres
require_once($class_path.'/searcher.class.php');
if($pmb_show_notice_id && $f_notice_id){
	require_once('circ/resa/authors/id_notice.inc.php');
} elseif ($ex_query){
	require_once('circ/resa/authors/expl.inc.php');
} else {
	$link = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_notice=!!id!!";
	$link_serial = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=view_serial&serial_id=!!id!!";
	$link_analysis = '';
	$link_bulletin = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!";
	$link_notice_bulletin = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!";

	$sh=new searcher_title("./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID");
}

