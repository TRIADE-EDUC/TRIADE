<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2011 THeUDS           **
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
//
$last_check_version = "";
$last_check_version_today = "";
$file_last_check_version = "log/lastcheck.tmp";
if (file_exists($file_last_check_version)) 
{
  $fp = fopen($file_last_check_version, "r");
  $t_check = fgets($fp);
  fclose($fp);
  //
  $last_check_version = substr($t_check, 11, 3);
  if ( ($last_check_version != "OK.") and ($last_check_version != "NEW") ) $last_check_version = "";
  if (substr($t_check, 0, 11) == (date("d/m/Y") . "#") ) 
  {
    $last_check_version_today = $last_check_version;
  }
}
?>