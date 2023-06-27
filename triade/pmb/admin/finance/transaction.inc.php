<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction.inc.php,v 1.1 2013-12-24 13:08:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Gestion des Types de transaction

require_once($class_path."/transaction/transaction_list.class.php");
require_once($class_path."/transaction/transaction.class.php");

if(!$action){	
	$transactype_list=new transactype_list();
	print $transactype_list->get_form();
}else{
	$transactype=new transactype($id);	
	$transactype->proceed();
}
