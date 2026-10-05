<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.inc.php,v 1.2 2018/07/13 06:58:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

$sc=new search(true,"search_fields_empr");
print $sc->show_form("./circ.php?categ=search","./circ.php?categ=search&sub=launch", "", "./circ.php?categ=search_perso&sub=form");

?>