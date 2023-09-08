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
define('INTRAMESSENGER',true);
require ("../common/config/config.inc.php");
//
if ( (_PUBLIC_OPTIONS_LIST != "") and (is_readable("options.php")) )
{
  header("location:options.php");
  die();
}
if ( (_PUBLIC_USERS_LIST != "") and (is_readable("users.php")) )
{
  header("location:users.php");
  die();
}
if ( (_PUBLIC_POST_AVATAR != "") and (is_readable("avatar.php")) )
{
  header("location:avatar.php");
  die();
}
if ( (_BOOKMARKS_PUBLIC != "") and (_BOOKMARKS != "") and (is_readable("bookmarks.php")) )
{
  header("location:bookmarks.php");
  die();
}
if ( (_SERVERS_STATUS != "") and (is_readable("servers_status.php")) )
{
  header("location:servers_status.php");
  die();
}
if ( (_SHOUTBOX_PUBLIC != "") and (_SHOUTBOX != "") and (is_readable("shoutbox_sticker.php")) )
{
  header("location:shoutbox_sticker.php");
  die();
}
?>

<HTML>
<HEAD>
  <META HTTP-EQUIV="REFRESH" CONTENT="0; URL=../">
</HEAD>
</HTML>