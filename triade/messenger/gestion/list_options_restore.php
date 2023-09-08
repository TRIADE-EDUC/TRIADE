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
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
if ( (!is_writeable("../common/config/config.inc.php")) or (!is_writeable("../common/config/config.inc.bak.php")) )
{
  header("location:check.php?lang=" . $lang . "&");
  die();
}
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if (filesize("../common/config/config.inc.bak.php") > 100)
  {
    if (copy("../common/config/config.inc.bak.php", "../common/config/config.inc.php"))
    { 
      // Empty backup file:
      $fp = fopen("../common/config/config.inc.bak.php", "w"); 
      fputs($fp, ""); 
      fclose($fp); 
    }
    //
    require("../common/log.inc.php");
    write_log("log_options_update", $nb_corrections . ";" . $options_fixed . ";");
  }
}
//
header("location:list_options_updating.php?lang=" . $lang . "&");
?>