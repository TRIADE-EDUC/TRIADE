<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expand_block_ajax.inc.php,v 1.16 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $display_cmd, $cmd_tab;

// functions particulières à ce module
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serial_display.class.php");

$cmd_tab=explode("|*|*|",$display_cmd);
foreach($cmd_tab as $cmd) {

	$html.=read_notice_contenu($cmd).'|*|*|';
}

ajax_http_send_response(substr($html,0,-5));

function read_notice_contenu($cmd) {
	global $msg,$categ,$id_empr;

	$param=unserialize(stripslashes($cmd));
	
	$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=".$param['id']."', 'cart')\"";

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
				$display->result="	<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click>$print_action !!serial_type!! !!ISBD!!";
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
				$display->result="	<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click>$print_action !!ISBD!!";
			}else{
				$display->result="	$print_action !!ISBD!!";
			}
			$display->finalize();
			$html=$display->result;				
		break;
	}
	
	return $param['id'].'|*|'.$html;
}	


?>