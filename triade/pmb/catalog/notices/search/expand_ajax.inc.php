<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expand_ajax.inc.php,v 1.16 2019-06-07 08:05:38 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $mono_display_cmd, $param, $cart_click, $current, $print_action, $categ, $id_empr, $display, $msg, $html;

// functions particulières à ce module
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serial_display.class.php");

$mono_display_cmd=stripslashes($mono_display_cmd);
$param=unserialize($mono_display_cmd);

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
		//$lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0, $ajax_mode=0 , $anti_loop='' ) {
		$display = new serial_display($param['id'], 6, $param['action_serial'], $param['action_analysis'], 
			$param['action_bulletin'], $param['lien_suppr_cart'], $param['lien_explnum'],$param['bouton_explnum'],
			$param['print'],1, 1, 1,1);
		if(SESSrights & CATALOGAGE_AUTH){
			$display->result="	<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click>$print_action !!serial_type!! !!ISBD!!";
		}else{
			$display->result="	$print_action !!serial_type!! !!ISBD!!";
		}
		$display->finalize();
		$html=$display->result;				
	break;
	case 'mono_display' :
		// on a affaire à un bulletin ou monographie
		//mono_display($id, $level=1, $action='', $expl=1, $expl_link='', $lien_suppr_cart="", $explnum_link='', $show_resa=0, 
		//$print=0, $show_explnum=1, $show_statut=0, $anti_loop='', $draggable=0, $no_link=false, $show_opac_hidden_fields=true,$ajax_mode=0,$show_planning=0)
		$display = new mono_display($param['id'], 6, $param['action'], $param['expl'], 
			$param['expl_link'], $param['lien_suppr_cart'], $param['explnum_link'],1,
			$param['print'],1, 1, '', 1, false, true, 0, 1);
		if(SESSrights & CATALOGAGE_AUTH){
			//$display->result="<div onMouseOver='if(init_drag) init_drag();'><img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click>$print_action !!ISBD!!</div>";
			$display->result="<div><img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click>$print_action !!ISBD!!</div>";
		}else{
			$display->result=" $print_action !!ISBD!!";
		}
		$display->finalize();
		$html=$display->result;
	break;
}
ajax_http_send_response($html);
?>