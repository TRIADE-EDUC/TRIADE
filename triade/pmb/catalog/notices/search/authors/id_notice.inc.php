<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: id_notice.inc.php,v 1.9 2023/09/06 06:55:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $include_path;
global $acces_j, $gestion_acces_active, $gestion_acces_user_notice, $class_path, $PMBuserid, $pmb_show_notice_id, $f_notice_id, $msg;

require_once($include_path."/templates/serials.tpl.php");

//droits d'acces lecture notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
}

$param_notice_id = explode(",",$pmb_show_notice_id);
$prefix_id = (!empty($param_notice_id[1]) ? $param_notice_id[1] : '');
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
			print "document.location = \"".analysis::get_permalink($ident->notice_id, $ident_bull)."\"";
			print "</script>";
		} else {
		    print "<script type=\"text/javascript\">";
		    print "document.location = \"".$base_path."/catalog.php?categ=serials&sub=analysis&action=analysis_orphan_form&analysis_id=".$f_notice_id."\"";
		    print "</script>";
		}
	//C'est une notice de periodique
	} elseif ($ident->niveau_biblio == 's' && $ident->niveau_hierar == '1'){
		print "<script type=\"text/javascript\">";
		print "document.location = \"".serial::get_permalink($ident->notice_id)."\"";
		print "</script>";
		
	//C'est une notice de bulletin
	} elseif ($ident->niveau_biblio == 'b' && $ident->niveau_hierar == '2'){
		$rqt_bull = "select bulletin_id from bulletins where num_notice='".$ident->notice_id."'";
		$res_bull = pmb_mysql_query($rqt_bull);
		if(pmb_mysql_num_rows($res_bull)){	
			$ident_bull = pmb_mysql_result($res_bull,0,0);	
			print "<script type=\"text/javascript\">";
			print "document.location = \"".bulletinage::get_permalink($ident_bull)."\"";
			print "</script>";
		}
	
	//C'est une notice de monographie
	} else {
		print "<script type=\"text/javascript\">";
		print "document.location = \"".notice::get_permalink($ident->notice_id)."\"";
		print "</script>";
	}
} else {
	error_message($msg[235], $msg['notice_id_query_failed']." ".$f_notice_id, 1, "./catalog.php?categ=search&mode=0");
	die();
}

?>