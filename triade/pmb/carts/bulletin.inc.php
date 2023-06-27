<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletin.inc.php,v 1.16 2019-05-29 12:42:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $item, $action, $idcaddie, $include_child, $what, $aff, $msg;

if($item) {
	$item_list = explode(',', $item);
	if(!count($item_list)) $item_list[0]=$item;
	
	foreach ($item_list as $elt) {	
		$bull=new bulletinage_display($elt);
		$aff_bull=$bull->header;	
		print pmb_bidi($aff_bull).'<br />';
	}
	
	switch($action) {
		case 'add_item':
			if($idcaddie)$caddie[0]=$idcaddie;
			foreach($caddie  as $idcaddie) {
				$myCart = new caddie($idcaddie);
				foreach ($item_list as $elt) {
					if($include_child) {					
						$tab_list_child=notice::get_list_child($elt);
						if(count($tab_list_child)) {
							foreach ($tab_list_child as $notice_id) {
								$myCart->add_item($notice_id,"BULL",$what);					
							}	
						}	
					} else {
						$myCart->add_item($elt,"BULL",$what);
					}
				}
				$myCart->compte_items();
			}	
			print "<script type='text/javascript'>window.close();</script>"; 
			break;
		case 'new_cart':
			break;
		case 'del_cart':
		case 'valid_new_cart':		
		default:
			print $aff;
			aff_paniers($item, "BULL", "./cart.php?&what=$what", "add_item", $msg["caddie_add_BULL"], "", 0, 1, 1);
			break;
		}
} else {
	print "<h1>".$msg["fonct_no_accessible"]."</h1>";
}

