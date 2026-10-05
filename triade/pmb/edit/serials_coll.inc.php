<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_coll.inc.php,v 1.30 2022/02/15 08:32:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once ($class_path."/records/records_bulletins_collstate_edition_controller.class.php");

records_bulletins_collstate_edition_controller::proceed($id);