<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk.inc.php,v 1.2 2013-12-24 13:08:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Gestion des caisses

require_once($class_path."/cashdesk/cashdesk_list.class.php");
require_once($class_path."/cashdesk/cashdesk.class.php");

if(!$action){	
	$cashdesk_list=new cashdesk_list();
	print $cashdesk_list->get_form();
}else{
	$cashdesk=new cashdesk($id);	
	$cashdesk->proceed();
}