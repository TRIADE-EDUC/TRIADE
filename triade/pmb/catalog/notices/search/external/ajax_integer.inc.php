<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_integer.inc.php,v 1.12 2023/09/06 07:01:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $item, $infos, $signature, $pmb_notice_controle_doublons, $id_notice, $ret, $recid, $retour, $notice_id;
global $url_view, $serialize_search, $result, $msg, $charset;

require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/mono_display_unimarc.class.php");
require_once($include_path."/external.inc.php");
require_once($class_path."/z3950_notice.class.php");
require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/serials.class.php");

if($item) {
	$infos = entrepot_to_unimarc($item);
}	
//on regarde si la signature existe déjà..;
$signature = "";
		

$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
//on reporte la signature de la notice calculée ou non...
if($pmb_notice_controle_doublons != 0){
	$sign = new notice_doublon(true,$infos['source_id']);
	$signature = $sign->gen_signature($item);
}
$z->signature = $signature;
if($infos['notice']) $z->notice = $infos['notice'];
if($infos['source_id']) $z->source_id = $infos['source_id'];
$z->var_to_post();
$ret=$z->insert_in_database();

//on conserve la trace de l'origine de la notice...
$id_notice = intval($ret[1]);
$rqt = "select recid from external_count where rid = '".addslashes($item)."'";
$res = pmb_mysql_query($rqt);
if(pmb_mysql_num_rows($res)) {
    $recid = pmb_mysql_result($res,0,0);
}
if($id_notice && $recid) {
    $req= "insert into notices_externes set num_notice = '".$id_notice."', recid = '".addslashes($recid)."'";
    pmb_mysql_query($req);
}
if ($ret[0]) {
	if($z->bull_id && $z->perio_id){
		$notice_display=new serial_display($ret[1],6);
	} else $notice_display=new mono_display($ret[1],6);
	$retour = "
	<script src='javascript/tablist.js'></script>
	<br /><div class='erreur'></div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('tick.gif')."' class='align_left'>
		</div>
		<div class='colonne80'>
			<strong>".(isset($notice_id) ? $msg["notice_connecteur_remplaced_ok"] : $msg["z3950_integr_not_ok"])."</strong>
			".$notice_display->result."
		</div>
	</div>";
	if($z->bull_id && $z->perio_id) {
	    $url_view = analysis::get_permalink($ret[1], $z->bull_id);
	} else {
	    $url_view = notice::get_permalink($ret[1]);
	}
	$retour .= "
		<div class='row'>
			<div class='row'>
				<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
			</div>
		<script type='text/javascript'>
			document.forms['dummy'].elements['ok'].focus();
		</script>
		</div>
	";
} else if ($ret[1]){
	if($z->bull_id && $z->perio_id){
		$notice_display=new serial_display($ret[1],6);
	} else {
	    $notice_display=new mono_display($ret[1],6);
	}
	$retour = "
	<script src='javascript/tablist.js'></script>
	<br /><div class='erreur'>$msg[540]</div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('tick.gif')."' class='align_left'>
		</div>
		<div class='colonne80'>
			<strong>".($msg["z3950_integr_not_existait"])."</strong><br /><br />
			".$notice_display->result."
		</div>
	</div>";
	if($z->bull_id && $z->perio_id) {
	    $url_view = analysis::get_permalink($ret[1], $z->bull_id);
	} else {
	    $url_view = notice::get_permalink($ret[1]);
	}
	$retour .= "
	<div class='row'>
			<div class='row'>
				<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
			</div>
	<script type='text/javascript'>
		document.forms['dummy'].elements['ok'].focus();
	</script>
	</div>
	";
}
else {
	$retour = "<script src='javascript/tablist.js'></script>";
	$retour .= form_error_message($msg["connecteurs_cant_integrate_title"], ($ret[1]?$msg["z3950_integr_not_existait"]:$msg["z3950_integr_not_newrate"]), $msg["connecteurs_back_to_list"], "catalog.php?categ=search&mode=7&sub=launch",array("serialized_search"=>$serialize_search));
}
$result = array(
	'id'=>$item,
	'html'=>($charset != "utf-8" ? encoding_normalize::utf8_normalize($retour) : $retour)
);
ajax_http_send_response($result);