<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_waiting.php,v 1.1 2018/04/24 10:18:09 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[mails_waiting]";
$base_noheader=1;
$base_nosession=1;
$base_nocheck = 1 ;

require_once ($base_path."/includes/init.inc.php");  

require_once ($class_path."/mails_waiting.class.php");

$mails_waiting = new mails_waiting();
$mails_waiting->send();

// deconnection MYSql
pmb_mysql_close();
