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
//
#error_reporting(E_ALL);
#return;

$domain = $_SERVER['SERVER_NAME'];
if ( ($domain == 'www.theuds.com') or ($domain == 'www.intramessenger.net') or ($domain == 'www.intramessenger.com') )  
  error_reporting(E_ALL);
else
{
  error_reporting(0);
  //
  if (substr_count(ini_get("disable_functions"), "ini_set") <= 0)
    ini_set('display_errors','0');
}
?>