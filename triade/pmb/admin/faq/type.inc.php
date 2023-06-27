<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: type.inc.php,v 1.1 2014-04-01 13:45:46 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/faq_types.class.php");

$faq_types= new faq_types("faq_types","id_type","libelle_type",$id_liste);
$faq_types->proceed($act);
?>