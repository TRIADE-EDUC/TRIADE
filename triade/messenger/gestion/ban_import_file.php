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
if (isset($_POST['ban'])) $ban = $_POST['ban'];  else  $ban = "";
if (isset($_POST['ban_type'])) $ban_type = $_POST['ban_type']; else $ban_type = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
//require ("../common/config/config.inc.php");
//
function import_file($file)
{
  GLOBAL $ban_type;
  //
  $handle = fopen($file, "r");
  if ($handle) 
  {
    require ("../common/sql.inc.php");
    //
    while (!feof($handle)) 
    {
      $buffer = fgets($handle, 4096);
      $buffer = trim($buffer);
      if (strlen($buffer) > 3)
      {
        $requete  = " insert into " . $PREFIX_IM_TABLE . "BAN_BANNED (BAN_TYPE, BAN_VALUE) ";
        $requete .= " values ('" . $ban_type . "', '" . $buffer . "' ) ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-U3a]", $requete);
      }
    }
    fclose($handle);
    mysqli_close($id_connect);
  }
}
//
if ($ban_type != "")
{
  $folder = "../common/config/";
  //
  if ( ($ban_type == "U") and (is_readable($folder . "ban_nickname.txt")) )
  {
    import_file($folder . "ban_nickname.txt");
    if (is_writable($folder . "ban_nickname.txt") == true)  unlink($folder . "ban_nickname.txt");
  }
  //
  if ( ($ban_type == "I") and (is_readable($folder . "ban_ip.txt")) )  
  {
    import_file($folder . "ban_ip.txt");
    if (is_writable($folder . "ban_ip.txt") == true)  unlink($folder . "ban_ip.txt");
  }
}
//
//
header("location:list_ban.php?ban=" . $ban . "&lang=" . $lang . "&");
?>
