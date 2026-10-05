<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate.inc.php,v 1.2 2019/06/05 09:04:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $explnum_id, $explnum_associate_tpl;

require_once $class_path.'/explnum.class.php';
require_once $class_path.'/explnum_associate.class.php';
require_once $include_path.'/templates/explnum_associate.tpl.php';

$explnum_associate = new explnum_associate(new explnum($explnum_id));
$explnum_associate->getPlayer($explnum_associate_tpl);

$explnum_associate->getAjaxCall($explnum_associate_tpl);

$explnum_associate->getReturnLink($explnum_associate_tpl);

print $explnum_associate_tpl;

?>