<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart.php,v 1.24 2019-03-18 15:01:00 ngantier Exp $

//Ajout aux paniers

$base_path = ".";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[print_cart_title]";


require_once($base_path."/includes/init.inc.php");
require_once($class_path."/caddie/caddie_controller.class.php");
require_once($class_path."/caddie/authorities_caddie_controller.class.php");

if (isset($authorities_caddie)) {
    authorities_caddie_controller::process_print($idcaddie_new);
} else {
    caddie_controller::process_print($idcaddie_new);
}
print $footer;
?>
