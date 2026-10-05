<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_history.inc.php,v 1.33 2021/06/11 10:21:59 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search_history.class.php");

$search_history = new search_history();
$search_history->print_search_form_list();
$search_history->print_hidden_search_form_list();

?>