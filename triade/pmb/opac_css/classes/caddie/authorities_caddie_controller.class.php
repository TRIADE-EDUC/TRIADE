<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie_controller.class.php,v 1.2 2019/04/24 13:48:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . "/caddie/caddie_root_controller.class.php");
require_once ($class_path . "/authorities_caddie.class.php");

class authorities_caddie_controller extends caddie_root_controller {

    protected static $model_class_name = 'authorities_caddie';
    protected static $procs_class_name = 'authorities_caddie_procs';

    
}

// fin de déclaration de la classe authorities_caddie_controller
