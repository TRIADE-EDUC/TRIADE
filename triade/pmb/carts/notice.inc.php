<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.inc.php,v 1.17 2019-05-29 12:42:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $item, $msg, $action, $idcaddie, $include_child, $current_print;

if($item) {
	print "<h1>".$msg["400"]."</h1>";
	$notice = new mono_display($item,1);
	print pmb_bidi('<strong>'.$notice->header.'</strong><br />');
}
switch($action) {
	case 'add_item':
		// cas du click sur le lien du panier
		if($idcaddie)$caddie[0]=$idcaddie;
		// Pour tous les paniers cochés
		foreach($caddie  as $idcaddie) {
			$myCart = new caddie($idcaddie);
			if($include_child) {					
				$tab_list_child=notice::get_list_child($item);
				if(count($tab_list_child))
				foreach ($tab_list_child as $notice_id) {
					$myCart->add_item($notice_id,"NOTI");					
				}		
			} else	$myCart->add_item($item,"NOTI");
			$myCart->compte_items();
		}
		print "<script type='text/javascript'>window.close();</script>"; 
		break;
	case 'new_cart':
		break;
	case 'del_cart':
	case 'valid_new_cart':		
	default:
		if(isset($current_print) && $current_print) {
			$action="print_prepare";
			require_once("./print_cart.php");
		} else {
			aff_paniers($item, "NOTI", "./cart.php?", "add_item", $msg["caddie_add_EXPL"], "", 0, 1, 1);
		}	
		break;
}
