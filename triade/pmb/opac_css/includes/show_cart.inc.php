<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_cart.inc.php,v 1.96.4.2 2025/02/13 07:33:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $count, $opac_nb_max_tri, $raz_cart, $action, $notice, $opac_search_results_per_page, $page;
global $opac_notices_depliable, $begin_result_liste;

// pour export panier
require_once("$base_path/admin/convert/start_export.class.php");

if (isset($_GET['sort'])) {
	$_SESSION['last_sortnotices'] = $_GET['sort'];
}
if (isset($count) && $count > $opac_nb_max_tri) {
	$_SESSION['last_sortnotices'] = '';
}

$cart_ = (isset($_SESSION['cart']) ? $_SESSION['cart'] : array());

if (!empty($raz_cart)) {
	$cart_ = array();
	cart::raz();
}

//Traitement des actions
if (!isset($action)) {
    $action = '';
}
if (!empty($action)) {
    if ($action == 'del' && !empty($notice) && is_countable($notice)) {
        cart::list_delete($notice);
		
        $cart_ = (isset($_SESSION['cart']) ? $_SESSION['cart'] : array());
		if (ceil(count($cart_) / $opac_search_results_per_page) < $page) {
		    $page = count($cart_) / $opac_search_results_per_page;
		}
	}
}
if (!isset($page) || $page == '') {
    $page = 1;
}
if (!empty($cart_)) {
    //Tri
    cart::sort();
}

print "<script src='".$base_path."/includes/javascript/cart.js'></script>";
print '<div id="cart_action">';

$instance_cart = new cart();
if (!empty($instance_cart->get_session_cart())) {
	print $instance_cart->get_display_actions();
}
print $instance_cart->get_display_title_entities();
if (!empty($instance_cart->get_session_cart())) {
	print '<div class="search_result">';
	if (!empty($opac_notices_depliable)) {
	    print $begin_result_liste;
	}
	print $instance_cart->get_display_action_sort();
	print $instance_cart->get_display_list_entities($cart_);
	print '</div>';
	print $instance_cart->get_display_pager();
}
print "</div>";
