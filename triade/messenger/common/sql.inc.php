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
require_once("config/mysql.config.inc.php");

if (isset($dbport))
{
  if (intval($dbport) > 0) $dbhost = $dbhost . ":" . intval($dbport);
}

## Connection serveur mysql

$id_connect = mysqli_connect($dbhost, $dbuname, $dbpass) or die("Unable to connect to MySQL server: " . mysqli_connect_error());
mysqli_select_db($id_connect, $database) or die("Unable to select database: " . mysqli_error());

unset($dbpass);


//if (!function_exists("write_log"))
require("log.inc.php");


//if (!function_exists("error_sql_log"))  {
Function error_sql_log($txt, $qry)
{
  GLOBAL $id_connect;
  
  if ( (mysqli_error($id_connect) == "Query execution was interrupted") or (mysqli_error($id_connect) == "Lost connection to MySQL server during query") or (mysqli_error($id_connect) == "MySQL server has gone away") )
    write_log("error_log_connection", $txt . "\n" . $qry . "\n" . mysqli_error($id_connect) );
  else
    write_log("error_log", $txt . "\n" . $qry . "\n" . mysqli_error($id_connect) );
  //
  //die ($txt . " Invalid request (Requête invalide) : " . mysqli_error($id_connect) . " <BR/>" . $qry);
  die ($txt . " Invalid request (Requête invalide) : " . mysqli_error($id_connect) . " <BR/>");
}

?>
