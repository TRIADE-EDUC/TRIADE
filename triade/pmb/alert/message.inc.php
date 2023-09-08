<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: message.inc.php,v 1.8 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte;

$temp_aff = alerte_message_administration () ;
if ($temp_aff) $aff_alerte.= "<ul>".$temp_aff."</ul>" ;

function alerte_message_administration () {	
	return "";
}

