<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs.inc.php,v 1.10 2016-11-15 14:26:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($include_path."/templates/empr_cart.tpl.php");
require_once ($class_path."/empr_caddie_procs.class.php");

empr_caddie_procs::proceed();
