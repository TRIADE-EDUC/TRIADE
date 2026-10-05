<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: newrecords_rss.php,v 1.1 2015/04/16 12:31:00 ngantier Exp $

$base_path=".";
$include_path=$base_path."/includes";
$class_path=$base_path."/classes";
require_once($base_path."/includes/includes_rss.inc.php");

require_once("$class_path/newrecords_flux.class.php");

$flux = new newrecords_flux() ;
$flux->xmlfile() ;
if(!$flux->envoi )die;
@header('Content-type: text/xml');
echo $flux->envoi ;