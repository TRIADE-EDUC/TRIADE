<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.tpl.php,v 1.12 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $etageres_header;
global $etageres_footer;
global $msg;

$etageres_header = "<div id='etageres'><h3><span id='titre_etagere'>".$msg['accueil_etageres_virtuelles']."</span></h3>";

$etageres_footer = "</div>" ;			

