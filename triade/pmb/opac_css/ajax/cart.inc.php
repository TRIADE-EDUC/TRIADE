<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.1.20.1 2025/02/13 07:33:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_integrate_anonymous_cart, $action, $msg;

if ($opac_integrate_anonymous_cart && $_SESSION['cart_anonymous']) {
	switch ($action) {
		case 'keep_anonymous_cart' :
			$_SESSION['cart'] = $_SESSION['cart_anonymous'];
			unset($_SESSION['cart_anonymous']);
			break;
		case 'merge_cart' :
			$nb_added = count(array_diff($_SESSION['cart_anonymous'], $_SESSION['cart']));
			$_SESSION['cart'] = array_unique(array_merge($_SESSION['cart_anonymous'], $_SESSION['cart']));
			print encoding_normalize::json_encode(array('msg' => sprintf($msg['cart_add_notices'], $nb_added, count($_SESSION['cart'])), 'nb_added' => $nb_added));
			unset($_SESSION['cart_anonymous']);
			break;
		case 'purge_cart':
			unset($_SESSION['cart_anonymous']);
			break;
		default :
			
			break;
	}
}