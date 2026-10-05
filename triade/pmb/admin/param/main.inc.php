<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.15 2021/02/09 18:01:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id;

require_once('./admin/param/param_func.inc.php');
require_once('./classes/parameters/parameters_controller.class.php');

print "<div class='row'>";
		
parameters_controller::proceed($id);

print "</div>";
