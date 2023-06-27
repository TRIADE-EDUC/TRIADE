<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2010 THeUDS           **
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
$ip = 			  $_REQUEST['ip'];
$n_version = 	intval($_REQUEST['version']);
//
require ("../common/constant.inc.php");
//
if ( intval($n_version) < intval(_CLIENT_VERSION_MINI) )
{
	// Version number to old (périmée)
	echo ">F01#KO#" . _CLIENT_VERSION_MINI . "#";
	//
	//require ("../common/log.inc.php");
	//write_log("error_version_log", $ip);
}
?>