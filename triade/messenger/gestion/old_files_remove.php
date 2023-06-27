<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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
require ("../common/display_errors.inc.php"); 
//
define('INTRAMESSENGER',true);
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['f'])) $file = $_GET['f']; else $file = "";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  $repert = "../distant/";
  $file = str_replace("/", "", $file);
  $file = str_replace("\\", "", $file);
  $file = str_replace("..", "", $file);
  $file = str_replace("*", "", $file);
  $file = str_replace("%", "", $file);
  $file = str_replace("'", "", $file);
  //
  if ($file == "ALL")
  {
    #require ("../common/functions.inc.php"); 
    require ("../common/functions_admin.inc.php"); 
    $old_distant_files = f_old_distant_files();
    foreach ($old_distant_files as $name) 
    {
      if (is_readable($repert . $name))
      {
        if (is_writeable($repert . $name))
        {
          unlink($repert . $name);
        }
      }
    }
  }
  else
  {
    if ( (is_writeable($repert . $file)) and ($file != "") )
    {
      unlink($repert . $file);
    }
  }
}
//
header("location:old_files_removing.php?lang=" . $lang . "&");
?>