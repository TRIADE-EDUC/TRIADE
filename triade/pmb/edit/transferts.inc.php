<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts.inc.php,v 1.25 2020/10/31 10:09:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $id;

require_once ($class_path."/transferts/transferts_edition_controller.class.php");

if($sub == 'retours') {
    transferts_edition_controller::set_list_ui_class_name('list_transferts_edition_retours_ui');
}
$id = intval($id);
transferts_edition_controller::proceed($id);
