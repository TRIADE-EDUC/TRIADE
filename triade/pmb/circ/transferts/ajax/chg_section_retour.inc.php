<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chg_section_retour.inc.php,v 1.2 2015/04/03 11:16:27 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$rqt = 	"UPDATE exemplaires". 
		" SET expl_section=". $param . 
		" WHERE expl_id=" . $idexpl; 
pmb_mysql_query( $rqt );

ajax_http_send_response($param,"text/xml");

?>