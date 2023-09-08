<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edition.inc.php,v 1.14 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $mode, $idcaddie;

require_once("./classes/notice_tpl_gen.class.php");

if(empty($mode)) $mode = 'simple';
caddie_controller::proceed_edition($idcaddie, $mode);