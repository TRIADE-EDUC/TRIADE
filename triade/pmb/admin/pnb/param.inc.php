<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param.inc.php,v 1.1 2018-05-28 15:16:40 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/pnb/pnb_param.class.php");

$pnb_param = new pnb_param();
$pnb_param->proceed();
