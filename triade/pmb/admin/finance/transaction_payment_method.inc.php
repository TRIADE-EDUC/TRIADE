<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_payment_method.inc.php,v 1.1 2018-12-19 13:59:19 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Gestion des Types de transaction

require_once($class_path."/transaction/transaction_payment_method_list.class.php");
require_once($class_path."/transaction/transaction_payment_method.class.php");

if(!$action){	
    $transaction_payment_method_list=new transaction_payment_method_list();
	print $transaction_payment_method_list->get_form();
}else{
	$transaction_payment_method=new transaction_payment_method($id);	
	$transaction_payment_method->proceed();
}
