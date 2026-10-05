<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: make_multi_sugg.inc.php,v 1.9 2020/10/31 09:05:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/classes/suggestion_multi.class.php");

if (!isset($action)) $action = '';
if (!isset($act)) $act = '';
if($action) $act=$action;
if (!isset($notices)) $notices = array();
$sug = new suggestion_multi($notices); 

switch($act){
	case 'save_multi_sugg':
		$sug->save();
		print "<div id='empr-sugg'>\n";
		require_once($base_path.'/empr/view_sugg.inc.php');
		print "</div>";		
		break;
	case 'transform_caddie':
	    if(!empty($notice) && is_array($notice) && count($notice)){
			if(count($notice)==1){ // fomulaire unique
				$id_notice = $notice[0];
				$form_action = 'empr.php';
				require_once($base_path.'/includes/make_sugg.inc.php');
				break;
			}
			$sug->liste_sugg = $notice;
		} else{
			$sug->liste_sugg = $_SESSION['cart'];
		}
		$sug->from_cart = 1;
		print $sug->display_form();
		break;
	case 'transform_list':
		$sug->liste_sugg = explode(",",$notice_filtre); 
		print $sug->display_form();
		break;
	default:
		print $sug->display_form();	
		break;
}