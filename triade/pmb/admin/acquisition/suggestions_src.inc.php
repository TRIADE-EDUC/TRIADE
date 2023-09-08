<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_src.inc.php,v 1.2 2017-04-19 12:37:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/suggestion_source.class.php");

if(!isset($id_src)) $id_src = 0;
if(!isset($act)) $act = '';
$sug_src = new suggestion_source($id_src);
$sug_src->proceed($act);
?>