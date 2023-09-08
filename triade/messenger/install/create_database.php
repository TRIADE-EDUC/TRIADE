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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['redir'])) $redir = $_GET['redir']; else $redir = "";
//
define('INTRAMESSENGER',true);
//
#require ("../common/sql.inc.php");
require_once("../common/config/mysql.config.inc.php");
$id_connect = mysqli_connect($dbhost, $dbuname, $dbpass) or die("Unable to connect to MySQL server : " . mysqli_error($id_connect));
unset($dbpass);
//
//
$requete  = " CREATE DATABASE IF NOT EXISTS `" . $database . "` ";
$result = mysqli_query($id_connect,$requete);
if (!$result) error_sql_log("[ERR-install_database-1]", $requete);
//
//
mysqli_close($id_connect);
//
//header("location:install.php?lang=" . $lang . "&");
if ($redir == "index")
  echo "<META http-equiv='refresh' content='1;url=../index.php?lang=" . $lang . "&'>";
else
  echo "<META http-equiv='refresh' content='1;url=install.php?lang=" . $lang . "&'>";
//
?>
