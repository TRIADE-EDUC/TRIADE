<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ticket-pret-electro.inc.php,v 1.2 2017-06-03 08:12:16 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$base_path/circ/pret_func.inc.php");
// liste des prêts et réservations

if (isset($id_groupe)) {
	electronic_ticket_groupe($id_groupe);
} else {
	electronic_ticket($id_empr) ;
}

?>
