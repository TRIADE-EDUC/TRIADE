<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pointage_selection.inc.php,v 1.9 2017-06-29 13:08:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once ($class_path."/empr_caddie_procs.class.php");

empr_caddie_controller::proceed_selection($idemprcaddie, 'gestion', 'pointage', 'selection');