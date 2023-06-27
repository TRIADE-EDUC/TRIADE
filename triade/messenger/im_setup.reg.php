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
header('Content-disposition: attachment; filename=im_setup.reg');
header('Content-type: application/octet-stream');
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: text/plain \n"); // Surtout ne pas enlever le \n
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
header("Expires: 0");
if (is_readable("im_setup.reg")) 
{
  //header("Content-Length: ".filesize('im_setup.reg'));
  readfile('im_setup.reg');
}
else
{
  //
  //$url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
  $url  = "http://" . $_SERVER['SERVER_NAME'];
  if ($_SERVER['SERVER_PORT'] <> 80) $url .= ":" . $_SERVER['SERVER_PORT'];
  $url .= dirname($_SERVER['PHP_SELF']);
  $pos = strrpos($url, "/");
  if ( $pos < (strlen($url)-1) ) $url .= "/";
  //
  $data  = "Windows Registry Editor Version 5.00\n\n";
  $data .= "[HKEY_CURRENT_USER\Software\THe UDS\IM]\n";
  $data .= '"url"="' . $url . '"';
  $data .= "\n";
  //$data .= '"lang"="' . $reg_lang . '"';
  //$data .= "\n";
  //
  echo $data;
  //
}
?>