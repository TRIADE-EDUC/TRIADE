<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2013 THeUDS           **
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
  //exit;
  die();
}

if (!defined("_ACP_PROTECT_BY_HTACCESS")) define("_ACP_PROTECT_BY_HTACCESS", "X");

if (_ACP_PROTECT_BY_HTACCESS == "")
{
  session_start();
  //
  if ( (!isset($_SESSION['acp_init'])) or (!isset($_SESSION['acp_login'])) or (!isset($_SESSION['acp_level'])) )
  {
    session_destroy();
    header("location:acp_connect.php");
    die();
  }
}

define('_C_ACP_RIGHT_administrators', '1');
define('_C_ACP_RIGHT_options', '2');
define('_C_ACP_RIGHT_users_unlock', '4');
define('_C_ACP_RIGHT_users', '8');
define('_C_ACP_RIGHT_user_contacts', '16');
define('_C_ACP_RIGHT_avatars', '32');
define('_C_ACP_RIGHT_groups', '64');
define('_C_ACP_RIGHT_roles', '128');
define('_C_ACP_RIGHT_shoutbox', '256');
define('_C_ACP_RIGHT_published_files', '512');
define('_C_ACP_RIGHT_bookmars', '1024');
define('_C_ACP_RIGHT_banned', '2048');
define('_C_ACP_RIGHT_servers_status', '4096');
define('_C_ACP_RIGHT_admin_messages', '8192');
define('_C_ACP_RIGHT_admin_messages_orders', '16384');
define('_C_ACP_RIGHT_admin_messages_emails', '32768');
define('_C_ACP_RIGHT_log_read', '65536');
define('_C_ACP_RIGHT_log_purge', '131072');
//define('_C_ACP_RIGHT_', '262144');
//define('_C_ACP_RIGHT_', '524288');
//define('_C_ACP_RIGHT_', '1048576');


function f_acp_login()
{
  $ret = "";
  if (_ACP_PROTECT_BY_HTACCESS == "")
  {
    if (isset($_SESSION['acp_login'])) $ret = $_SESSION['acp_login'];
  }
  //
  return $ret;
}

function check_acp_rights($level)
{
  $ret = "";
  $adm_level = 0;
  if (_ACP_PROTECT_BY_HTACCESS == "")
  {
    if (isset($_SESSION['acp_level'])) $adm_level = $_SESSION['acp_level'];
    if ($adm_level & $level) $ret = "OK";
  }
  else
    $ret = "OK";
  //
  if ($ret != "OK")
  {
    header("location:index.php");
    die();
  }
}


function f_check_acp_rights($level)
{
  $ret = "";
  $adm_level = 0;
  if (_ACP_PROTECT_BY_HTACCESS == "")
  {
    if (isset($_SESSION['acp_level'])) $adm_level = $_SESSION['acp_level'];
    if ($adm_level & $level) $ret = "OK";
  }
  else
    $ret = "OK";
  //
  return $ret;
}


?>