<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
if ( !defined('INTRAMESSENGER') )
{
  exit;
}

## Parametre Mysql
require("../common/config/mysql.config.inc.php");


if (isset($dbport))
{
  if (intval($dbport) > 0) $dbhost = $dbhost . ":" . intval($dbport);
}


## Connection serveur mysql
//$id_connect = mysql_connect($dbhost, $dbuname, $dbpass) or die("Unable to connect to MySQL server : " . mysql_error());
//mysql_select_db($database, $id_connect) or die("Unable to select database : " . mysql_error());

$id_connect = mysqli_connect($dbhost, $dbuname, $dbpass) or die("Unable to connect to MySQL server: " . mysqli_connect_error());
mysqli_select_db($id_connect, $database) or die("Unable to select database: " . mysqli_error($id_connect));


unset($dbpass);

/*
require_once("log.inc.php");

Function error_sql_log($txt, $qry)
{
	write_log("error_log", $txt . "\n" . $qry . "\n" . mysql_error() );

	die ($txt . " Invalid request (Requête invalide) : " . mysql_error() . " <BR>");
}
*/
?>