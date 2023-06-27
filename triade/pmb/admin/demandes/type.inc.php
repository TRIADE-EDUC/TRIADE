<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: type.inc.php,v 1.3 2017-06-02 10:05:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($id_liste)) $id_liste = 0;
if(!isset($act)) $act = '';

require_once($class_path."/demandes_types.class.php");

$dmd_type = new demandes_types("demandes_type","id_type","libelle_type",$id_liste);
$dmd_type->proceed($act);
?>