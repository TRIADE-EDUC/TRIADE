<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: id_notice.inc.php,v 1.4 2022/05/11 07:18:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $msg, $begin_result_liste, $end_result_liste;
global $pmb_show_notice_id, $f_notice_id, $id_empr, $groupID;

//droits d'acces lecture notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
}

$param_notice_id = explode(",",$pmb_show_notice_id);
$prefix_id = $param_notice_id[1];
if($prefix_id){
	$f_notice_id = str_replace($prefix_id,"",$f_notice_id);
}

$f_notice_id = intval($f_notice_id);
$rqt = "select * from notices where notice_id='".$f_notice_id."'";
$res = pmb_mysql_query($rqt);

if(pmb_mysql_num_rows($res)){
	$ident = pmb_mysql_fetch_object($res);

	//C'est une notice d'article, on renvoie vers le bulletin
	if($ident->niveau_biblio == 'a' && $ident->niveau_hierar == '2'){
		$rqt_bull = "select analysis_bulletin from analysis where analysis_notice='".$ident->notice_id."'";
		$res_bull = pmb_mysql_query($rqt_bull);
		if(pmb_mysql_num_rows($res_bull)){
			$ident_bull = pmb_mysql_result($res_bull,0,0);
			print "<script type=\"text/javascript\">";
			print "document.location = \"./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=".$ident_bull."\"";
			print "</script>";
		}

	//C'est une notice de periodique, on affiche la liste des bulletins
	} elseif ($ident->niveau_biblio == 's' && $ident->niveau_hierar == '1'){
		print $begin_result_liste;
		$link_serial = "./circ.php?categ=resa_planning&resa_action=search_resa&id_empr=$id_empr&groupID=$groupID&mode=view_serial&serial_id=!!id!!";
		$link_analysis = '';
		$link_bulletin = '';
		$serial = new serial_display($ident->notice_id, 6, $link_serial, $link_analysis, $link_bulletin);
		print $serial->result;
		print $end_result_liste;

	//C'est une notice de bulletin
	} elseif ($ident->niveau_biblio == 'b' && $ident->niveau_hierar == '2'){
		$rqt_bull = "select bulletin_id from bulletins where num_notice='".$ident->notice_id."'";
		$res_bull = pmb_mysql_query($rqt_bull);
		if(pmb_mysql_num_rows($res_bull)){
			$ident_bull = pmb_mysql_result($res_bull,0,0);
			print "<script type=\"text/javascript\">";
			print "document.location = \"./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=".$ident_bull."\"";
			print "</script>";
		}

	//C'est une notice de monographie
	} else {
		print "<script type=\"text/javascript\">";
		print  "document.location = \"./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_notice=".$ident->notice_id."\"";
		print "</script>";
	}
} else {
	error_message($msg[235], $msg['notice_id_query_failed']." ".$f_notice_id, 1, "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=0");
	die();
}
