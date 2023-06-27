<?php
// Sypex Dumper 2 authorization file for IntraMessenger
define('INTRAMESSENGER',true);
require('../../../config/mysql.config.inc.php');
//if ($this->connect($dbhost, '', $dbuname, $dbpass))
//{
//  mysql_selectdb($database);
	$this->CFG['my_db'] = $database;
	$this->CFG['my_host'] = $dbhost;
	$this->CFG['my_user'] = $dbuname;
	$this->CFG['my_pass'] = $dbpass;
	$auth = 1;
//}
//  if ($lang != "") $this->CFG['lang'] = $lang;
if (!defined("_LANG"))   
{
  require('../../../config/config.inc.php');
  if (defined("_LANG")) $this->CFG['lang'] = strtolower(_LANG);
}
?>