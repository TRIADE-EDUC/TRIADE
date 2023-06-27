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

GLOBAL $dbhost;
if ( ($extern_dbhost == "") and ($dbhost != "") ) $extern_dbhost = $dbhost;


if (isset($extern_dbport))
{
  if (intval($extern_dbport) > 0) $dbhost = $dbhost . ":" . intval($extern_dbport);
}


## Connection mysql server
//$id_connect_extern = mysql_connect($extern_dbhost, $extern_dbuname, $extern_dbpass) or die("Unable to connect to MySQL server : " . mysql_error());
//$id_connect_extern = mysqli_connect($extern_dbhost, $extern_dbuname, $extern_dbpass) or die("Unable to connect to MySQL server: " . mysqli_error($id_connect_extern));
$id_connect_extern = mysqli_connect($extern_dbhost, $extern_dbuname, $extern_dbpass) or die("Unable to connect to MySQL server: " . mysqli_connect_error()));

//mysql_select_db($extern_database, $id_connect_extern) or die("Unable to select database : " . mysql_error());
mysqli_select_db($id_connect_extern, $extern_database) or die("Unable to select database : " . mysqli_error($id_connect_extern));

?>