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
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['action'])) $action = $_POST['action']; else $action = "";
if (isset($_POST['reg_lang'])) $reg_lang = $_POST['reg_lang']; else $reg_lang = "";
//
if (function_exists('file_put_contents') == false) 
{
  function file_put_contents($file, $string) 
  {
    $f = fopen($file, 'w');
    fwrite($f, $string);
    fclose($f);
  }
}
//
//$url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
$url  = "http://" . $_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_PORT'] <> 80) $url .= ":" . $_SERVER['SERVER_PORT'];
$url .= dirname($_SERVER['PHP_SELF']);
$pos = strrpos($url, "/");
if ($pos > 0) $url = substr($url, 0, $pos + 1);
//
$rep = dirname($_SERVER['SCRIPT_FILENAME']);
$pos = strrpos($rep, "/");
if ($pos > 0) 
  $rep = substr($rep, 0, $pos + 1);
else
{
  $pos = strrpos($rep, chr(92), 1); 
  if ($pos > 0) $rep = substr($rep, 0, $pos + 1);
}

$file_reg = $rep . "im_setup.reg";
//
if ($action == "create")
{
  if (file_exists($file_reg) == false) 
  {
    if (touch($file_reg) == false) 
      die("<BR/><BR/><font color='red'>Cannot create file /<B>im_setup.reg</B>");
  }
  //
  if (is_writable($file_reg) == true)
  {
    $data  = "Windows Registry Editor Version 5.00\n\n";
    $data .= "[HKEY_CURRENT_USER\Software\THe UDS\IM]\n";
    $data .= '"url"="' . $url . '"';
    $data .= "\n";
    $data .= '"lang"="' . $reg_lang . '"';
    $data .= "\n";
    //
    file_put_contents($file_reg, $data);
    //
    header("location:list_options_updating.php?lang=" . $lang . "&");  
  }
  else
    echo "<font color='red'>Cannot write in file /<B>im_setup.reg</B>";
}
//
//
if ($action == "delete")
{
  if (is_writable($file_reg) == true)  unlink($file_reg);
  //
  header("location:list_options_updating.php?lang=" . $lang . "&");  
}
?>