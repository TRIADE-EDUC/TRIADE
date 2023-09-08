<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expand_block_ajax.inc.php,v 1.12 2019-05-29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $link_serial, $link_analysis, $link_bulletin, $link_explnum_serial, $link_explnum_analysis, $link_explnum_bulletin, $display_cmd;

// functions particulières à ce module
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serial_display.class.php");

$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
$link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
$link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";

$cmd_tab=explode("|*|*|",$display_cmd);


foreach($cmd_tab as $cmd) {
	if(trim($cmd)){
		$html.=read_notice_contenu($cmd).'|*|*|';
	}
}

ajax_http_send_response(substr($html,0,-5));

function read_notice_contenu($cmd) {
	global $msg,$categ,$id_empr;
	
	$cmd = explode("|*|",$cmd);
	$param=unserialize(stripslashes($cmd[0]));
	
	$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=".$param['id']."', 'cart')\"";
	$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$param['id'].");\" onMouseOut=\"set_flag_info_div(false);\"";

	$current=$_SESSION["CURRENT"];
	if ($current!==false) {
		$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&notice_id=".$param['id']."&action_print=print_prepare','print'); w.focus(); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
	}		
	$categ=$param['categ'];
	$id_empr=$param['id_empr'];
					
	switch($param['function_to_call']) {
		case 'serial_display' :
			// on a affaire à un périodique
			// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", 
			//$lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
			$display = new serial_display($param['id'],6, $param['action_serial'], $param['action_analysis'], 
				$param['action_bulletin'], $param['lien_suppr_cart'], $param['lien_explnum'],$param['bouton_explnum'],
				$param['print'],1,1, 1, 1);
			if(SESSrights & CATALOGAGE_AUTH){
				$display->result="	<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click $cart_over_out>$print_action !!serial_type!! !!ISBD!!";
			}else{
				$display->result="	$print_action !!serial_type!! !!ISBD!!";
			}
			$display->finalize();
			$html=$display->result;
		break;
		case 'mono_display' :
			// on a affaire à un bulletin ou monographie
			$display = new mono_display($param['id'], 6, $param['action'], $param['expl'], 
				$param['expl_link'], $param['lien_suppr_cart'], $param['explnum_link'],1,
				$param['print'],1, 1, $param['anti_loop'], 1, false, true, 0, 1);	
			if(SESSrights & CATALOGAGE_AUTH){
				$display->result="	<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click $cart_over_out>$print_action !!ISBD!!";
			}else{
				$display->result="	$print_action !!ISBD!!";
			}
			$display->finalize();
			
			$html=$display->result;			
		break;
	}
	
	return $param['id'].'|*|'.$html.($cmd[1] ? '|*|'.$cmd[1] : '');
}


?>