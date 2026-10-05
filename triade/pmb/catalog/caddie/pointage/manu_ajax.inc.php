<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: manu_ajax.inc.php,v 1.6 2023/08/28 14:01:11 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $charset, $idcaddie, $id_item;

if($idcaddie) {
	$myCart = new caddie($idcaddie);
	switch ($action) {
		case 'add_item':
			if($id_item) {
				$res_pointage = $myCart->pointe_item($id_item,$myCart->type);
			}			
		break;
		case 'del_item':
			$res_pointage = $myCart->depointe_item($id_item);
			break;
		default:
		break;
	}
	$aff_cart_nb_items = $myCart->aff_cart_nb_items();
} 

if(!$id_item) $id_item = 0;
if(!$idcaddie) $idcaddie = 0;
if(!$res_pointage) $res_pointage = 0;
$result = array(
	'id'=>$id_item,
	'idcaddie'=>$idcaddie,
	'res_pointage'=>$res_pointage,
	'aff_cart_nb_items'=>($charset != "utf-8" ? encoding_normalize::utf8_normalize($aff_cart_nb_items) : $aff_cart_nb_items)
);
ajax_http_send_response($result);
