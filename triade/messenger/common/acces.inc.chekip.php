<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2009 THeUDS           **
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
function f_verif_ip($ip)
{
	$ip_local = $_SERVER['REMOTE_ADDR'];	
	//$ip_local = getenv['REMOTE_ADDR'];	
	if (!preg_match('/^[0-9.]+$/i', $ip) ) $ip = "";
	//
	if ( ($ip_local != '') and ($ip_local != '127.0.0.1') )
	{
		if ($ip <> $ip_local)
		{
			die ("KO#IP#Authentification incorrecte (IP address).");
		}
	}
	return 'OK';
}
?>